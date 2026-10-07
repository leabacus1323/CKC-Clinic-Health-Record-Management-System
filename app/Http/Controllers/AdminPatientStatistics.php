<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patients;
use Illuminate\Support\Facades\DB;

class AdminPatientStatistics extends Controller
{
    /**
     * Show patients by course
     */
    public function byCourse($courseId)
    {
        $course = DB::table('courses')->where('id', $courseId)->first();

        if (!$course) {
            return back()->with('error', 'Course not found');
        }

        $patients = Patients::where('course_id', $courseId)
            ->with(['user', 'educationalLevel'])
            ->get();

        return view('admin.patient-statistics.by-course', compact('course', 'patients'));
    }

    /**
     * Show patients by educational level
     */
    public function byEducationalLevel($levelId)
    {
        $level = DB::table('educational_level')->where('id', $levelId)->first();

        if (!$level) {
            return back()->with('error', 'Educational level not found');
        }

        $patients = Patients::where('edulvl_id', $levelId)
            ->with(['user', 'course', 'educationalLevel'])
            ->get();

        return view('admin.patient-statistics.by-educational-level', compact('level', 'patients'));
    }
}
