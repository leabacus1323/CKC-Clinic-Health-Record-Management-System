# 🔄 Database Migration Guide: SQLite to MySQL

## Current Status
✅ Your `.env` is now configured to use the remote MySQL database:
- **Host:** sql100.infinityfree.com
- **Port:** 3306
- **Database:** if0_40397174_XXX (you need to replace XXX with actual database name)
- **Username:** if0_40397174
- **Password:** CHANGE_ME_DB_PASSWORD

⚠️ **IMPORTANT:** Update `DB_DATABASE` in `.env` with the actual database name from InfinityFree.

---

## 📋 Migration Steps

### Method 1: Automated Script (Recommended)

1. **Update database name in `.env`:**
   ```env
   DB_DATABASE=if0_40397174_XXX  # Replace XXX with actual name
   ```

2. **Run the migration script:**
   ```powershell
   .\migrate_to_mysql.ps1
   ```

3. **Follow the prompts** to:
   - Create SQLite backup
   - Test MySQL connection
   - Run migrations on MySQL
   - Choose data migration method

---

### Method 2: Manual Migration (For Advanced Users)

#### Step 1: Backup SQLite Database
```powershell
Copy-Item database\database.sqlite database\database_backup.sqlite
```

#### Step 2: Run Migrations on MySQL
```powershell
php artisan migrate --force
```

#### Step 3: Export SQLite Data

**Option A: Using DB Browser for SQLite**
1. Download: https://sqlitebrowser.org/
2. Open `database\database.sqlite`
3. Go to **File → Export → Database to SQL file**
4. Save as `sqlite_export.sql`
5. Edit the file to make it MySQL compatible:
   - Remove SQLite-specific syntax
   - Change `AUTOINCREMENT` to `AUTO_INCREMENT`
   - Adjust data types if needed

**Option B: Using Laravel Tinker**
```powershell
php artisan tinker
```

Then run for each table:
```php
// Read from SQLite (switch temporarily)
config(['database.default' => 'sqlite']);
$users = DB::table('users')->get();

// Write to MySQL
config(['database.default' => 'mysql']);
DB::table('users')->insert($users->toArray());
```

#### Step 4: Import to MySQL

**Via phpMyAdmin (if available):**
1. Login to your hosting phpMyAdmin
2. Select your database
3. Click **Import**
4. Upload the edited SQL file
5. Click **Go**

**Via Command Line (if SSH available):**
```bash
mysql -h sql100.infinityfree.com -P 3306 -u if0_40397174 -p if0_40397174_XXX < sqlite_export.sql
```

---

## 🔧 Troubleshooting

### MySQL Connection Failed
```powershell
# Test connection
php artisan migrate:status

# Clear config cache
php artisan config:clear

# Check credentials in .env
```

### Remote Server Blocked Your IP
Some hosting providers block direct MySQL access. Solutions:
1. Use **phpMyAdmin** for imports (usually available)
2. Enable **Remote MySQL** in hosting control panel
3. Add your IP to **allowed hosts**

### Data Type Conflicts
SQLite and MySQL have different data types:
- SQLite `INTEGER` → MySQL `INT` or `BIGINT`
- SQLite `TEXT` → MySQL `VARCHAR` or `TEXT`
- SQLite `REAL` → MySQL `DOUBLE` or `DECIMAL`

Laravel migrations handle this automatically, but manual SQL exports may need adjustments.

### Foreign Key Constraints
If you get foreign key errors:
```powershell
# Disable foreign keys temporarily
php artisan tinker
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
# Run your import
DB::statement('SET FOREIGN_KEY_CHECKS=1;');
```

---

## ⚡ Quick Commands

```powershell
# Test MySQL connection
php artisan migrate:status

# Run migrations on MySQL
php artisan migrate --force

# Rollback all migrations (careful!)
php artisan migrate:reset

# Fresh migration (deletes all data!)
php artisan migrate:fresh

# Run seeder
php artisan db:seed --class=SQLiteToMySQLSeeder

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## 🔄 Switching Between SQLite and MySQL

### Switch to MySQL (Production/Hosting):
In `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=sql100.infinityfree.com
DB_PORT=3306
DB_DATABASE=if0_40397174_XXX
DB_USERNAME=if0_40397174
DB_PASSWORD=CHANGE_ME_DB_PASSWORD
```

### Switch to SQLite (Local Development):
In `.env`:
```env
DB_CONNECTION=sqlite
# DB_HOST=sql100.infinityfree.com
# DB_PORT=3306
# DB_DATABASE=if0_40397174_XXX
# DB_USERNAME=if0_40397174
# DB_PASSWORD=CHANGE_ME_DB_PASSWORD
```

Then run:
```powershell
php artisan config:clear
```

---

## 📝 Important Notes

1. **Database Name:** You MUST replace `if0_40397174_XXX` with the actual database name shown in InfinityFree control panel under "MySQL Databases"

2. **Security:** Never commit `.env` to Git with production credentials

3. **Backups:** Always backup before migration:
   ```powershell
   Copy-Item database\database.sqlite database\backups\backup_$(Get-Date -Format 'yyyyMMdd_HHmmss').sqlite
   ```

4. **Testing:** Test the MySQL connection before migrating data

5. **InfinityFree Limitations:**
   - May have connection limits
   - May require remote MySQL to be enabled
   - Check if direct MySQL access is allowed

---

## 🎯 Recommended Workflow

1. ✅ Update `DB_DATABASE` name in `.env`
2. ✅ Run `.\migrate_to_mysql.ps1`
3. ✅ Test connection with `php artisan migrate:status`
4. ✅ Run migrations with `php artisan migrate --force`
5. ✅ Use Option 3 (seeder) to migrate data
6. ✅ Verify data in phpMyAdmin
7. ✅ Test application functionality
8. ✅ Keep SQLite backup file

---

## 🆘 Need Help?

If migration fails:
1. Check InfinityFree control panel for correct database details
2. Verify remote MySQL access is enabled
3. Try using phpMyAdmin for manual import
4. Keep your SQLite backup safe - you can always switch back

---

**Good luck with your migration! 🚀**
