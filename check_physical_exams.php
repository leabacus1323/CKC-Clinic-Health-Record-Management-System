<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check table structure
$columns = DB::select("PRAGMA table_info(physical_examinations)");
echo "Physical Examinations Table Structure:\n";
foreach ($columns as $column) {
    echo "- {$column->name} ({$column->type})\n";
}

echo "\n\nPhysical Examinations Records:\n";
$records = DB::table('physical_examinations')->get();
echo "Total records: " . $records->count() . "\n";

if ($records->count() > 0) {
    echo "\nSample record:\n";
    print_r($records->first());
}
