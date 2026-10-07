# CKC Clinic Health Record Management System - Complete Setup Guide

This Laravel-based clinic record management system is already configured for SQLite. Follow these steps to get it running.

## Prerequisites Installation

### 1. Install PHP (Required)
Download and install PHP 8.2+ from: https://windows.php.net/download/
- Download the "Thread Safe" version
- Extract to `C:\php`
- Add `C:\php` to your system PATH
- Copy `php.ini-development` to `php.ini`
- Enable required extensions in `php.ini`:
  ```ini
  extension=pdo_sqlite
  extension=sqlite3
  extension=mbstring
  extension=openssl
  extension=curl
  extension=fileinfo
  extension=zip
  ```

### 2. Install Composer (Required)
Download and install from: https://getcomposer.org/download/
- This will automatically detect your PHP installation
- Restart your terminal after installation

### 3. Install Node.js (Optional, for frontend assets)
Download from: https://nodejs.org/
- Choose the LTS version
- This includes npm automatically

## Project Setup Steps

### Step 1: Install Dependencies
```powershell
# Install PHP dependencies
composer install

# Install Node.js dependencies (optional)
npm install
```

### Step 2: Create SQLite Database
```powershell
# Create the SQLite database file
New-Item -ItemType File -Path "database\database.sqlite" -Force
```

### Step 3: Configure Application
```powershell
# Generate application key
php artisan key:generate

# Clear caches
php artisan config:clear
php artisan cache:clear
```

### Step 4: Set Up Database
```powershell
# Run migrations to create tables
php artisan migrate

# Seed the database with sample data
php artisan db:seed
```

### Step 5: Build Assets (Optional)
```powershell
# Build frontend assets
npm run build
```

### Step 6: Start the Application
```powershell
# Start the development server
php artisan serve
```

Your application will be available at: http://localhost:8000

## Database Information

- **Type**: SQLite
- **Location**: `database/database.sqlite`
- **Configuration**: Already set in `.env` file

## Available Database Tables

Based on the migrations, your clinic system includes:
- Users & Authentication (users, admins)
- Patients Management
- Doctors/Staff Management  
- Appointments
- Health Records
- Medical Examinations (Physical & Dental)
- Immunizations & Records
- Prescriptions
- Reports
- Educational Levels
- Courses

## Default Seeders

The system includes seeders for:
- Default admin user
- Educational levels
- Sample patients
- Appointments
- Medical examinations
- Immunization records
- Prescription records

## Troubleshooting

### Common Issues:

1. **"php not recognized"**: PHP is not installed or not in PATH
2. **"composer not recognized"**: Composer is not installed
3. **Migration errors**: Check SQLite permissions and database file creation
4. **Permission errors**: Run terminal as administrator if needed

### Manual Database Setup (if migrations fail):
```powershell
# Reset and retry migrations
php artisan migrate:fresh --seed
```

### Check Database Connection:
```powershell
# Test database connection
php artisan tinker
# Then run: DB::connection()->getPdo();
```

## Project Structure

This is a Laravel 11 clinic management system with:
- Patient registration and management
- Doctor/staff management
- Appointment scheduling
- Health record tracking
- Medical examinations
- Immunization tracking
- Prescription management
- Reporting features

The system uses Tailwind CSS with Flowbite components for the frontend.