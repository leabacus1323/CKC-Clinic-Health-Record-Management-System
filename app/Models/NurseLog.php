<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NurseLog extends Model
{
    use HasFactory;

    public const INTERVENTION_PRESCRIBED_MEDICINE = 'prescribed_medicine';
    public const INTERVENTION_MGH = 'mgh';
    public const INTERVENTION_REST = 'rest';
    public const INTERVENTION_FIRST_AID = 'first_aid';
    public const INTERVENTION_OTHER = 'other';

    protected $fillable = [
        'patient_id',
        'staff_user_id',
        'time_in',
        'time_out',
        'complaint',
        'intervention_type',
        'intervention_notes',
        'medicine_id',
        'prescription_record_id',
        'medicine_quantity',
        'mgh_status',
        'rest_minutes',
        'nurse_bed_id',
        'first_aid_details',
    ];

    protected function casts(): array
    {
        return [
            'time_in' => 'datetime',
            'time_out' => 'datetime',
            'complaint' => 'encrypted',
            'intervention_notes' => 'encrypted',
            'first_aid_details' => 'encrypted',
            'medicine_quantity' => 'integer',
            'rest_minutes' => 'integer',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patients::class, 'patient_id', 'id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_user_id', 'id');
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

    public function interventions(): HasMany
    {
        return $this->hasMany(NurseLogIntervention::class);
    }

    public function getInterventionLabelAttribute(): string
    {
        if ($this->relationLoaded('interventions') && $this->interventions->isNotEmpty()) {
            return $this->interventions
                ->pluck('intervention_type')
                ->unique()
                ->map(fn (string $type): string => self::interventionLabel($type))
                ->implode(', ');
        }

        return self::interventionLabel($this->intervention_type);
    }

    public static function interventionLabel(?string $type): string
    {
        return match ($type) {
            self::INTERVENTION_PRESCRIBED_MEDICINE => 'Prescribed Medicine',
            self::INTERVENTION_MGH => 'MGH / May Go Home',
            self::INTERVENTION_REST => 'Rest',
            self::INTERVENTION_FIRST_AID => 'First Aid',
            default => 'Other Intervention',
        };
    }
}

