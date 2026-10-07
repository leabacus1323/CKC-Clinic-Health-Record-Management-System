<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ImmunizationRecords;
use Carbon\Carbon;

class ImmunizationRecordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Immunization records with actual vaccination history
        
        // Patient 1 - Student
        ImmunizationRecords::create([
            'patient_id' => 1,
            'vaccine_name' => 'BCG',
            'vaccine_type' => 'Injectables',
            'administered_by' => 'Dr. John Smith',
            'dosage' => '0.1 mL',
            'site_of_administration' => 'Left Upper Arm',
            'expiration_date' => Carbon::now()->addYears(2),
            'notes' => 'Given during infancy as per EPI schedule',
        ]);

        ImmunizationRecords::create([
            'patient_id' => 1,
            'vaccine_name' => 'DPT',
            'vaccine_type' => 'Injectables',
            'administered_by' => 'Dr. John Smith',
            'dosage' => '0.5 mL',
            'site_of_administration' => 'Right Upper Arm',
            'expiration_date' => Carbon::now()->addYears(2),
            'notes' => 'First dose (DPT 1) at 2 months',
        ]);

        ImmunizationRecords::create([
            'patient_id' => 1,
            'vaccine_name' => 'Measles',
            'vaccine_type' => 'Injectables',
            'administered_by' => 'Dr. Maria Santos',
            'dosage' => '0.5 mL',
            'site_of_administration' => 'Right Thigh',
            'expiration_date' => Carbon::now()->addYears(3),
            'notes' => 'First dose at 9 months',
        ]);

        ImmunizationRecords::create([
            'patient_id' => 1,
            'vaccine_name' => 'MMR',
            'vaccine_type' => 'Injectables',
            'administered_by' => 'Dr. Maria Santos',
            'dosage' => '0.5 mL',
            'site_of_administration' => 'Right Upper Arm',
            'expiration_date' => Carbon::now()->addYears(3),
            'notes' => 'First dose at 12 months',
        ]);

        // Patient 2 - Student
        ImmunizationRecords::create([
            'patient_id' => 2,
            'vaccine_name' => 'BCG',
            'vaccine_type' => 'Injectables',
            'administered_by' => 'Dr. Maria Santos',
            'dosage' => '0.1 mL',
            'site_of_administration' => 'Left Upper Arm',
            'expiration_date' => Carbon::now()->addYears(2),
            'notes' => 'Neonatal BCG given at birth',
        ]);

        ImmunizationRecords::create([
            'patient_id' => 2,
            'vaccine_name' => 'Oral Polio',
            'vaccine_type' => 'Tablet',
            'administered_by' => 'Dr. John Smith',
            'dosage' => '2 drops',
            'site_of_administration' => 'Oral',
            'expiration_date' => Carbon::now()->addYears(1),
            'notes' => 'OPV dose during clinic visit',
        ]);

        ImmunizationRecords::create([
            'patient_id' => 2,
            'vaccine_name' => 'Hepatitis A or B',
            'vaccine_type' => 'Injectables',
            'administered_by' => 'Dr. Maria Santos',
            'dosage' => '0.5 mL',
            'site_of_administration' => 'Right Upper Arm',
            'expiration_date' => Carbon::now()->addYears(2),
            'notes' => 'Hepatitis B vaccine series completed',
        ]);

        // Patient 3 - Student
        ImmunizationRecords::create([
            'patient_id' => 3,
            'vaccine_name' => 'BCG',
            'vaccine_type' => 'Injectables',
            'administered_by' => 'Dr. John Smith',
            'dosage' => '0.1 mL',
            'site_of_administration' => 'Left Upper Arm',
            'expiration_date' => Carbon::now()->addYears(2),
            'notes' => 'BCG given at appropriate age',
        ]);

        ImmunizationRecords::create([
            'patient_id' => 3,
            'vaccine_name' => 'MMR',
            'vaccine_type' => 'Injectables',
            'administered_by' => 'Dr. Maria Santos',
            'dosage' => '0.5 mL',
            'site_of_administration' => 'Right Upper Arm',
            'expiration_date' => Carbon::now()->addYears(3),
            'notes' => 'Bumped schedule - given at age 15 months',
        ]);

        ImmunizationRecords::create([
            'patient_id' => 3,
            'vaccine_name' => 'DPT',
            'vaccine_type' => 'Injectables',
            'administered_by' => 'Dr. John Smith',
            'dosage' => '0.5 mL',
            'site_of_administration' => 'Right Upper Arm',
            'expiration_date' => Carbon::now()->addYears(2),
            'notes' => 'DPT booster as catch-up',
        ]);

        // Patient 4 - Faculty/Staff (Young)
        ImmunizationRecords::create([
            'patient_id' => 4,
            'vaccine_name' => 'BCG',
            'vaccine_type' => 'Injectables',
            'administered_by' => 'Dr. Maria Santos',
            'dosage' => '0.1 mL',
            'site_of_administration' => 'Left Upper Arm',
            'expiration_date' => Carbon::now()->addYears(2),
            'notes' => 'Late BCG given at age 5 during catch-up',
        ]);

        ImmunizationRecords::create([
            'patient_id' => 4,
            'vaccine_name' => 'Measles',
            'vaccine_type' => 'Injectables',
            'administered_by' => 'Dr. John Smith',
            'dosage' => '0.5 mL',
            'site_of_administration' => 'Right Upper Arm',
            'expiration_date' => Carbon::now()->addYears(3),
            'notes' => 'Measles vaccine administered',
        ]);

        ImmunizationRecords::create([
            'patient_id' => 4,
            'vaccine_name' => 'DPT',
            'vaccine_type' => 'Injectables',
            'administered_by' => 'Dr. Maria Santos',
            'dosage' => '0.5 mL',
            'site_of_administration' => 'Right Upper Arm',
            'expiration_date' => Carbon::now()->addYears(2),
            'notes' => 'DPT catch-up vaccination',
        ]);

        // Patient 5 - Student (Adolescent)
        ImmunizationRecords::create([
            'patient_id' => 5,
            'vaccine_name' => 'MMR',
            'vaccine_type' => 'Injectables',
            'administered_by' => 'Dr. John Smith',
            'dosage' => '0.5 mL',
            'site_of_administration' => 'Right Upper Arm',
            'expiration_date' => Carbon::now()->addYears(3),
            'notes' => 'MMR vaccine - first dose at age 12',
        ]);

        ImmunizationRecords::create([
            'patient_id' => 5,
            'vaccine_name' => 'Hepatitis A or B',
            'vaccine_type' => 'Injectables',
            'administered_by' => 'Dr. Maria Santos',
            'dosage' => '0.5 mL',
            'site_of_administration' => 'Left Upper Arm',
            'expiration_date' => Carbon::now()->addYears(2),
            'notes' => 'Hepatitis B booster given',
        ]);

        ImmunizationRecords::create([
            'patient_id' => 5,
            'vaccine_name' => 'DPT',
            'vaccine_type' => 'Injectables',
            'administered_by' => 'Dr. John Smith',
            'dosage' => '0.5 mL',
            'site_of_administration' => 'Right Upper Arm',
            'expiration_date' => Carbon::now()->addYears(2),
            'notes' => 'DPT booster - pre-adolescent dose',
        ]);
    }
}
