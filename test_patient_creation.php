<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING PATIENT CREATION WITHOUT SCHOOL_ID ===\n\n";

try {
    // Test creating a patient without school_id (should work now)
    $testUser = App\Models\User::first();
    if (!$testUser) {
        echo "❌ No users found to test with\n";
        exit;
    }

    echo "🔍 Testing patient creation without school_id...\n";
    echo "   Using user: {$testUser->name} (ID: {$testUser->id})\n";

    // Check if patient already exists
    $existingPatient = App\Models\Patients::where('user_id', $testUser->id)->first();
    if ($existingPatient) {
        echo "   ⚠️ Patient already exists for this user (ID: {$existingPatient->id})\n";
        echo "   Deleting existing patient for test...\n";
        $existingPatient->delete();
    }

    // Try to create patient without school_id
    $patient = App\Models\Patients::create([
        'user_id' => $testUser->id,
        'patient_type' => 1,
        'address' => 'Test Address',
        'medical_condition' => 'None',
        'medical_illness' => 'None',
        'operations' => 'None',
        'allergies' => 'None',
        'emergency_contact_name' => 'Test Contact',
        'emergency_contact_number' => '123456789',
        'emergency_relationship' => 'Friend',
    ]);

    echo "✅ Patient created successfully without school_id!\n";
    echo "   Patient ID: {$patient->id}\n";
    echo "   School ID: " . ($patient->school_id ?? 'NULL') . "\n";

    // Clean up - delete the test patient
    $patient->delete();
    echo "   🧹 Test patient deleted\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
