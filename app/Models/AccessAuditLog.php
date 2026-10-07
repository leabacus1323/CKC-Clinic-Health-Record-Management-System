<?php

namespace App\Models;

use App\Models\Concerns\AppendOnly;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessAuditLog extends Model
{
    use AppendOnly;

    public $timestamps = false;

    protected $fillable = [
        'public_id', 'actor_user_id', 'patient_id', 'action', 'access_type',
        'scopes', 'purpose', 'route_name', 'request_method', 'ip_address',
        'user_agent', 'metadata', 'previous_hash', 'entry_hash', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'scopes' => 'array',
            'metadata' => 'array',
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
