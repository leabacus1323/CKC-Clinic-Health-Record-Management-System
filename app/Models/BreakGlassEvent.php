<?php

namespace App\Models;

use App\Models\Concerns\AppendOnly;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BreakGlassEvent extends Model
{
    use AppendOnly;

    public $timestamps = false;

    protected $fillable = [
        'public_id', 'actor_user_id', 'patient_id', 'threat_description',
        'justification', 'recipient_party', 'disclosed_scopes', 'session_fingerprint',
        'activated_at', 'expires_at', 'ip_address', 'user_agent', 'previous_hash',
        'entry_hash', 'created_at',
    ];

    protected $hidden = ['session_fingerprint'];

    protected function casts(): array
    {
        return [
            'disclosed_scopes' => 'array',
            'activated_at' => 'datetime',
            'expires_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patients::class, 'patient_id');
    }
}
