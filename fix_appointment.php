<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING PATIENT RECORDS ===\n\n";

// Check all patient records
echo "📋 All patient records:\n";
$allPatients = App\Models\Patients::with('user')->get();
foreach ($allPatients as $patient) {
    $userName = $patient->user ? $patient->user->name : 'No user linked';
    echo "   - Patient ID: {$patient->id}, User ID: {$patient->user_id}, Name: {$userName}\n";
}

echo "\n📋 All appointments:\n";
$allAppointments = App\Models\Appointment::all();
foreach ($allAppointments as $apt) {
    echo "   - Appointment ID: {$apt->id}, Patient ID: {$apt->patient_id}, Doctor ID: {$apt->doc_id}, Date: {$apt->date}\n";
}

echo "\n🔍 Looking for Anahi Trinity Gislason patient record:\n";
$anachiUser = App\Models\User::where('name', 'LIKE', '%Anahi Trinity Gislason%')->first();
if ($anachiUser) {
    $anachiPatient = App\Models\Patients::where('user_id', $anachiUser->id)->first();
    if ($anachiPatient) {
        echo "   ✅ Found: Patient ID {$anachiPatient->id} for user ID {$anachiUser->id}\n";

        // Update the appointment to use the correct patient ID
        $appointment = App\Models\Appointment::where('doc_id', 1)->first();
        if ($appointment) {
            echo "   🔧 Updating appointment patient_id from {$appointment->patient_id} to {$anachiPatient->id}\n";
            $appointment->patient_id = $anachiPatient->id;
            $appointment->save();
            echo "   ✅ Appointment updated successfully!\n";
        }
    } else {
        echo "   ❌ No patient record found for Anahi Trinity Gislason\n";
    }
} else {
    echo "   ❌ User 'Anahi Trinity Gislason' not found\n";
}

echo "\n=== DONE ===\n";
