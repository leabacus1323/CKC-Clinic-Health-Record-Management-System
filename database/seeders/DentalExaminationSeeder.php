<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DentalExamination;

class DentalExaminationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dental Examination Records for patients
        // Teeth status is stored as JSON with teeth numbers and conditions
        
        DentalExamination::create([
            'patient_id' => 1,
            'doctor_id' => 1,
            'teeth_status' => json_encode([
                'teeth' => [
                    ['teeth_no' => 1, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 2, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 3, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 4, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 5, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 6, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 7, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 8, 'teeth_condition' => 'healthy'],
                ]
            ]),
            'diagnosis' => 'Excellent oral hygiene. All teeth present and healthy. No cavities detected.',
        ]);

        DentalExamination::create([
            'patient_id' => 2,
            'doctor_id' => 2,
            'teeth_status' => json_encode([
                'teeth' => [
                    ['teeth_no' => 1, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 2, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 3, 'teeth_condition' => 'cavity'],
                    ['teeth_no' => 4, 'teeth_condition' => 'cavity'],
                    ['teeth_no' => 5, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 6, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 7, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 8, 'teeth_condition' => 'healthy'],
                ]
            ]),
            'diagnosis' => 'Fair oral hygiene. Two cavities detected on teeth 3 and 4. Dental restoration needed.',
        ]);

        DentalExamination::create([
            'patient_id' => 2,
            'doctor_id' => 1,
            'teeth_status' => json_encode([
                'teeth' => [
                    ['teeth_no' => 1, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 2, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 3, 'teeth_condition' => 'filled'],
                    ['teeth_no' => 4, 'teeth_condition' => 'filled'],
                    ['teeth_no' => 5, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 6, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 7, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 8, 'teeth_condition' => 'healthy'],
                ]
            ]),
            'diagnosis' => 'Treatment follow-up. Fillings completed on teeth 3 and 4. Healing progressing normally.',
        ]);

        DentalExamination::create([
            'patient_id' => 3,
            'doctor_id' => 2,
            'teeth_status' => json_encode([
                'teeth' => [
                    ['teeth_no' => 1, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 2, 'teeth_condition' => 'crowded'],
                    ['teeth_no' => 3, 'teeth_condition' => 'crowded'],
                    ['teeth_no' => 4, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 5, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 6, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 7, 'teeth_condition' => 'crowded'],
                    ['teeth_no' => 8, 'teeth_condition' => 'crowded'],
                ]
            ]),
            'diagnosis' => 'Permanent dentition with slight crowding. No cavities. Orthodontic consultation recommended.',
        ]);

        DentalExamination::create([
            'patient_id' => 4,
            'doctor_id' => 1,
            'teeth_status' => json_encode([
                'teeth' => [
                    ['teeth_no' => 1, 'teeth_condition' => 'primary'],
                    ['teeth_no' => 2, 'teeth_condition' => 'primary'],
                    ['teeth_no' => 3, 'teeth_condition' => 'erupting'],
                    ['teeth_no' => 4, 'teeth_condition' => 'erupting'],
                    ['teeth_no' => 5, 'teeth_condition' => 'primary'],
                    ['teeth_no' => 6, 'teeth_condition' => 'primary'],
                    ['teeth_no' => 7, 'teeth_condition' => 'primary'],
                    ['teeth_no' => 8, 'teeth_condition' => 'primary'],
                ]
            ]),
            'diagnosis' => 'Mixed dentition. Primary molars present. Permanent teeth erupting normally. No decay.',
        ]);

        DentalExamination::create([
            'patient_id' => 5,
            'doctor_id' => 2,
            'teeth_status' => json_encode([
                'teeth' => [
                    ['teeth_no' => 1, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 2, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 3, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 4, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 5, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 6, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 7, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 8, 'teeth_condition' => 'healthy'],
                ]
            ]),
            'diagnosis' => 'Excellent oral health. Complete set of permanent teeth. Perfect occlusion.',
        ]);

        DentalExamination::create([
            'patient_id' => 1,
            'doctor_id' => 2,
            'teeth_status' => json_encode([
                'teeth' => [
                    ['teeth_no' => 1, 'teeth_condition' => 'clean'],
                    ['teeth_no' => 2, 'teeth_condition' => 'clean'],
                    ['teeth_no' => 3, 'teeth_condition' => 'clean'],
                    ['teeth_no' => 4, 'teeth_condition' => 'clean'],
                    ['teeth_no' => 5, 'teeth_condition' => 'clean'],
                    ['teeth_no' => 6, 'teeth_condition' => 'clean'],
                    ['teeth_no' => 7, 'teeth_condition' => 'clean'],
                    ['teeth_no' => 8, 'teeth_condition' => 'clean'],
                ]
            ]),
            'diagnosis' => 'Prophylaxis completed. All surfaces cleaned and polished. Patient educated on prevention.',
        ]);

        DentalExamination::create([
            'patient_id' => 3,
            'doctor_id' => 1,
            'teeth_status' => json_encode([
                'teeth' => [
                    ['teeth_no' => 1, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 2, 'teeth_condition' => 'crowded'],
                    ['teeth_no' => 3, 'teeth_condition' => 'crowded'],
                    ['teeth_no' => 4, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 5, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 6, 'teeth_condition' => 'healthy'],
                    ['teeth_no' => 7, 'teeth_condition' => 'crowded'],
                    ['teeth_no' => 8, 'teeth_condition' => 'crowded'],
                ]
            ]),
            'diagnosis' => 'Follow-up from initial assessment. Teeth remain in good condition. Patient maintaining hygiene.',
        ]);
    }
}
