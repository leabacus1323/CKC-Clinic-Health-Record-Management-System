<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EducationalLevel;
use Carbon\Carbon;

class EducationalLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $course = ['BSIT','BSHRM'];
        $strand = ['ABM','STEM'];
        EducationalLevel::truncate();
        //
        $data = [
            [
                'level_name' => "Kindergarten",
                'year_level' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'level_name' => "Kindergarten",
                'year_level' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'level_name' => "Kindergarten",
                'year_level' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'level_name' => "Elementary",
                'year_level' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'level_name' => "Elementary",
                'year_level' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'level_name' => "Elementary",
                'year_level' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'level_name' => "Elementary",
                'year_level' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'level_name' => "Elementary",
                'year_level' => 5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'level_name' => "Elementary",
                'year_level' => 6,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'level_name' => "Junior High",
                'year_level' => 7,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'level_name' => "Junior High",
                'year_level' => 8,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'level_name' => "Junior High",
                'year_level' => 9,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'level_name' => "Junior High",
                'year_level' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'level_name' => "Senior High",
                'year_level' => 11,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'level_name' => "Senior High",
                'year_level' => 12,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'level_name' => "College",
                'year_level' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'level_name' => "College",
                'year_level' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'level_name' => "College",
                'year_level' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],[
                'level_name' => "College",
                'year_level' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];
        EducationalLevel::insert($data);
    }
}
