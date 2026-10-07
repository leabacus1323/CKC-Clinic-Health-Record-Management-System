<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed reusable demonstration accounts for every supported user role.
     *
     * Default password for all seeded accounts: password123
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@ckc.edu',
                'user_type' => User::TYPE_ADMIN,
                'f_name' => 'Admin',
                'm_name' => 'System',
                'l_name' => 'User',
                'dob' => '1990-01-01',
                'address' => 'CKC Campus, Building A',
                'gender' => 'M',
                'contact_no' => '09123456789',
            ],
            [
                'name' => 'Dr. John Smith',
                'email' => 'doctor1@ckc.edu',
                'user_type' => User::TYPE_DOCTOR,
                'f_name' => 'John',
                'm_name' => 'Medical',
                'l_name' => 'Smith',
                'dob' => '1985-05-15',
                'address' => 'CKC Medical Center',
                'gender' => 'M',
                'contact_no' => '09123456790',
            ],
            [
                'name' => 'Dr. Maria Santos',
                'email' => 'doctor2@ckc.edu',
                'user_type' => User::TYPE_DOCTOR,
                'f_name' => 'Maria',
                'm_name' => 'Rosa',
                'l_name' => 'Santos',
                'dob' => '1988-03-20',
                'address' => 'CKC Medical Center',
                'gender' => 'F',
                'contact_no' => '09223456790',
            ],
            [
                'name' => 'Jane Staff',
                'email' => 'staff@ckc.edu',
                'user_type' => User::TYPE_STAFF,
                'f_name' => 'Jane',
                'm_name' => 'Office',
                'l_name' => 'Staff',
                'dob' => '1992-08-20',
                'address' => 'CKC Administration',
                'gender' => 'F',
                'contact_no' => '09123456791',
            ],
            [
                'name' => 'John Student',
                'email' => 'student1@ckc.edu',
                'user_type' => User::TYPE_STUDENT,
                'f_name' => 'John',
                'm_name' => 'Academic',
                'l_name' => 'Student',
                'dob' => '2000-12-10',
                'address' => 'CKC Dormitory B',
                'gender' => 'M',
                'contact_no' => '09123456792',
            ],
            [
                'name' => 'Maria Garcia',
                'email' => 'student2@ckc.edu',
                'user_type' => User::TYPE_STUDENT,
                'f_name' => 'Maria',
                'm_name' => 'Elena',
                'l_name' => 'Garcia',
                'dob' => '2001-06-15',
                'address' => 'CKC Dormitory C',
                'gender' => 'F',
                'contact_no' => '09223456792',
            ],
            [
                'name' => 'Pedro Cruz',
                'email' => 'student3@ckc.edu',
                'user_type' => User::TYPE_STUDENT,
                'f_name' => 'Pedro',
                'm_name' => 'Antonio',
                'l_name' => 'Cruz',
                'dob' => '2000-09-22',
                'address' => 'CKC Dormitory A',
                'gender' => 'M',
                'contact_no' => '09323456792',
            ],
            [
                'name' => 'Ana Lopez',
                'email' => 'student4@ckc.edu',
                'user_type' => User::TYPE_STUDENT,
                'f_name' => 'Ana',
                'm_name' => 'Marie',
                'l_name' => 'Lopez',
                'dob' => '2001-02-18',
                'address' => 'CKC Dormitory B',
                'gender' => 'F',
                'contact_no' => '09423456792',
            ],
            [
                'name' => 'Carlos Reyes',
                'email' => 'student5@ckc.edu',
                'user_type' => User::TYPE_STUDENT,
                'f_name' => 'Carlos',
                'm_name' => 'David',
                'l_name' => 'Reyes',
                'dob' => '2000-11-30',
                'address' => 'CKC Dormitory C',
                'gender' => 'M',
                'contact_no' => '09523456792',
            ],
            [
                'name' => 'Prof. Angela Rivera',
                'email' => 'faculty@ckc.edu',
                'user_type' => User::TYPE_FACULTY,
                'f_name' => 'Angela',
                'm_name' => 'Mae',
                'l_name' => 'Rivera',
                'dob' => '1987-04-12',
                'address' => 'CKC Faculty Office',
                'gender' => 'F',
                'contact_no' => '09623456792',
            ],
            [
                'name' => 'Mr. Daniel Mendoza',
                'email' => 'teacher@ckc.edu',
                'user_type' => User::TYPE_TEACHER,
                'f_name' => 'Daniel',
                'm_name' => 'Luis',
                'l_name' => 'Mendoza',
                'dob' => '1989-09-05',
                'address' => 'CKC Senior High School Department',
                'gender' => 'M',
                'contact_no' => '09723456792',
            ],
            [
                'name' => 'Mrs. Elena Garcia',
                'email' => 'guardian@ckc.edu',
                'user_type' => User::TYPE_GUARDIAN,
                'f_name' => 'Elena',
                'm_name' => 'Santos',
                'l_name' => 'Garcia',
                'dob' => '1978-02-16',
                'address' => 'Gingoog City, Misamis Oriental',
                'gender' => 'F',
                'contact_no' => '09823456792',
            ],
            [
                'name' => 'Dr. Roberto Villanueva',
                'email' => 'principal@ckc.edu',
                'user_type' => User::TYPE_PRINCIPAL,
                'f_name' => 'Roberto',
                'm_name' => 'Cruz',
                'l_name' => 'Villanueva',
                'dob' => '1975-07-10',
                'address' => 'CKC Principal Office',
                'gender' => 'M',
                'contact_no' => '09923456792',
            ],
            [
                // Emergency Administrator uses the principal/emergency role
                // because TYPE_PRINCIPAL is the application's break-glass role.
                'name' => 'Emergency Administrator',
                'email' => 'emergency.admin@ckc.edu',
                'user_type' => User::TYPE_PRINCIPAL,
                'f_name' => 'Emergency',
                'm_name' => 'Response',
                'l_name' => 'Administrator',
                'dob' => '1980-01-15',
                'address' => 'CKC Emergency Operations Office',
                'gender' => 'M',
                'contact_no' => '09083456792',
            ],
        ];

        foreach ($users as $attributes) {
            User::updateOrCreate(
                ['email' => $attributes['email']],
                array_merge($attributes, [
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ])
            );
        }
    }
}
