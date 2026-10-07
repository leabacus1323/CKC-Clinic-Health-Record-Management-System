<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        $medicines = [
            ['name' => 'Paracetamol', 'description' => 'Pain reliever', 'quantity' => '100', 'medicine_type' => 'Tablet', 'expiration_date' => Carbon::now()->addYears(2), 'status' => 'Active'],
            ['name' => 'Ibuprofen', 'description' => 'Anti-inflammatory', 'quantity' => '50', 'medicine_type' => 'Tablet', 'expiration_date' => Carbon::now()->addYears(2), 'status' => 'Active'],
            ['name' => 'Amoxicillin', 'description' => 'Antibiotic', 'quantity' => '75', 'medicine_type' => 'Capsule', 'expiration_date' => Carbon::now()->addYears(1.5), 'status' => 'Active'],
            ['name' => 'Cough Syrup', 'description' => 'Cough suppressant', 'quantity' => '30', 'medicine_type' => 'Liquid', 'expiration_date' => Carbon::now()->addYear(), 'status' => 'Active'],
            ['name' => 'Aspirin', 'description' => 'Pain and fever relief', 'quantity' => '120', 'medicine_type' => 'Tablet', 'expiration_date' => Carbon::now()->addYears(2), 'status' => 'Active'],
            ['name' => 'Cetirizine', 'description' => 'Antihistamine', 'quantity' => '60', 'medicine_type' => 'Tablet', 'expiration_date' => Carbon::now()->addYears(1.5), 'status' => 'Active'],
            ['name' => 'Omeprazole', 'description' => 'Acid reflux medication', 'quantity' => '40', 'medicine_type' => 'Capsule', 'expiration_date' => Carbon::now()->addYears(2), 'status' => 'Active'],
            ['name' => 'Metformin', 'description' => 'Diabetes medication', 'quantity' => '200', 'medicine_type' => 'Tablet', 'expiration_date' => Carbon::now()->addYears(2), 'status' => 'Active'],
            ['name' => 'Lisinopril', 'description' => 'Blood pressure medication', 'quantity' => '90', 'medicine_type' => 'Tablet', 'expiration_date' => Carbon::now()->addYears(2), 'status' => 'Active'],
            ['name' => 'Atorvastatin', 'description' => 'Cholesterol medication', 'quantity' => '80', 'medicine_type' => 'Tablet', 'expiration_date' => Carbon::now()->addYears(2), 'status' => 'Active'],
        ];

        foreach ($medicines as $medicine) {
            Medicine::create($medicine);
        }
    }
}
