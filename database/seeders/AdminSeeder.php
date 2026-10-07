<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user for admin login
        Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@ckc.edu',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
            'user_type' => 0, // Admin
            'f_name' => 'Admin',
            'm_name' => 'System',
            'l_name' => 'User',
            'dob' => '1990-01-01',
            'address' => 'CKC Campus, Building A',
            'gender' => 'M',
            'contact_no' => '09123456789',
        ]);
    }
}
