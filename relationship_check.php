<?php

use App\Models\Patients;
use App\Models\ImmunizationRecords;
use App\Models\DentalExamination;
use App\Models\PhysicalExamination;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->boot();

echo "=== DATABASE RELATIONSHIP ANALYSIS ===\n\n";

// Check immunization_records table structure
try {
    echo "1. IMMUNIZATION RECORDS TABLE:\n";
    $columns = DB::select("PRAGMA table_info(immunization_records)");
    foreach ($columns as $column) {
        echo "  - {$column->name} ({$column->type})\n";
    }

    $foreignKeys = DB::select("PRAGMA foreign_key_list(immunization_records)");
    echo "  Foreign Keys:\n";
    foreach ($foreignKeys as $fk) {
        echo "    -> {$fk->from} references {$fk->table}.{$fk->to}\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "Error checking immunization_records: " . $e->getMessage() . "\n\n";
}

// Check dental_examinations table structure
try {
    echo "2. DENTAL EXAMINATIONS TABLE:\n";
    $columns = DB::select("PRAGMA table_info(dental_examinations)");
    foreach ($columns as $column) {
        echo "  - {$column->name} ({$column->type})\n";
    }

    $foreignKeys = DB::select("PRAGMA foreign_key_list(dental_examinations)");
    echo "  Foreign Keys:\n";
    foreach ($foreignKeys as $fk) {
        echo "    -> {$fk->from} references {$fk->table}.{$fk->to}\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "Error checking dental_examinations: " . $e->getMessage() . "\n\n";
}

// Check physical_examinations table structure
try {
    echo "3. PHYSICAL EXAMINATIONS TABLE:\n";
    $columns = DB::select("PRAGMA table_info(physical_examinations)");
    foreach ($columns as $column) {
        echo "  - {$column->name} ({$column->type})\n";
    }

    $foreignKeys = DB::select("PRAGMA foreign_key_list(physical_examinations)");
    echo "  Foreign Keys:\n";
    foreach ($foreignKeys as $fk) {
        echo "    -> {$fk->from} references {$fk->table}.{$fk->to}\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "Error checking physical_examinations: " . $e->getMessage() . "\n\n";
}

echo "=== MODEL RELATIONSHIP ANALYSIS ===\n\n";

echo "Patient Model Relationships:\n";
$patientMethods = get_class_methods(Patients::class);
$relationships = ['immunization', 'dentalExamination', 'physicalExamination'];
foreach ($relationships as $rel) {
    if (in_array($rel, $patientMethods)) {
        echo "  ✓ {$rel}() method exists\n";
    } else {
        echo "  ✗ {$rel}() method missing\n";
    }
}
echo "\n";