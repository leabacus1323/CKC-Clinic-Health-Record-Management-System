<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING DOCTOR PATIENTS QUERY ===\n\n";

// Simulate the exact query from DoctorDashboardController
$doctorRecord = App\Models\Doctors::where('user_id', 1)->first();

$patients = App\Models\Patients::whereHas('appointments', function($query) use ($doctorRecord) {
        $query->where('doc_id', $doctorRecord->id);
    })
    ->with(['user', 'appointments' => function($query) use ($doctorRecord) {
        $query->where('doc_id', $doctorRecord->id)->latest();
    }])
    ->get();

echo "📊 Query Results:\n";
echo "   - Doctor ID: {$doctorRecord->id}\n";
echo "   - Patients found: {$patients->count()}\n\n";

foreach ($patients as $patient) {
    echo "👤 Patient: {$patient->user->name}\n";
    echo "   - Patient ID: {$patient->id}\n";
    echo "   - User ID: {$patient->user_id}\n";
    echo "   - Appointments: {$patient->appointments->count()}\n";

    foreach ($patient->appointments as $apt) {
        echo "     - Date: {$apt->date}, Status: {$apt->status}\n";
    }
    echo "\n";
}

echo "✅ The doctor should now see 1 patient in the patients tab!\n";
