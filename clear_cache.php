<?php
/**
 * Clear Laravel Caches - Upload to htdocs/ and run once
 * Visit: https://your-domain.infinityfreeapp.com/clear_cache.php
 * DELETE THIS FILE AFTER RUNNING!
 */

echo "<h2>🧹 Clearing Laravel Caches...</h2>";

// Clear config cache
if (file_exists(__DIR__.'/bootstrap/cache/config.php')) {
    unlink(__DIR__.'/bootstrap/cache/config.php');
    echo "✅ Config cache cleared<br>";
} else {
    echo "○ No config cache found<br>";
}

// Clear all possible route cache files
$routeCacheFiles = [
    __DIR__.'/bootstrap/cache/routes-v7.php',
    __DIR__.'/bootstrap/cache/routes.php',
];
$routeCacheCount = 0;
foreach ($routeCacheFiles as $file) {
    if (is_file($file) && unlink($file)) {
        $routeCacheCount++;
    }
}
echo $routeCacheCount > 0
    ? "✅ Route cache cleared ({$routeCacheCount} file(s))<br>"
    : "○ No route cache found<br>";

// Clear view cache
$viewPath = __DIR__.'/storage/framework/views/';
if (is_dir($viewPath)) {
    $files = glob($viewPath.'*');
    $viewCount = 0;
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore' && unlink($file)) {
            $viewCount++;
        }
    }
    echo "✅ View cache cleared ({$viewCount} files)<br>";
}

// Clear application cache
$cachePath = __DIR__.'/storage/framework/cache/data/';
if (is_dir($cachePath)) {
    $count = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cachePath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $file) {
        if ($file->isFile()) {
            unlink($file->getRealPath());
            $count++;
        }
    }
    echo "✅ Application cache cleared ($count files)<br>";
}

// Clear session files
$sessionPath = __DIR__.'/storage/framework/sessions/';
if (is_dir($sessionPath)) {
    $files = glob($sessionPath.'*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "✅ Session cache cleared<br>";
}

echo "<br><h3 style='color: green;'>✅ All caches cleared successfully!</h3>";
echo "<p><strong>⚠️ DELETE THIS FILE NOW FOR SECURITY!</strong></p>";
echo "<p>You can delete it via File Manager or FTP.</p>";
