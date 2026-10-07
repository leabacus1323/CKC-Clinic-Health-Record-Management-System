<?php

namespace Database\Seeders;

use App\Models\NurseBed;
use Illuminate\Database\Seeder;

class NurseBedSeeder extends Seeder
{
    /**
     * Seed the clinic beds used for rest interventions.
     */
    public function run(): void
    {
        $beds = [
            ['name' => 'Bed 1', 'location' => 'Clinic Rest Area', 'description' => 'Primary bed for students or employees advised to rest.'],
            ['name' => 'Bed 2', 'location' => 'Clinic Rest Area', 'description' => 'Secondary bed for patients under observation.'],
            ['name' => 'Bed 3', 'location' => 'Clinic Rest Area', 'description' => 'Additional bed for temporary rest intervention.'],
        ];

        foreach ($beds as $bed) {
            NurseBed::updateOrCreate(
                ['name' => $bed['name']],
                $bed + ['is_active' => true]
            );
        }
    }
}
