<?php

namespace App\Http\Controllers;

use App\Models\Patients;
use App\Models\Appointment;
use App\Models\Doctors;
use App\Models\HealthRecords;
use App\Models\PrescriptionRecord;
use App\Models\ImmunizationRecords;
use App\Models\PhysicalExamination;
use App\Models\DentalExamination;
use App\Models\Immunization;
use App\Models\Medicine;
use App\Models\GeneratedReport;
use App\Models\CertificateRequest;
use App\Models\Announcement;
use App\Models\NurseBed;
use App\Models\NurseLog;
use App\Models\User;
use App\Models\Course;
use App\Models\EducationalLevel;
use App\Support\SqlDateExpressions;
use App\Services\PatientEducationRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class StaffDashboardController extends Controller
{
    /**
     * Display staff dashboard with statistics and recent activities
     * Staff has access to all doctor functionalities for managing patients
     */
    public function index()
    {
        $ttl = max(1, config('performance.dashboard_cache_ttl', 60));

        $dashboard = Cache::remember('dashboard.staff.summary', $ttl, function (): array {
            $today = Carbon::today()->toDateString();
            $weekStart = Carbon::now()->startOfWeek()->toDateString();
            $weekEnd = Carbon::now()->endOfWeek()->toDateString();

            $appointmentCounts = DB::table('appointments')->selectRaw(
                'SUM(CASE WHEN date = ? THEN 1 ELSE 0 END) AS today_appointments,
                 SUM(CASE WHEN date BETWEEN ? AND ? THEN 1 ELSE 0 END) AS weekly_appointments,
                 SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS pending_appointments',
                [$today, $weekStart, $weekEnd, Appointment::STATUS_PENDING]
            )->first();

            $monthExpression = SqlDateExpressions::monthNumber('date');
            $monthly = DB::table('appointments')
                ->whereYear('date', Carbon::now()->year)
                ->selectRaw("{$monthExpression} AS month, COUNT(*) AS aggregate")
                ->groupByRaw($monthExpression)
                ->pluck('aggregate', 'month');

            $monthlyStats = collect(range(1, 12))->mapWithKeys(
                fn (int $month) => [$month => (int) ($monthly[$month] ?? 0)]
            );

            return [
                'todayAppointments' => (int) ($appointmentCounts->today_appointments ?? 0),
                'weeklyAppointments' => (int) ($appointmentCounts->weekly_appointments ?? 0),
                'totalPatients' => DB::table('patients')
                    ->join('users', 'users.id', '=', 'patients.user_id')
                    ->whereIn('users.user_type', [User::TYPE_STUDENT, User::TYPE_STAFF])
                    ->count(),
                'pendingAppointments' => (int) ($appointmentCounts->pending_appointments ?? 0),
                'monthlyStats' => $monthlyStats,
                'byCourse' => DB::table('patients')
                    ->join('courses', 'patients.course_id', '=', 'courses.id')
                    ->select('courses.id', 'courses.course_name', DB::raw('COUNT(*) AS total_patients'))
                    ->groupBy('courses.id', 'courses.course_name')
                    ->orderByDesc('total_patients')
                    ->get(),
                'byEducationalLevel' => DB::table('patients')
                    ->join('educational_level', 'patients.edulvl_id', '=', 'educational_level.id')
                    ->select('educational_level.id', 'educational_level.level_name', DB::raw('COUNT(*) AS total_patients'))
                    ->groupBy('educational_level.id', 'educational_level.level_name')
                    ->orderByDesc('total_patients')
                    ->get(),
            ];
        });

        $dashboard['recentAppointments'] = Appointment::query()
            ->select(['id', 'patient_id', 'doc_id', 'date', 'time', 'status'])
            ->with([
                'patient:id,user_id', 'patient.user:id,name',
                'doctor:id,user_id', 'doctor.user:id,name',
            ])
            ->latest('date')->latest('time')->limit(5)->get();

        return view('staff.dashboard', $dashboard);
    }

    // AJAX Tab Methods for Shell Navigation
    public function ajaxDashboard()
    {
        $ttl = max(1, config('performance.dashboard_cache_ttl', 60));

        $data = Cache::remember('dashboard.staff.ajax-summary', $ttl, function (): array {
            $counts = DB::table('appointments')->selectRaw(
                'SUM(CASE WHEN date = ? THEN 1 ELSE 0 END) AS today_appointments,
                 SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS pending_appointments',
                [Carbon::today()->toDateString(), Appointment::STATUS_PENDING]
            )->first();

            $recordCounts = DB::selectOne('SELECT
                (SELECT COUNT(*) FROM health_records)
                + (SELECT COUNT(*) FROM physical_examinations)
                + (SELECT COUNT(*) FROM dental_examinations)
                + (SELECT COUNT(*) FROM immunization_records)
                + (SELECT COUNT(*) FROM prescription_records) AS total_records');

            return [
                'todayAppointments' => (int) ($counts->today_appointments ?? 0),
                'totalPatients' => DB::table('patients')
                    ->join('users', 'users.id', '=', 'patients.user_id')
                    ->whereIn('users.user_type', [User::TYPE_STUDENT, User::TYPE_STAFF])
                    ->count(),
                'pendingAppointments' => (int) ($counts->pending_appointments ?? 0),
                'healthRecordsCount' => (int) ($recordCounts->total_records ?? 0),
            ];
        });

        return view('staff.partials.dashboard', $data);
    }

    public function ajaxAppointments()
    {
        return view('staff.partials.appointments');
    }


    public function ajaxNurseLogs()
    {
        $logs = NurseLog::with(['patient.user', 'staff:id,name', 'medicine:id,name,quantity,unit', 'prescription:id,status,date_prescribed', 'bed', 'interventions.medicine:id,name,quantity,unit', 'interventions.prescription:id,status,date_prescribed', 'interventions.bed'])
            ->latest('time_in')
            ->limit(10)
            ->get();

        $summary = [
            'today' => NurseLog::whereDate('time_in', today())->count(),
            'open' => NurseLog::whereNull('time_out')->count(),
            'medicine' => $this->countNurseLogsTodayByIntervention(NurseLog::INTERVENTION_PRESCRIBED_MEDICINE),
            'mgh' => $this->countNurseLogsTodayByIntervention(NurseLog::INTERVENTION_MGH),
        ];

        $bedSummary = $this->nurseBedSummary();

        return view('staff.partials.nurse-logs', compact('logs', 'summary', 'bedSummary'));
    }


    private function countNurseLogsTodayByIntervention(string $type): int
    {
        return NurseLog::query()
            ->whereDate('time_in', today())
            ->where(function ($query) use ($type): void {
                $query->where('intervention_type', $type)
                    ->orWhereHas('interventions', function ($q) use ($type): void {
                        $q->where('intervention_type', $type);
                    });
            })
            ->count();
    }

    private function nurseBedSummary(): array
    {
        $beds = NurseBed::query()
            ->with('activeRestLog')
            ->where('is_active', true)
            ->get();

        $occupied = $beds->filter(fn (NurseBed $bed): bool => $bed->activeRestLog !== null)->count();

        return [
            'total' => $beds->count(),
            'occupied' => $occupied,
            'available' => max($beds->count() - $occupied, 0),
        ];
    }

    public function ajaxPatients()
    {
        return view('staff.partials.patients');
    }

    public function ajaxHealthRecords()
    {
        return view('staff.partials.health-records');
    }

    public function ajaxMedicines()
    {
        return view('staff.partials.medicines');
    }

    public function ajaxPrescriptions()
    {
        return view('staff.partials.prescriptions');
    }

    public function ajaxStatistics()
    {
        $ttl = max(1, config('performance.dashboard_cache_ttl', 60));

        $statistics = Cache::remember('dashboard.staff.statistics', $ttl, function (): array {
            return [
                'totalPatients' => DB::table('patients')->count(),
                'byCourse' => DB::table('patients')
                    ->join('courses', 'patients.course_id', '=', 'courses.id')
                    ->select('courses.id', 'courses.course_name', DB::raw('COUNT(*) AS total_patients'))
                    ->groupBy('courses.id', 'courses.course_name')
                    ->orderByDesc('total_patients')
                    ->get(),
                'byEducationalLevel' => DB::table('patients')
                    ->join('educational_level', 'patients.edulvl_id', '=', 'educational_level.id')
                    ->select('educational_level.id', 'educational_level.level_name', DB::raw('COUNT(*) AS total_patients'))
                    ->groupBy('educational_level.id', 'educational_level.level_name')
                    ->orderByDesc('total_patients')
                    ->get(),
            ];
        });

        return view('staff.partials.statistics', $statistics);
    }

    /**
     * Show patients by course
     */
    public function patientsByCourse($courseId)
    {
        $course = Course::findOrFail($courseId);
        $patients = Patients::with('user', 'educationalLevel')
            ->where('course_id', $courseId)
            ->get();

        return view('staff.statistics.by-course', compact('course', 'patients'));
    }

    /**
     * Show patients by educational level
     */
    public function patientsByLevel($levelId)
    {
        $level = EducationalLevel::findOrFail($levelId);
        $patients = Patients::with('user', 'course')
            ->where('edulvl_id', $levelId)
            ->get();

        return view('staff.statistics.by-level', compact('level', 'patients'));
    }

    public function ajaxImport()
    {
        return view('staff.partials.import');
    }

    /**
     * Show form for creating a new medicine
     */
    public function createMedicine()
    {
        return view('staff.medicines.create');
    }

    /**
     * Show form for creating a new prescription
     */
    public function createPrescription()
    {
        $patients = Patients::with('user')->whereHas('user', function($q) {
            $q->whereIn('user_type', [1, 2]);
        })->get();
        $medicines = Medicine::where('quantity', '>', 0)->get();
        $doctors = Doctors::with('user')->get();
        return view('staff.prescriptions.create', compact('patients', 'medicines', 'doctors'));
    }

    public function ajaxReports()
    {
        return view('staff.partials.reports');
    }

    /**
     * Display appointments page
     */
    public function appointments(Request $request)
    {
        $query = Appointment::with(['patient.user', 'doctor'])
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient.user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $appointments = $query->paginate(15);
        $doctors = Doctors::with('user')->get();

        // Get pending appointments for the pending section
        $pendingAppointments = Appointment::with(['patient.user', 'doctor'])
            ->where('status', Appointment::STATUS_PENDING)
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();

        // Get confirmed appointments for today
        $todayAppointments = Appointment::with(['patient.user', 'doctor'])
            ->where('status', Appointment::STATUS_CONFIRMED)
            ->whereDate('date', Carbon::today())
            ->orderBy('time', 'asc')
            ->get();

        // Calendar data for current month
        $calendarData = [];
        $currentMonthAppointments = Appointment::with(['patient.user', 'doctor'])
            ->whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->get();

        foreach ($currentMonthAppointments as $apt) {
            $dateKey = Carbon::parse($apt->date)->format('Y-m-d');
            if (!isset($calendarData[$dateKey])) {
                $calendarData[$dateKey] = [];
            }
            $calendarData[$dateKey][] = [
                'id' => $apt->id,
                'patient_name' => $apt->patient->user->name ?? 'N/A',
                'doctor_name' => $apt->doctor->user->name ?? 'N/A',
                'time' => Carbon::parse($apt->time)->format('h:i A'),
                'status' => $apt->status,
            ];
        }

        return view('staff.appointments', compact('appointments', 'doctors', 'pendingAppointments', 'todayAppointments', 'calendarData'));
    }

    /**
     * Update appointment status
     */
    public function updateAppointmentStatus(Request $request, $appointmentId)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', [
                Appointment::STATUS_PENDING,
                Appointment::STATUS_CONFIRMED,
                Appointment::STATUS_CANCELLED,
                'Completed'
            ])
        ]);

        $appointment = Appointment::findOrFail($appointmentId);
        $appointment->status = $request->status;
        $appointment->save();

        return redirect()->route('staff.dashboard')->with('success', 'Appointment ' . strtolower($request->status) . ' successfully.');
    }

    /**
     * Display patients list
     */
    public function patients(Request $request)
    {
        $query = Patients::with(['user', 'educationalLevel'])
            ->whereHas('user', function($q) {
                $q->whereIn('user_type', [1, 2]); // Students and Staff only
            });

        // Search functionality - search in user name, email, and patient data
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('id', 'like', "%{$search}%")
                ->orWhere('school_id', 'like', "%{$search}%");
            });
        }

        // Filter by patient type
        if ($request->filled('patient_type')) {
            $query->where('patient_type', $request->patient_type);
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('staff.patients', compact('patients'));
    }

    /**
     * View individual patient details
     */
    public function viewPatient($patientId)
    {
        $patient = Patients::with(['user', 'educationalLevel'])->findOrFail($patientId);

        // Get health records
        $healthRecords = HealthRecords::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get appointments
        $appointments = Appointment::where('patient_id', $patientId)
            ->with('doctor.user')
            ->orderBy('date', 'desc')
            ->get();

        // Get prescriptions
        $prescriptions = PrescriptionRecord::where('patient_id', $patientId)
            ->with(['medicine', 'doctor'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get physical examinations
        $physicalExams = PhysicalExamination::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get dental examinations
        $dentalExams = DentalExamination::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get immunization records
        $immunizations = ImmunizationRecords::where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('staff.patient-details', compact('patient', 'healthRecords', 'appointments', 'prescriptions', 'physicalExams', 'dentalExams', 'immunizations'));
    }

    /**
     * Show create patient form
     */
    public function createPatient()
    {
        $edulvl = EducationalLevel::query()->orderBy('level_name')->orderBy('year_level')->get();
        $courses = Course::query()->orderBy('course_name')->get();

        return view('staff.patients-create', compact('edulvl', 'courses'));
    }

    /**
     * Store new patient
     */
    public function storePatient(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'patient_type' => 'required|in:1,2,3',
            'edulvl_id' => 'nullable|integer|exists:educational_level,id',
            'course_id' => 'nullable|integer|exists:courses,id',
            'school_id' => 'nullable|string|max:50',
            'bloodtype' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:255',
            'medical_condition' => 'nullable|string|max:500',
            'allergies' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|string|max:20',
            'emergency_relationship' => 'nullable|string|max:50',
        ]);

        $nameParts = preg_split('/\s+/', trim($request->name), -1, PREG_SPLIT_NO_EMPTY) ?: ['Patient'];
        $firstName = $request->input('f_name', $nameParts[0]);
        $lastName = $request->input('l_name', count($nameParts) > 1 ? end($nameParts) : $nameParts[0]);
        $middleName = $request->input('m_name', '');
        $educationalLevelId = $request->input('edulvl_id')
            ?: \App\Models\EducationalLevel::query()->orderBy('id')->value('id');

        if (! $educationalLevelId) {
            return back()->withInput()->with('error', 'Create at least one educational level before adding a patient.');
        }

        if (PatientEducationRequirement::requiresCourse((int) $educationalLevelId) && ! $request->filled('course_id')) {
            return back()->withInput()->withErrors([
                'course_id' => PatientEducationRequirement::courseRequiredMessage(),
            ]);
        }

        $patient = DB::transaction(function () use ($request, $firstName, $middleName, $lastName, $educationalLevelId) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'user_type' => $request->patient_type,
                'f_name' => $firstName,
                'm_name' => $middleName,
                'l_name' => $lastName,
                'dob' => $request->input('dob', '1900-01-01'),
                'address' => $request->input('address') ?: 'Not provided',
                'gender' => $request->input('gender', 'U'),
                'contact_no' => $request->input('contact_no') ?: 'Not provided',
            ]);

            return Patients::create([
                'user_id' => $user->id,
                'patient_type' => $request->patient_type,
                'school_id' => $request->input('school_id') ?: 'TEMP-' . $user->id,
                'edulvl_id' => $educationalLevelId,
                'course_id' => $request->filled('course_id') ? $request->input('course_id') : null,
                'bloodtype' => $request->bloodtype,
                'address' => $request->input('address') ?: 'Not provided',
                'medical_condition' => $request->input('medical_condition') ?: 'None reported',
                'medical_illness' => $request->input('medical_illness') ?: 'None reported',
                'operations' => $request->input('operations') ?: 'None reported',
                'allergies' => $request->input('allergies') ?: 'None reported',
                'emergency_contact_name' => $request->input('emergency_contact_name') ?: 'Not provided',
                'emergency_contact_number' => $request->input('emergency_contact_number') ?: 'Not provided',
                'emergency_relationship' => $request->input('emergency_relationship') ?: 'Not provided',
            ]);
        });

        return redirect()->route('staff.patient-details', $patient->id)
            ->with('success', 'Patient created successfully!');
    }

    /**
     * Update patient information
     */
    public function updatePatient(Request $request, $patientId)
    {
        $patient = Patients::findOrFail($patientId);

        $validated = $request->validate([
            'bloodtype' => 'sometimes|nullable|string|max:10',
            'address' => 'sometimes|nullable|string|max:255',
            'medical_condition' => 'sometimes|nullable|string|max:255',
            'allergies' => 'sometimes|nullable|string|max:50',
            'emergency_contact_name' => 'sometimes|nullable|string|max:255',
            'emergency_contact_number' => 'sometimes|nullable|string|max:255',
            'emergency_relationship' => 'sometimes|nullable|string|max:255',
        ]);

        $updates = [];
        $requiredLegacyFields = [
            'address', 'medical_condition', 'allergies',
            'emergency_contact_name', 'emergency_contact_number', 'emergency_relationship',
        ];

        foreach ($validated as $field => $value) {
            $updates[$field] = in_array($field, $requiredLegacyFields, true)
                ? ($value ?? '')
                : $value;
        }

        if ($updates !== []) {
            $patient->update($updates);
        }

        return back()->with('success', 'Patient information updated successfully.');
    }

    /**
     * Display health records page
     */
    public function healthRecords(Request $request)
    {
        // Load only existing relationships: patient.user
        $query = HealthRecords::with(['patient.user']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('f_name', 'like', "%{$search}%")
                  ->orWhere('m_name', 'like', "%{$search}%")
                  ->orWhere('l_name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        $healthRecords = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $totalHealthRecords = HealthRecords::count();
        $totalPhysicalExams = PhysicalExamination::count();
        $totalDentalExams = DentalExamination::count();
        $totalImmunizations = ImmunizationRecords::count();
        $totalPrescriptions = PrescriptionRecord::count();

        // Top diagnoses - ensure diagnosis is a valid string before counting
        $diagnosisStats = HealthRecords::whereNotNull('diagnosis')
            ->where('diagnosis', '!=', '')
            ->whereRaw('LENGTH(diagnosis) > 0')
            ->get()
            ->pluck('diagnosis')
            ->map(function($d) { return trim($d); })
            ->filter()
            ->countBy()
            ->map(function($count, $diagnosis) {
                return (object)['diagnosis' => $diagnosis, 'count' => $count];
            })
            ->sortByDesc(function($item) { return $item->count; })
            ->values();

        // Get paginated examination records for tabs
        $physicalExaminations = PhysicalExamination::with(['patient.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $dentalExaminations = DentalExamination::with(['patient.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $immunizationRecords = ImmunizationRecords::with(['patient.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('staff.health-records', compact('healthRecords', 'totalHealthRecords', 'totalPhysicalExams', 'totalDentalExams', 'totalImmunizations', 'totalPrescriptions', 'diagnosisStats', 'physicalExaminations', 'dentalExaminations', 'immunizationRecords'));
    }

    /**
     * Print health record for a specific patient
     */
    public function printHealthRecord($patientId)
    {
        $patient = Patients::with(['user', 'healthRecords', 'physicalExaminations', 'dentalExaminations', 'immunizations'])
            ->where('id', $patientId)
            ->first();

        if (!$patient) {
            abort(404, 'Patient not found');
        }

        $healthRecords = $patient->healthRecords;
        $physicalExaminations = $patient->physicalExaminations;
        $dentalExaminations = $patient->dentalExaminations;
        $immunizationRecords = $patient->immunizations;

        return view('staff.printable.health-record', compact(
            'patient',
            'healthRecords',
            'physicalExaminations',
            'dentalExaminations',
            'immunizationRecords'
        ));
    }

    /**
     * Display medicines page
     */
    public function medications(Request $request)
    {
        $query = Medicine::query();

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('generic_name', 'like', "%{$search}%")
                    ->orWhere('manufacturer', 'like', "%{$search}%");
            });
        }

        if ($request->filled('availability')) {
            if ($request->availability === 'in_stock') {
                $query->where('quantity', '>', 0);
            } elseif ($request->availability === 'out_of_stock') {
                $query->where('quantity', '<=', 0);
            } elseif ($request->availability === 'low_stock') {
                $query->whereBetween('quantity', [1, 10]);
            }
        }

        $medicines = $query->orderBy('name')->paginate(15)->withQueryString();
        $stats = Medicine::query()->selectRaw(
            'COUNT(*) AS total,
             SUM(CASE WHEN quantity > 0 THEN 1 ELSE 0 END) AS available,
             SUM(CASE WHEN quantity BETWEEN 1 AND 10 THEN 1 ELSE 0 END) AS low_stock,
             SUM(CASE WHEN expiration_date IS NOT NULL AND expiration_date < ? THEN 1 ELSE 0 END) AS expired',
            [Carbon::today()->toDateString()]
        )->first();

        return view('staff.medicines', [
            'medicines' => $medicines,
            'totalMedicines' => (int) ($stats->total ?? 0),
            'availableMedicines' => (int) ($stats->available ?? 0),
            'lowStockMedicines' => (int) ($stats->low_stock ?? 0),
            'expiredMedicines' => (int) ($stats->expired ?? 0),
        ]);
    }

    /**
     * Store new medicine
     */
    public function storeMedicine(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'dosage' => 'nullable|string|max:100',
            'quantity' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'expiration_date' => 'nullable|date',
            'description' => 'nullable|string',
            'medicine_type' => 'nullable|string|max:100',
        ]);

        Medicine::create($request->all());

        return redirect()->route('staff.medications')
            ->with('success', 'Medicine added successfully');
    }

    /**
     * Update medicine
     */
    public function updateMedicine(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'dosage' => 'nullable|string|max:100',
            'quantity' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'expiration_date' => 'nullable|date',
            'description' => 'nullable|string',
            'medicine_type' => 'nullable|string',
        ]);

        $medicine = Medicine::findOrFail($id);
        $medicine->update($request->all());

        return redirect()->back()->with('success', 'Medicine updated successfully');
    }

    /**
     * Show edit form for medicine
     */
    public function editMedicine($id)
    {
        $medicine = Medicine::findOrFail($id);
        return view('staff.medicines.edit', compact('medicine'));
    }

    /**
     * Delete medicine
     */
    public function deleteMedicine($id)
    {
        $medicine = Medicine::findOrFail($id);
        $medicine->delete();

        return redirect()->back()->with('success', 'Medicine deleted successfully');
    }

    /**
     * Display prescriptions page
     */
    public function prescriptions(Request $request)
    {
        $query = PrescriptionRecord::with(['patient.user', 'medicine', 'doctor']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('f_name', 'like', "%{$search}%")
                  ->orWhere('m_name', 'like', "%{$search}%")
                  ->orWhere('l_name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $prescriptions = $query->orderBy('created_at', 'desc')->paginate(15);
        $patients = Patients::with('user')->whereHas('user', function($q) {
            $q->whereIn('user_type', [1, 2]);
        })->get();
        $medicines = Medicine::where('quantity', '>', 0)->get();

        // Statistics
        $totalPrescriptions = PrescriptionRecord::count();
        $activePrescriptions = PrescriptionRecord::where('status', 'active')->count();
        $completedPrescriptions = PrescriptionRecord::where('status', 'completed')->count();
        $discontinuedPrescriptions = PrescriptionRecord::where('status', 'discontinued')->count();

        return view('staff.prescriptions', compact('prescriptions', 'patients', 'medicines', 'totalPrescriptions', 'activePrescriptions', 'completedPrescriptions', 'discontinuedPrescriptions'));
    }

    /**
     * Store new prescription
     */
    public function storePrescription(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medicine_id' => 'required|exists:medicine,id',
            'dosage' => 'required|string|max:255',
            'frequency' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'instruction' => 'nullable|string',
            'instructions' => 'nullable|string',
            'status' => 'nullable|in:active,completed,discontinued',
            'doctor_id' => 'nullable|exists:doctors,id',
        ]);

        // Get staff's doctor profile if exists, use form doctor_id first
        $staffDoctor = \App\Models\Doctors::where('user_id', Auth::id())->first();

        PrescriptionRecord::create([
            'patient_id' => $request->patient_id,
            'medicine_id' => $request->medicine_id,
            'dosage' => $request->dosage,
            'frequency' => $request->frequency ?? $request->instructions,
            'duration' => $request->duration,
            'instruction' => $request->instruction ?? $request->instructions,
            'doctor_id' => $request->doctor_id ?? $staffDoctor->id ?? null,
            'prescribed_by' => Auth::id(),
            'status' => $request->status ?? 'active',
        ]);

        return redirect()->back()->with('success', 'Prescription created successfully');
    }

    /**
     * Update prescription
     */
    public function updatePrescriptionRecord(Request $request, $id)
    {
        $request->validate([
            'dosage' => 'required|string|max:255',
            'frequency' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'instruction' => 'nullable|string',
        ]);

        $prescription = PrescriptionRecord::findOrFail($id);
        $prescription->update([
            'dosage' => $request->dosage,
            'frequency' => $request->frequency,
            'duration' => $request->duration,
            'instruction' => $request->instruction,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Prescription updated successfully'
        ]);
    }

    /**
     * Discontinue prescription
     */
    public function discontinuePrescription(Request $request, $id)
    {
        $prescription = PrescriptionRecord::findOrFail($id);
        $prescription->status = 'discontinued';
        $prescription->discontinuation_reason = $request->discontinuation_reason;
        $prescription->date_discontinued = now();
        $prescription->save();

        return response()->json([
            'success' => true,
            'message' => 'Prescription discontinued successfully'
        ]);
    }

    /**
     * Get prescription history for a patient
     */
    public function prescriptionHistory($patientId)
    {
        $prescriptions = PrescriptionRecord::where('patient_id', $patientId)
            ->with(['medicine', 'doctor'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($prescriptions);
    }

    /**
     * Display reports page
     */
    public function reports()
    {
        // Appointment statistics
        $appointmentStats = [
            'total' => Appointment::count(),
            'this_month' => Appointment::whereMonth('date', Carbon::now()->month)
                ->whereYear('date', Carbon::now()->year)
                ->count(),
            'pending' => Appointment::where('status', Appointment::STATUS_PENDING)->count(),
            'completed' => Appointment::where('status', 'Completed')->count(),
        ];

        // Patient statistics
        $patientStats = [
            'total_patients' => Patients::whereHas('user', function($q) {
                $q->whereIn('user_type', [1, 2]);
            })->count(),
            'new_this_month' => Patients::whereHas('user', function($q) {
                $q->whereIn('user_type', [1, 2]);
            })
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count(),
        ];

        // Monthly trends (last 12 months)
        $monthlyTrends = Appointment::selectRaw(SqlDateExpressions::monthNumber('date').' as month, '.SqlDateExpressions::yearNumber('date').' as year, COUNT(*) as count')
            ->whereNotNull('date')
            ->groupBy('month', 'year')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        // Top conditions (if you have a conditions field in health records)
        $topConditions = [];

        // Recent health records
        $recentHealthRecords = HealthRecords::with(['patient.user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Medicine inventory status
        $medicineStats = [
            'total' => Medicine::count(),
            'in_stock' => Medicine::where('quantity', '>', 10)->count(),
            'low_stock' => Medicine::where('quantity', '>', 0)->where('quantity', '<=', 10)->count(),
            'out_of_stock' => Medicine::where('quantity', '<=', 0)->count(),
            'expired' => Medicine::where('expiration_date', '<', Carbon::now())->count(),
        ];

        return view('staff.reports', compact('appointmentStats', 'patientStats', 'monthlyTrends', 'topConditions', 'recentHealthRecords', 'medicineStats'));
    }

    /**
     * Display announcements list
     */
    public function announcements()
    {
        $announcements = Announcement::orderByDesc('created_at')->paginate(15);

        return view('staff.announcements', compact('announcements'));
    }

    /**
     * Show announcement creation form
     */
    public function createAnnouncement()
    {
        return view('staff.create-announcement');
    }

    /**
     * Store a new announcement
     */
    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Announcement::create([
            'title' => $request->title,
            'message' => $request->message,
            'created_by' => Auth::id(),
            'active' => true,
        ]);

        Cache::forget('navigation.active_announcements.latest');
        Cache::forget('navigation.active_announcements.count');

        return redirect()->route('staff.announcements')->with('success', 'Announcement sent successfully.');
    }

    /**
     * Show form to create new health record
     */
    public function createHealthRecord()
    {
        // Staff can create records for all patients
        $patients = Patients::with('user')
            ->whereHas('user', function($q) {
                $q->whereIn('user_type', [1, 2]);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Get available immunizations for dropdown
        $immunizations = Immunization::orderBy('name')->get();
        $doctors = Doctors::with('user')->orderBy('created_at', 'desc')->get();

        return view('staff.create-health-record', compact('patients', 'immunizations', 'doctors'));
    }

    /**
     * Store a new health record
     */
    public function storeHealthRecord(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'record_type' => 'required|in:physical,dental,immunization',
            'doctor_id' => 'required_if:record_type,physical,dental|exists:doctors,id',
        ]);

        try {
            switch ($request->record_type) {
                case 'physical':
                    $this->createPhysicalExamination($request);
                    break;
                case 'dental':
                    $this->createDentalExamination($request);
                    break;
                case 'immunization':
                    $this->createImmunizationRecord($request);
                    break;
            }

            return redirect()->route('staff.health-records')
                ->with('success', 'Health record created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating health record: ' . $e->getMessage());
        }
    }

    /**
     * Create Physical Examination Record
     */
    private function createPhysicalExamination(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'height' => 'required|numeric',
            'weight' => 'required|numeric',
            'bp' => 'required|string|max:20',
            'heart' => 'required|string|max:255',
            'lungs' => 'required|string|max:255',
            'eyes' => 'required|string|max:255',
            'ears' => 'required|string|max:255',
            'nose' => 'required|string|max:255',
            'throat' => 'required|string|max:255',
            'skin' => 'required|string|max:255',
            'remarks' => 'required|string',
        ]);

        PhysicalExamination::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'height' => $request->height,
            'weight' => $request->weight,
            'bp' => $request->bp,
            'heart' => $request->heart,
            'lungs' => $request->lungs,
            'eyes' => $request->eyes,
            'ears' => $request->ears,
            'nose' => $request->nose,
            'throat' => $request->throat,
            'skin' => $request->skin,
            'remarks' => $request->remarks,
        ]);
    }

    /**
     * Create Dental Examination Record
     */
    private function createDentalExamination(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'diagnosis' => 'nullable|string',
            'dental_diagnosis' => 'nullable|string',
            'teeth_status' => 'nullable|array',
        ]);

        $teethStatus = $request->teeth_status ?? [];

        // Form sends nested array teeth_status[upper][1], teeth_status[lower][1]
        // Flatten to simple array with keys 1-32
        if (is_array($teethStatus)) {
            $flatTeethStatus = [];
            foreach ($teethStatus as $key => $value) {
                if (is_array($value)) {
                    // Handle nested structure like ['upper' => [...], 'lower' => [...]]
                    foreach ($value as $subKey => $subValue) {
                        if ($key === 'upper') {
                            $flatTeethStatus[(int)$subKey] = $subValue;
                        } elseif ($key === 'lower') {
                            // Lower teeth start from 17
                            $flatTeethStatus[16 + (int)$subKey] = $subValue;
                        }
                    }
                } elseif (is_numeric($key)) {
                    // Already flat array with numeric keys
                    $flatTeethStatus[(int)$key] = $value;
                }
            }
            $teethStatus = $flatTeethStatus;
        }

        DentalExamination::create([
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'diagnosis' => $request->input('dental_diagnosis', $request->diagnosis),
            'teeth_status' => $teethStatus,
        ]);
    }

    /**
     * Create Immunization Record
     */
    private function createImmunizationRecord(Request $request)
    {
        $request->validate([
            'vaccine_name' => 'required|string|max:255',
            'vaccine_type' => 'nullable|string|max:255',
            'dosage' => 'nullable|string|max:255',
            'site_of_administration' => 'nullable|string|max:255',
            'expiration_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        ImmunizationRecords::create([
            'patient_id' => $request->patient_id,
            'vaccine_name' => $request->vaccine_name,
            'vaccine_type' => $request->vaccine_type,
            'administered_by' => Auth::user()->name . ' (Staff)',
            'dosage' => $request->dosage,
            'site_of_administration' => $request->site_of_administration,
            'expiration_date' => $request->expiration_date,
            'notes' => $request->notes,
        ]);
    }

    /**
     * Edit Health Record
     */
    public function editHealthRecord($id)
    {
        $record = HealthRecords::findOrFail($id);
        return view('staff.edit-health-record', compact('record'));
    }

    /**
     * Update Health Record
     */
    public function updateHealthRecord(Request $request, $id)
    {
        $record = HealthRecords::findOrFail($id);

        $request->validate([
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'symptoms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $record->update($request->only(['diagnosis', 'treatment', 'symptoms', 'notes']));

        return redirect()->route('staff.health-records')
            ->with('success', 'Health record updated successfully.');
    }

    /**
     * Edit Physical Examination
     */
    public function editPhysicalExam($id)
    {
        $exam = PhysicalExamination::with(['patient.user'])->findOrFail($id);
        return view('staff.edit-physical-exam', compact('exam'));
    }

    /**
     * Update Physical Examination
     */
    public function updatePhysicalExam(Request $request, $id)
    {
        $exam = PhysicalExamination::findOrFail($id);

        $request->validate([
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'bp' => 'nullable|string|max:20',
            'heart' => 'nullable|string|max:255',
            'lungs' => 'nullable|string|max:255',
            'eyes' => 'nullable|string|max:255',
            'ears' => 'nullable|string|max:255',
            'nose' => 'nullable|string|max:255',
            'throat' => 'nullable|string|max:255',
            'skin' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $exam->update($request->only(['height', 'weight', 'bp', 'heart', 'lungs', 'eyes', 'ears', 'nose', 'throat', 'skin', 'remarks', 'doctor_id']));

        return redirect()->route('staff.health-records')
            ->with('success', 'Physical examination updated successfully.');
    }

    /**
     * Edit Dental Examination
     */
    public function editDentalExam($id)
    {
        $exam = DentalExamination::with(['patient.user', 'doctor'])->findOrFail($id);
        return view('staff.edit-dental-exam', compact('exam'));
    }

    /**
     * Update Dental Examination
     */
    public function updateDentalExam(Request $request, $id)
    {
        $exam = DentalExamination::findOrFail($id);

        $request->validate([
            'diagnosis' => 'nullable|string',
            'teeth_status' => 'nullable|array',
            'doctor_id' => 'nullable|exists:doctors,id',
        ]);

        $teethStatus = $request->teeth_status;
        // Form sends flat array teeth_status[1], teeth_status[2], etc.
        if (is_array($teethStatus)) {
            $flatTeethStatus = [];
            foreach ($teethStatus as $key => $status) {
                if (is_numeric($key)) {
                    $flatTeethStatus[(int)$key] = $status;
                }
            }
            $teethStatus = $flatTeethStatus;
        }

        $exam->update(array_filter([
            'diagnosis' => $request->diagnosis,
            'teeth_status' => $teethStatus ?? [],
            'doctor_id' => $request->doctor_id,
        ], fn($v) => $v !== null));

        return redirect()->route('staff.health-records')
            ->with('success', 'Dental examination updated successfully.');
    }

    /**
     * Edit Immunization Record
     */
    public function editImmunization($id)
    {
        $record = ImmunizationRecords::with(['patient.user'])->findOrFail($id);
        return view('staff.edit-immunization', compact('record'));
    }

    /**
     * Update Immunization Record
     */
    public function updateImmunization(Request $request, $id)
    {
        $record = ImmunizationRecords::findOrFail($id);

        $request->validate([
            'vaccine_name' => 'required|string',
            'vaccine_type' => 'nullable|string',
            'dosage' => 'nullable|string',
            'site_of_administration' => 'nullable|string',
            'expiration_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $record->update($request->only(['vaccine_name', 'vaccine_type', 'dosage', 'site_of_administration', 'expiration_date', 'notes']));

        return redirect()->route('staff.health-records')
            ->with('success', 'Immunization record updated successfully.');
    }

    /**
     * Show report generation page
     */
    public function showReportGeneration()
    {
        $savedReports = GeneratedReport::where('generated_by_type', 'staff')
            ->where('generated_by_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('staff.generate-report', compact('savedReports'));
    }

    /**
     * Generate report based on parameters
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:patient_statistics,patient_education_levels,appointment_statistics,health_records,prescriptions,medicine_inventory',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'title' => 'required|string|max:255',
        ]);

        $reportType = $request->report_type;
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->subYear();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now();

        $reportData = $this->compileReportData($reportType, $dateFrom, $dateTo);

        // Save report to database
        $report = GeneratedReport::create([
            'title' => $request->title,
            'report_type' => $reportType,
            'description' => $request->description,
            'parameters' => [
                'date_from' => $dateFrom->toDateString(),
                'date_to' => $dateTo->toDateString(),
            ],
            'generated_by_type' => 'staff',
            'generated_by_id' => Auth::id(),
            'status' => 'completed',
        ]);

        return view('staff.view-report', compact('report', 'reportData', 'dateFrom', 'dateTo'));
    }

    /**
     * View existing report
     */
    public function viewReport($id)
    {
        $report = GeneratedReport::findOrFail($id);

        // Check authorization
        if ($report->generated_by_id !== Auth::id() || $report->generated_by_type !== 'staff') {
            abort(403, 'Unauthorized access to this report.');
        }

        $parameters = is_array($report->parameters) ? $report->parameters : [];
        $dateFrom = Carbon::parse($parameters['date_from'] ?? optional($report->created_at)->copy()?->subYear() ?? now()->subYear());
        $dateTo = Carbon::parse($parameters['date_to'] ?? $report->created_at ?? now());

        $reportData = $this->compileReportData($report->report_type, $dateFrom, $dateTo);

        return view('staff.view-report', compact('report', 'reportData', 'dateFrom', 'dateTo'));
    }

    /**
     * Delete report
     */
    public function deleteReport($id)
    {
        $report = GeneratedReport::findOrFail($id);

        // Check authorization
        if ($report->generated_by_id !== Auth::id() || $report->generated_by_type !== 'staff') {
            abort(403, 'Unauthorized to delete this report.');
        }

        $report->delete();

        return redirect()->route('staff.reports.generate')
            ->with('success', 'Report deleted successfully.');
    }

    /**
     * Export report as PDF
     */
    public function exportReport($id, $format = 'pdf')
    {
        $report = GeneratedReport::findOrFail($id);

        // Check authorization
        if ($report->generated_by_id !== Auth::id() || $report->generated_by_type !== 'staff') {
            abort(403, 'Unauthorized to export this report.');
        }

        $parameters = is_array($report->parameters) ? $report->parameters : [];
        $dateFrom = Carbon::parse($parameters['date_from'] ?? optional($report->created_at)->copy()?->subYear() ?? now()->subYear());
        $dateTo = Carbon::parse($parameters['date_to'] ?? $report->created_at ?? now());

        $reportData = $this->compileReportData($report->report_type, $dateFrom, $dateTo);

        if ($format === 'csv') {
            return $this->exportAsCSV($report, $reportData);
        }

        // For now, return a simple download view
        // You can integrate a PDF library like DomPDF or mPDF later
        return view('staff.export-report', compact('report', 'reportData', 'dateFrom', 'dateTo'));
    }

    /**
     * Compile report data based on type
     */
    private function compileReportData($reportType, $dateFrom, $dateTo)
    {
        switch ($reportType) {
            case 'patient_statistics':
                return $this->compilePatientStatistics($dateFrom, $dateTo);
            case 'patient_education_levels':
                return $this->compilePatientEducationLevelReport($dateFrom, $dateTo);
            case 'appointment_statistics':
                return $this->compileAppointmentStatistics($dateFrom, $dateTo);
            case 'health_records':
                return $this->compileHealthRecordsReport($dateFrom, $dateTo);
            case 'prescriptions':
                return $this->compilePrescriptionsReport($dateFrom, $dateTo);
            case 'medicine_inventory':
                return $this->compileMedicineInventoryReport();
            default:
                return [];
        }
    }

    private function compilePatientStatistics($dateFrom, $dateTo)
    {
        return [
            'total_patients' => Patients::whereHas('user', function($q) {
                $q->whereIn('user_type', [1, 2]);
            })->count(),
            'new_patients' => Patients::whereBetween('created_at', [$dateFrom, $dateTo])
                ->whereHas('user', function($q) {
                    $q->whereIn('user_type', [1, 2]);
                })->count(),
            'gender_distribution' => Patients::join('users', 'patients.user_id', '=', 'users.id')
                ->selectRaw('users.gender, COUNT(*) as count')
                ->whereIn('users.user_type', [1, 2])
                ->groupBy('users.gender')
                ->get(),
            'by_educational_level' => Patients::join('educational_level', 'patients.edulvl_id', '=', 'educational_level.id')
                ->join('users', 'patients.user_id', '=', 'users.id')
                ->selectRaw('educational_level.level_name, COUNT(*) as count')
                ->whereIn('users.user_type', [1, 2])
                ->groupBy('educational_level.level_name')
                ->get(),
            // Detailed patient list
            'recent_patients' => Patients::with(['user', 'educationalLevel'])
                ->whereHas('user', function($q) {
                    $q->whereIn('user_type', [1, 2]);
                })
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get(),
            'all_patients' => Patients::with(['user', 'educationalLevel'])
                ->whereHas('user', function($q) {
                    $q->whereIn('user_type', [1, 2]);
                })
                ->orderBy('created_at', 'desc')
                ->take(100)
                ->get(),
        ];
    }

    private function compilePatientEducationLevelReport($dateFrom, $dateTo)
    {
        return [
            'total_patients' => Patients::whereHas('user', function($q) {
                    $q->whereIn('user_type', [1, 2]);
                })->count(),
            'by_educational_level' => Patients::join('educational_level', 'patients.edulvl_id', '=', 'educational_level.id')
                ->join('users', 'patients.user_id', '=', 'users.id')
                ->selectRaw('educational_level.level_name, COUNT(*) as count')
                ->whereIn('users.user_type', [1, 2])
                ->groupBy('educational_level.level_name')
                ->orderBy('count', 'desc')
                ->get(),
        ];
    }

    private function compileAppointmentStatistics($dateFrom, $dateTo)
    {
        return [
            'total_appointments' => Appointment::whereBetween('date', [$dateFrom, $dateTo])->count(),
            'by_status' => Appointment::selectRaw('status, COUNT(*) as count')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->groupBy('status')
                ->get(),
            'by_month' => Appointment::selectRaw(SqlDateExpressions::yearMonth('date').' as month, COUNT(*) as count')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            'pending' => Appointment::where('status', Appointment::STATUS_PENDING)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->count(),
            'completed' => Appointment::where('status', 'Completed')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->count(),
            'cancelled' => Appointment::where('status', 'Cancelled')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->count(),
            // Detailed appointment lists
            'recent_appointments' => Appointment::with(['patient.user', 'doctor.user'])
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->orderBy('date', 'desc')
                ->orderBy('time', 'desc')
                ->take(50)
                ->get(),
            'pending_appointments' => Appointment::with(['patient.user', 'doctor.user'])
                ->where('status', 'Pending')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->orderBy('date', 'asc')
                ->take(50)
                ->get(),
            'completed_appointments' => Appointment::with(['patient.user', 'doctor.user'])
                ->where('status', 'Completed')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->orderBy('date', 'desc')
                ->take(50)
                ->get(),
            'cancelled_appointments' => Appointment::with(['patient.user', 'doctor.user'])
                ->where('status', 'Cancelled')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->orderBy('date', 'desc')
                ->take(50)
                ->get(),
        ];
    }

    private function compileHealthRecordsReport($dateFrom, $dateTo)
    {
        $healthRecordsCount = HealthRecords::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $physicalExamsCount = PhysicalExamination::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $dentalExamsCount = DentalExamination::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $immunizationsCount = ImmunizationRecords::whereBetween('created_at', [$dateFrom, $dateTo])->count();

        return [
            'total_records' => $healthRecordsCount + $physicalExamsCount + $dentalExamsCount + $immunizationsCount,
            'physical_exams' => $physicalExamsCount,
            'dental_exams' => $dentalExamsCount,
            'immunizations' => $immunizationsCount,
            // Detailed records with relationships
            'recent_records' => HealthRecords::with(['patient.user'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get(),
            // Physical examinations detail
            'physical_examinations' => PhysicalExamination::with(['patient.user', 'doctor.user'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get(),
            // Dental examinations detail
            'dental_examinations' => DentalExamination::with(['patient.user', 'doctor.user'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get(),
            // Immunization records detail
            'immunization_records' => ImmunizationRecords::with(['patient.user'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get(),
            // Top diagnoses
            'top_diagnoses' => (function() use ($dateFrom, $dateTo) {
                return HealthRecords::whereNotNull('diagnosis')
                    ->whereBetween('created_at', [$dateFrom, $dateTo])
                    ->get()
                    ->pluck('diagnosis')
                    ->map(function($d) { return trim($d); })
                    ->filter()
                    ->countBy()
                    ->map(function($count, $diagnosis) {
                        return (object)['diagnosis' => $diagnosis, 'count' => $count];
                    })
                    ->sortByDesc(function($item) { return $item->count; })
                    ->values()
                    ->slice(0, 20);
            })(),
        ];
    }

    private function compilePrescriptionsReport($dateFrom, $dateTo)
    {
        return [
            'total_prescriptions' => PrescriptionRecord::whereBetween('date_prescribed', [$dateFrom, $dateTo])->count(),
            'by_status' => PrescriptionRecord::selectRaw('status, COUNT(*) as count')
                ->whereBetween('date_prescribed', [$dateFrom, $dateTo])
                ->groupBy('status')
                ->get(),
            'most_prescribed' => PrescriptionRecord::join('medicine', 'prescription_records.medicine_id', '=', 'medicine.id')
                ->selectRaw('medicine.name, COUNT(*) as count')
                ->whereBetween('prescription_records.date_prescribed', [$dateFrom, $dateTo])
                ->groupBy('medicine.name')
                ->orderByDesc('count')
                ->take(10)
                ->get(),
            'recent_prescriptions' => PrescriptionRecord::with(['patient.user', 'medicine', 'doctor.user'])
                ->whereBetween('date_prescribed', [$dateFrom, $dateTo])
                ->orderBy('date_prescribed', 'desc')
                ->take(50)
                ->get(),
        ];
    }

    private function compileMedicineInventoryReport()
    {
        return [
            'total_medicines' => Medicine::count(),
            'in_stock' => Medicine::where('quantity', '>', 10)->count(),
            'low_stock' => Medicine::where('quantity', '>', 0)->where('quantity', '<=', 10)->count(),
            'out_of_stock' => Medicine::where('quantity', '<=', 0)->count(),
            'expired' => Medicine::where('expiration_date', '<', Carbon::now())->count(),
            'expiring_soon' => Medicine::where('expiration_date', '>', Carbon::now())
                ->where('expiration_date', '<=', Carbon::now()->addMonths(3))
                ->count(),
            'low_stock_items' => Medicine::where('quantity', '>', 0)
                ->where('quantity', '<=', 10)
                ->orderBy('quantity')
                ->get(),
            'expired_items' => Medicine::where('expiration_date', '<', Carbon::now())
                ->orderBy('expiration_date')
                ->get(),
            'all_medicines' => Medicine::orderBy('name')->get(),
        ];
    }

    private function exportAsCSV($report, $reportData)
    {
        $filename = str_replace(' ', '_', $report->title) . '_' . now()->format('YmdHis') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($reportData, $report) {
            $file = fopen('php://output', 'w');

            // Add report header
            fputcsv($file, ['Report Title', $report->title]);
            fputcsv($file, ['Report Type', str_replace('_', ' ', ucwords($report->report_type))]);
            fputcsv($file, ['Generated On', $report->created_at->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Date Range', ($report->parameters['date_from'] ?? 'All available') . ' to ' . ($report->parameters['date_to'] ?? 'current')]);
            fputcsv($file, []);

            // Add data based on report type
            if ($report->report_type === 'appointment_statistics') {
                fputcsv($file, ['Summary']);
                fputcsv($file, ['Total Appointments', $reportData['total_appointments'] ?? 0]);
                fputcsv($file, ['Pending', $reportData['pending'] ?? 0]);
                fputcsv($file, ['Completed', $reportData['completed'] ?? 0]);
                fputcsv($file, ['Cancelled', $reportData['cancelled'] ?? 0]);
                fputcsv($file, []);
                fputcsv($file, ['Detailed Appointments']);
                fputcsv($file, ['Date', 'Time', 'Patient', 'Doctor', 'Status', 'Reason']);
                foreach ($reportData['recent_appointments'] ?? [] as $appt) {
                    fputcsv($file, [
                        $appt->date ?? 'N/A',
                        $appt->time ?? 'N/A',
                        $appt->patient->user->name ?? 'N/A',
                        $appt->doctor->user->name ?? 'N/A',
                        $appt->status ?? 'N/A',
                        $appt->reason ?? 'N/A'
                    ]);
                }
            } elseif ($report->report_type === 'health_records_summary') {
                fputcsv($file, ['Summary']);
                fputcsv($file, ['Total Records', $reportData['total_records'] ?? 0]);
                fputcsv($file, ['Physical Exams', $reportData['physical_exams'] ?? 0]);
                fputcsv($file, ['Dental Exams', $reportData['dental_exams'] ?? 0]);
                fputcsv($file, ['Immunizations', $reportData['immunizations'] ?? 0]);
                fputcsv($file, []);

                // Health Records Detail
                fputcsv($file, ['Health Records Detail']);
                fputcsv($file, ['Date', 'Patient', 'Diagnosis', 'Symptoms', 'Treatment']);
                foreach ($reportData['recent_records'] ?? [] as $record) {
                    fputcsv($file, [
                        $record->created_at->format('Y-m-d') ?? 'N/A',
                        $record->patient->user->name ?? 'N/A',
                        $record->diagnosis ?? 'N/A',
                        $record->symptoms ?? 'N/A',
                        $record->treatment ?? 'N/A'
                    ]);
                }
                fputcsv($file, []);

                // Physical Examinations Detail
                fputcsv($file, ['Physical Examinations Detail']);
                fputcsv($file, ['Date', 'Patient', 'Height', 'Weight', 'BP', 'Heart', 'Remarks']);
                foreach ($reportData['physical_examinations'] ?? [] as $exam) {
                    fputcsv($file, [
                        $exam->created_at->format('Y-m-d') ?? 'N/A',
                        $exam->patient->user->name ?? 'N/A',
                        $exam->height ?? 'N/A',
                        $exam->weight ?? 'N/A',
                        $exam->bp ?? 'N/A',
                        $exam->heart ?? 'N/A',
                        $exam->remarks ?? 'N/A'
                    ]);
                }
                fputcsv($file, []);

                // Dental Examinations Detail
                fputcsv($file, ['Dental Examinations Detail']);
                fputcsv($file, ['Date', 'Patient', 'Doctor', 'Diagnosis']);
                foreach ($reportData['dental_examinations'] ?? [] as $exam) {
                    fputcsv($file, [
                        $exam->created_at->format('Y-m-d') ?? 'N/A',
                        $exam->patient->user->name ?? 'N/A',
                        $exam->doctor->user->name ?? 'N/A',
                        $exam->diagnosis ?? 'N/A'
                    ]);
                }
                fputcsv($file, []);

                // Immunization Records Detail
                fputcsv($file, ['Immunization Records Detail']);
                fputcsv($file, ['Date', 'Patient', 'Vaccine', 'Dosage', 'Administered By']);
                foreach ($reportData['immunization_records'] ?? [] as $record) {
                    fputcsv($file, [
                        $record->created_at->format('Y-m-d') ?? 'N/A',
                        $record->patient->user->name ?? 'N/A',
                        $record->vaccine_name ?? 'N/A',
                        $record->dosage ?? 'N/A',
                        $record->administered_by ?? 'N/A'
                    ]);
                }
            } elseif ($report->report_type === 'prescriptions_report') {
                fputcsv($file, ['Summary']);
                fputcsv($file, ['Total Prescriptions', $reportData['total_prescriptions'] ?? 0]);
                fputcsv($file, []);

                // Prescriptions by Status
                fputcsv($file, ['Prescriptions by Status']);
                fputcsv($file, ['Status', 'Count']);
                foreach ($reportData['by_status'] ?? [] as $item) {
                    fputcsv($file, [$item->status ?? 'N/A', $item->count ?? 0]);
                }
                fputcsv($file, []);

                // Recent Prescriptions Detail
                fputcsv($file, ['Recent Prescriptions Detail']);
                fputcsv($file, ['Date', 'Patient', 'Doctor', 'Medicine', 'Dosage', 'Status']);
                foreach ($reportData['recent_prescriptions'] ?? [] as $prescription) {
                    fputcsv($file, [
                        $prescription->date_prescribed ?? 'N/A',
                        $prescription->patient->user->name ?? 'N/A',
                        ($prescription->doctor->user->name ?? 'N/A'),
                        $prescription->medicine->name ?? 'N/A',
                        $prescription->dosage ?? 'N/A',
                        $prescription->status ?? 'N/A'
                    ]);
                }
            } else {
                foreach ($reportData as $key => $value) {
                    if (is_array($value) || is_object($value)) {
                        fputcsv($file, [ucwords(str_replace('_', ' ', $key))]);

                        if ($value instanceof \Illuminate\Support\Collection) {
                            $items = $value->toArray();
                            if (count($items) > 0) {
                                fputcsv($file, array_keys((array)$items[0]));
                                foreach ($items as $item) {
                                    fputcsv($file, (array)$item);
                                }
                            }
                        }
                        fputcsv($file, []);
                    } else {
                        fputcsv($file, [ucwords(str_replace('_', ' ', $key)), $value]);
                    }
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display certificate requests for staff to review
     */
    public function certificateRequests(Request $request)
    {
        $user = Auth::user();

        // Build query for certificate requests
        $query = CertificateRequest::query()
            ->with(['patient.user', 'certificateType']);

        // Filter by status (show all if no filter, or filter by specific status)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // No default filter - show all certificates

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient.user', function($q) use ($search) {
                $q->where('firstname', 'like', '%' . $search . '%')
                  ->orWhere('lastname', 'like', '%' . $search . '%');
            });
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get counts for tabs
        $pendingCount = CertificateRequest::where('status', 'pending')->count();
        $approvedCount = CertificateRequest::where('status', 'approved')->count();
        $issuedCount = CertificateRequest::where('status', 'issued')->count();
        $rejectedCount = CertificateRequest::where('status', 'rejected')->count();

        return view('staff.certificate-requests', compact(
            'requests',
            'pendingCount',
            'approvedCount',
            'issuedCount',
            'rejectedCount'
        ));
    }

    /**
     * Show details of a certificate request
     */
    public function showCertificateRequest($id)
    {
        $request = CertificateRequest::with(['patient.user', 'certificateType', 'appointment'])
            ->findOrFail($id);

        return view('staff.certificate-request-detail', compact('request'));
    }

    /**
     * Approve a certificate request
     */
    public function approveCertificateRequest(Request $request, $id)
    {
        return redirect()->back()->with('error', 'Staff cannot approve certificate requests. Only doctors may perform approval actions.');
    }

    /**
     * Reject a certificate request
     */
    public function rejectCertificateRequest(Request $request, $id)
    {
        return redirect()->back()->with('error', 'Staff cannot reject certificate requests. Only doctors may perform rejection actions.');
    }

    /**
     * Mark a certificate as issued
     */
    public function issueCertificateRequest($id)
    {
        $certificateRequest = CertificateRequest::findOrFail($id);

        if ($certificateRequest->status !== CertificateRequest::STATUS_APPROVED) {
            return redirect()->back()->with('error', 'Only approved certificates can be marked as issued.');
        }

        $certificateRequest->update([
            'status' => CertificateRequest::STATUS_ISSUED,
            'issued_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Certificate marked as issued.');
    }

    /**
     * Print a certificate
     */
    public function printCertificate($id)
    {
        $certificate = CertificateRequest::with(['patient.user', 'certificateType', 'doctor.user', 'appointment'])
            ->findOrFail($id);

        // Only allow printing if certificate is approved or issued
        if (!in_array($certificate->status, [CertificateRequest::STATUS_APPROVED, CertificateRequest::STATUS_ISSUED])) {
            return redirect()->back()->with('error', 'Certificate must be approved before printing.');
        }

        return view('staff.certificate-print', compact('certificate'));
    }
}
