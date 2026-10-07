<?php

namespace App\Services;

use App\Models\AccessAuditLog;
use App\Models\Patients;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccessAuditService
{
    public function __construct(private readonly MedicalAuditService $externalAudit)
    {
    }

    public function record(
        ?User $actor,
        ?Patients $patient,
        string $action,
        string $accessType,
        array $scopes = [],
        ?string $purpose = null,
        array $metadata = [],
        ?Request $request = null,
    ): AccessAuditLog {
        $request ??= request();

        $this->externalAudit->record(
            $actor,
            $this->externalActionType($action),
            $action,
            $patient,
            [
                'resource_type' => 'ProtectedHealthAccess',
                'resource_id' => $metadata['record_id'] ?? $metadata['authorization_id'] ?? $metadata['guardian_link_id'] ?? null,
                'access_type' => $accessType,
                'scope_names' => array_values($scopes),
                'purpose_code' => $purpose ? hash('sha256', $purpose) : null,
            ],
            $request,
        );

        return DB::transaction(function () use ($actor, $patient, $action, $accessType, $scopes, $purpose, $metadata, $request) {
            $previous = AccessAuditLog::query()->latest('id')->lockForUpdate()->first();
            $createdAt = now()->startOfSecond();
            $publicId = (string) Str::uuid();
            $userAgent = mb_substr((string) $request?->userAgent(), 0, 2000);
            $payload = [
                'public_id' => $publicId,
                'actor_user_id' => $actor?->id,
                'patient_id' => $patient?->id,
                'action' => $action,
                'access_type' => $accessType,
                'scopes' => array_values($scopes),
                'purpose' => $purpose,
                'route_name' => $request?->route()?->getName(),
                'request_method' => $request?->method(),
                'ip_address' => $request?->ip(),
                'user_agent' => $userAgent,
                'metadata' => $metadata,
                'created_at' => $createdAt->format('Y-m-d H:i:s'),
                'previous_hash' => $previous?->entry_hash,
            ];

            return AccessAuditLog::create([
                ...$payload,
                'user_agent' => $userAgent,
                'entry_hash' => hash('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)),
                'created_at' => $createdAt,
            ]);
        });
    }

    private function externalActionType(string $eventName): string
    {
        $eventName = strtolower($eventName);

        if (str_contains($eventName, 'download') || str_contains($eventName, 'export') || str_contains($eventName, 'print')) {
            return 'EXPORT';
        }

        if (str_contains($eventName, 'view')) {
            return 'READ';
        }

        if (str_contains($eventName, 'delete') || str_contains($eventName, 'disabled') || str_contains($eventName, 'revoked')) {
            return 'DELETE';
        }

        return 'WRITE';
    }

}
