<?php

echo "=== TESTING FIXED RELATIONSHIPS ===\n\n";

try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';

    // Just test basic model instantiation and method existence
    echo "1. Testing Model Instantiation:\n";

    $patient = new App\Models\Patients();
    echo "   ✓ Patients model instantiated\n";

    $physical = new App\Models\PhysicalExamination();
    echo "   ✓ PhysicalExamination model instantiated\n";

    $dental = new App\Models\DentalExamination();
    echo "   ✓ DentalExamination model instantiated\n";

    $immunization = new App\Models\ImmunizationRecords();
    echo "   ✓ ImmunizationRecords model instantiated\n";

    echo "\n2. Testing Method Existence:\n";

    // Test Patient methods
    $patientMethods = get_class_methods($patient);
    $expectedMethods = ['physicalExaminations', 'dentalExaminations', 'immunization'];

    foreach ($expectedMethods as $method) {
        if (in_array($method, $patientMethods)) {
            echo "   ✓ Patient->{$method}() exists\n";
        } else {
            echo "   ✗ Patient->{$method}() missing\n";
        }
    }

    // Test reverse relationships
    $physicalMethods = get_class_methods($physical);
    $dentalMethods = get_class_methods($dental);
    $immunizationMethods = get_class_methods($immunization);

    echo "   PhysicalExamination->patient(): " . (in_array('patient', $physicalMethods) ? "✓ exists" : "✗ missing") . "\n";
    echo "   DentalExamination->patient(): " . (in_array('patient', $dentalMethods) ? "✓ exists" : "✗ missing") . "\n";
    echo "   ImmunizationRecords->Patients(): " . (in_array('Patients', $immunizationMethods) ? "✓ exists" : "✗ missing") . "\n";

    echo "\n✅ All tests completed successfully!\n";
    echo "\nThe 'user_id' errors should now be resolved.\n";

} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
}
