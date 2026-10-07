<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Patients;
use App\Models\Doctors;

echo "=== USERS TABLE ===\n";
$users = User::all();
foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, User Type: {$user->user_type}\n";
}

echo "\n=== PATIENTS TABLE ===\n";
$patients = Patients::all();
foreach ($patients as $patient) {
    echo "ID: {$patient->id}, User ID: {$patient->user_id}, Patient Type: {$patient->patient_type}\n";
}

echo "\n=== DOCTORS TABLE ===\n";
$doctors = Doctors::all();
foreach ($doctors as $doctor) {
    echo "ID: {$doctor->id}, User ID: {$doctor->user_id}, Specialization: {$doctor->specialization}\n";
}

echo "\n=== PATIENTS WITH USER INFO ===\n";
$patientsWithUsers = Patients::join('users', 'users.id', '=', 'patients.user_id')
    ->select('patients.*', 'users.name', 'users.user_type')
    ->get();

foreach ($patientsWithUsers as $patient) {
    echo "Patient ID: {$patient->id}, Name: {$patient->name}, User Type: {$patient->user_type}, Patient Type: {$patient->patient_type}\n";
}

echo "\n=== CHECKING JANE STAFF ===\n";
$jane = User::where('name', 'LIKE', '%Jane%')->first();
if ($jane) {
    echo "Jane User - ID: {$jane->id}, Name: {$jane->name}, User Type: {$jane->user_type}\n";

    $janePatient = Patients::where('user_id', $jane->id)->first();
    if ($janePatient) {
        echo "Jane Patient Record - ID: {$janePatient->id}, Patient Type: {$janePatient->patient_type}\n";
    } else {
        echo "Jane has NO patient record!\n";
    }

    $janeDoctor = Doctors::where('user_id', $jane->id)->first();
    if ($janeDoctor) {
        echo "Jane Doctor Record - ID: {$janeDoctor->id}, Specialization: {$janeDoctor->specialization}\n";
    }
} else {
    echo "Jane not found in users table\n";
}

?>
