# Script to Migrate from SQLite to MySQL
# This will export data from local SQLite and import to remote MySQL

Write-Host "=== Database Migration: SQLite to MySQL ===" -ForegroundColor Cyan
Write-Host ""

# Step 1: Backup current SQLite database
Write-Host "Step 1: Creating backup of SQLite database..." -ForegroundColor Yellow
$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$backupPath = "database\backups"
if (!(Test-Path $backupPath)) {
    New-Item -Path $backupPath -ItemType Directory -Force | Out-Null
}
Copy-Item "database\database.sqlite" "$backupPath\database_backup_$timestamp.sqlite"
Write-Host "[OK] Backup created: $backupPath\database_backup_$timestamp.sqlite" -ForegroundColor Green
Write-Host ""

# Step 2: Test MySQL connection
Write-Host "Step 2: Testing MySQL connection..." -ForegroundColor Yellow
$output = php artisan migrate:status 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] MySQL connection successful!" -ForegroundColor Green
} else {
    Write-Host "[ERROR] MySQL connection failed. Please check your .env settings." -ForegroundColor Red
    Write-Host "Output: $output" -ForegroundColor Yellow
    exit 1
}
Write-Host ""

# Step 3: Run migrations on MySQL
Write-Host "Step 3: Running migrations on MySQL database..." -ForegroundColor Yellow
$response = Read-Host "This will create all tables in the MySQL database. Continue? (y/n)"
if ($response -ne "y") {
    Write-Host "Migration cancelled." -ForegroundColor Yellow
    exit 0
}

php artisan migrate --force
if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Migrations completed successfully!" -ForegroundColor Green
} else {
    Write-Host "[ERROR] Migration failed." -ForegroundColor Red
    exit 1
}
Write-Host ""

# Step 4: Data migration options
Write-Host "Step 4: Data export/import options:" -ForegroundColor Yellow
Write-Host ""
Write-Host "Option 1: Manual SQL Export (Recommended for large databases)" -ForegroundColor Cyan
Write-Host "  - Install SQLite Browser: https://sqlitebrowser.org/" -ForegroundColor White
Write-Host "  - Open database\database.sqlite" -ForegroundColor White
Write-Host "  - Go to File -> Export -> Database to SQL file" -ForegroundColor White
Write-Host "  - Select all tables and export" -ForegroundColor White
Write-Host "  - Import to MySQL using phpMyAdmin" -ForegroundColor White
Write-Host ""

Write-Host "Option 2: Using Laravel Tinker (Good for small datasets)" -ForegroundColor Cyan
Write-Host "  Run these commands in separate terminal:" -ForegroundColor White
Write-Host "  php artisan tinker" -ForegroundColor Gray
Write-Host "  Then copy data table by table" -ForegroundColor Gray
Write-Host ""

Write-Host "Option 3: Create a custom seeder from SQLite data" -ForegroundColor Cyan
Write-Host "  Create a seeder that reads from SQLite backup" -ForegroundColor White
Write-Host "  and imports to MySQL" -ForegroundColor White
Write-Host ""

$choice = Read-Host "Which option would you like? (1/2/3 or skip)"

if ($choice -eq "3") {
    Write-Host ""
    Write-Host "Creating data migration seeder..." -ForegroundColor Yellow
    
    # Create seeder file content
    $seederContent = @"
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SQLiteToMySQLSeeder extends Seeder
{
    public function run()
    {
        echo "Reading data from SQLite backup...\n";
        
        // List of tables to migrate
        `$tables = [
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
        ];
        
        foreach (`$tables as `$table) {
            try {
                `$sqlitePath = database_path('database.sqlite');
                `$pdo = new \PDO('sqlite:' . `$sqlitePath);
                `$stmt = `$pdo->query("SELECT * FROM `$table");
                `$data = `$stmt->fetchAll(\PDO::FETCH_ASSOC);
                
                if (count(`$data) > 0) {
                    // Insert data in chunks
                    `$chunks = array_chunk(`$data, 100);
                    foreach (`$chunks as `$chunk) {
                        DB::table(`$table)->insert(`$chunk);
                    }
                    
                    echo "✓ Migrated `$table: " . count(`$data) . " records\n";
                } else {
                    echo "○ `$table: No data to migrate\n";
                }
            } catch (\Exception `$e) {
                echo "✗ Error migrating `$table: " . `$e->getMessage() . "\n";
                continue;
            }
        }
        
        echo "Data migration completed!\n";
    }
}
"@
    
    Set-Content -Path "database\seeders\SQLiteToMySQLSeeder.php" -Value $seederContent -Encoding UTF8
    Write-Host "[OK] Seeder created: database\seeders\SQLiteToMySQLSeeder.php" -ForegroundColor Green
    Write-Host ""
    Write-Host "Now run this command to migrate the data:" -ForegroundColor Cyan
    Write-Host "php artisan db:seed --class=SQLiteToMySQLSeeder" -ForegroundColor White
    Write-Host ""
}

Write-Host ""
Write-Host "=== Migration Setup Complete ===" -ForegroundColor Green
Write-Host ""
Write-Host "Your .env is now configured for MySQL." -ForegroundColor Cyan
Write-Host "SQLite backup saved in: database\backups\" -ForegroundColor Cyan
Write-Host ""

