# 🚀 Deployment Guide to InfinityFree Hosting

## ✅ What We've Done

1. ✅ Switched back to SQLite for local development
2. ✅ Created MySQL-compatible export file: `mysql_import.sql`
3. ✅ Exported 23 records from your local database

---

## 📋 Step-by-Step Deployment

### **Step 1: Prepare Files for Upload**

Create a ZIP of your project (exclude these folders to reduce size):
```powershell
# Folders to EXCLUDE from upload:
# - node_modules/
# - vendor/
# - database/database.sqlite
# - .git/
# - storage/logs/*
```

**Files you MUST include:**
- All PHP files (app/, config/, routes/, etc.)
- `.env` file (edit for production - see below)
- `composer.json` and `composer.lock`
- `public/` folder (this will be your web root)

---

### **Step 2: Upload to InfinityFree**

1. **Login to InfinityFree Control Panel**
2. **Open File Manager** or use FTP
3. **Upload to `htdocs/` folder**
4. **Extract the files**

---

### **Step 3: Configure Production .env**

On your hosting, edit `.env`:

```env
APP_NAME="CKC Clinic Health Record Management System"
APP_ENV=production
APP_KEY=base64:GENERATE_A_NEW_KEY_ON_THE_SERVER
APP_DEBUG=false  # IMPORTANT: Set to false in production!
APP_URL=https://your-domain.infinityfreeapp.com

# MySQL Database
DB_CONNECTION=mysql
DB_HOST=sql100.infinityfree.com
DB_PORT=3306
DB_DATABASE=if0_40397174_ctcclinic
DB_USERNAME=if0_40397174
DB_PASSWORD=CHANGE_ME_DB_PASSWORD

# Session & Cache
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync

# Mail (configure if needed)
MAIL_MAILER=log
```

---

### **Step 4: Install Composer Dependencies**

**Option A: Via SSH (if available)**
```bash
cd htdocs
composer install --no-dev --optimize-autoloader
```

**Option B: Upload vendor folder**
If no SSH access:
1. On your local machine: `composer install --no-dev`
2. ZIP the `vendor/` folder
3. Upload and extract to hosting

---

### **Step 5: Set Up Database**

1. **Login to phpMyAdmin** from InfinityFree control panel

2. **Select your database:** `if0_40397174_ctcclinic`

3. **Run migrations to create tables:**
   
   **If you have SSH access:**
   ```bash
   php artisan migrate --force
   ```
   
   **If NO SSH access:**
   - You'll need to manually create tables using SQL
   - Or use a migration SQL export

4. **Import your data:**
   - Click **Import** tab in phpMyAdmin
   - Choose file: `mysql_import.sql` (from your local project)
   - Click **Go**
   - Wait for import to complete

---

### **Step 6: Configure Web Root**

InfinityFree requires the `public/` folder to be your web root.

**Option A: Move files (Recommended)**
```
htdocs/
  ├── public/  (this becomes your web root)
  │   └── index.php
  ├── app/
  ├── config/
  └── ... (all other Laravel folders)
```

Then update `public/index.php` paths:
```php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

**Option B: Use .htaccess redirect**
In `htdocs/.htaccess`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

### **Step 7: Set Permissions**

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

Or via File Manager:
- Right-click → Permissions
- Set `storage/` and `bootstrap/cache/` to **755**

---

### **Step 8: Create Storage Link**

**If you have SSH:**
```bash
php artisan storage:link
```

**If NO SSH:**
Manually create a symlink or copy files from `storage/app/public/` to `public/storage/`

---

### **Step 9: Clear Caches**

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

### **Step 10: Test Your Site**

Visit your site: `https://your-domain.infinityfreeapp.com`

**Test these:**
- ✅ Home page loads
- ✅ Login works
- ✅ Database connection works
- ✅ Can create appointments
- ✅ Can view patient records

---

## ⚠️ Important Security Notes

### **Production .env Settings:**
```env
APP_ENV=production
APP_DEBUG=false  # Never true in production!
```

### **Hide .env file:**
Add to `public/.htaccess`:
```apache
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

---

## 🔧 Troubleshooting

### **500 Internal Server Error**
1. Check file permissions (755 for directories, 644 for files)
2. Check `.env` is configured correctly
3. Check `storage/` and `bootstrap/cache/` are writable
4. Enable debug temporarily: `APP_DEBUG=true` (then check errors)

### **Database Connection Failed**
1. Verify database credentials in `.env`
2. Check database exists in InfinityFree panel
3. Verify MySQL is using correct host: `sql100.infinityfree.com`

### **Routes Not Working (404)**
1. Check `.htaccess` exists in `public/` folder
2. Ensure mod_rewrite is enabled
3. Clear route cache: `php artisan route:clear`

### **Storage/Logs Not Writable**
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

## 📝 Quick Checklist

Before going live:

- [ ] `APP_ENV=production` in `.env`
- [ ] `APP_DEBUG=false` in `.env`
- [ ] Database credentials correct
- [ ] Composer dependencies installed
- [ ] Migrations run (tables created)
- [ ] Data imported via `mysql_import.sql`
- [ ] Storage permissions set (755/775)
- [ ] Storage link created
- [ ] Caches cleared
- [ ] `.htaccess` configured
- [ ] Test login works
- [ ] Test database operations work
- [ ] PWA manifest loads correctly

---

## 🎯 Alternative: Use Git Deploy

If InfinityFree supports Git:
```bash
git clone https://github.com/Ledeuadian/CTCclinicrecord.git
cd CTCclinicrecord
composer install --no-dev
php artisan migrate --force
```

---

## 📞 Support

**InfinityFree Limitations:**
- No SSH access (on free plan)
- Limited PHP execution time
- No background jobs/queues
- Hit limits on requests

**Consider upgrading to paid hosting for:**
- SSH access
- Better performance
- SSL certificates
- No ads
- Better support

---

**Your local system is now back to SQLite. The MySQL export file is ready for upload! 🚀**
