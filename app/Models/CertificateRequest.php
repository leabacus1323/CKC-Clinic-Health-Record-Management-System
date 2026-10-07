<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Patients;
use App\Models\CertificateType;
use App\Models\Doctors;
use App\Models\Appointment;

class CertificateRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'certificate_type_id',
        'doctor_id',
        'appointment_id',
        'reason',
        'status',
        'doctor_notes',
        'issued_date',
        'document_path',
    ];

    protected $casts = [
        'issued_date' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_ISSUED = 'issued';

    /**
     * Get the patient that owns this certificate request.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patients::class);
    }

    /**
     * Get the certificate type.
     */
    public function certificateType(): BelongsTo
    {
        return $this->belongsTo(CertificateType::class);
    }

    /**
     * Get the assigned doctor.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctors::class);
    }

    /**
     * Get the associated appointment.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Scope to get pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to get requests for a specific patient.
     */
    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    /**
     * Check if request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if request is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if request can be approved.
     */
    public function canBeApproved(): bool
    {
        return $this->isPending();
    }
}