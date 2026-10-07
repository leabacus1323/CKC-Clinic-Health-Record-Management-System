<?php

namespace App\Services;

use App\Models\AccessAuditLog;
use App\Models\BreakGlassEvent;
use Illuminate\Database\Eloquent\Model;

class AuditIntegrityService
{
    public function verify(): array
    {
        return [
            'access_audit_logs' => $this->verifyAccessLogs(),
            'break_glass_events' => $this->verifyBreakGlassEvents(),
        ];
    }

    private function verifyAccessLogs(): array
    {
        return $this->verifyChain(
            AccessAuditLog::query()->orderBy('id')->cursor(),
            fn (AccessAuditLog $entry): array => [
                'public_id' => $entry->public_id,
                'actor_user_id' => $entry->actor_user_id,
                'patient_id' => $entry->patient_id,
                'action' => $entry->action,
                'access_type' => $entry->access_type,
                'scopes' => array_values($entry->scopes ?? []),
                'purpose' => $entry->purpose,
                'route_name' => $entry->route_name,
                'request_method' => $entry->request_method,
                'ip_address' => $entry->ip_address,
                'user_agent' => $entry->user_agent,
                'metadata' => $entry->metadata ?? [],
                'created_at' => $entry->created_at?->format('Y-m-d H:i:s'),
                'previous_hash' => $entry->previous_hash,
            ],
        );
    }

    private function verifyBreakGlassEvents(): array
    {
        return $this->verifyChain(
            BreakGlassEvent::query()->orderBy('id')->cursor(),
            fn (BreakGlassEvent $entry): array => [
                'public_id' => $entry->public_id,
                'actor_user_id' => $entry->actor_user_id,
                'patient_id' => $entry->patient_id,
                'threat_description' => $entry->threat_description,
                'justification' => $entry->justification,
                'recipient_party' => $entry->recipient_party,
                'disclosed_scopes' => array_values($entry->disclosed_scopes ?? []),
                'session_fingerprint' => $entry->getRawOriginal('session_fingerprint'),
                'activated_at' => $entry->activated_at?->format('Y-m-d H:i:s'),
                'expires_at' => $entry->expires_at?->format('Y-m-d H:i:s'),
                'ip_address' => $entry->ip_address,
                'user_agent' => $entry->user_agent,
                'previous_hash' => $entry->previous_hash,
            ],
        );
    }

    private function verifyChain(iterable $entries, callable $payloadBuilder): array
    {
        $previousHash = null;
        $checked = 0;

        /** @var Model $entry */
        foreach ($entries as $entry) {
            $checked++;
            if (! hash_equals((string) ($previousHash ?? ''), (string) ($entry->previous_hash ?? ''))) {
                return ['valid' => false, 'checked' => $checked, 'failed_id' => $entry->getKey(), 'reason' => 'previous_hash_mismatch'];
            }

            $expected = hash('sha256', json_encode($payloadBuilder($entry), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            if (! hash_equals($expected, (string) $entry->entry_hash)) {
                return ['valid' => false, 'checked' => $checked, 'failed_id' => $entry->getKey(), 'reason' => 'entry_hash_mismatch'];
            }

            $previousHash = $entry->entry_hash;
        }

        return ['valid' => true, 'checked' => $checked, 'failed_id' => null, 'reason' => null];
    }
}
