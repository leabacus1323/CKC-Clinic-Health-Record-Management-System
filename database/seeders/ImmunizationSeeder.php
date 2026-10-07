<?php

namespace Database\Seeders;

use App\Models\Immunization;
use Illuminate\Database\Seeder;

class ImmunizationSeeder extends Seeder
{
    public function run(): void
    {
        $immunizations = [
            ['name' => 'BCG', 'description' => 'Bacille Calmette-Guérin'],
            ['name' => 'DPT', 'description' => 'Diphtheria, Pertussis, Tetanus'],
            ['name' => 'Oral Polio', 'description' => 'Oral Polio Vaccine'],
            ['name' => 'Measles', 'description' => 'Measles Vaccine'],
            ['name' => 'MMR', 'description' => 'Measles, Mumps, Rubella'],
            ['name' => 'Hepatitis A', 'description' => 'Hepatitis A Vaccine'],
            ['name' => 'Hepatitis B', 'description' => 'Hepatitis B Vaccine'],
            ['name' => 'COVID-19', 'description' => 'COVID-19 Vaccine'],
            ['name' => 'Influenza', 'description' => 'Flu Vaccine'],
            ['name' => 'Tetanus', 'description' => 'Tetanus Booster'],
        ];

        foreach ($immunizations as $immunization) {
            Immunization::create($immunization);
        }
    }
}
