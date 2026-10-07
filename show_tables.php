<?php
echo "Tables in your database:\n";
echo "========================\n\n";

try {
    $pdo = new PDO('sqlite:database/database.sqlite');
    $query = "SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;";
    $tables = $pdo->query($query)->fetchAll(PDO::FETCH_COLUMN);

    if (empty($tables)) {
        echo "No tables found in the database.\n";
    } else {
        foreach($tables as $table) {
            echo "- " . $table . "\n";
        }

        echo "\nTotal tables: " . count($tables) . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
