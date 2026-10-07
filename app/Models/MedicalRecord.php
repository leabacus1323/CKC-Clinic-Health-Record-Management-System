<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $table = 'medical_records';
    protected $primaryKey = 'medical_id';

    protected $fillable = [
        'patient_id',
        'staff_id',
        'date_of_consultation',
        'reason_for_consultation',
        'diagnosis',
        'prescription',
        'follow_up_appointment',
    ];

    protected function casts(): array
    {
        return [
            'date_of_consultation' => 'date',
            'follow_up_appointment' => 'date',
            'reason_for_consultation' => 'encrypted',
            'diagnosis' => 'encrypted',
            'prescription' => 'encrypted',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patients::class, 'patient_id');
    }
}
