<?php

require 'vendor/autoload.php';

use App\Models\User;
use App\Models\Admin;

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== USERS TABLE ===\n";
$users = User::select('id', 'name', 'email', 'user_type')->get();
foreach($users as $user) {
    $userTypeText = match($user->user_type) {
        1 => 'Student',
        2 => 'Faculty & Staff',
        3 => 'Doctor',
        default => 'Unknown'
    };
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, User Type: {$user->user_type} ({$userTypeText})\n";
}

echo "\n=== ADMINS TABLE ===\n";
$admins = Admin::select('id', 'name', 'email', 'user_type')->get();
foreach($admins as $admin) {
    $userTypeText = match($admin->user_type) {
        1 => 'Student Admin',
        2 => 'Faculty & Staff Admin',
        default => 'Unknown Admin'
    };
    echo "ID: {$admin->id}, Name: {$admin->name}, Email: {$admin->email}, User Type: {$admin->user_type} ({$userTypeText})\n";
}

echo "\n=== DOCTORS (from users with user_type=3) ===\n";
$doctors = User::where('user_type', 3)->with('doctors')->get();
foreach($doctors as $doctor) {
    $specialization = $doctor->doctors ? $doctor->doctors->specialization : 'No specialization set';
    echo "ID: {$doctor->id}, Name: {$doctor->name}, Email: {$doctor->email}, Specialization: {$specialization}\n";
}
