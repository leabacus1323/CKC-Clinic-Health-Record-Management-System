<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            ['course_name' => 'BS Information Technology', 'course_description' => 'Bachelor of Science in Information Technology program focusing on software development, networks, and IT systems'],
            ['course_name' => 'BS Human Resource Management', 'course_description' => 'Bachelor of Science in Human Resource Management program for HR professionals'],
            ['course_name' => 'BS Nursing', 'course_description' => 'Bachelor of Science in Nursing program for healthcare professionals'],
            ['course_name' => 'BS Business Administration', 'course_description' => 'Bachelor of Science in Business Administration program for business leaders'],
            ['course_name' => 'BS Education', 'course_description' => 'Bachelor of Science in Education program for teachers and educators'],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
