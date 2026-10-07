<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Dental Records for student@ckc.edu ===\n\n";

// Get user
$user = DB::table('users')->where('email', 'student@ckc.edu')->first();
if (!$user) {
    echo "User not found!\n";
    exit;
}
echo "User ID: {$user->id}\n";
echo "User Name: {$user->name}\n\n";

// Get patient
$patient = DB::table('patients')->where('user_id', $user->id)->first();
if (!$patient) {
    echo "Patient record not found!\n";
    exit;
}
echo "Patient ID: {$patient->id}\n\n";

// Check dental examinations
echo "=== Dental Examinations ===\n";
$dentalExams = DB::table('dental_examinations')->get();
echo "Total dental examinations in database: " . $dentalExams->count() . "\n\n";

if ($dentalExams->count() > 0) {
    foreach ($dentalExams as $exam) {
        echo "Dental Exam ID: {$exam->id}\n";
        echo "  patient_id: " . ($exam->patient_id ?? 'NULL') . "\n";
        echo "  doctor_id: {$exam->doctor_id}\n";
        echo "  diagnosis: {$exam->diagnosis}\n";
        echo "  created_at: {$exam->created_at}\n\n";
    }
}

// Check if any dental exam matches this patient
$patientDentalExams = DB::table('dental_examinations')
    ->where('patient_id', $patient->id)
    ->get();

echo "Dental exams for patient ID {$patient->id}: " . $patientDentalExams->count() . "\n";

// Check doctor
if ($dentalExams->count() > 0) {
    $doctorId = $dentalExams->first()->doctor_id;
    $doctor = DB::table('doctors')->where('id', $doctorId)->first();
    if ($doctor) {
        $doctorUser = DB::table('users')->where('id', $doctor->user_id)->first();
        echo "\nDoctor: " . ($doctorUser ? $doctorUser->name : 'Unknown') . "\n";
    }
}
