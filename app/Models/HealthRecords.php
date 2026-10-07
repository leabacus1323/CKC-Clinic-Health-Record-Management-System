<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthRecords extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'patient_id',
        'diagnosis',
        'symptoms',
        'treatment',
        'notes',
        'date_recorded',
    ];


    protected function casts(): array
    {
        return [
            'date_recorded' => 'date',
            'diagnosis' => 'encrypted',
            'symptoms' => 'encrypted',
            'treatment' => 'encrypted',
            'notes' => 'encrypted',
        ];
    }

    /**
     * Health Record belongs to a User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Health Record belongs to a Patient
     * This relationship is used in doctor queries like whereHas('patient.appointments')
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patients::class, 'patient_id', 'id');
    }
}
