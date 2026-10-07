<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check patient 5
echo "Patient ID 5 Details:\n";
$patient = DB::table('patients')->where('id', 5)->first();
if ($patient) {
    print_r($patient);
} else {
    echo "Patient 5 not found!\n";
}

echo "\n\nPhysical Exams for Patient 5:\n";
$exams = DB::table('physical_examinations')->where('patient_id', 5)->get();
echo "Total records: " . $exams->count() . "\n";
foreach ($exams as $exam) {
    echo "\nExam ID: {$exam->id}\n";
    echo "Patient ID: {$exam->patient_id}\n";
    echo "Doctor ID: {$exam->doctor_id}\n";
    echo "Height: {$exam->height}\n";
    echo "Weight: {$exam->weight}\n";
    echo "Created: {$exam->created_at}\n";
}

echo "\n\nAll Physical Exams:\n";
$allExams = DB::table('physical_examinations')->get();
foreach ($allExams as $exam) {
    echo "ID: {$exam->id}, Patient ID: {$exam->patient_id}, Doctor ID: {$exam->doctor_id}\n";
}
