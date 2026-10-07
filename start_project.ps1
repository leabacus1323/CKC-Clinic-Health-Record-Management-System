# Laravel Project Startup Script
# This script will start MariaDB (if not running) and Laravel development server

Write-Host "=== CKC Clinic HRMS Laravel Project Startup ===" -ForegroundColor Cyan
Write-Host ""

# Check if MariaDB service exists and start it
Write-Host "Checking MariaDB service..." -ForegroundColor Yellow

# Try to find MariaDB service (any version)
$service = Get-Service -Name "MariaDB*" -ErrorAction SilentlyContinue | Select-Object -First 1

if ($service) {
    if ($service.Status -eq "Stopped") {
        Write-Host "Starting MariaDB service ($($service.Name))..." -ForegroundColor Yellow
        Start-Service -Name $service.Name
        Start-Sleep -Seconds 3
    }
    Write-Host "MariaDB service is running!" -ForegroundColor Green
} else {
    Write-Host "MariaDB service not found. Starting manually..." -ForegroundColor Yellow
    # Create tmp directory if it doesn't exist
    if (!(Test-Path "C:\Program Files\MariaDB 12.0\data\tmp")) {
        New-Item -Path "C:\Program Files\MariaDB 12.0\data\tmp" -ItemType Directory -Force | Out-Null
    }
    Start-Process -FilePath "C:\Program Files\MariaDB 12.0\bin\mysqld.exe" -ArgumentList "--datadir=`"C:\Program Files\MariaDB 12.0\data`"","--tmpdir=`"C:\Program Files\MariaDB 12.0\data\tmp`"" -WindowStyle Hidden
    Start-Sleep -Seconds 5
    Write-Host "MariaDB started manually." -ForegroundColor Green
}

# Test database connection
Write-Host "Testing database connection..." -ForegroundColor Yellow
try {
    $result = php artisan migrate:status --no-interaction 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "Database connection successful!" -ForegroundColor Green
    } else {
        Write-Host "Warning: Database connection issues detected." -ForegroundColor Yellow
    }
} catch {
    Write-Host "Warning: Could not test database connection." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Project Information:" -ForegroundColor Cyan
Write-Host "- Database: MariaDB (ckc-shrms)" -ForegroundColor White
Write-Host "- Test Patient Account: patient1@test.com / patient1" -ForegroundColor White
Write-Host "- Admin Account: lyjieme@gmail.com / Admin_2k24" -ForegroundColor White
Write-Host ""

# Ask if user wants to start Laravel server
$response = Read-Host "Do you want to start Laravel development server? (y/n)"
if ($response -eq "y" -or $response -eq "Y" -or $response -eq "") {
    Write-Host "Starting Laravel development server..." -ForegroundColor Yellow
    Write-Host "Server will be available at: http://localhost:8000" -ForegroundColor Green
    Write-Host "Patient Portal: http://localhost:8000/patient/dashboard" -ForegroundColor Green
    Write-Host "Admin Portal: http://localhost:8000/admin/login" -ForegroundColor Green
    Write-Host ""
    Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Yellow
    Write-Host ""

    php artisan serve
} else {
    Write-Host "Laravel server not started. You can start it manually with: php artisan serve" -ForegroundColor White
}

Write-Host ""
Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
