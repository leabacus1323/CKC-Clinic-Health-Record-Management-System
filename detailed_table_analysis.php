<?php

echo "=== DETAILED TABLE ANALYSIS ===\n\n";

try {
    $pdo = new PDO('sqlite:database/database.sqlite');

    // Key tables we want to analyze
    $keyTables = ['patients', 'users', 'immunization_records', 'dental_examinations', 'physical_examinations'];

    foreach ($keyTables as $tableName) {
        echo "TABLE: {$tableName}\n";
        echo str_repeat("=", strlen($tableName) + 7) . "\n";

        // Get column information
        $columns = $pdo->query("PRAGMA table_info({$tableName})")->fetchAll(PDO::FETCH_ASSOC);

        echo "Columns:\n";
        foreach ($columns as $column) {
            $info = "  - {$column['name']} ({$column['type']})";
            if ($column['notnull']) $info .= " NOT NULL";
            if ($column['pk']) $info .= " PRIMARY KEY";
            if ($column['dflt_value'] !== null) $info .= " DEFAULT {$column['dflt_value']}";
            echo $info . "\n";
        }

        // Get foreign key information
        $foreignKeys = $pdo->query("PRAGMA foreign_key_list({$tableName})")->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($foreignKeys)) {
            echo "\nForeign Keys:\n";
            foreach ($foreignKeys as $fk) {
                echo "  - {$fk['from']} -> {$fk['table']}.{$fk['to']}\n";
            }
        } else {
            echo "\nNo foreign keys found.\n";
        }

        echo "\n" . str_repeat("-", 50) . "\n\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
