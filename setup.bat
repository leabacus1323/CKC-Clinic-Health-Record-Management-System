@echo off
echo === CKC Clinic Health Record Management System - Quick Setup ===
echo.

REM Check if PHP is installed
php -v >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] PHP is not installed or not in PATH
    echo Please install PHP 8.2+ from: https://windows.php.net/download/
    echo Add PHP to your system PATH and restart this script
    pause
    exit /b 1
)

REM Check if Composer is installed
composer -V >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Composer is not installed or not in PATH
    echo Please install Composer from: https://getcomposer.org/download/
    echo Restart this script after installation
    pause
    exit /b 1
)

echo [INFO] PHP and Composer are available
echo.

echo [STEP 1] Installing Composer dependencies...
composer install
if %errorlevel% neq 0 (
    echo [ERROR] Composer install failed!
    pause
    exit /b 1
)

echo [STEP 2] Creating SQLite database...
if not exist "database\database.sqlite" (
    echo. > "database\database.sqlite"
    echo [SUCCESS] SQLite database created
) else (
    echo [INFO] SQLite database already exists
)

echo [STEP 3] Generating application key...
php artisan key:generate
if %errorlevel% neq 0 (
    echo [ERROR] Key generation failed!
    pause
    exit /b 1
)

echo [STEP 4] Clearing caches...
php artisan config:clear
php artisan cache:clear
php artisan route:clear

echo [STEP 5] Running database migrations...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo [ERROR] Migration failed!
    pause
    exit /b 1
)

echo [STEP 6] Seeding database with sample data...
php artisan db:seed --force
if %errorlevel% neq 0 (
    echo [WARNING] Some seeders may have failed, but continuing...
)

echo [STEP 7] Creating storage link...
php artisan storage:link

echo.
echo === Setup Complete! ===
echo ✓ SQLite database created and migrated
echo ✓ Sample data seeded
echo.
echo To start the development server, run:
echo   php artisan serve
echo.
echo Your application will be available at: http://localhost:8000
echo.
pause