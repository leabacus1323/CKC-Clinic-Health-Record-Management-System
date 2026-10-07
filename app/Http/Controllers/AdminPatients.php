<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patients;
use App\Models\User;
use App\Models\EducationalLevel;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Database\QueryException;
use App\Models\CertificateRequest;
use App\Models\Course;
use App\Services\PatientEducationRequirement;

class AdminPatients extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $registry = in_array($request->query('registry'), ['student', 'non_student'], true) ? $request->query('registry') : '';

        $patients = Patients::query()
            ->leftJoin('educational_level', 'educational_level.id', '=', 'patients.edulvl_id')
            ->leftJoin('users as u', function ($join) {
                $join->on('u.id', '=', 'patients.user_id')
                    ->whereIn('patients.patient_type', [1, 3])
                    ->whereIn('u.user_type', [1, 2, 4]);
            })
            ->leftJoin('admins as a', function ($join) {
                $join->on('a.id', '=', 'patients.user_id')
                    ->where('patients.patient_type', 2)
                    ->whereIn('a.user_type', [1, 2]);
            })
            ->when($registry !== '', fn ($query) => $query->where('patients.registry_type', $registry))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('patients.school_id', 'like', "%{$search}%")
                        ->orWhere('u.name', 'like', "%{$search}%")
                        ->orWhere('u.email', 'like', "%{$search}%")
                        ->orWhere('a.name', 'like', "%{$search}%")
                        ->orWhere('a.email', 'like', "%{$search}%");
                });
            })
            ->select([
                'patients.id',
                'patients.school_id',
                'patients.patient_type',
                'patients.registry_type',
                'patients.privacy_regime',
                'patients.address',
                'patients.medical_condition',
                'patients.medical_illness',
                'patients.operations',
                'patients.allergies',
                'patients.emergency_contact_name',
                'patients.emergency_contact_number',
                'patients.emergency_relationship',
                DB::raw('CASE WHEN patients.patient_type IN (1, 3) THEN u.name ELSE a.name END AS name'),
                'educational_level.level_name',
                'educational_level.year_level',
            ])
            ->orderByDesc('patients.id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.patients.index', compact('patients', 'search', 'registry'));
    }

    /**
     * Export patients data to CSV
     */
    public function export(): StreamedResponse
    {
        $fileName = 'patients-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function (): void {
            $output = fopen('php://output', 'wb');
            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'ID', 'Patient Name', 'Email', 'Patient Type', 'School ID', 'Blood Type',
                'Address', 'Medical Condition', 'Allergies', 'Emergency Contact Name',
                'Emergency Contact Number', 'Emergency Relationship', 'Educational Level', 'Created At',
            ]);

            Patients::query()
                ->leftJoin('educational_level', 'educational_level.id', '=', 'patients.edulvl_id')
                ->leftJoin('users as u', function ($join) {
                    $join->on('u.id', '=', 'patients.user_id')
                        ->whereIn('patients.patient_type', [1, 3])
                        ->whereIn('u.user_type', [1, 2, 4]);
                })
                ->leftJoin('admins as a', function ($join) {
                    $join->on('a.id', '=', 'patients.user_id')
                        ->where('patients.patient_type', 2)
                        ->whereIn('a.user_type', [1, 2]);
                })
                ->select([
                    'patients.id',
                    DB::raw('CASE WHEN patients.patient_type IN (1, 3) THEN u.name ELSE a.name END AS patient_name'),
                    DB::raw('CASE WHEN patients.patient_type IN (1, 3) THEN u.email ELSE a.email END AS email'),
                    DB::raw("CASE WHEN patients.patient_type = 1 THEN 'Student' WHEN patients.patient_type = 3 THEN 'Faculty' ELSE 'Staff' END AS patient_type_label"),
                    'patients.school_id', 'patients.bloodtype', 'patients.address',
                    'patients.medical_condition', 'patients.allergies',
                    'patients.emergency_contact_name', 'patients.emergency_contact_number',
                    'patients.emergency_relationship', 'educational_level.level_name',
                    'patients.created_at',
                ])
                ->orderBy('patients.id')
                ->cursor()
                ->each(function ($patient) use ($output): void {
                    fputcsv($output, [
                        $patient->id, $patient->patient_name, $patient->email,
                        $patient->patient_type_label, $patient->school_id, $patient->bloodtype,
                        $patient->address, $patient->medical_condition, $patient->allergies,
                        $patient->emergency_contact_name, $patient->emergency_contact_number,
                        $patient->emergency_relationship, $patient->level_name, $patient->created_at,
                    ]);
                });

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        // Only get users with user_type 1 (Students) or 2 (Staff), exclude 3 (Doctors)
        $user = User::whereIn('user_type', [1, 2])->get()->toArray();
        $admin = Admin::where('user_type', 2)->get()->toArray(); // Admins are typically type 2
        $edulvl = EducationalLevel::all();
        $courses = Course::query()->orderBy('course_name')->get();

        $users = collect(array_merge($user, $admin));

        return view('admin.patients.create', compact('users', 'edulvl', 'courses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'patient_type' => 'required',
            'edulvl_id' => 'required|integer|exists:educational_level,id',
            'course_id' => 'nullable|integer|exists:courses,id',
            'user_id' => 'required',
            'school_id' => 'required',
            'address' => 'required',
            'medical_condition' => 'required',
            'medical_illness' => 'required',
            'operations' => 'required',
            'allergies' => 'required',
            'emergency_contact_name' => 'required',
            'emergency_contact_number' => 'required',
            'emergency_relationship' => 'required',
        ]);

        if (PatientEducationRequirement::requiresCourse((int) $request->edulvl_id) && ! $request->filled('course_id')) {
            return redirect()->back()
                ->withErrors(['course_id' => PatientEducationRequirement::courseRequiredMessage()])
                ->withInput();
        }

        // Check if the combination of user_id and patient_type already exists
        $patientExists = \DB::table('patients')
        ->where('user_id', $request->user_id)
        ->where('patient_type', $request->patient_type)
        ->exists();

        // If the combination already exists, return an error
        if ($patientExists) {
            return redirect()->back()->withErrors([
                'user_id' => 'A patient with this user_id and patient_type already exists.'
            ])->withInput();
        }

        // Create a new patient
        Patients::create([
            'patient_type' => $request->patient_type,
            'edulvl_id' => $request->edulvl_id,
            'course_id' => $request->filled('course_id') ? $request->course_id : null,
            'user_id' => $request->user_id,
            'school_id' => $request->school_id,
            'bloodtype' => $request->bloodtype,
            'address' => $request->address,
            'medical_condition' => $request->medical_condition,
            'medical_illness' => $request->medical_illness,
            'operations' => $request->operations,
            'allergies' => $request->allergies,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_number' => $request->emergency_contact_number,
            'emergency_relationship' => $request->emergency_relationship,
        ]);

        //
        return redirect()->route('admin.patients.create')->with('success','Patients created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        // Only get users with user_type 1 (Students) or 2 (Staff), exclude 3 (Doctors)
        $user = User::whereIn('user_type', [1, 2])->get()->toArray();
        $admin = Admin::where('user_type', 2)->get()->toArray(); // Admins are typically type 2
        $edulvl = EducationalLevel::all();
        $courses = Course::query()->orderBy('course_name')->get();

        $users = collect(array_merge($user, $admin));

        //
        $patients = Patients::where('patients.id', $id)
            ->leftJoin('users as u', function ($join) {
                $join->on('u.id', '=', 'patients.user_id')
                    ->whereIn('patients.patient_type', [1, 3]); // Students and Faculty
            })
            ->leftJoin('admins as a', function ($join) {
                $join->on('a.id', '=', 'patients.user_id')
                    ->where('patients.patient_type', 2); // Staff
            })
            ->join('educational_level', 'educational_level.id', '=', 'patients.edulvl_id')
            ->select(
                'patients.*',
                DB::raw("CASE WHEN patients.patient_type IN (1, 3) THEN u.name ELSE a.name END as name"),
                'educational_level.level_name',
                'educational_level.year_level'
            )
            ->first();


        //
        return view('admin.patients.edit', compact('patients','users','edulvl','courses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the request data
        $request->validate([
            'patient_type' => 'required',
            'edulvl_id' => 'required|integer|exists:educational_level,id',
            'course_id' => 'nullable|integer|exists:courses,id',
            'user_id' => 'required',
            'school_id' => 'required',
            'address' => 'required',
            'medical_condition' => 'required',
            'medical_illness' => 'required',
            'operations' => 'required',
            'allergies' => 'required',
            'emergency_contact_name' => 'required',
            'emergency_contact_number' => 'required',
            'emergency_relationship' => 'required',
        ]);

        if (PatientEducationRequirement::requiresCourse((int) $request->edulvl_id) && ! $request->filled('course_id')) {
            return redirect()->back()
                ->withErrors(['course_id' => PatientEducationRequirement::courseRequiredMessage()])
                ->withInput();
        }

        // Find the patient by ID
        $patients = Patients::findOrFail($id);

        // Update the patient details
        $patients->patient_type = $request->input('patient_type');
        $patients->edulvl_id = $request->input('edulvl_id');
        $patients->course_id = $request->filled('course_id') ? $request->input('course_id') : null;
        $patients->user_id = $request->input('user_id');
        $patients->school_id = $request->input('school_id');
        $patients->bloodtype = $request->input('bloodtype');
        $patients->address = $request->input('address');
        $patients->medical_condition = $request->input('medical_condition');
        $patients->medical_illness = $request->input('medical_illness');
        $patients->operations = $request->input('operations');
        $patients->allergies = $request->input('allergies');
        $patients->emergency_contact_name = $request->input('emergency_contact_name');
        $patients->emergency_contact_number = $request->input('emergency_contact_number');
        $patients->emergency_relationship = $request->input('emergency_relationship');

        // Save the updated patients
        $patients->save();

        // Redirect back with a success message
        return redirect()->route('admin.patients.index')->with('success','Patient updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Patients $patient)
    {
        $hasProtectedRecords = $patient->appointments()->exists()
            || $patient->healthRecords()->exists()
            || $patient->immunizations()->exists()
            || $patient->prescriptions()->exists()
            || $patient->dentalExaminations()->exists()
            || $patient->physicalExaminations()->exists()
            || $patient->guardians()->exists()
            || $patient->dataAuthorizations()->exists()
            || $patient->breakGlassEvents()->exists()
            || CertificateRequest::where('patient_id', $patient->id)->exists();

        if ($hasProtectedRecords) {
            return $this->deletionConflict($request, 'This patient has protected health, appointment, consent, or emergency records and cannot be deleted. Retain the record or implement an approved archival workflow.');
        }

        try {
            $patient->delete();
        } catch (QueryException $exception) {
            report($exception);
            return $this->deletionConflict($request, 'This patient is still referenced by protected records and cannot be deleted.');
        }

        return redirect()->route('admin.patients.index')->with('success', 'Patient deleted successfully!');
    }

    private function deletionConflict(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 409);
        }

        return redirect()->route('admin.patients.index')->with('error', $message);
    }
}
