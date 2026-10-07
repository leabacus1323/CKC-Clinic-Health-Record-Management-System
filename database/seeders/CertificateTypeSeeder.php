<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CertificateType;

class CertificateTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Sick Leave',
                'slug' => 'sick_leave',
                'description' => 'Certificate recommending a period of rest due to illness or medical condition.',
            ],
            [
                'name' => 'Fit to Work / Return to Work',
                'slug' => 'fit_to_work',
                'description' => 'Certifies that the patient is physically and medically fit to perform work duties.',
            ],
            [
                'name' => 'School / P.E.',
                'slug' => 'school_pe',
                'description' => 'Medical certificate for school or physical education requirements.',
            ],
            [
                'name' => 'Fit to Travel',
                'slug' => 'fit_to_travel',
                'description' => 'Certifies that the patient is medically fit to travel.',
            ],
            [
                'name' => 'Employment',
                'slug' => 'employment',
                'description' => 'Medical certificate for employment purposes.',
            ],
            [
                'name' => "Driver's License",
                'slug' => 'drivers_license',
                'description' => 'Medical certificate for driver\'s license application or renewal.',
            ],
            [
                'name' => 'Health Certificate',
                'slug' => 'health_certificate',
                'description' => 'General health certificate confirming patient examination and health status.',
            ],
            [
                'name' => 'Dental Fit-to-Work',
                'slug' => 'dental_fit_to_work',
                'description' => 'Required for specific roles (e.g., seafarers) to rule out active oral infections.',
            ],
            [
                'name' => 'Sports Dental Clearance',
                'slug' => 'sports_dental_clearance',
                'description' => 'Confirms no loose teeth or trauma risks during contact sports.',
            ],
            [
                'name' => 'Dental Excuse Note',
                'slug' => 'dental_excuse_note',
                'description' => 'Validates absences due to dental procedures or required recovery time (e.g., after extractions).',
            ],
        ];

        foreach ($types as $type) {
            CertificateType::updateOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }
    }
}
