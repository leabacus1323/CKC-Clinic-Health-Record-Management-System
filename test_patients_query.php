<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING PATIENTS INDEX QUERY ===\n\n";

try {
    // Test the exact query from AdminPatients index
    $patients = \App\Models\Patients::join('educational_level', 'educational_level.id', '=', 'patients.edulvl_id')
        ->leftJoin('users as u', function($join) {
            $join->on('u.id', '=', 'patients.user_id')
                ->where('patients.patient_type', 1);
        })
        ->leftJoin('admins as a', function($join) {
            $join->on('a.id', '=', 'patients.user_id')
                ->where('patients.patient_type', 2);
        })
        ->select(
            'patients.*',
            \Illuminate\Support\Facades\DB::raw("CASE WHEN patients.patient_type = 1 THEN u.name ELSE a.name END as name"),
            'educational_level.level_name',
            'educational_level.year_level'
        )
        ->get();

    echo "Query executed successfully!\n";
    echo "Number of patients found: " . $patients->count() . "\n\n";

    foreach ($patients as $patient) {
        echo "Patient ID: {$patient->id}\n";
        echo "Name: {$patient->name}\n";
        echo "User ID: {$patient->user_id}\n";
        echo "Patient Type: {$patient->patient_type}\n";
        echo "School ID: {$patient->school_id}\n";
        echo "Education Level: {$patient->level_name} {$patient->year_level}\n";
        echo "Address: {$patient->address}\n";
        echo "---\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Query failed!\n";
}

echo "\n=== END TEST ===\n";
