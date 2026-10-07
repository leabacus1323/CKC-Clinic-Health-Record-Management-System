<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthAnalyticsSnapshot extends Model
{
    protected $fillable = [
        'period_days',
        'period_start',
        'period_end',
        'metrics',
        'trends',
        'recommendations',
        'generated_by',
        'generated_by_user_id',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'period_days' => 'integer',
            'period_start' => 'date',
            'period_end' => 'date',
            'metrics' => 'array',
            'trends' => 'array',
            'recommendations' => 'array',
            'generated_at' => 'datetime',
        ];
    }

    public function generatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by_user_id');
    }
}
