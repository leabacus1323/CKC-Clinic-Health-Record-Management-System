<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Doctors;
use App\Models\Patients;
use App\Models\Appointment;

// Bootstrap Laravel app
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing Health Record Creation Prerequisites...\n\n";

// Check if we have doctors
$doctors = Doctors::count();
echo "Total Doctors: {$doctors}\n";

if ($doctors > 0) {
    $sampleDoctor = Doctors::with('user')->first();
    echo "Sample Doctor: {$sampleDoctor->name} (User ID: {$sampleDoctor->user_id})\n";
}

// Check if we have patients
$patients = Patients::count();
echo "Total Patients: {$patients}\n";

if ($patients > 0) {
    $samplePatient = Patients::with('user')->first();
    echo "Sample Patient: {$samplePatient->user->name} (ID: {$samplePatient->id})\n";
}

// Check if we have appointments
$appointments = Appointment::count();
echo "Total Appointments: {$appointments}\n";

if ($appointments > 0) {
    $sampleAppointment = Appointment::with(['patient.user', 'doctor'])->first();
    echo "Sample Appointment: Patient {$sampleAppointment->patient->user->name} with Dr. {$sampleAppointment->doctor->name}\n";
}

// Check doctor-patient relationships
if ($doctors > 0 && $patients > 0) {
    $doctorPatientRelations = Appointment::selectRaw('COUNT(DISTINCT CONCAT(doc_id, "-", patient_id)) as count')->value('count');
    echo "Doctor-Patient Relations: {$doctorPatientRelations}\n";
}

echo "\nHealth Record Creation should work if there are doctors with patient appointments.\n";