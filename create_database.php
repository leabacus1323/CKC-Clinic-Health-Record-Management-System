<?php
// Quick script to create MySQL database and run migrations
try {
    // Connect without database to create it
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS ckc_shrms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ Database 'ckc_shrms' created successfully!\n";
    
    // Select the database
    $pdo->exec("USE ckc_shrms");
    
    // Run migrations
    echo "✓ Now run: php artisan migrate --force\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nIf you get access denied, you may need to:\n";
    echo "1. Open MariaDB command line: mysql -u root -p\n";
    echo "2. Run: ALTER USER 'root'@'localhost' IDENTIFIED BY '';\n";
}