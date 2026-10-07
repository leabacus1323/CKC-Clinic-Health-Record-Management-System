<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class NurseBed extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function activeRestLog(): HasOne
    {
        return $this->hasOne(NurseLog::class, 'nurse_bed_id')
            ->where('intervention_type', NurseLog::INTERVENTION_REST)
            ->whereNull('time_out')
            ->latestOfMany('time_in');
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->is_active && $this->activeRestLog === null;
    }

    public function getAvailabilityLabelAttribute(): string
    {
        return $this->is_available ? 'Available' : 'Occupied';
    }
}
