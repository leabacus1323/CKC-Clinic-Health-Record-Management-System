<?php
require_once 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Patients;
use Illuminate\Support\Facades\DB;

echo "=== DELETING PATIENTS WITH ID 1 AND 2 ===\n";

try {
    // Show current patients before deletion
    echo "\nBefore deletion:\n";
    $patients = Patients::join('users', 'users.id', '=', 'patients.user_id')
        ->select('patients.id', 'patients.user_id', 'users.name', 'users.user_type', 'patients.patient_type')
        ->get();

    foreach ($patients as $patient) {
        echo "Patient ID: {$patient->id}, User ID: {$patient->user_id}, Name: {$patient->name}, User Type: {$patient->user_type}, Patient Type: {$patient->patient_type}\n";
    }

    // Delete patients with ID 1 and 2
    echo "\nDeleting patients with ID 1 and 2...\n";
    $deleted = Patients::whereIn('id', [1, 2])->delete();
    echo "Deleted {$deleted} patient records.\n";

    // Show remaining patients after deletion
    echo "\nAfter deletion:\n";
    $remainingPatients = Patients::join('users', 'users.id', '=', 'patients.user_id')
        ->select('patients.id', 'patients.user_id', 'users.name', 'users.user_type', 'patients.patient_type')
        ->get();

    if ($remainingPatients->count() > 0) {
        foreach ($remainingPatients as $patient) {
            echo "Patient ID: {$patient->id}, User ID: {$patient->user_id}, Name: {$patient->name}, User Type: {$patient->user_type}, Patient Type: {$patient->patient_type}\n";
        }
    } else {
        echo "No patients remaining in the table.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== DELETION COMPLETE ===\n";
?>
