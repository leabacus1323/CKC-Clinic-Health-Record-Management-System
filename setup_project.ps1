# CKC Clinic Health Record Management System - SQLite Setup Script
# This script will set up the Laravel project with SQLite database

Write-Host "=== CKC Clinic Health Record Management System Setup ===" -ForegroundColor Green

# Step 1: Install Composer Dependencies
Write-Host "Step 1: Installing Composer dependencies..." -ForegroundColor Yellow
if (Get-Command composer -ErrorAction SilentlyContinue) {
    composer install
    if ($LASTEXITCODE -ne 0) {
        Write-Host "❌ Composer install failed!" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "❌ Composer not found! Please install Composer first." -ForegroundColor Red
    exit 1
}

# Step 2: Install NPM Dependencies
Write-Host "Step 2: Installing NPM dependencies..." -ForegroundColor Yellow
if (Get-Command npm -ErrorAction SilentlyContinue) {
    npm install
    if ($LASTEXITCODE -ne 0) {
        Write-Host "❌ NPM install failed!" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "⚠️ NPM not found! Frontend assets won't be available." -ForegroundColor Yellow
}

# Step 3: Create SQLite Database File
Write-Host "Step 3: Creating SQLite database file..." -ForegroundColor Yellow
$dbPath = "database\database.sqlite"
if (!(Test-Path $dbPath)) {
    New-Item -ItemType File -Path $dbPath -Force
    Write-Host "✅ SQLite database file created at $dbPath" -ForegroundColor Green
} else {
    Write-Host "✅ SQLite database file already exists" -ForegroundColor Green
}

# Step 4: Generate Application Key (if not already generated)
Write-Host "Step 4: Generating application key..." -ForegroundColor Yellow
php artisan key:generate --ansi
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Key generation failed!" -ForegroundColor Red
    exit 1
}

# Step 5: Clear all caches
Write-Host "Step 5: Clearing application caches..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Step 6: Run Database Migrations
Write-Host "Step 6: Running database migrations..." -ForegroundColor Yellow
php artisan migrate --force
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Migration failed!" -ForegroundColor Red
    exit 1
}

# Step 7: Run Database Seeders
Write-Host "Step 7: Running database seeders..." -ForegroundColor Yellow
php artisan db:seed --force
if ($LASTEXITCODE -ne 0) {
    Write-Host "⚠️ Some seeders may have failed, but continuing..." -ForegroundColor Yellow
}

# Step 8: Create storage link
Write-Host "Step 8: Creating storage link..." -ForegroundColor Yellow
php artisan storage:link
if ($LASTEXITCODE -ne 0) {
    Write-Host "⚠️ Storage link creation failed, but continuing..." -ForegroundColor Yellow
}

# Step 9: Build frontend assets
Write-Host "Step 9: Building frontend assets..." -ForegroundColor Yellow
if (Get-Command npm -ErrorAction SilentlyContinue) {
    npm run build
    if ($LASTEXITCODE -ne 0) {
        Write-Host "⚠️ Frontend build failed, but continuing..." -ForegroundColor Yellow
    }
}

Write-Host "" 
Write-Host "=== Setup Complete! ===" -ForegroundColor Green
Write-Host "✅ SQLite database created and migrated" -ForegroundColor Green
Write-Host "✅ Sample data seeded" -ForegroundColor Green
Write-Host "" 
Write-Host "To start the development server:" -ForegroundColor Cyan
Write-Host "php artisan serve" -ForegroundColor White
Write-Host "" 
Write-Host "Your application will be available at: http://localhost:8000" -ForegroundColor Cyan
Write-Host "" 
Write-Host "Database location: $(Resolve-Path $dbPath)" -ForegroundColor Gray