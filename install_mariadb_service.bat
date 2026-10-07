@echo off
echo Installing MariaDB as Windows Service...
echo This requires Administrator privileges.
echo.

REM Stop any running MariaDB processes first
taskkill /F /IM mysqld.exe 2>nul
timeout /t 3 /nobreak >nul

REM Install MariaDB as a service
"C:\Program Files\MariaDB 12.0\bin\mysqld.exe" --install MariaDB --defaults-file="C:\Program Files\MariaDB 12.0\data\my.ini" --datadir="C:\Program Files\MariaDB 12.0\data"

if %ERRORLEVEL% EQU 0 (
    echo MariaDB service installed successfully!
    echo.
    echo Starting MariaDB service...
    net start MariaDB

    if %ERRORLEVEL% EQU 0 (
        echo MariaDB service started successfully!
        echo MariaDB will now start automatically when Windows boots.
        echo.
        echo Service Details:
        echo - Service Name: MariaDB
        echo - Startup Type: Automatic
        echo - Data Directory: C:\Program Files\MariaDB 12.0\data
        echo.
        echo You can manage the service using:
        echo - Start: net start MariaDB
        echo - Stop:  net stop MariaDB
        echo - Or use Windows Services manager (services.msc)
    ) else (
        echo Failed to start MariaDB service.
        echo Please check the error message above.
    )
) else (
    echo Failed to install MariaDB service.
    echo Make sure you're running as Administrator.
)

echo.
pause
