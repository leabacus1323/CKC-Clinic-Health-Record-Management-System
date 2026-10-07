<?php
/**
 * Export SQLite Database to MySQL-Compatible SQL
 * Run with: php export_for_mysql.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== SQLite to MySQL Export Tool ===\n\n";

// Ensure we're using SQLite
config(['database.default' => 'sqlite']);

// Output file
$outputFile = 'mysql_import.sql';
$handle = fopen($outputFile, 'w');

// Write MySQL header
fwrite($handle, "-- MySQL Database Export\n");
fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
fwrite($handle, "-- Database: CKC Clinic Health Record Management System\n\n");
fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

// Tables to export
$tables = [
    'users',
    'admins',
    'patients',
    'doctors',
    'appointments',
    'medicine',
    'prescription_records',
    'health_records',
    'dental_examinations',
    'physical_examinations',
    'immunization_records',
    'immunizations',
    'courses',
    'educational_levels',
    'reports',
];

foreach ($tables as $table) {
    try {
        if (!Schema::hasTable($table)) {
            echo "⊘ Table '$table' does not exist, skipping...\n";
            continue;
        }

        $data = DB::table($table)->get();

        if ($data->isEmpty()) {
            echo "○ $table: No data\n";
            continue;
        }

        fwrite($handle, "-- Table: $table\n");
        fwrite($handle, "TRUNCATE TABLE `$table`;\n");

        foreach ($data as $row) {
            $row = (array) $row;

            // Escape values for MySQL
            $values = array_map(function($value) {
                if (is_null($value)) {
                    return 'NULL';
                }
                if (is_numeric($value)) {
                    return $value;
                }
                return "'" . addslashes($value) . "'";
            }, $row);

            $columns = array_keys($row);
            $columnsStr = '`' . implode('`, `', $columns) . '`';
            $valuesStr = implode(', ', $values);

            fwrite($handle, "INSERT INTO `$table` ($columnsStr) VALUES ($valuesStr);\n");
        }

        fwrite($handle, "\n");
        echo "✓ $table: " . count($data) . " records\n";

    } catch (Exception $e) {
        echo "✗ Error exporting $table: " . $e->getMessage() . "\n";
        continue;
    }
}

fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
fclose($handle);

echo "\n=== Export Complete ===\n";
echo "File saved: $outputFile\n";
echo "\nNext steps:\n";
echo "1. Open phpMyAdmin in your InfinityFree account\n";
echo "2. Select database: if0_40397174_ctcclinic\n";
echo "3. First, run migrations on the MySQL database (creates tables)\n";
echo "4. Then import this file: $outputFile\n";
echo "\nNote: Make sure to run 'php artisan migrate' on your hosting first!\n";
