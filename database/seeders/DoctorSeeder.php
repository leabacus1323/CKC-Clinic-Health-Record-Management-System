<?php

namespace Database\Seeders;

use App\Models\Doctors;
use App\Models\User;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        // Get doctor users
        $doctor1 = User::where('email', 'doctor1@ckc.edu')->first();
        $doctor2 = User::where('email', 'doctor2@ckc.edu')->first();

        if ($doctor1) {
            Doctors::create([
                'user_id' => $doctor1->id,
                'specialization' => 'General Medicine',
                'address' => 'Medical Center, Building C, Room 101',
            ]);
        }

        if ($doctor2) {
            Doctors::create([
                'user_id' => $doctor2->id,
                'specialization' => 'Pediatrics',
                'address' => 'Medical Center, Building C, Room 102',
            ]);
        }
    }
}
