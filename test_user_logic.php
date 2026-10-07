<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Admin;

echo "=== TESTING NEW USER TYPE 2 LOGIC ===\n\n";

echo "1. CURRENT STATE - faculty1@gmail.com:\n";
$existingAdmin = Admin::where('email', 'faculty1@gmail.com')->first();
if ($existingAdmin) {
    echo "   ⚠️  Still in Admins table (created before change)\n";
    echo "   - Email: {$existingAdmin->email}\n";
    echo "   - User Type: {$existingAdmin->user_type}\n";
}

$existingUser = User::where('email', 'faculty1@gmail.com')->first();
if ($existingUser) {
    echo "   ✅ Also found in Users table\n";
    echo "   - Email: {$existingUser->email}\n";
    echo "   - User Type: {$existingUser->user_type}\n";
} else {
    echo "   ❌ NOT in Users table\n";
}

echo "\n2. ALL CURRENT ADMINS:\n";
$allAdmins = Admin::all();
foreach ($allAdmins as $admin) {
    echo "   - {$admin->email} (Type: {$admin->user_type}, ID: {$admin->id})\n";
}

echo "\n3. ALL USERS BY TYPE:\n";
echo "   Type 1 (Students):\n";
$students = User::where('user_type', 1)->get();
foreach ($students as $user) {
    echo "     - {$user->email}\n";
}

echo "   Type 2 (Faculty & Staff):\n";
$faculty = User::where('user_type', 2)->get();
foreach ($faculty as $user) {
    echo "     - {$user->email}\n";
}

echo "   Type 3 (Doctors):\n";
$doctors = User::where('user_type', 3)->get();
foreach ($doctors as $user) {
    echo "     - {$user->email}\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";
echo "💡 Note: faculty1@gmail.com was created before the change,\n";
echo "   so it's still in Admins table. New Type 2 users will\n";
echo "   now go to Users table only.\n";
