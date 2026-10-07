<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Appointment;
use App\Models\DentalExamination;
use App\Models\PhysicalExamination;
use App\Models\PrescriptionRecord;

class Doctors extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specialization',
        'address',
        'is_available',
    ];

    /**
     * Get the user that owns the Doctors
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Doctor to Appointments Relationship (One-to-Many)
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'doc_id', 'id');
    }

    /**
     * Doctor to Dental Examinations Relationship (One-to-Many)
     */
    public function dentalExaminations(): HasMany
    {
        return $this->hasMany(DentalExamination::class, 'doctor_id', 'id');
    }

    /**
     * Doctor to Physical Examinations Relationship (One-to-Many)
     */
    public function physicalExaminations(): HasMany
    {
        return $this->hasMany(PhysicalExamination::class, 'doctor_id', 'id');
    }

    /**
     * Doctor to Prescription Records Relationship (One-to-Many)
     */
    public function prescriptionRecords(): HasMany
    {
        return $this->hasMany(PrescriptionRecord::class, 'doctor_id', 'id');
    }
}
