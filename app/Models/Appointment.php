<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;
    protected $fillable = [
        'patient_id',
        'date',
        'time',
        'doc_id',
        'status',
        'reason',
    ];


    protected function casts(): array
    {
        return [
            'date' => 'date',
            'reason' => 'encrypted',
        ];
    }

    const STATUS_PENDING = 'Pending';
    const STATUS_CONFIRMED = 'Confirmed';
    const STATUS_CANCELLED = 'Cancelled';

    /**
     * Get the patient that owns the appointment
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patients::class, 'patient_id');
    }

    /**
     * Get the doctor that owns the appointment
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctors::class, 'doc_id');
    }
}
