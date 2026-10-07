<?php

require 'vendor/autoload.php';
require 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== CHECKING TABLE RELATIONSHIPS ===\n\n";

// Check if tables exist
$tables = ['patients', 'immunization_records', 'dental_examinations', 'physical_examinations'];

foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        echo "✓ Table '$table' exists\n";

        // Get table structure
        $columns = DB::select("PRAGMA table_info($table)");
        echo "Columns in $table:\n";
        foreach ($columns as $column) {
            $info = "  - {$column->name} ({$column->type})";
            if ($column->notnull) $info .= " NOT NULL";
            if ($column->dflt_value !== null) $info .= " DEFAULT {$column->dflt_value}";
            if ($column->pk) $info .= " PRIMARY KEY";
            echo $info . "\n";
        }

        // Check foreign keys
        $foreignKeys = DB::select("PRAGMA foreign_key_list($table)");
        if (!empty($foreignKeys)) {
            echo "Foreign Keys in $table:\n";
            foreach ($foreignKeys as $fk) {
                echo "  - {$fk->from} -> {$fk->table}.{$fk->to}\n";
            }
        } else {
            echo "No foreign keys found in $table\n";
        }
        echo "\n";
    } else {
        echo "✗ Table '$table' does not exist\n\n";
    }
}

echo "=== CHECKING MODEL RELATIONSHIPS ===\n\n";

// Check Patient model relationships
try {
    $patient = new App\Models\Patients();
    echo "Patient Model Methods:\n";
    $methods = get_class_methods($patient);
    $relationshipMethods = array_filter($methods, function($method) {
        return in_array($method, ['immunization', 'dentalExamination', 'physicalExamination', 'prescription', 'appointments', 'User']);
    });

    foreach ($relationshipMethods as $method) {
        echo "  - $method()\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "Error checking Patient model: " . $e->getMessage() . "\n\n";
}

// Test actual relationships
try {
    echo "Testing Patient->immunization relationship:\n";
    $patient = App\Models\Patients::first();
    if ($patient) {
        $immunization = $patient->immunization;
        echo "  Patient ID: {$patient->id} has immunization record: " . ($immunization ? 'YES' : 'NO') . "\n";
    } else {
        echo "  No patients found in database\n";
    }
} catch (Exception $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}

echo "\n";
