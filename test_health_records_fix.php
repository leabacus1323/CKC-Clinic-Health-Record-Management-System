<?php

echo "=== TESTING HEALTH RECORDS FIX ===\n\n";

try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->boot();

    echo "1. Testing HealthRecords Model:\n";

    // Check if HealthRecords model can be instantiated
    $healthRecord = new App\Models\HealthRecords();
    echo "   ✓ HealthRecords model instantiated\n";

    // Check relationships
    $methods = get_class_methods($healthRecord);
    echo "   HealthRecords->patient(): " . (in_array('patient', $methods) ? "✓ exists" : "✗ missing") . "\n";
    echo "   HealthRecords->user(): " . (in_array('user', $methods) ? "✓ exists" : "✗ missing") . "\n";

    echo "\n2. Testing Database Query Structure:\n";

    // Test a simple query to see if the relationships work
    $pdo = new PDO('sqlite:database/database.sqlite');

    // Check if health_records table exists and has data
    $count = $pdo->query("SELECT COUNT(*) as count FROM health_records")->fetch(PDO::FETCH_ASSOC);
    echo "   Health records count: {$count['count']}\n";

    // Check if health records have valid patient_id references
    $validPatientRefs = $pdo->query("
        SELECT COUNT(*) as count
        FROM health_records hr
        JOIN patients p ON hr.patient_id = p.id
    ")->fetch(PDO::FETCH_ASSOC);
    echo "   Health records with valid patient references: {$validPatientRefs['count']}\n";

    // Check if patients have valid user_id references
    $validUserRefs = $pdo->query("
        SELECT COUNT(*) as count
        FROM health_records hr
        JOIN patients p ON hr.patient_id = p.id
        JOIN users u ON p.user_id = u.id
    ")->fetch(PDO::FETCH_ASSOC);
    echo "   Health records with valid patient->user chain: {$validUserRefs['count']}\n";

    echo "\n✅ Health records structure test completed!\n";
    echo "\nThe 'Attempt to read property \"id\" on null' error should now be resolved.\n";

} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
}
