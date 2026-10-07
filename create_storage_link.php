<?php
/**
 * Create Storage Symlink - For InfinityFree hosting
 * Upload to htdocs/ and run once
 * Visit: https://your-domain.infinityfreeapp.com/create_storage_link.php
 * DELETE THIS FILE AFTER RUNNING!
 */

echo "<h2>🔗 Creating Storage Symlink...</h2>";

$target = __DIR__ . '/storage/app/public';
$link = __DIR__ . '/public/storage';

// Check if public folder exists
if (!is_dir(__DIR__ . '/public')) {
    echo "<p style='color: red;'>❌ Error: public/ folder not found!</p>";
    echo "<p>If you moved public contents to htdocs/, change the link path:</p>";
    echo "<p><code>\$link = __DIR__ . '/storage';</code></p>";
    exit;
}

// Remove existing link if it exists
if (file_exists($link)) {
    if (is_link($link)) {
        unlink($link);
        echo "🗑️ Removed existing symlink<br>";
    } else {
        echo "<p style='color: orange;'>⚠️ A file/folder named 'storage' already exists in public/</p>";
        echo "<p>Manual action needed: Delete or rename it first.</p>";
        exit;
    }
}

// Try to create symlink
if (symlink($target, $link)) {
    echo "<p style='color: green; font-size: 20px;'>✅ Storage link created successfully!</p>";
    echo "<p>Symlink: <code>$link</code></p>";
    echo "<p>Points to: <code>$target</code></p>";
} else {
    echo "<p style='color: red;'>❌ Failed to create symlink.</p>";
    echo "<p><strong>Alternative Solution:</strong> Manually copy files from storage/app/public/ to public/storage/</p>";
    echo "<p>Or upload files directly to public/storage/ via File Manager.</p>";
}

echo "<br><p><strong>⚠️ DELETE THIS FILE NOW FOR SECURITY!</strong></p>";
