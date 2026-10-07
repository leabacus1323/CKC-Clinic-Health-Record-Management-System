<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING SQLITE DATE FUNCTIONS ===\n\n";

try {
    // Test the fixed monthly stats query
    echo "🔍 Testing monthly stats query...\n";
    $doctor = App\Models\Doctors::first();
    if ($doctor) {
        $monthlyStats = App\Models\Appointment::where('doc_id', $doctor->id)
            ->selectRaw('strftime("%m", date) as month, COUNT(*) as count')
            ->whereYear('date', \Carbon\Carbon::now()->year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        echo "✅ Monthly stats query executed successfully!\n";
        echo "   Found data for " . count($monthlyStats) . " months\n";
    }

    // Test the fixed monthly trends query
    echo "\n🔍 Testing monthly trends query...\n";
    if ($doctor) {
        $monthlyTrends = App\Models\Appointment::where('doc_id', $doctor->id)
            ->selectRaw('strftime("%m", date) as month, strftime("%Y", date) as year, COUNT(*) as count')
            ->whereYear('date', '>=', \Carbon\Carbon::now()->subYear()->year)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        echo "✅ Monthly trends query executed successfully!\n";
        echo "   Found " . $monthlyTrends->count() . " trend records\n";
    }

    echo "\n✅ All SQLite date function queries are working!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
