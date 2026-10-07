<?php

namespace App\Services;

use App\Exceptions\AuditUnavailableException;
use App\Models\Patients;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class MedicalAuditService
{
    public function __construct(private readonly MedicalAuditConfigurationValidator $configuration)
    {
    }

    public function record(
        ?User $actor,
        string $actionType,
        string $eventName,
        ?Patients $patient = null,
        array $metadata = [],
        ?Request $request = null,
    ): void {
        $this->configuration->assertSecure();

        $request ??= request();
        $occurredAt = now()->utc()->startOfSecond();
        $eventId = (string) Str::ulid();

        $payload = [
            'event_id' => $eventId,
            'actor_user_id' => $actor?->id,
            'actor_role' => $actor?->roleLabel(),
            'patient_id' => $patient?->id,
            'action_type' => strtoupper($actionType),
            'event_name' => $eventName,
            'route_name' => $request?->route()?->getName(),
            'request_method' => $request?->method(),
            'request_path' => $request?->path(),
            'response_status' => $metadata['response_status'] ?? null,
            'ip_address' => $request?->ip(),
            'user_agent' => mb_substr((string) $request?->userAgent(), 0, 1000),
            'resource_type' => $metadata['resource_type'] ?? null,
            'resource_id' => isset($metadata['resource_id']) ? (string) $metadata['resource_id'] : null,
            'metadata' => $this->sanitizeMetadata($metadata),
            'occurred_at' => $occurredAt->format('Y-m-d H:i:s'),
        ];

        $signingKey = (string) config('medical_security.audit.signing_key');
        if ($signingKey === '') {
            // Local/test environments may omit a dedicated key, but production
            // enforcement above always requires one.
            $signingKey = (string) config('app.key');
        }
        $payload['integrity_hmac'] = hash_hmac(
            'sha256',
            json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            $signingKey
        );

        try {
            match ((string) config('medical_security.audit.driver', 'database')) {
                'http' => $this->sendHttp($payload),
                'array' => $this->recordInMemoryForTests($payload),
                default => $this->insertDatabase($payload),
            };
        } catch (Throwable $exception) {
            report($exception);

            if ((bool) config('medical_security.audit.fail_closed', true)) {
                throw new AuditUnavailableException(
                    'The protected audit service is unavailable. Sensitive access was blocked.',
                    previous: $exception
                );
            }
        }
    }

    private function insertDatabase(array $payload): void
    {
        DB::connection((string) config('medical_security.audit.connection', 'audit'))
            ->table((string) config('medical_security.audit.table', 'medical_access_events'))
            ->insert([
                ...$payload,
                'metadata' => json_encode($payload['metadata'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ]);
    }

    private function sendHttp(array $payload): void
    {
        $endpoint = (string) config('medical_security.audit.http_endpoint');
        if ($endpoint === '') {
            throw new AuditUnavailableException('MEDICAL_AUDIT_HTTP_ENDPOINT is not configured.');
        }

        $request = Http::asJson()
            ->acceptJson()
            ->timeout((int) config('medical_security.audit.http_timeout_seconds', 5));

        $token = (string) config('medical_security.audit.http_token');
        if ($token !== '') {
            $request = $request->withToken($token);
        }

        $request->retry(2, 200, throw: false)
            ->post($endpoint, $payload)
            ->throw();
    }

    private function recordInMemoryForTests(array $payload): void
    {
        if (! app()->environment('testing')) {
            throw new AuditUnavailableException('The array audit driver is permitted only in testing.');
        }

        $events = app()->bound('medical-audit.test-events')
            ? app('medical-audit.test-events')
            : [];

        app()->instance('medical-audit.test-events', [...$events, $payload]);
    }

    private function sanitizeMetadata(array $metadata): array
    {
        unset($metadata['response_status']);

        $blockedKeys = [
            'diagnosis', 'symptoms', 'treatment', 'notes', 'prescription',
            'prescriptions', 'medical_condition', 'medical_illness', 'operations',
            'allergies', 'password', 'password_confirmation', 'token',
            'authorization', 'file_contents', 'clinical_text', 'reason_for_visit',
        ];

        $clean = [];
        foreach ($metadata as $key => $value) {
            $normalizedKey = strtolower((string) $key);
            if (in_array($normalizedKey, $blockedKeys, true)) {
                continue;
            }

            if (is_array($value)) {
                $clean[$key] = $this->sanitizeMetadata($value);
                continue;
            }

            if (is_scalar($value) || $value === null) {
                $clean[$key] = is_string($value) ? mb_substr($value, 0, 1000) : $value;
            }
        }

        return $clean;
    }
}
