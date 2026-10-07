<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\EducationalLevel;
use App\Models\Course;
use App\Models\ImmunizationRecords;
use App\Models\PrescriptionRecord;
use App\Models\DentalExamination;
use App\Models\PhysicalExamination;
use App\Models\HealthRecords;
use App\Models\Appointment;
use App\Models\NurseLog;

class Patients extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (Patients $patient): void {
            $student = (int) $patient->patient_type === 1;
            $patient->registry_type = $student ? 'student' : 'non_student';
            if (! $patient->privacy_regime || $patient->isDirty('patient_type')) {
                $patient->privacy_regime = $student
                    ? (string) config('health_privacy.student_privacy_regime', 'ferpa_student')
                    : (string) config('health_privacy.non_student_privacy_regime', 'hipaa_workforce');
            }
        });
    }
   
    protected $fillable = [
        'patient_type',
        'registry_type',
        'privacy_regime',
        'edulvl_id',
        'course_id',
        'user_id',
        'school_id',
        'bloodtype',
        'address',
        'medical_condition',
        'medical_illness',
        'operations',
        'allergies',
        'life_safety_visible_to_teachers',
        'life_safety_alert',
        'emergency_medication',
        'life_safety_instructions',
        'life_safety_reviewed_at',
        'life_safety_reviewed_by',
        'emergency_contact_name',
        'emergency_contact_number',
        'emergency_relationship',
    ];


    protected function casts(): array
    {
        return [
            'patient_type' => 'integer',
            'life_safety_visible_to_teachers' => 'boolean',
            'life_safety_reviewed_at' => 'datetime',
        ];
    }

    public function isStudentRegistry(): bool
    {
        return $this->registry_type === 'student' || (int) $this->patient_type === 1;
    }

    public function isMinor(): bool
    {
        return $this->user?->dob !== null
            && now()->diffInYears($this->user->dob) < 18;
    }

    /**
     * Determine whether consent authority belongs to an approved guardian.
     * The postsecondary option supports institutions where record rights transfer
     * to the attending student regardless of age.
     */
    public function requiresGuardianConsent(): bool
    {
        if (! $this->isStudentRegistry()) {
            return false;
        }

        if (config('health_privacy.student_rights_transfer_mode') === 'postsecondary') {
            return false;
        }

        return $this->isMinor();
    }

    /**
     * Patient to User Relationship (Foreign Key)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    /**
     * Patient to Educational Level Relationship (Foreign Key)
     */
    public function educationalLevel(): BelongsTo
    {
        return $this->belongsTo(EducationalLevel::class, 'edulvl_id', 'id');
    }

    /**
     * Patient to Course Relationship (Foreign Key)
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    /**
     * Patient to Immunization Records Relationship (One-to-Many)
     */
    public function immunizations()
    {
        return $this->hasMany(ImmunizationRecords::class,'patient_id','id');
    }

    /**
     * Patient to Prescription Records Relationship (One-to-Many)
     */
    public function prescriptions()
    {
        return $this->hasMany(PrescriptionRecord::class,'patient_id','id');
    }

    /**
     * Patient to Dental Examination Relationship (One-to-Many)
     */
    public function dentalExaminations(): HasMany
    {
        return $this->hasMany(DentalExamination::class, 'patient_id', 'id');
    }

    /**
     * Patient to Physical Examination Relationship (One-to-Many)
     */
    public function physicalExaminations(): HasMany
    {
        return $this->hasMany(PhysicalExamination::class, 'patient_id', 'id');
    }

    /**
     * Patient to Health Records Relationship (One-to-Many)
     */
    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecords::class, 'patient_id', 'id');
    }

    /**
     * Patient to Appointments Relationship (One-to-Many)
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id', 'id');
    }

    public function guardians(): HasMany
    {
        return $this->hasMany(PatientGuardian::class, 'patient_id');
    }

    public function dataAuthorizations(): HasMany
    {
        return $this->hasMany(DataAuthorization::class, 'patient_id');
    }

    public function breakGlassEvents(): HasMany
    {
        return $this->hasMany(BreakGlassEvent::class, 'patient_id');
    }
    /**
     * Patient to Nurse Logs Relationship (One-to-Many)
     */
    public function nurseLogs(): HasMany
    {
        return $this->hasMany(NurseLog::class, 'patient_id', 'id');
    }

}
