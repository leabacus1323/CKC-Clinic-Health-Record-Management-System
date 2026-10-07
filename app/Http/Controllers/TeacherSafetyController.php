<?php

namespace App\Http\Controllers;

use App\Models\Patients;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherSafetyController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $students = Patients::query()
            ->select([
                'id', 'user_id', 'school_id', 'registry_type', 'course_id', 'edulvl_id',
                'life_safety_visible_to_teachers', 'life_safety_alert', 'emergency_medication',
                'life_safety_instructions', 'life_safety_reviewed_at',
            ])
            ->where('registry_type', 'student')
            ->where('life_safety_visible_to_teachers', true)
            ->where(function ($query) {
                $query->whereNotNull('life_safety_alert')
                    ->orWhereNotNull('emergency_medication')
                    ->orWhereNotNull('life_safety_instructions');
            })
            ->with([
                'user:id,name,f_name,m_name,l_name',
                'course:id,course_name',
                'educationalLevel:id,level_name',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('school_id', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('school_id')
            ->paginate(25)
            ->withQueryString();

        return view('teacher.safety-index', compact('students', 'search'));
    }

    public function show(Patients $patient): View
    {
        abort_unless(
            $patient->registry_type === 'student' && $patient->life_safety_visible_to_teachers,
            404
        );

        $patient->loadMissing([
            'user:id,name,f_name,m_name,l_name',
            'course:id,course_name',
            'educationalLevel:id,level_name',
        ]);

        return view('teacher.safety-show', compact('patient'));
    }
}
