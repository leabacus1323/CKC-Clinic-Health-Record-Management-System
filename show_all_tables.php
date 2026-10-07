<?php

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->boot();

echo "=== ALL EXISTING TABLES ===\n\n";

try {
    // Get all table names from SQLite
    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");

    $tableCount = 0;
    foreach ($tables as $table) {
        $tableCount++;
        echo "{$tableCount}. {$table->name}\n";

        // Get column info for each table
        $columns = DB::select("PRAGMA table_info({$table->name})");
        echo "   Columns:\n";
        foreach ($columns as $column) {
            $info = "     - {$column->name} ({$column->type})";
            if ($column->notnull) $info .= " NOT NULL";
            if ($column->pk) $info .= " [PRIMARY KEY]";
            echo $info . "\n";
        }

        // Check for foreign keys
        $foreignKeys = DB::select("PRAGMA foreign_key_list({$table->name})");
        if (!empty($foreignKeys)) {
            echo "   Foreign Keys:\n";
            foreach ($foreignKeys as $fk) {
                echo "     -> {$fk->from} references {$fk->table}.{$fk->to}\n";
            }
        }
        echo "\n";
    }

    echo "Total tables: {$tableCount}\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}