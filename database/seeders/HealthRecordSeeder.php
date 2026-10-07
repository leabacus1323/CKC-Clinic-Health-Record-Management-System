<?php

namespace Database\Seeders;

use App\Models\HealthRecords;
use App\Models\Patients;
use App\Models\User;
use Illuminate\Database\Seeder;

class HealthRecordSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patients::all();
        $doctors = User::where('user_type', 3)->get();

        foreach ($patients as $patient) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                HealthRecords::create([
                    'user_id' => $patient->user_id,
                    'patient_id' => $patient->id,
                    'diagnosis' => ['Common Cold', 'Flu', 'Skin Infection', 'Headache', 'Fever'][rand(0, 4)],
                    'symptoms' => ['Cough', 'Sore Throat', 'Body Aches', 'Fatigue'][rand(0, 3)],
                    'treatment' => ['Rest and fluids', 'Medication', 'Physical therapy'][rand(0, 2)],
                    'date_recorded' => now()->subDays(rand(1, 30)),
                    'notes' => 'Patient examined and treated successfully.',
                ]);
            }
        }
    }
}
