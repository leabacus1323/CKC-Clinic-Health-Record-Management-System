<?php
/**
 * Auto-detect SQLite schema and export to MySQL
 * This reads your actual database structure
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Smart MySQL Export ===\n\n";

config(['database.default' => 'sqlite']);

$dbPath = database_path('database.sqlite');
$pdo = new PDO('sqlite:' . $dbPath);

// Get all tables
$tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);

echo "Found " . count($tables) . " tables\n\n";

$outputFile = 'complete_mysql_import.sql';
$handle = fopen($outputFile, 'w');

fwrite($handle, "-- Complete MySQL Database Export\n");
fwrite($handle, "-- Auto-generated from SQLite: " . date('Y-m-d H:i:s') . "\n\n");
fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
fwrite($handle, "SET time_zone = \"+00:00\";\n\n");

foreach ($tables as $tableName) {
    echo "Processing: $tableName\n";

    // Get CREATE TABLE statement from SQLite
    $createStmt = $pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name='$tableName'")->fetchColumn();

    // Convert SQLite CREATE TABLE to MySQL
    $mysqlCreate = convertToMySQL($tableName, $createStmt);

    fwrite($handle, "-- Table: $tableName\n");
    fwrite($handle, "DROP TABLE IF EXISTS `$tableName`;\n");
    fwrite($handle, "$mysqlCreate\n\n");

    // Export data
    $data = $pdo->query("SELECT * FROM `$tableName`")->fetchAll(PDO::FETCH_ASSOC);

    if (count($data) > 0) {
        fwrite($handle, "-- Data for $tableName (" . count($data) . " records)\n");

        foreach ($data as $row) {
            $columns = array_keys($row);
            $values = array_map(function($value) {
                if (is_null($value)) return 'NULL';
                if (is_numeric($value) && strpos($value, '.') === false) return $value;
                return "'" . addslashes($value) . "'";
            }, array_values($row));

            $columnsStr = '`' . implode('`, `', $columns) . '`';
            $valuesStr = implode(', ', $values);

            fwrite($handle, "INSERT INTO `$tableName` ($columnsStr) VALUES ($valuesStr);\n");
        }

        fwrite($handle, "\n");
        echo "  ✓ Exported " . count($data) . " records\n";
    } else {
        echo "  ○ No data\n";
    }
}

fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
fclose($handle);

echo "\n=== Export Complete ===\n";
echo "File: $outputFile\n";

function convertToMySQL($tableName, $sqliteSQL) {
    // Start with basic structure
    $mysql = "CREATE TABLE `$tableName` (\n";

    // Extract column definitions
    preg_match('/CREATE TABLE[^(]*\((.*)\)/is', $sqliteSQL, $matches);
    if (!isset($matches[1])) {
        return "CREATE TABLE `$tableName` (id INT PRIMARY KEY);";
    }

    $columnDefs = $matches[1];
    $lines = explode(',', $columnDefs);
    $mysqlLines = [];
    $primaryKey = null;
    $uniqueKeys = [];

    foreach ($lines as $line) {
        $line = trim($line);

        // Skip constraints for now
        if (stripos($line, 'CONSTRAINT') !== false) continue;
        if (stripos($line, 'FOREIGN KEY') !== false) continue;

        // Handle PRIMARY KEY
        if (stripos($line, 'PRIMARY KEY') !== false) {
            preg_match('/PRIMARY KEY\s*\((.*?)\)/i', $line, $pkMatch);
            if (isset($pkMatch[1])) {
                $primaryKey = trim($pkMatch[1], '`" ');
            }
            continue;
        }

        // Handle UNIQUE
        if (stripos($line, 'UNIQUE') !== false && !preg_match('/^\w+/', $line)) {
            preg_match('/UNIQUE\s*\((.*?)\)/i', $line, $ukMatch);
            if (isset($ukMatch[1])) {
                $uniqueKeys[] = trim($ukMatch[1], '`" ');
            }
            continue;
        }

        // Parse column definition
        if (preg_match('/^["\']?(\w+)["\']?\s+(.+)$/i', $line, $colMatch)) {
            $colName = $colMatch[1];
            $colDef = $colMatch[2];

            // Convert types
            $colDef = preg_replace('/INTEGER/i', 'BIGINT', $colDef);
            $colDef = preg_replace('/DATETIME/i', 'TIMESTAMP', $colDef);
            $colDef = preg_replace('/TEXT/i', 'TEXT', $colDef);
            $colDef = preg_replace('/VARCHAR/i', 'VARCHAR(255)', $colDef);
            $colDef = preg_replace('/VARCHAR\(255\)\(\d+\)/i', 'VARCHAR($1)', $colDef);

            // Handle AUTOINCREMENT
            if (stripos($colDef, 'AUTOINCREMENT') !== false) {
                $colDef = 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT';
            }

            // Handle NOT NULL
            if (stripos($colDef, 'NOT NULL') === false && stripos($colDef, 'NULL') === false) {
                if (stripos($colDef, 'TIMESTAMP') !== false) {
                    $colDef .= ' NULL';
                }
            }

            // Handle DEFAULT CURRENT_TIMESTAMP
            $colDef = preg_replace('/DEFAULT \(CURRENT_TIMESTAMP\)/i', 'DEFAULT CURRENT_TIMESTAMP', $colDef);

            $mysqlLines[] = "  `$colName` $colDef";
        }
    }

    // Add primary key
    if ($primaryKey) {
        $mysqlLines[] = "  PRIMARY KEY (`$primaryKey`)";
    }

    // Add unique keys
    foreach ($uniqueKeys as $uk) {
        $ukName = str_replace('`', '', $uk) . '_unique';
        $mysqlLines[] = "  UNIQUE KEY `{$tableName}_{$ukName}` ($uk)";
    }

    $mysql .= implode(",\n", $mysqlLines);
    $mysql .= "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    return $mysql;
}
