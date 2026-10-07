<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Patients;
use App\Models\Doctors;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patients::all();
        $doctors = Doctors::all();

        if ($patients->isEmpty() || $doctors->isEmpty()) {
            return;
        }

        foreach ($patients as $patient) {
            for ($i = 0; $i < rand(2, 4); $i++) {
                $doctor = $doctors->random();
                Appointment::create([
                    'patient_id' => $patient->id,
                    'date' => now()->addDays(rand(1, 30))->format('Y-m-d'),
                    'time' => ['09:00', '10:00', '14:00', '15:00', '16:00'][rand(0, 4)],
                    'doc_id' => $doctor->id,
                    'status' => ['Pending', 'Confirmed', 'Cancelled'][rand(0, 2)],
                    'reason' => ['Regular checkup', 'Follow-up', 'Sick visit', 'Vaccination'][rand(0, 3)],
                ]);
            }
        }
    }
}
