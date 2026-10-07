<?php

require 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Create test patient user
$user = User::create([
    'name' => 'Patient Test User',
    'email' => 'patient1@test.com',
    'password' => Hash::make('patient1'),
    'user_type' => 1, // 1 = Student/Patient according to comment
    'f_name' => 'Patient',
    'm_name' => 'Test',
    'l_name' => 'User',
    'dob' => '1990-01-01',
    'address' => '123 Test Street, Test City',
    'gender' => 'Male',
    'contact_no' => '09123456789',
    'email_verified_at' => now()
]);

echo "User created successfully!\n";
echo "ID: " . $user->id . "\n";
echo "Email: " . $user->email . "\n";
echo "Password: patient1\n";
echo "Login URL: http://localhost:8000/login\n";
