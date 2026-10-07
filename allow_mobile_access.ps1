# Run this script as Administrator to allow mobile access

Write-Host "Adding Windows Firewall rule for Laravel server..." -ForegroundColor Yellow

# Add firewall rule for inbound connections on port 8000
New-NetFirewallRule -DisplayName "Laravel Dev Server Port 8000" `
    -Direction Inbound `
    -LocalPort 8000 `
    -Protocol TCP `
    -Action Allow `
    -Profile Any

Write-Host "Firewall rule added successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "Now you can access the server from your mobile at:" -ForegroundColor Cyan
Write-Host "http://192.168.1.2:8000" -ForegroundColor White
Write-Host ""
Write-Host "Make sure your mobile is connected to the same WiFi network." -ForegroundColor Yellow
