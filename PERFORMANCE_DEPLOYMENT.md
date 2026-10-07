# CKC Clinic Health Record Management System — Performance Upgrade

## Production deployment

Create a fresh private `.env` on the server, then run:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize
```

Required PHP extensions should include `mbstring`, `dom`/`xml`, `pdo_mysql`, `openssl`, `tokenizer`, `ctype`, and `fileinfo`.

Recommended production values:

```dotenv
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error
CACHE_STORE=file
SESSION_DRIVER=file
PERFORMANCE_NAVIGATION_CACHE_TTL=30
PERFORMANCE_ANNOUNCEMENT_CACHE_TTL=300
PERFORMANCE_DASHBOARD_CACHE_TTL=60
```

Redis is preferable for cache and sessions when the hosting plan supports it. The included implementation remains compatible with Laravel's file cache on shared hosting.

## Changes applied

- Replaced full-table loading with server-side search and 25-row pagination for admin patients, users, and doctors.
- Changed patient CSV export to a streamed cursor export so PHP does not build the entire file in memory.
- Consolidated and briefly cached admin, doctor, and staff dashboard aggregates.
- Removed unused full-table medicine and patient queries from AJAX placeholder tabs.
- Moved navigation notification queries out of Blade and grouped counts by status.
- Limited eager-loaded columns on recent-appointment and doctor listing queries.
- Added composite indexes for frequent joins, filters, status/date lookups, and sorting.
- Scoped Chart.js loading to doctor/staff dashboard shell routes.
- Converted the 2.2 MB login background to a 325 KB WebP asset.
- Reduced the shared logo from 1200×1200 to 512×512 and removed its duplicate copy.
- Reworked the service worker to cache only static assets; authenticated clinic pages and application data are never stored by it.
- Added Apache text compression when `mod_deflate` is available.
- Added Composer shortcuts: `composer optimize` and `composer optimize-clear`.

## Validation completed

- 160 core PHP files passed `php -l` syntax validation.
- All six modified Blade templates compiled and their generated PHP passed syntax validation.
- Laravel loaded all 301 application routes successfully.
- The Vite production build completed successfully.
- `composer.json` passed Composer validation. The existing exact `maatwebsite/excel` version constraint produced a non-blocking warning.

Database migrations and browser workflows were not executed against the uploaded `.env` because it appeared to reference a real database. Apply the migration first on a backup or staging database and verify the primary admin, doctor, staff, and patient workflows before production deployment.

## Security action required

The uploaded archive contained multiple environment files with live-looking credentials. They are excluded from the optimized package. Rotate the database password and any reused application, mail, or API secrets before deployment.

The package also excludes dependency folders, Git history, IDE metadata, runtime logs/caches, database backup exports, and credential helper files. Recreate dependencies through Composer and npm using the lock files.

## Existing dependency audit

The installed JavaScript dependency tree reported existing audit findings. Automatic major upgrades were not applied because they could break the current Laravel/Vite frontend. Review and update dependencies separately on a test branch.
