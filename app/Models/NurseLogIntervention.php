<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NurseLogIntervention extends Model
{
    use HasFactory;

    protected $fillable = [
        'nurse_log_id',
        'intervention_type',
        'notes',
        'medicine_id',
        'prescription_record_id',
        'medicine_quantity',
        'dosage',
        'frequency',
        'duration',
        'prescription_instruction',
        'mgh_status',
        'rest_minutes',
        'nurse_bed_id',
        'first_aid_details',
    ];

    protected function casts(): array
    {
        return [
            'notes' => 'encrypted',
            'first_aid_details' => 'encrypted',
            'prescription_instruction' => 'encrypted',
            'medicine_quantity' => 'integer',
            'rest_minutes' => 'integer',
        ];
    }

    public function nurseLog(): BelongsTo
    {
        return $this->belongsTo(NurseLog::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class, 'medicine_id', 'id');
    }

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(PrescriptionRecord::class, 'prescription_record_id', 'id');
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(NurseBed::class, 'nurse_bed_id', 'id');
    }

    public function getInterventionLabelAttribute(): string
    {
        return NurseLog::interventionLabel($this->intervention_type);
    }
}
