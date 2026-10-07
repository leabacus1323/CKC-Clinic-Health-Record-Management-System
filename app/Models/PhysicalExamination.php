<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Patients;
use App\Models\Doctors;
use App\Models\User;

class PhysicalExamination extends Model
{
    use HasFactory;

    protected $fillable = [
        'height',
        'weight',
        'heart',
        'lungs',
        'eyes',
        'ears',
        'nose',
        'throat',
        'skin',
        'bp',
        'remarks',
        'patient_id',
        'doctor_id',
    ];


    protected function casts(): array
    {
        return [
            'heart' => 'encrypted',
            'lungs' => 'encrypted',
            'eyes' => 'encrypted',
            'ears' => 'encrypted',
            'nose' => 'encrypted',
            'throat' => 'encrypted',
            'skin' => 'encrypted',
            'remarks' => 'encrypted',
        ];
    }

    /**
     * Physical Examination to Patient Relationship (Foreign Key)
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patients::class, 'patient_id', 'id');
    }

    /**
     * Physical Examination to Doctor Relationship (Foreign Key)
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctors::class, 'doctor_id', 'id');
    }

    /**
     * Get the user through the patient relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id', 'id')
            ->join('patients', 'users.id', '=', 'patients.user_id')
            ->where('patients.id', $this->patient_id);
    }
}
