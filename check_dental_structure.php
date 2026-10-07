<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Dental Examinations Table Structure:\n";
$columns = DB::select("PRAGMA table_info(dental_examinations)");
foreach ($columns as $column) {
    echo "- {$column->name} ({$column->type})\n";
}

echo "\n\nJohn Student's Dental Exam (Patient ID 5):\n";
$dentalExam = DB::table('dental_examinations')->where('patient_id', 5)->first();
if ($dentalExam) {
    print_r($dentalExam);
} else {
    echo "No dental exam found for patient 5\n";
}
