<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Patients;

class ImmunizationRecords extends Model
{
    use HasFactory;

    protected $fillable = [
        'immunization_requirement_id',
        'vaccine_name',
        'vaccine_type',
        'administered_at',
        'dose_number',
        'administered_by',
        'dosage',
        'site_of_administration',
        'expiration_date',
        'notes',
        'patient_id',
        'verification_status',
        'source',
        'document_path',
        'document_original_name',
        'document_mime',
        'document_size',
        'document_sha256',
        'uploaded_by_user_id',
        'verified_by_user_id',
        'verified_at',
        'rejected_at',
        'rejection_reason',
    ];


    protected function casts(): array
    {
        return [
            'administered_at' => 'date',
            'expiration_date' => 'date',
            'verified_at' => 'datetime',
            'rejected_at' => 'datetime',
            'notes' => 'encrypted',
            'rejection_reason' => 'encrypted',
        ];
    }

    /**
     * Immunization Records to Patient Relationship (Foreign Key)
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patients::class,'patient_id','id');
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(ImmunizationRequirement::class, 'immunization_requirement_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }
}
