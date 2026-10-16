# 🚀 InfinityFree Deployment Checklist

## ✅ What You've Done:
- ✓ Database imported successfully
- ✓ Files uploaded to htdocs/

## 📋 Configuration Steps:

### Step 1: Fix .env File (CRITICAL!)

In File Manager, edit `htdocs/.env`:

```env
APP_NAME="CKC Clinic Health Record Management System"
APP_ENV=production
APP_KEY=base64:GENERATE_A_NEW_KEY_ON_THE_SERVER
APP_DEBUG=false
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
SESSION_LIFETIME=120
CACHE_STORE=file
QUEUE_CONNECTION=sync

# Disable Broadcasting
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local

# Mail: use a real SMTP provider; "log" does not deliver email
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-verified-sender-address
MAIL_FROM_NAME="${APP_NAME}"
```

**IMPORTANT Changes:**
- `APP_ENV=production` (not local)
- `APP_DEBUG=false` (NEVER true in production!)
- `APP_URL=` your actual HTTPS domain (not localhost)
- `DB_CONNECTION=mysql` (not sqlite)
- `MAIL_MAILER=smtp` with credentials from a real email provider. `MAIL_MAILER=log`
  only writes messages to the server log; it never sends them to the user's inbox.

Use a provider and SMTP settings supported by your hosting plan. InfinityFree may
restrict outbound SMTP connections, so confirm the host and port are reachable
from the account before relying on them. For Gmail, use a Google App Password,
not the account password. Keep production credentials only in the server's
private `.env`; do not upload your local `.env`.

---

### Step 2: Configure Web Root

InfinityFree serves from `htdocs/`, but Laravel needs to serve from `public/`.

**Option A: Move public contents (Recommended)**

1. Move everything from `htdocs/public/` to `htdocs/`
2. Delete the empty `htdocs/public/` folder
3. Edit `htdocs/index.php`:

Change:
```php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

To:
```php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
```

**Option B: Use .htaccess redirect**

Keep files as-is and create `htdocs/.htaccess`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

### Step 3: Install Composer Dependencies

InfinityFree doesn't have SSH on free plan, so:

**On your local machine:**
```powershell
composer install --no-dev --optimize-autoloader
```

Then **ZIP and upload** the `vendor/` folder to `htdocs/vendor/`

---

### Step 4: Set Permissions

In File Manager:
- Right-click `storage/` → Permissions → `755` (recursive)
- Right-click `bootstrap/cache/` → Permissions → `755` (recursive)

---

### Step 5: Create Storage Link

Create a file: `htdocs/create_storage_link.php`

```php
<?php
// Create storage symlink for InfinityFree
$target = __DIR__ . '/storage/app/public';
$link = __DIR__ . '/public/storage';

if (file_exists($link)) {
    echo "Storage link already exists!";
} else {
    if (symlink($target, $link)) {
        echo "Storage link created successfully!";
    } else {
        echo "Failed to create storage link. Manually copy files instead.";
    }
}
```

Visit: `https://your-domain.infinityfreeapp.com/create_storage_link.php`

Then **delete** this file after running.

---

### Step 6: Protect Sensitive Files

Create/edit `htdocs/.htaccess`:

```apache
# Laravel Rewrite Rules
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect to public folder if using Option B
    # RewriteRule ^(.*)$ public/$1 [L]
    
    # Or if using Option A (public contents moved to htdocs)
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Protect .env file
<Files .env>
    Order allow,deny
    Deny from all
</Files>

# Disable directory browsing
Options -Indexes

# Protect sensitive directories
RedirectMatch 403 ^/\.git
RedirectMatch 403 ^/storage/
RedirectMatch 403 ^/bootstrap/cache/
```

---

### Step 7: Clear Caches (Important!)

After changing `.env`, remove `bootstrap/cache/config.php` in File Manager if it
exists so Laravel reloads the production settings. Do not remove other files
from `bootstrap/cache/`. If you use the cache-clearing script below, delete that
script from the public site as soon as it finishes.

Create a file: `htdocs/clear_cache.php`

```php
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

// Clear all caches
echo "Clearing caches...<br>";

// Config cache
if (file_exists(__DIR__.'/bootstrap/cache/config.php')) {
    unlink(__DIR__.'/bootstrap/cache/config.php');
    echo "✓ Config cache cleared<br>";
}

// Route cache
if (file_exists(__DIR__.'/bootstrap/cache/routes-v7.php')) {
    unlink(__DIR__.'/bootstrap/cache/routes-v7.php');
    echo "✓ Route cache cleared<br>";
}

// View cache
array_map('unlink', glob(__DIR__.'/storage/framework/views/*'));
echo "✓ View cache cleared<br>";

// Application cache
array_map('unlink', glob(__DIR__.'/storage/framework/cache/data/*/*'));
echo "✓ Application cache cleared<br>";

echo "<br>All caches cleared! Delete this file now.";
```

Visit: `https://your-domain.infinityfreeapp.com/clear_cache.php`

Then **delete** this file.

---

### Step 8: Test Your Site

Visit: `https://your-domain.infinityfreeapp.com`

**Test checklist:**
- [ ] Homepage loads without errors
- [ ] Login page works
- [ ] Can login with admin account
- [ ] Dashboard loads
- [ ] Database queries work
- [ ] No 500 errors
- [ ] PWA manifest loads: `/manifest.json`

---

## 🔧 Troubleshooting

### 500 Internal Server Error

1. **Check permissions:**
   - `storage/` = 755
   - `bootstrap/cache/` = 755

2. **Check .env file:**
   - Must be in `htdocs/.env`
   - `APP_DEBUG=false`
   - Database credentials correct

3. **Enable debug temporarily:**
   - Edit `.env`: `APP_DEBUG=true`
   - Visit site and check error
   - **Set back to false** after fixing!

### Database Connection Failed

1. Verify in `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=sql100.infinityfree.com
   DB_DATABASE=if0_40397174_ctcclinic
   ```

2. Check InfinityFree control panel for correct database name

### Routes Not Working (404)

1. Make sure `.htaccess` exists in correct location
2. Verify mod_rewrite is enabled (usually is)
3. Check index.php paths are correct

### "No Application Encryption Key"

Run this in `htdocs/`:
```php
<?php
echo 'base64:'.base64_encode(random_bytes(32));
```
Copy the output to `.env` as `APP_KEY=`

---

## 📱 PWA on Production

After deployment:
1. Your app MUST use HTTPS (InfinityFree provides this)
2. Visit site on mobile browser
3. You should see "Add to Home Screen" prompt
4. Install and test!

---

## ⚠️ InfinityFree Limitations

Free hosting has:
- ❌ No SSH access
- ❌ No command line (artisan commands)
- ❌ Limited PHP execution time
- ❌ Daily hit limits
- ❌ Ads may appear
- ✅ But it's free and good for testing!

---

## 🎯 Quick File Structure

After setup, your `htdocs/` should look like:

**Option A (Recommended):**
```
htdocs/
├── index.php (from public/)
├── manifest.json (from public/)
├── .htaccess (modified)
├── .env (configured)
├── app/
├── bootstrap/
├── config/
├── database/
├── routes/
├── storage/ (755 permissions)
├── vendor/
└── ... other Laravel folders
```

**Option B (Alternative):**
```
htdocs/
├── .htaccess (redirects to public/)
├── .env
├── app/
├── public/
│   └── index.php
├── storage/ (755 permissions)
└── vendor/
```

---

## ✅ Final Checklist

Before going live:
- [ ] `.env` configured for production
- [ ] `APP_DEBUG=false`
- [ ] Database credentials correct
- [ ] `vendor/` folder uploaded
- [ ] Permissions set (755)
- [ ] Storage link created
- [ ] Caches cleared
- [ ] `.htaccess` configured
- [ ] Sensitive files protected
- [ ] Test all functionality
- [ ] PWA manifest loads

---

**Your site should now be live! 🎉**

Visit: `https://your-domain.infinityfreeapp.com`
