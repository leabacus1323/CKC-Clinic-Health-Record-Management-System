<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->boot();

use App\Models\Patients;
use App\Models\Doctors;
use App\Models\ImmunizationRecords;
use App\Models\DentalExamination;
use App\Models\PhysicalExamination;
use App\Models\PrescriptionRecord;
use App\Models\HealthRecords;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

echo "=== COMPREHENSIVE RELATIONSHIP TEST ===\n\n";

try {
    // Test Patient Relationships
    echo "1. PATIENT MODEL RELATIONSHIPS:\n";
    echo "   Available relationship methods:\n";
    $patientMethods = get_class_methods(Patients::class);
    $relationshipMethods = array_filter($patientMethods, function($method) {
        return in_array($method, [
            'User', 'user', 'immunization', 'prescription', 'dentalExaminations',
            'physicalExaminations', 'healthRecords', 'appointments'
        ]);
    });

    foreach ($relationshipMethods as $method) {
        echo "   ✓ {$method}()\n";
    }

    // Test Doctor Relationships
    echo "\n2. DOCTOR MODEL RELATIONSHIPS:\n";
    echo "   Available relationship methods:\n";
    $doctorMethods = get_class_methods(Doctors::class);
    $doctorRelationships = array_filter($doctorMethods, function($method) {
        return in_array($method, [
            'user', 'appointments', 'dentalExaminations', 'physicalExaminations', 'prescriptionRecords'
        ]);
    });

    foreach ($doctorRelationships as $method) {
        echo "   ✓ {$method}()\n";
    }

    // Test reverse relationships
    echo "\n3. REVERSE RELATIONSHIP TESTS:\n";

    // Test ImmunizationRecords -> Patient
    $immunizationMethods = get_class_methods(ImmunizationRecords::class);
    $hasPatientMethod = in_array('Patients', $immunizationMethods) || in_array('patient', $immunizationMethods);
    echo "   ImmunizationRecords -> Patient: " . ($hasPatientMethod ? "✓ Connected" : "✗ Missing") . "\n";

    // Test DentalExamination -> Patient & Doctor
    $dentalMethods = get_class_methods(DentalExamination::class);
    $hasDentalPatient = in_array('patient', $dentalMethods);
    $hasDentalDoctor = in_array('doctor', $dentalMethods);
    echo "   DentalExamination -> Patient: " . ($hasDentalPatient ? "✓ Connected" : "✗ Missing") . "\n";
    echo "   DentalExamination -> Doctor: " . ($hasDentalDoctor ? "✓ Connected" : "✗ Missing") . "\n";

    // Test PhysicalExamination -> Patient & Doctor
    $physicalMethods = get_class_methods(PhysicalExamination::class);
    $hasPhysicalPatient = in_array('patient', $physicalMethods);
    $hasPhysicalDoctor = in_array('doctor', $physicalMethods);
    echo "   PhysicalExamination -> Patient: " . ($hasPhysicalPatient ? "✓ Connected" : "✗ Missing") . "\n";
    echo "   PhysicalExamination -> Doctor: " . ($hasPhysicalDoctor ? "✓ Connected" : "✗ Missing") . "\n";

    // Test PrescriptionRecord relationships
    $prescriptionMethods = get_class_methods(PrescriptionRecord::class);
    $hasPrescriptionPatient = in_array('patient', $prescriptionMethods);
    $hasPrescriptionDoctor = in_array('doctor', $prescriptionMethods);
    $hasPrescriptionMedicine = in_array('medicine', $prescriptionMethods);
    echo "   PrescriptionRecord -> Patient: " . ($hasPrescriptionPatient ? "✓ Connected" : "✗ Missing") . "\n";
    echo "   PrescriptionRecord -> Doctor: " . ($hasPrescriptionDoctor ? "✓ Connected" : "✗ Missing") . "\n";
    echo "   PrescriptionRecord -> Medicine: " . ($hasPrescriptionMedicine ? "✓ Connected" : "✗ Missing") . "\n";

    echo "\n4. DATABASE CONSTRAINT VERIFICATION:\n";

    // Check table structures to verify foreign keys
    $tables = ['immunization_records', 'dental_examinations', 'physical_examinations', 'prescription_records'];

    foreach ($tables as $table) {
        echo "   {$table}:\n";
        $foreignKeys = DB::select("PRAGMA foreign_key_list({$table})");
        if (!empty($foreignKeys)) {
            foreach ($foreignKeys as $fk) {
                echo "     -> {$fk->from} references {$fk->table}.{$fk->to}\n";
            }
        } else {
            echo "     No foreign keys found\n";
        }
    }

    echo "\n✅ RELATIONSHIP TEST COMPLETED SUCCESSFULLY!\n";

} catch (Exception $e) {
    echo "❌ Error during relationship test: " . $e->getMessage() . "\n";
}
