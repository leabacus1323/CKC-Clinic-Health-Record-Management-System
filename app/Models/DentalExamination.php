<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Patients;
use App\Models\Doctors;

class DentalExamination extends Model
{
    use HasFactory;

    protected $fillable = [
        'teeth_status',
        'patient_id',
        'doctor_id',
        'diagnosis',
    ];

    protected function casts(): array
    {
        return [
            'teeth_status' => 'encrypted:array',
            'diagnosis' => 'encrypted',
        ];
    }

    /**
     * Dental Examination to Patient Relationship (Foreign Key)
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patients::class, 'patient_id', 'id');
    }

    /**
     * Dental Examination to Doctor Relationship (Foreign Key)
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctors::class, 'doctor_id', 'id');
    }
}
