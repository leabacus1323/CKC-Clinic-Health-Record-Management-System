<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simulate what the controller does for John Student
$user = DB::table('users')->where('email', 'student@ckc.edu')->first();
echo "User: {$user->name} (ID: {$user->id})\n";

$patient = DB::table('patients')->where('user_id', $user->id)->first();
echo "Patient ID: {$patient->id}\n\n";

// What the controller now queries
echo "Physical Examinations (patient_id={$patient->id}):\n";
$physicalExaminations = DB::table('physical_examinations')
    ->where('patient_id', $patient->id)
    ->get();
echo "Count: " . $physicalExaminations->count() . "\n";

foreach ($physicalExaminations as $exam) {
    echo "- Exam ID: {$exam->id}, Height: {$exam->height}, Weight: {$exam->weight}, BP: {$exam->bp}\n";
}

echo "\nDental Examinations (patient_id={$patient->id}):\n";
$dentalExaminations = DB::table('dental_examinations')
    ->where('patient_id', $patient->id)
    ->get();
echo "Count: " . $dentalExaminations->count() . "\n";

echo "\nImmunization Records (patient_id={$patient->id}):\n";
$immunizationRecords = DB::table('immunization_records')
    ->where('patient_id', $patient->id)
    ->get();
echo "Count: " . $immunizationRecords->count() . "\n";

echo "\nPrescription Records (patient_id={$patient->id}):\n";
$prescriptionRecords = DB::table('prescription_records')
    ->where('patient_id', $patient->id)
    ->get();
echo "Count: " . $prescriptionRecords->count() . "\n";

echo "\nHealth Records (user_id={$user->id}):\n";
$healthRecords = DB::table('health_records')
    ->where('user_id', $user->id)
    ->get();
echo "Count: " . $healthRecords->count() . "\n";
