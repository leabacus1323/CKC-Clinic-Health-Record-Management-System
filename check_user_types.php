<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$user = DB::table('users')->where('email', 'admin@ckc.edu')->first();
echo "Admin user_type: " . $user->user_type . "\n";

echo "\nAll seeded users:\n";
$users = DB::table('users')->select('email', 'user_type')->get();
foreach ($users as $u) {
    $type = match($u->user_type) {
        0 => 'Admin',
        1 => 'Student',
        2 => 'Staff',
        3 => 'Doctor',
        default => 'Unknown'
    };
    echo "  " . $u->email . " -> user_type: " . $u->user_type . " (" . $type . ")\n";
}
