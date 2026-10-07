<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImmunizationRequirement extends Model
{
    protected $fillable = [
        'code', 'name', 'description', 'registry_type', 'minimum_doses',
        'due_age_months', 'booster_interval_months', 'required_by_date', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'required_by_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function scopeForRegistry(Builder $query, string $registryType): Builder
    {
        return $query->where('is_active', true)
            ->whereIn('registry_type', ['all', $registryType]);
    }

    public function records(): HasMany
    {
        return $this->hasMany(ImmunizationRecords::class, 'immunization_requirement_id');
    }
}
