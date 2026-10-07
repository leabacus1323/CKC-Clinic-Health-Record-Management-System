<?php
/**
 * Script to fix corrupted patient_type values in the patients table.
 * patient_type should be 1 (Student) or 2 (Faculty & Staff).
 * Any string value (student, staff, faculty, external) should be converted to the correct integer.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Fixing corrupted patient_type values ===\n\n";

// Map string values to integers
$fixMap = [
    'student' => 1,
    'staff' => 2,
    'faculty' => 2,
    'external' => 2,
];

// Find all patients with non-integer patient_type
$patients = DB::table('patients')->get();

echo "Total patients: " . count($patients) . "\n\n";

$fixedCount = 0;
foreach ($patients as $patient) {
    // Check if patient_type is not 1 or 2
    if (!in_array($patient->patient_type, [1, 2])) {
        // It's a string value, need to fix it
        $newValue = $fixMap[$patient->patient_type] ?? null;

        if ($newValue !== null) {
            DB::table('patients')
                ->where('id', $patient->id)
                ->update(['patient_type' => $newValue]);

            echo "Fixed patient ID {$patient->id}: '{$patient->patient_type}' -> {$newValue}\n";
            $fixedCount++;
        } else {
            echo "WARNING: Unknown patient_type '{$patient->patient_type}' for patient ID {$patient->id}, skipping.\n";
        }
    }
}

echo "\nTotal fixed: {$fixedCount} patients\n";

// Show current state
echo "\n=== Current patient_type distribution ===\n";
$stats = DB::table('patients')
    ->select('patient_type', DB::raw('count(*) as count'))
    ->groupBy('patient_type')
    ->get();

foreach ($stats as $stat) {
    $label = $stat->patient_type == 1 ? 'Student' : ($stat->patient_type == 2 ? 'Faculty & Staff' : 'Unknown (' . $stat->patient_type . ')');
    echo "{$label}: {$stat->count}\n";
}

echo "\nDone!\n";
