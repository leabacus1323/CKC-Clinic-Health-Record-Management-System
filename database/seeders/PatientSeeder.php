<?php

namespace Database\Seeders;

use App\Models\Patients;
use App\Models\User;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patientUsers = User::whereIn('user_type', [
            User::TYPE_STUDENT,
            User::TYPE_FACULTY,
        ])->get();

        foreach ($patientUsers as $user) {
            $isStudent = $user->user_type === User::TYPE_STUDENT;
            $prefix = $isStudent ? 'STU' : 'EMP';

            Patients::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'school_id' => $prefix . str_pad((string) $user->id, 5, '0', STR_PAD_LEFT),
                    'patient_type' => $isStudent ? 1 : 2,
                    'edulvl_id' => $isStudent ? random_int(1, 6) : null,
                    'address' => $user->address,
                    'medical_condition' => 'None',
                    'medical_illness' => 'None',
                    'operations' => 'None',
                    'allergies' => 'None',
                    'emergency_contact_name' => 'Emergency Contact',
                    'emergency_contact_number' => '09170000000',
                    'emergency_relationship' => 'Family',
                ]
            );
        }
    }
}
