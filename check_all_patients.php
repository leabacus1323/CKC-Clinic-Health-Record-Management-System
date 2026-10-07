<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "All Patients:\n";
$patients = DB::table('patients')
    ->join('users', 'patients.user_id', '=', 'users.id')
    ->select('patients.id as patient_id', 'patients.user_id', 'users.name', 'users.email')
    ->get();

foreach ($patients as $patient) {
    echo "Patient ID: {$patient->patient_id}, User ID: {$patient->user_id}, Name: {$patient->name}, Email: {$patient->email}\n";
}

echo "\n\nPhysical Examinations with Patient Info:\n";
$exams = DB::table('physical_examinations')
    ->join('patients', 'physical_examinations.patient_id', '=', 'patients.id')
    ->join('users', 'patients.user_id', '=', 'users.id')
    ->select('physical_examinations.*', 'patients.id as patient_id', 'users.name', 'users.email')
    ->get();

foreach ($exams as $exam) {
    echo "Exam ID: {$exam->id}, Patient ID: {$exam->patient_id}, Name: {$exam->name}, Email: {$exam->email}\n";
}
