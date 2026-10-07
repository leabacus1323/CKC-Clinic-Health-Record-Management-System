<?php
/**
 * Deployment Checker - Verify your InfinityFree setup
 * Upload to htdocs/ and visit to check configuration
 * Visit: https://your-domain.infinityfreeapp.com/check_deployment.php
 * DELETE THIS FILE AFTER CHECKING!
 */

echo "<!DOCTYPE html><html><head><title>Deployment Check</title>";
echo "<style>body{font-family:Arial;padding:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;}</style>";
echo "</head><body>";
echo "<h1>🔍 CKC Clinic Deployment Checker</h1>";

$errors = 0;
$warnings = 0;

// Check 1: .env file exists
echo "<h3>1. Environment File (.env)</h3>";
if (file_exists(__DIR__.'/.env')) {
    echo "<p class='ok'>✅ .env file exists</p>";

    // Check .env contents
    $env = file_get_contents(__DIR__.'/.env');

    if (strpos($env, 'APP_ENV=production') !== false) {
        echo "<p class='ok'>✅ APP_ENV is set to production</p>";
    } else {
        echo "<p class='error'>❌ APP_ENV should be 'production'</p>";
        $errors++;
    }

    if (strpos($env, 'APP_DEBUG=false') !== false) {
        echo "<p class='ok'>✅ APP_DEBUG is false (secure)</p>";
    } else {
        echo "<p class='error'>❌ APP_DEBUG should be 'false' in production!</p>";
        $errors++;
    }

    if (strpos($env, 'DB_CONNECTION=mysql') !== false) {
        echo "<p class='ok'>✅ Database is set to MySQL</p>";
    } else {
        echo "<p class='error'>❌ DB_CONNECTION should be 'mysql'</p>";
        $errors++;
    }

    if (strpos($env, 'APP_KEY=base64:') !== false) {
        echo "<p class='ok'>✅ APP_KEY is set</p>";
    } else {
        echo "<p class='error'>❌ APP_KEY is missing!</p>";
        $errors++;
    }
} else {
    echo "<p class='error'>❌ .env file not found!</p>";
    $errors++;
}

// Check 2: Vendor folder
echo "<h3>2. Composer Dependencies</h3>";
if (is_dir(__DIR__.'/vendor')) {
    echo "<p class='ok'>✅ vendor/ folder exists</p>";
    if (file_exists(__DIR__.'/vendor/autoload.php')) {
        echo "<p class='ok'>✅ Composer autoload found</p>";
    } else {
        echo "<p class='error'>❌ vendor/autoload.php missing</p>";
        $errors++;
    }
} else {
    echo "<p class='error'>❌ vendor/ folder not found! Run: composer install --no-dev</p>";
    $errors++;
}

// Check 3: Storage permissions
echo "<h3>3. Directory Permissions</h3>";
if (is_writable(__DIR__.'/storage')) {
    echo "<p class='ok'>✅ storage/ is writable</p>";
} else {
    echo "<p class='error'>❌ storage/ is not writable! Set to 755</p>";
    $errors++;
}

if (is_dir(__DIR__.'/bootstrap/cache') && is_writable(__DIR__.'/bootstrap/cache')) {
    echo "<p class='ok'>✅ bootstrap/cache/ is writable</p>";
} else {
    echo "<p class='error'>❌ bootstrap/cache/ is not writable! Set to 755</p>";
    $errors++;
}

// Check 4: Storage directories exist
echo "<h3>4. Storage Structure</h3>";
$requiredDirs = [
    '/storage/framework/cache',
    '/storage/framework/sessions',
    '/storage/framework/views',
    '/storage/logs',
    '/storage/app/public',
];

foreach ($requiredDirs as $dir) {
    if (is_dir(__DIR__.$dir)) {
        echo "<p class='ok'>✅ $dir exists</p>";
    } else {
        echo "<p class='warning'>⚠️ $dir missing (will be created automatically)</p>";
        $warnings++;
    }
}

// Check 5: Public folder setup
echo "<h3>5. Public Folder Configuration</h3>";
if (file_exists(__DIR__.'/index.php')) {
    echo "<p class='ok'>✅ index.php found in root (Option A setup)</p>";
} elseif (file_exists(__DIR__.'/public/index.php')) {
    echo "<p class='ok'>✅ index.php found in public/ (Option B setup)</p>";
} else {
    echo "<p class='error'>❌ index.php not found!</p>";
    $errors++;
}

if (file_exists(__DIR__.'/.htaccess')) {
    echo "<p class='ok'>✅ .htaccess file exists</p>";
} else {
    echo "<p class='warning'>⚠️ .htaccess file not found</p>";
    $warnings++;
}

// Check 6: Database connection
echo "<h3>6. Database Connection</h3>";
try {
    if (file_exists(__DIR__.'/vendor/autoload.php')) {
        require __DIR__.'/vendor/autoload.php';

        $env = parse_ini_file(__DIR__.'/.env');
        $host = $env['DB_HOST'] ?? 'unknown';
        $database = $env['DB_DATABASE'] ?? 'unknown';
        $username = $env['DB_USERNAME'] ?? 'unknown';
        $password = $env['DB_PASSWORD'] ?? '';

        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
        echo "<p class='ok'>✅ Database connection successful!</p>";
        echo "<p>Connected to: $database on $host</p>";

        // Check if tables exist
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<p class='ok'>✅ Found " . count($tables) . " tables in database</p>";

    } else {
        echo "<p class='warning'>⚠️ Cannot test (vendor/ missing)</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    $errors++;
}

// Check 7: PWA files
echo "<h3>7. PWA Configuration</h3>";
$pwaFile = file_exists(__DIR__.'/manifest.json') ? __DIR__.'/manifest.json' : __DIR__.'/public/manifest.json';
if (file_exists($pwaFile)) {
    echo "<p class='ok'>✅ manifest.json found</p>";
} else {
    echo "<p class='warning'>⚠️ manifest.json not found</p>";
    $warnings++;
}

$swFile = file_exists(__DIR__.'/sw.js') ? __DIR__.'/sw.js' : __DIR__.'/public/sw.js';
if (file_exists($swFile)) {
    echo "<p class='ok'>✅ sw.js (service worker) found</p>";
} else {
    echo "<p class='warning'>⚠️ sw.js not found</p>";
    $warnings++;
}

// Summary
echo "<hr>";
echo "<h2>📊 Summary</h2>";
if ($errors === 0 && $warnings === 0) {
    echo "<h3 style='color:green;'>🎉 Perfect! Your deployment looks good!</h3>";
    echo "<p>You can now delete this file and test your application.</p>";
} elseif ($errors === 0) {
    echo "<h3 style='color:orange;'>⚠️ Deployment is OK with $warnings warning(s)</h3>";
    echo "<p>Your app should work, but check the warnings above.</p>";
} else {
    echo "<h3 style='color:red;'>❌ Found $errors critical error(s) and $warnings warning(s)</h3>";
    echo "<p>Fix the errors above before proceeding.</p>";
}

echo "<br><p><strong>⚠️ DELETE THIS FILE AFTER CHECKING FOR SECURITY!</strong></p>";
echo "</body></html>";
