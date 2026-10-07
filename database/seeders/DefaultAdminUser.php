<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class DefaultAdminUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'name'  =>  'Administrator',
            'email' =>  'lyjieme@gmail.com',
            'password'  =>  Hash::make('Admin_2k24'),
            'name' => 'Admin',
            'user_type' => '2',
            'f_name' => 'Admin',
            'm_name' => 'Admin',
            'l_name' => 'Admin',
            'dob' => now(),
            'address' => 'Barangay 24 - A, Purok 5, Gingoog City',
            'gender' => 'M',
            'contact_no' => '09363991178',
        ]);
    }
}
