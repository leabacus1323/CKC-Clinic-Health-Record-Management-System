<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Appointment dates by month/year ===\n";

$appointments = DB::select("
    SELECT DISTINCT DATE_FORMAT(date, '%Y-%m') as month_year, COUNT(*) as count 
    FROM appointments 
    WHERE date IS NOT NULL 
    GROUP BY DATE_FORMAT(date, '%Y-%m') 
    ORDER BY month_year DESC 
    LIMIT 12
");

foreach($appointments as $a) {
    echo $a->month_year . ': ' . $a->count . " appointments\n";
}

echo "\n=== Current monthlyStats query (staff dashboard) ===\n";
$monthlyStats = \App\Models\Appointment::selectRaw('DATE_FORMAT(date, "%m") as month, COUNT(*) as count')
    ->whereYear('date', \Carbon\Carbon::now()->year)
    ->groupBy('month')
    ->orderBy('month')
    ->get();

foreach ($monthlyStats as $stat) {
    echo 'Month ' . $stat->month . ' (' . date('M', mktime(0, 0, 0, $stat->month, 1)) . '): ' . $stat->count . " appointments\n";
}
