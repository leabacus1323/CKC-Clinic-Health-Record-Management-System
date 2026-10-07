<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataAuthorization extends Model
{
    protected $fillable = [
        'public_id', 'patient_id', 'grantor_user_id', 'recipient_user_id',
        'recipient_name', 'recipient_email', 'scopes', 'purpose', 'signature_name',
        'signature_hash', 'authorization_version', 'authorization_text', 'status', 'effective_at', 'expires_at', 'revoked_at',
        'revoked_by_user_id', 'revocation_reason', 'signed_ip', 'signed_user_agent',
    ];

    protected $hidden = ['signature_hash', 'signed_ip', 'signed_user_agent'];

    protected function casts(): array
    {
        return [
            'scopes' => 'array',
            'effective_at' => 'datetime',
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->where('effective_at', '<=', now())
            ->where(function (Builder $q): void {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function hasValidSignature(): bool
    {
        $scopes = array_values(array_unique($this->scopes ?? []));
        sort($scopes);

        $payload = implode('|', [
            $this->public_id,
            $this->patient_id,
            $this->grantor_user_id,
            mb_strtolower($this->recipient_email),
            json_encode($scopes),
            $this->purpose,
            $this->effective_at?->format('Y-m-d H:i:s'),
            $this->signed_ip,
            $this->authorization_version,
            hash('sha256', (string) $this->authorization_text),
        ]);

        return hash_equals(
            (string) $this->signature_hash,
            hash_hmac('sha256', $payload, (string) config('app.key')),
        );
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patients::class, 'patient_id');
    }

    public function grantor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'grantor_user_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }
}
