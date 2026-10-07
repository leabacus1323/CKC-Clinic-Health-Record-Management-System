<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== All appointments grouped by year ===\n";

$years = DB::select("SELECT YEAR(date) as year, COUNT(*) as count FROM appointments WHERE date IS NOT NULL GROUP BY YEAR(date) ORDER BY year DESC");

foreach($years as $y) {
    echo "Year {$y->year}: {$y->count} appointments\n";
}

echo "\n=== All distinct dates ===\n";
$dates = DB::select("SELECT DISTINCT date FROM appointments WHERE date IS NOT NULL ORDER BY date DESC");
foreach($dates as $d) {
    echo $d->date . "\n";
}