<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfrastructureEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'actor_user_id', 'actor_user_type', 'actor_role', 'action',
        'event_category', 'description', 'route_name', 'request_method',
        'response_status', 'ip_address', 'user_agent', 'request_id', 'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'actor_user_type' => 'integer',
            'response_status' => 'integer',
            'occurred_at' => 'datetime',
        ];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
