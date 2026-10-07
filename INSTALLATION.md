# 🚀 Quick Installation Guide - CKC Clinic Health Record Management System

## Choose Your Installation Method

### 🖥️ **Quick Start (Recommended for Testing)**

Perfect for trying out the system on your local machine.

```bash
# 1. Clone the project
git clone https://github.com/Ledeuadian/CTCclinicrecord.git
cd CTCclinicrecord

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
copy .env.example .env          # Windows
cp .env.example .env            # Linux/Mac

# 4. Create database (SQLite)
New-Item database\database.sqlite -ItemType File  # Windows PowerShell
touch database/database.sqlite                     # Linux/Mac

# 5. Configure application
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan laravel-pwa:publish

# 6. Build frontend assets
npm run build

# 7. Start the server
php artisan serve
```

**Access at:** `http://localhost:8000`

---

## 🐳 **Production Setup (Docker)**

Best for production deployment with MySQL database.

```bash
# 1. Clone and configure
git clone https://github.com/Ledeuadian/CTCclinicrecord.git
cd CTCclinicrecord
cp .env.example .env

# 2. Edit .env file - Update database settings:
# DB_CONNECTION=mysql
# DB_HOST=db
# DB_PORT=3306
# DB_DATABASE=ckc_clinic
# DB_USERNAME=ckc_user
# DB_PASSWORD=your_secure_password

# 3. Install and build
composer install
npm install
npm run build
php artisan key:generate

# 4. Start Docker containers
docker-compose up -d --build

# 5. Setup database
docker-compose exec app php artisan migrate
docker-compose exec app php artisan storage:link
docker-compose exec app php artisan laravel-pwa:publish

# Optional: Seed test data
docker-compose exec app php artisan db:seed
```

**Access at:** `http://localhost`

---

## ⚡ Windows Quick Install Script

Save this as `install.ps1` and run in PowerShell:

```powershell
# Install CKC Clinic Health Record Management System
Write-Host "Installing CKC Clinic Health Record Management System..." -ForegroundColor Green

# Clone repository
git clone https://github.com/Ledeuadian/CTCclinicrecord.git
cd CTCclinicrecord

# Install dependencies
Write-Host "Installing PHP dependencies..." -ForegroundColor Yellow
composer install

Write-Host "Installing Node dependencies..." -ForegroundColor Yellow
npm install

# Setup environment
Write-Host "Configuring environment..." -ForegroundColor Yellow
Copy-Item .env.example .env

# Create SQLite database
New-Item -Path "database\database.sqlite" -ItemType File -Force

# Generate key and migrate
Write-Host "Setting up application..." -ForegroundColor Yellow
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan laravel-pwa:publish

# Build assets
Write-Host "Building frontend assets..." -ForegroundColor Yellow
npm run build

Write-Host "Installation complete! Run 'php artisan serve' to start." -ForegroundColor Green
```

Run with: `.\install.ps1`

---

## 🐧 Linux/Mac Quick Install Script

Save this as `install.sh` and run:

```bash
#!/bin/bash

echo "Installing CKC Clinic Health Record Management System..."

# Clone repository
git clone https://github.com/Ledeuadian/CTCclinicrecord.git
cd CTCclinicrecord

# Install dependencies
echo "Installing dependencies..."
composer install
npm install

# Setup environment
echo "Configuring environment..."
cp .env.example .env

# Create SQLite database
touch database/database.sqlite

# Generate key and migrate
echo "Setting up application..."
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan laravel-pwa:publish

# Build assets
echo "Building frontend assets..."
npm run build

# Set permissions
chmod -R 775 storage bootstrap/cache

echo "Installation complete! Run 'php artisan serve' to start."
```

Run with: `chmod +x install.sh && ./install.sh`

---

## ✅ Post-Installation Checklist

- [ ] Application loads at `http://localhost:8000`
- [ ] Login page is accessible
- [ ] Database migrations completed successfully
- [ ] PWA manifest loads (check browser DevTools)
- [ ] No errors in browser console
- [ ] Storage directory is writable
- [ ] Default admin account works (if seeded)

---

## 🔧 Troubleshooting

### "Permission Denied" Error
```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache

# Windows - Run PowerShell as Administrator
```

### Database Connection Error
```bash
# Verify .env settings
# For SQLite: Check database file exists
# For MySQL: Verify credentials and server is running
```

### "Mix Manifest Not Found"
```bash
npm run build
php artisan config:clear
```

### PWA Not Installing
```bash
# Ensure HTTPS (or localhost)
# Check manifest.json loads: http://localhost:8000/manifest.json
# Clear browser cache and service workers
```

---

## 🎯 Next Steps

1. **Change default passwords** in production
2. **Configure email** settings in `.env`
3. **Set up backups** for database
4. **Enable HTTPS** for production (required for PWA)
5. **Customize** clinic logo in `public/ckc.png`
6. **Test PWA** installation on mobile devices

---

## 📚 Additional Resources

- [Full Documentation](README.md)
- [Laravel Documentation](https://laravel.com/docs)
- [PWA Documentation](https://web.dev/progressive-web-apps/)
- [Report Issues](https://github.com/Ledeuadian/CTCclinicrecord/issues)

---

**Need help?** Open an issue on GitHub or check the main README.md
