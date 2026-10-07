<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Admin;

echo "=== INVESTIGATING faculty1@gmail.com ADMIN ACCESS ===\n\n";

// Check Users table
echo "1. USERS TABLE:\n";
$user = User::where('email', 'faculty1@gmail.com')->first();
if ($user) {
    echo "✅ Found in Users table:\n";
    echo "   - ID: {$user->id}\n";
    echo "   - Email: {$user->email}\n";
    echo "   - User Type: {$user->user_type}\n";
    echo "   - Name: {$user->name}\n";
    echo "   - Created: {$user->created_at}\n";
} else {
    echo "❌ NOT found in Users table\n";
}

echo "\n2. ADMINS TABLE:\n";
$admin = Admin::where('email', 'faculty1@gmail.com')->first();
if ($admin) {
    echo "🚨 FOUND IN ADMINS TABLE! (This explains admin access)\n";
    echo "   - ID: {$admin->id}\n";
    echo "   - Email: {$admin->email}\n";
    echo "   - User Type: {$admin->user_type}\n";
    echo "   - Name: {$admin->name}\n";
    echo "   - Created: {$admin->created_at}\n";
} else {
    echo "❌ NOT found in Admins table\n";
}

echo "\n3. ALL CURRENT ADMINS:\n";
$allAdmins = Admin::all();
foreach ($allAdmins as $admin) {
    echo "   - {$admin->email} (Type: {$admin->user_type}, ID: {$admin->id})\n";
}

echo "\n4. ALL USERS WITH TYPE 2:\n";
$facultyUsers = User::where('user_type', 2)->get();
foreach ($facultyUsers as $user) {
    echo "   - {$user->email} (Type: {$user->user_type}, ID: {$user->id})\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";
