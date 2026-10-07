# MariaDB Service Installation Script
# Run this as Administrator

Write-Host "Installing MariaDB as Windows Service..." -ForegroundColor Yellow
Write-Host "This requires Administrator privileges." -ForegroundColor Yellow
Write-Host ""

# Check if running as Administrator
if (-NOT ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")) {
    Write-Host "ERROR: This script must be run as Administrator!" -ForegroundColor Red
    Write-Host "Right-click PowerShell and select 'Run as Administrator'" -ForegroundColor Red
    pause
    exit 1
}

# Stop any running MariaDB processes first
Write-Host "Stopping any running MariaDB processes..." -ForegroundColor Yellow
Get-Process -Name "mysqld" -ErrorAction SilentlyContinue | Stop-Process -Force
Start-Sleep -Seconds 3

# Copy configuration file to MariaDB data directory
$configSource = ".\mariadb_config.ini"
$configDest = "C:\Program Files\MariaDB 12.0\data\my.ini"

if (Test-Path $configSource) {
    Write-Host "Copying configuration file..." -ForegroundColor Yellow
    Copy-Item $configSource $configDest -Force
} else {
    Write-Host "Warning: Configuration file not found, using default settings" -ForegroundColor Yellow
}

# Install MariaDB as a service
Write-Host "Installing MariaDB service..." -ForegroundColor Yellow
$installCmd = "& `"C:\Program Files\MariaDB 12.0\bin\mysqld.exe`" --install MariaDB --defaults-file=`"C:\Program Files\MariaDB 12.0\data\my.ini`" --datadir=`"C:\Program Files\MariaDB 12.0\data`""

try {
    Invoke-Expression $installCmd
    Write-Host "MariaDB service installed successfully!" -ForegroundColor Green

    # Start the service
    Write-Host "Starting MariaDB service..." -ForegroundColor Yellow
    Start-Service -Name "MariaDB"

    # Set service to start automatically
    Set-Service -Name "MariaDB" -StartupType Automatic

    Write-Host "MariaDB service started successfully!" -ForegroundColor Green
    Write-Host "MariaDB will now start automatically when Windows boots." -ForegroundColor Green
    Write-Host ""
    Write-Host "Service Details:" -ForegroundColor Cyan
    Write-Host "- Service Name: MariaDB" -ForegroundColor White
    Write-Host "- Startup Type: Automatic" -ForegroundColor White
    Write-Host "- Data Directory: C:\Program Files\MariaDB 12.0\data" -ForegroundColor White
    Write-Host ""
    Write-Host "You can manage the service using:" -ForegroundColor Cyan
    Write-Host "- Start: Start-Service MariaDB" -ForegroundColor White
    Write-Host "- Stop:  Stop-Service MariaDB" -ForegroundColor White
    Write-Host "- Status: Get-Service MariaDB" -ForegroundColor White
    Write-Host "- Or use Windows Services manager (services.msc)" -ForegroundColor White

} catch {
    Write-Host "Failed to install or start MariaDB service." -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "Press any key to continue..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
