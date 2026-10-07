<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check John Student
$user = DB::table('users')->where('email', 'student@ckc.edu')->first();
echo "User ID: {$user->id}\n";

$patient = DB::table('patients')->where('user_id', $user->id)->first();
echo "Patient ID: {$patient->id}\n";

echo "\nPhysical Exams for John Student:\n";
$exams = DB::table('physical_examinations')->where('patient_id', $patient->id)->get();
echo "Total exams with patient_id={$patient->id}: " . $exams->count() . "\n";

foreach ($exams as $exam) {
    echo "Exam ID: {$exam->id}, Patient ID: {$exam->patient_id}, Doctor ID: {$exam->doctor_id}\n";
}

echo "\nWhat the controller is currently querying (user_id={$user->id}):\n";
$wrongExams = DB::table('physical_examinations')->where('user_id', $user->id)->get();
echo "Total exams with user_id={$user->id}: " . $wrongExams->count() . "\n";
