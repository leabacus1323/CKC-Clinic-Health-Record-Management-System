<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PrescriptionRecord;
use Carbon\Carbon;

class PrescriptionRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Prescription Records - linking patients, doctors, and medicines
        // Medicine IDs: 1=Paracetamol, 2=Ibuprofen, 3=Amoxicillin, 4=Cephalexin, 5=Metformin
        // 6=Lisinopril, 7=Atorvastatin, 8=Omeprazole, 9=Ranitidine, 10=Simethicone
        // 11=Vitamin C, 12=Vitamin D, 13=Iron Supplement, 14=Calcium Supplement, 15=Multivitamin

        // Patient 1 prescriptions (student)
        PrescriptionRecord::create([
            'patient_id' => 1,
            'doctor_id' => 1,
            'medicine_id' => 1, // Paracetamol
            'dosage' => '500mg',
            'instruction' => 'Oral tablet. Take every 6-8 hours as needed for fever or pain.',
            'frequency' => 'Every 6-8 hours as needed',
            'duration' => '7 days',
            'date_prescribed' => Carbon::now()->subDays(5),
            'status' => 'active',
        ]);

        PrescriptionRecord::create([
            'patient_id' => 1,
            'doctor_id' => 1,
            'medicine_id' => 11, // Vitamin C
            'dosage' => '1000mg',
            'instruction' => 'Oral tablet. Take once daily with food for immune support.',
            'frequency' => 'Once daily',
            'duration' => '30 days',
            'date_prescribed' => Carbon::now()->subDays(10),
            'status' => 'active',
        ]);

        // Patient 2 prescriptions (student - had dental work)
        PrescriptionRecord::create([
            'patient_id' => 2,
            'doctor_id' => 2,
            'medicine_id' => 3, // Amoxicillin
            'dosage' => '500mg',
            'instruction' => 'Oral capsule. Take three times daily with or without food. Complete full course.',
            'frequency' => 'Three times daily',
            'duration' => '7 days',
            'date_prescribed' => Carbon::now()->subDays(15),
            'date_discontinued' => Carbon::now()->subDays(8),
            'status' => 'completed',
        ]);

        PrescriptionRecord::create([
            'patient_id' => 2,
            'doctor_id' => 2,
            'medicine_id' => 2, // Ibuprofen
            'dosage' => '200mg',
            'instruction' => 'Oral tablet. Take every 6-8 hours as needed for post-treatment pain. Take with food.',
            'frequency' => 'Every 6-8 hours as needed',
            'duration' => '5 days',
            'date_prescribed' => Carbon::now()->subDays(15),
            'date_discontinued' => Carbon::now()->subDays(10),
            'status' => 'completed',
        ]);

        PrescriptionRecord::create([
            'patient_id' => 2,
            'doctor_id' => 2,
            'medicine_id' => 13, // Iron Supplement
            'dosage' => '25mg',
            'instruction' => 'Oral tablet. Take once daily with meals. May cause dark stool.',
            'frequency' => 'Once daily with meals',
            'duration' => '60 days',
            'date_prescribed' => Carbon::now()->subDays(20),
            'status' => 'active',
        ]);

        // Patient 3 prescriptions (student)
        PrescriptionRecord::create([
            'patient_id' => 3,
            'doctor_id' => 1,
            'medicine_id' => 15, // Multivitamin
            'dosage' => '1 tablet',
            'instruction' => 'Oral tablet. Take once daily preferably in the morning with breakfast.',
            'frequency' => 'Once daily',
            'duration' => '90 days',
            'date_prescribed' => Carbon::now()->subDays(8),
            'status' => 'active',
        ]);

        PrescriptionRecord::create([
            'patient_id' => 3,
            'doctor_id' => 1,
            'medicine_id' => 12, // Vitamin D
            'dosage' => '1000 IU',
            'instruction' => 'Oral tablet. Take once daily with a meal containing fat for better absorption.',
            'frequency' => 'Once daily',
            'duration' => '90 days',
            'date_prescribed' => Carbon::now()->subDays(8),
            'status' => 'active',
        ]);

        // Patient 4 prescriptions (faculty/staff)
        PrescriptionRecord::create([
            'patient_id' => 4,
            'doctor_id' => 2,
            'medicine_id' => 2, // Ibuprofen
            'dosage' => '400mg',
            'instruction' => 'Oral tablet. Take twice daily with meals for musculoskeletal pain management.',
            'frequency' => 'Twice daily with meals',
            'duration' => '10 days',
            'date_prescribed' => Carbon::now()->subDays(3),
            'status' => 'active',
        ]);

        PrescriptionRecord::create([
            'patient_id' => 4,
            'doctor_id' => 2,
            'medicine_id' => 8, // Omeprazole
            'dosage' => '20mg',
            'instruction' => 'Oral capsule. Take once daily before breakfast on an empty stomach.',
            'frequency' => 'Once daily before breakfast',
            'duration' => '14 days',
            'date_prescribed' => Carbon::now()->subDays(3),
            'status' => 'active',
        ]);

        PrescriptionRecord::create([
            'patient_id' => 4,
            'doctor_id' => 2,
            'medicine_id' => 6, // Lisinopril
            'dosage' => '10mg',
            'instruction' => 'Oral tablet. Take once daily in the morning. Ongoing for blood pressure management.',
            'frequency' => 'Once daily in morning',
            'duration' => 'Ongoing',
            'date_prescribed' => Carbon::now()->subDays(25),
            'status' => 'active',
        ]);

        PrescriptionRecord::create([
            'patient_id' => 4,
            'doctor_id' => 2,
            'medicine_id' => 7, // Atorvastatin
            'dosage' => '20mg',
            'instruction' => 'Oral tablet. Take once daily at bedtime. Ongoing for cholesterol management.',
            'frequency' => 'Once daily at bedtime',
            'duration' => 'Ongoing',
            'date_prescribed' => Carbon::now()->subDays(25),
            'status' => 'active',
        ]);

        // Patient 5 prescriptions (student)
        PrescriptionRecord::create([
            'patient_id' => 5,
            'doctor_id' => 1,
            'medicine_id' => 4, // Cephalexin
            'dosage' => '250mg',
            'instruction' => 'Oral capsule. Take four times daily for skin infection. Complete full course.',
            'frequency' => 'Four times daily',
            'duration' => '7 days',
            'date_prescribed' => Carbon::now()->subDays(12),
            'date_discontinued' => Carbon::now()->subDays(5),
            'status' => 'completed',
        ]);

        PrescriptionRecord::create([
            'patient_id' => 5,
            'doctor_id' => 1,
            'medicine_id' => 1, // Paracetamol
            'dosage' => '500mg',
            'instruction' => 'Oral tablet. Take every 6-8 hours for symptomatic relief of cold/flu.',
            'frequency' => 'Every 6-8 hours',
            'duration' => '5 days',
            'date_prescribed' => Carbon::now()->subDays(12),
            'date_discontinued' => Carbon::now()->subDays(7),
            'status' => 'completed',
        ]);

        PrescriptionRecord::create([
            'patient_id' => 5,
            'doctor_id' => 1,
            'medicine_id' => 14, // Calcium Supplement
            'dosage' => '600mg',
            'instruction' => 'Oral tablet. Take once daily with meals. Important for bone health during adolescent growth.',
            'frequency' => 'Once daily with meals',
            'duration' => '60 days',
            'date_prescribed' => Carbon::now()->subDays(3),
            'status' => 'active',
        ]);
    }
}
