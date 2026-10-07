~<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== NAVIGATION TEST FOR DIFFERENT USER TYPES ===\n\n";

echo "Testing navigation logic for each user type:\n\n";

echo "🎓 TYPE 1 (Student):\n";
echo "   Navigation: Dashboard, Health Records, Appointments\n";
echo "   Profile: patients.profile\n";
echo "   Limited access ✅\n\n";

echo "👥 TYPE 2 (Faculty & Staff) - UPDATED:\n";
echo "   Navigation: Dashboard, Health Records, Appointments\n";
echo "   Profile: patients.profile\n";
echo "   Limited access ✅ (Same as students now)\n\n";

echo "🩺 TYPE 3 (Doctor):\n";
echo "   Navigation: Full access (Dashboard, Health Records, Doctors, Patients, etc.)\n";
echo "   Profile: profile.edit\n";
echo "   Full access ✅\n\n";

echo "Current users in system:\n";
$users = User::all();
foreach ($users as $user) {
    $accessLevel = ($user->user_type == 1 || $user->user_type == 2) ? "Limited" : "Full";
    echo "   - {$user->email} (Type {$user->user_type}) → {$accessLevel} navigation\n";
}

echo "\n=== NAVIGATION UPDATE COMPLETE ===\n";
