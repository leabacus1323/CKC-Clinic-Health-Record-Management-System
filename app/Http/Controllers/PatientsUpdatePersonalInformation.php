<?php

namespace App\Http\Controllers;

use App\Models\Patients;
use App\Models\User;
use App\Models\Course;
use App\Services\PatientEducationRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PatientsUpdatePersonalInformation extends Controller
{
    /**
     * Show patient profile
     */
    public function profile()
    {
        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        return view('patients.profile', compact('user', 'patient'));
    }

    /**
     * Show form for creating new patient profile
     */
    public function create()
    {
        $user = Auth::user();
        $existingPatient = Patients::where('user_id', $user->id)->first();

        if ($existingPatient) {
            return redirect()->route('patients.profile')
                ->with('info', 'You already have a patient profile.');
        }

        $edulvl = \App\Models\EducationalLevel::all();
        $courses = Course::query()->orderBy('course_name')->get();

        return view('patients.create-profile', compact('edulvl', 'courses'));
    }

    /**
     * Store a newly created patient profile
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_type' => 'required|string',
            'school_id' => 'nullable|string|max:255',
            'edulvl_id' => 'nullable|integer|exists:educational_level,id',
            'course_id' => 'nullable|integer|exists:courses,id',
            'bloodtype' => 'nullable|string|max:10',
            'address' => 'required|string|max:255',
            'medical_condition' => 'nullable|string',
            'medical_illness' => 'nullable|string',
            'operations' => 'nullable|string',
            'allergies' => 'nullable|string',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_number' => 'required|string|max:255',
            'emergency_relationship' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        // Check if patient profile already exists
        $existingPatient = Patients::where('user_id', $user->id)->first();
        if ($existingPatient) {
            return redirect()->route('patients.profile')
                ->with('error', 'Patient profile already exists.');
        }

        // Convert patient_type string to integer: 1 = Student, 2 = Staff, 3 = Faculty
        $patientTypeMap = [
            'student' => 1,
            'staff' => 2,
            'faculty' => 3,
            'external' => 2,
        ];
        $patientType = $patientTypeMap[$request->patient_type] ?? 1;

        if (PatientEducationRequirement::requiresCourse((int) $request->edulvl_id) && ! $request->filled('course_id')) {
            return redirect()->back()
                ->withErrors(['course_id' => PatientEducationRequirement::courseRequiredMessage()])
                ->withInput();
        }

        Patients::create([
            'user_id' => $user->id,
            'patient_type' => $patientType,
            'school_id' => $request->school_id ?? null,
            'edulvl_id' => $request->edulvl_id ?? null,
            'course_id' => $request->filled('course_id') ? $request->course_id : null,
            'bloodtype' => $request->bloodtype ?? null,
            'address' => $request->address,
            'medical_condition' => $request->medical_condition,
            'medical_illness' => $request->medical_illness,
            'operations' => $request->operations,
            'allergies' => $request->allergies,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_number' => $request->emergency_contact_number,
            'emergency_relationship' => $request->emergency_relationship,
        ]);

        return redirect()->route('patients.profile')
            ->with('success', 'Patient profile created successfully!');
    }

    /**
     * Show the form for editing patient profile
     */
    public function edit()
    {
        $user = Auth::user();
        $patients = Patients::where('user_id', $user->id)->first();

        if (!$patients) {
            return redirect()->route('patients.profile.create')
                ->with('info', 'Please create your patient profile first.');
        }

        $edulvl = \App\Models\EducationalLevel::all();
        $courses = Course::query()->orderBy('course_name')->get();

        return view('patients.edit-profile', compact('user', 'patients', 'edulvl', 'courses'));
    }

    /**
     * Update patient profile and user information
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'patient_type' => 'required|string',
            'school_id' => 'nullable|string|max:255',
            'edulvl_id' => 'nullable|integer|exists:educational_level,id',
            'course_id' => 'nullable|integer|exists:courses,id',
            'bloodtype' => 'nullable|string|max:10',
            'address' => 'required|string|max:255',
            'medical_condition' => 'nullable|string',
            'medical_illness' => 'nullable|string',
            'operations' => 'nullable|string',
            'allergies' => 'nullable|string',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_number' => 'required|string|max:255',
            'emergency_relationship' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $patients = Patients::where('user_id', $user->id)->first();

        if (!$patients) {
            return redirect()->route('patients.profile.create')
                ->with('error', 'Patient profile not found.');
        }

        if (PatientEducationRequirement::requiresCourse((int) $request->edulvl_id) && ! $request->filled('course_id')) {
            return redirect()->back()
                ->withErrors(['course_id' => PatientEducationRequirement::courseRequiredMessage()])
                ->withInput();
        }

        // Update user information
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Convert patient_type string to integer: 1 = Student, 2 = Staff, 3 = Faculty
        $patientTypeMap = [
            'student' => 1,
            'staff' => 2,
            'faculty' => 3,
            'external' => 2,
        ];

        // Update patient information
        $patients->update([
            'patient_type' => $patientTypeMap[$request->patient_type] ?? $patients->patient_type,
            'school_id' => $request->school_id ?? $patients->school_id,
            'edulvl_id' => $request->edulvl_id ?? $patients->edulvl_id,
            'course_id' => $request->filled('course_id') ? $request->course_id : null,
            'bloodtype' => $request->bloodtype ?? $patients->bloodtype,
            'address' => $request->address,
            'medical_condition' => $request->medical_condition,
            'medical_illness' => $request->medical_illness,
            'operations' => $request->operations,
            'allergies' => $request->allergies,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_number' => $request->emergency_contact_number,
            'emergency_relationship' => $request->emergency_relationship,
        ]);

        return redirect()->route('patients.profile')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * Update only basic user information (name, email)
     */
    public function updateBasicInfo(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
        ]);

        $user = Auth::user();

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Basic information updated successfully!');
    }

    /**
     * Update only medical information
     */
    public function updateMedicalInfo(Request $request)
    {
        $validated = $request->validate([
            'medical_condition' => 'sometimes|nullable|string|max:255',
            'medical_illness' => 'sometimes|nullable|string|max:255',
            'operations' => 'sometimes|nullable|string|max:255',
            'allergies' => 'sometimes|nullable|string|max:50',
        ]);

        $user = Auth::user();
        $patients = Patients::where('user_id', $user->id)->first();

        if (!$patients) {
            return redirect()->route('patients.profile.create')
                ->with('error', 'Patient profile not found.');
        }

        // These legacy columns are NOT NULL. Update only submitted fields and
        // normalize an intentionally cleared value to an empty string.
        $updates = [];
        foreach (['medical_condition', 'medical_illness', 'operations', 'allergies'] as $field) {
            if (array_key_exists($field, $validated)) {
                $updates[$field] = $validated[$field] ?? '';
            }
        }

        if ($updates !== []) {
            $patients->update($updates);
        }

        return back()->with('success', 'Medical information updated successfully!');
    }

    /**
     * Update only emergency contact information
     */
    public function updateEmergencyContact(Request $request)
    {
        $request->validate([
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_number' => 'required|string|max:255',
            'emergency_relationship' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $patients = Patients::where('user_id', $user->id)->first();

        if (!$patients) {
            return redirect()->route('patients.profile.create')
                ->with('error', 'Patient profile not found.');
        }

        $patients->update([
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_number' => $request->emergency_contact_number,
            'emergency_relationship' => $request->emergency_relationship,
        ]);

        return back()->with('success', 'Emergency contact updated successfully!');
    }
}
