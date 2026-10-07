<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = \Illuminate\Http\Request::capture());

// Check if staff user exists
$staff = \App\Models\User::where('user_type', 2)->first();
if ($staff) {
    echo "Staff user found: " . $staff->email . "\n";
} else {
    echo "No staff user found. Creating one...\n";
    $staff = \App\Models\User::create([
        'name' => 'Test Staff',
        'email' => 'staff@test.com',
        'password' => bcrypt('password123'),
        'user_type' => 2,
        'f_name' => 'Test',
        'l_name' => 'Staff',
        'dob' => '1990-01-01',
        'gender' => 'M',
        'address' => 'Test Address',
        'contact_no' => '0000000000'
    ]);
    echo "Staff user created: " . $staff->email . "\n";
}
echo "Use this email to login: " . $staff->email . "\n";
echo "Password: password123\n";
