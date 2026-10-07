<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientGuardian extends Model
{
    protected $fillable = [
        'patient_id', 'guardian_user_id', 'invited_email', 'full_name',
        'relationship', 'can_manage_consents', 'is_active', 'verified_at',
        'approved_at', 'approved_by_user_id', 'approval_notes', 'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'can_manage_consents' => 'boolean',
            'is_active' => 'boolean',
            'verified_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patients::class, 'patient_id');
    }

    public function guardianUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guardian_user_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
