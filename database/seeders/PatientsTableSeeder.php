<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PatientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('patients')->insert([
            'name' => 'Lyjieme Barro',
            'age' => 26,
            'sex' => 0,
            'date_of_birth' => now(),
            'contact_number' => '+639363991178',
            'medical_history' => 'N/A',
            'allergies' => 'N/A',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
