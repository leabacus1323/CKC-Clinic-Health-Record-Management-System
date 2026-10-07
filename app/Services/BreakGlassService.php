<?php

namespace App\Services;

use App\Models\BreakGlassEvent;
use App\Models\Patients;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BreakGlassService
{
    public function __construct(private readonly AccessAuditService $audit)
    {
    }

    public function activate(User $actor, Patients $patient, array $data, Request $request): BreakGlassEvent
    {
        if ($actor->user_type !== User::TYPE_PRINCIPAL) {
            throw new AuthorizationException('Only the designated Principal / Emergency Administrator may activate emergency access.');
        }

        $minutes = max(1, min(30, (int) config('health_privacy.break_glass_minutes', 10)));
        $activatedAt = now()->startOfSecond();
        $expiresAt = $activatedAt->copy()->addMinutes($minutes);
        $scopes = config('health_privacy.break_glass_scopes', []);
        $sessionFingerprint = $this->sessionFingerprint($request, $actor);

        $event = DB::transaction(function () use ($actor, $patient, $data, $request, $activatedAt, $expiresAt, $scopes, $sessionFingerprint) {
            $previous = BreakGlassEvent::query()->latest('id')->lockForUpdate()->first();
            $publicId = (string) Str::uuid();
            $userAgent = mb_substr((string) $request->userAgent(), 0, 2000);
            $payload = [
                'public_id' => $publicId,
                'actor_user_id' => $actor->id,
                'patient_id' => $patient->id,
                'threat_description' => $data['threat_description'],
                'justification' => $data['justification'],
                'recipient_party' => ($data['recipient_party'] ?? null) ?: $actor->name.' (emergency viewer)',
                'disclosed_scopes' => $scopes,
                'session_fingerprint' => $sessionFingerprint,
                'activated_at' => $activatedAt->format('Y-m-d H:i:s'),
                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
                'ip_address' => $request->ip(),
                'user_agent' => $userAgent,
                'previous_hash' => $previous?->entry_hash,
            ];

            return BreakGlassEvent::create([
                ...$payload,
                'user_agent' => $userAgent,
                'entry_hash' => hash('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)),
                'created_at' => $activatedAt,
            ]);
        });

        $request->session()->put("break_glass.{$patient->id}", [
            'event' => $event->public_id,
            'expires_at' => $expiresAt->timestamp,
            'fingerprint' => $sessionFingerprint,
        ]);

        $this->audit->record($actor, $patient, 'break_glass_activated', 'break_glass', $scopes, $data['justification'], [
            'event_public_id' => $event->public_id,
            'threat_description' => $data['threat_description'],
            'recipient_party' => ($data['recipient_party'] ?? null) ?: $actor->name.' (emergency viewer)',
            'expires_at' => $expiresAt->toISOString(),
        ], $request);

        Log::critical('Emergency break-glass access activated.', [
            'event' => $event->public_id,
            'actor_user_id' => $actor->id,
            'patient_id' => $patient->id,
            'expires_at' => $expiresAt->toISOString(),
        ]);

        $this->notifyPrivacyOfficer($event, $actor, $patient);

        return $event;
    }

    public function activeEvent(User $actor, Patients $patient, Request $request): ?BreakGlassEvent
    {
        $grant = $request->session()->get("break_glass.{$patient->id}");
        if (! is_array($grant) || ($grant['expires_at'] ?? 0) <= now()->timestamp) {
            $request->session()->forget("break_glass.{$patient->id}");
            return null;
        }

        if (! hash_equals((string) ($grant['fingerprint'] ?? ''), $this->sessionFingerprint($request, $actor))) {
            return null;
        }

        return BreakGlassEvent::query()
            ->where('public_id', $grant['event'] ?? '')
            ->where('actor_user_id', $actor->id)
            ->where('patient_id', $patient->id)
            ->where('expires_at', '>', now())
            ->first();
    }

    private function sessionFingerprint(Request $request, User $actor): string
    {
        return hash_hmac('sha256', $request->session()->getId().'|'.$actor->id, (string) config('app.key'));
    }

    private function notifyPrivacyOfficer(BreakGlassEvent $event, User $actor, Patients $patient): void
    {
        $email = config('health_privacy.privacy_officer_email');
        if (! $email) {
            return;
        }

        try {
            Mail::raw(
                "Emergency access {$event->public_id} was activated by {$actor->name} for patient record #{$patient->id}. It expires at {$event->expires_at}.",
                fn ($message) => $message->to($email)->subject('Immediate privacy alert: emergency record access')
            );
        } catch (\Throwable $exception) {
            Log::error('Privacy-officer break-glass email failed.', ['error' => $exception->getMessage()]);
        }
    }
}
