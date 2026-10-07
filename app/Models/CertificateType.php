<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificateType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the certificate requests for this type.
     */
    public function certificateRequests(): HasMany
    {
        return $this->hasMany('App\Models\CertificateRequest');
    }

    /**
     * Scope to get only active certificate types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}