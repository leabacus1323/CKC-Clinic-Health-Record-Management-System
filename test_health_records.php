<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING HEALTH RECORDS RELATIONSHIPS ===\n\n";

try {
    // Test if the patient relationship exists
    $healthRecord = new App\Models\HealthRecords();
    
    echo "✅ HealthRecords model loaded successfully\n";
    
    // Test patient relationship method
    if (method_exists($healthRecord, 'patient')) {
        echo "✅ patient() relationship method exists\n";
    } else {
        echo "❌ patient() relationship method missing\n";
    }
    
    // Test user relationship method
    if (method_exists($healthRecord, 'user')) {
        echo "✅ user() relationship method exists\n";
    } else {
        echo "❌ user() relationship method missing\n";
    }
    
    // Test the query that was failing
    echo "\n🔍 Testing the doctor dashboard query...\n";
    $doctor = App\Models\Doctors::first();
    if ($doctor) {
        $healthRecords = App\Models\HealthRecords::whereHas('patient.appointments', function($query) use ($doctor) {
                $query->where('doc_id', $doctor->id);
            })->count();
        echo "✅ Query executed successfully! Found {$healthRecords} health records.\n";
    } else {
        echo "⚠️ No doctors found to test with\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
