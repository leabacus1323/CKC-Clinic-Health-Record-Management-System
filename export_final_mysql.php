<?php
/**
 * Reliable MySQL Export - Uses Laravel schema to ensure accuracy
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Reliable MySQL Export ===\n\n";

config(['database.default' => 'sqlite']);

// Get SQLite connection
$dbPath = database_path('database.sqlite');
$sqlite = new PDO('sqlite:' . $dbPath);

// Output file
$outputFile = 'complete_mysql_import.sql';
$handle = fopen($outputFile, 'w');

fwrite($handle, "-- MySQL Database Export\n");
fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n\n");
fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n\n");

// Get all tables
$tables = $sqlite->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $tableName) {
    echo "Processing: $tableName...";
    
    // Get column info from SQLite
    $columns = $sqlite->query("PRAGMA table_info(`$tableName`)")->fetchAll(PDO::FETCH_ASSOC);
    
    // Start CREATE TABLE
    fwrite($handle, "-- Table: $tableName\n");
    fwrite($handle, "DROP TABLE IF EXISTS `$tableName`;\n");
    fwrite($handle, "CREATE TABLE `$tableName` (\n");
    
    $columnDefs = [];
    $primaryKey = null;
    
    foreach ($columns as $col) {
        $name = $col['name'];
        $type = strtoupper($col['type']);
        $notNull = $col['notnull'] ? 'NOT NULL' : 'NULL';
        $default = $col['dflt_value'];
        $isPk = $col['pk'];
        
        // Convert SQLite types to MySQL
        $mysqlType = 'VARCHAR(255)';
        
        if (strpos($type, 'INT') !== false) {
            if ($isPk) {
                $mysqlType = 'BIGINT UNSIGNED NOT NULL AUTO_INCREMENT';
                $primaryKey = $name;
            } else {
                $mysqlType = 'BIGINT';
            }
        } elseif (strpos($type, 'VARCHAR') !== false) {
            preg_match('/VARCHAR\((\d+)\)/', $type, $matches);
            $length = $matches[1] ?? 255;
            $mysqlType = "VARCHAR($length)";
        } elseif (strpos($type, 'TEXT') !== false) {
            $mysqlType = 'TEXT';
        } elseif (strpos($type, 'DATE') !== false && strpos($type, 'DATETIME') === false) {
            $mysqlType = 'DATE';
        } elseif (strpos($type, 'DATETIME') !== false || strpos($type, 'TIMESTAMP') !== false) {
            $mysqlType = 'TIMESTAMP';
        } elseif (strpos($type, 'DECIMAL') !== false || strpos($type, 'NUMERIC') !== false) {
            preg_match('/\(([^)]+)\)/', $type, $matches);
            $precision = $matches[1] ?? '10,2';
            $mysqlType = "DECIMAL($precision)";
        } elseif (strpos($type, 'REAL') !== false || strpos($type, 'DOUBLE') !== false) {
            $mysqlType = 'DOUBLE';
        }
        
        // Build column definition
        if ($isPk) {
            $columnDefs[] = "  `$name` $mysqlType";
        } else {
            $def = "  `$name` $mysqlType";
            
            if ($notNull === 'NOT NULL' && $default === null) {
                $def .= ' NOT NULL';
            } else {
                $def .= ' NULL';
            }
            
            if ($default !== null && $default !== 'NULL') {
                $default = str_replace("'", "", $default);
                if ($default === 'CURRENT_TIMESTAMP' || $default === '(CURRENT_TIMESTAMP)') {
                    $def .= ' DEFAULT CURRENT_TIMESTAMP';
                } elseif (is_numeric($default)) {
                    $def .= " DEFAULT $default";
                } else {
                    $def .= " DEFAULT '$default'";
                }
            }
            
            $columnDefs[] = $def;
        }
    }
    
    // Add primary key
    if ($primaryKey) {
        $columnDefs[] = "  PRIMARY KEY (`$primaryKey`)";
    }
    
    // Check for unique constraints
    $indexes = $sqlite->query("PRAGMA index_list(`$tableName`)")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($indexes as $index) {
        if ($index['unique'] == 1 && strpos($index['name'], 'autoindex') === false) {
            $indexInfo = $sqlite->query("PRAGMA index_info(`{$index['name']}`)")->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($indexInfo)) {
                $col = $indexInfo[0]['name'];
                $columnDefs[] = "  UNIQUE KEY `{$index['name']}` (`$col`)";
            }
        }
    }
    
    fwrite($handle, implode(",\n", $columnDefs));
    fwrite($handle, "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n");
    
    // Export data
    $data = $sqlite->query("SELECT * FROM `$tableName`")->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($data) > 0) {
        fwrite($handle, "-- Data for $tableName\n");
        
        foreach ($data as $row) {
            $columns = array_keys($row);
            $values = array_map(function($value) {
                if (is_null($value)) return 'NULL';
                if (is_numeric($value) && strpos($value, '.') === false && strlen($value) < 19) return $value;
                return "'" . addslashes($value) . "'";
            }, array_values($row));
            
            $columnsStr = '`' . implode('`, `', $columns) . '`';
            $valuesStr = implode(', ', $values);
            
            fwrite($handle, "INSERT INTO `$tableName` ($columnsStr) VALUES ($valuesStr);\n");
        }
        
        fwrite($handle, "\n");
        echo " ✓ " . count($data) . " records\n";
    } else {
        echo " ○ No data\n";
    }
}

fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
fclose($handle);

echo "\n=== Export Complete ===\n";
echo "File: $outputFile\n";
echo "Ready to import in phpMyAdmin!\n";
