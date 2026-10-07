<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Patients;
use App\Models\Doctors;
use App\Models\Medicine;

class PrescriptionRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'dosage',
        'frequency',
        'duration',
        'instruction',
        'date_prescribed',
        'date_discontinued',
        'status',
        'discontinuation_reason',
        'patient_id',
        'doctor_id',
        'medicine_id',
        'prescribed_by',
    ];


    protected function casts(): array
    {
        return [
            'date_prescribed' => 'date',
            'date_discontinued' => 'date',
            'instruction' => 'encrypted',
            'discontinuation_reason' => 'encrypted',
        ];
    }

    /**
     * Prescription Records to Patient Relationship (Foreign Key)
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patients::class,'patient_id','id');
    }

    /**
     * Prescription Records to Doctor Relationship (Foreign Key)
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctors::class,'doctor_id','id');
    }

    /**
     * Prescription Records to Medicine Relationship (Foreign Key)
     */
    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class,'medicine_id','id');
    }
}
