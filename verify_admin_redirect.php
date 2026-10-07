<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

// Check if user exists
$user = DB::table('users')->where('email', 'admin@ckc.edu')->first();

if (!$user) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

echo "✓ Admin user found:\n";
echo "  - ID: " . $user->id . "\n";
echo "  - Email: " . $user->email . "\n";
echo "  - User Type: " . $user->user_type . "\n";
echo "  - Name: " . $user->name . "\n";

if ($user->user_type == 0) {
    echo "\n✓ User type is 0 (Admin) - should redirect to /admin/dashboard\n";
} else {
    echo "\n❌ User type is NOT 0 - this is the problem!\n";
    echo "   Current: " . $user->user_type . "\n";
    echo "   Expected: 0\n";
    echo "\n   Updating user_type to 0...\n";
    DB::table('users')->where('email', 'admin@ckc.edu')->update(['user_type' => 0]);
    echo "   ✓ Updated! Please try logging in again.\n";
}
