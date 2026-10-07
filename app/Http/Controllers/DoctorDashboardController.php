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
use App\Models\CertificateRequest;
use App\Models\GeneratedReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Support\SqlDateExpressions;
use Carbon\Carbon;

class DoctorDashboardController extends Controller
{
    /**
     * Display doctor dashboard with statistics and recent activities
     */
    public function index()
    {
        $doctor = Auth::user()->doctor()->first();

        if (! $doctor) {
            return redirect()->route('doctor.profile.show')->with('error', 'Doctor profile not found.');
        }

        return view('doctor.dashboard', array_merge(
            ['doctor' => $doctor],
            $this->doctorDashboardData($doctor->id)
        ));
    }

    // AJAX Tab Methods for Shell Navigation
    public function ajaxDashboard()
    {
        $doctor = Auth::user()->doctor()->first();

        if (! $doctor) {
            return '<div class="p-4 text-red-600">Doctor profile not found.</div>';
        }

        return view('doctor.partials.dashboard', array_merge(
            ['doctor' => $doctor],
            $this->doctorDashboardData($doctor->id)
        ));
    }

    public function ajaxAppointments()
    {
        return view('doctor.partials.appointments');
    }

    public function ajaxPatients()
    {
        return view('doctor.partials.patients');
    }

    public function ajaxHealthRecords()
    {
        return view('doctor.partials.health-records');
    }

    public function ajaxMedications()
    {
        return view('doctor.partials.medications');
    }

    public function ajaxPrescriptions()
    {
        return view('doctor.partials.prescriptions');
    }

    public function ajaxReports()
    {
        return view('doctor.partials.reports');
    }

    /**
     * Show form for creating a new medicine
     */
    public function createMedicine()
    {
        return view('doctor.medications.create');
    }

    /**
     * Show form for creating a new prescription record
     */
    public function createPrescriptionRecord()
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();
        $patients = $doctor ? Patients::whereHas('appointments', function($query) use ($doctor) {
            $query->where('doc_id', $doctor->id);
        })->with('user')->get() : collect();
        $medicines = Medicine::where('quantity', '>', 0)->get();
        return view('doctor.prescriptions.create', compact('patients', 'medicines'));
    }

    /**
     * Display appointment management for doctors
     */
    public function appointments()
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->with('error', 'Doctor profile not found.');
        }

        // Get pending appointments
        $pendingAppointments = Appointment::where('doc_id', $doctor->id)
            ->where('status', Appointment::STATUS_PENDING)
            ->with(['patient.user'])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();

        // Get all active appointments for calendar (confirmed and pending)
        $calendarAppointments = Appointment::where('doc_id', $doctor->id)
            ->whereIn('status', [Appointment::STATUS_PENDING, Appointment::STATUS_CONFIRMED])
            ->with(['patient.user'])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();

        // Prepare calendar data - group appointments by date
        $calendarData = [];
        foreach ($calendarAppointments as $appointment) {
            $date = Carbon::parse($appointment->date)->format('Y-m-d');
            if (!isset($calendarData[$date])) {
                $calendarData[$date] = [];
            }
            $calendarData[$date][] = [
                'id' => $appointment->id,
                'patient_name' => $appointment->patient->user->name ?? 'N/A',
                'time' => Carbon::parse($appointment->time)->format('g:i A'),
                'status' => $appointment->status,
                'patient_id' => $appointment->patient_id
            ];
        }

        // Get current month and year for calendar display
        $currentMonth = Carbon::now()->format('Y-m');
        $currentDate = Carbon::now();

        // Get all appointments for the table view
        $appointments = Appointment::where('doc_id', $doctor->id)
            ->with(['patient.user'])
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->paginate(15);

        return view('doctor.appointments', compact(
            'appointments',
            'doctor',
            'pendingAppointments',
            'calendarAppointments',
            'calendarData',
            'currentMonth',
            'currentDate'
        ));
    }

    /**
     * Update appointment status
     */
    public function updateAppointmentStatus(Request $request, $appointmentId)
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return back()->with('error', 'Unauthorized action.');
        }

        $appointment = Appointment::where('id', $appointmentId)
            ->where('doc_id', $doctor->id)
            ->first();

        if (!$appointment) {
            return back()->with('error', 'Appointment not found.');
        }

        $request->validate([
            'status' => 'required|in:Pending,Confirmed,Completed,Cancelled'
        ]);

        $appointment->update(['status' => $request->status]);

        return redirect()->route('doctor.dashboard')->with('success', 'Appointment ' . strtolower($request->status) . ' successfully.');
    }

    /**
     * Display doctor's patients
     */
    public function patients(Request $request)
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->with('error', 'Doctor profile not found.');
        }

        // Get patients who have appointments with this doctor
        $query = Patients::whereHas('appointments', function($query) use ($doctor) {
                $query->where('doc_id', $doctor->id);
            })
            ->with(['user', 'appointments' => function($query) use ($doctor) {
                $query->where('doc_id', $doctor->id)->latest();
            }]);

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

        return view('doctor.patients', compact('patients', 'doctor'));
    }

    /**
     * View individual patient record
     */
    public function viewPatient($patientId)
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->with('error', 'Doctor profile not found.');
        }

        $patient = Patients::with('user')->findOrFail($patientId);

        // Verify doctor has treated this patient
        $hasAppointment = Appointment::where('doc_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->exists();

        if (!$hasAppointment) {
            return back()->with('error', 'You can only view patients you have treated.');
        }

        // Get patient's medical records
        $healthRecords = HealthRecords::where('user_id', $patient->user_id)->get();
        $prescriptions = PrescriptionRecord::where('patient_id', $patient->id)
            ->with('medicine')
            ->get();
        $immunizations = ImmunizationRecords::where('patient_id', $patient->id)->get();
        $physicalExams = PhysicalExamination::where('patient_id', $patient->id)
            ->with(['patient.user', 'doctor'])
            ->get();
        $dentalExams = DentalExamination::where('patient_id', $patient->id)->get();

        $appointments = Appointment::where('doc_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->orderBy('date', 'desc')
            ->get();

        return view('doctor.patient-details', compact(
            'patient',
            'doctor',
            'healthRecords',
            'prescriptions',
            'immunizations',
            'physicalExams',
            'dentalExams',
            'appointments'
        ));
    }

    /**
     * Update patient information
     */
    public function updatePatient(Request $request, $patientId)
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->with('error', 'Doctor profile not found.');
        }

        // Check if doctor has an appointment with this patient
        $hasAppointment = Appointment::where('doc_id', $doctor->id)
            ->where('patient_id', $patientId)
            ->exists();

        if (!$hasAppointment) {
            return back()->with('error', 'You can only update patients you have treated.');
        }

        $patient = Patients::findOrFail($patientId);

        $request->validate([
            'bloodtype' => 'nullable|string|max:10',
        ]);

        $patient->update([
            'bloodtype' => $request->bloodtype,
        ]);

        return back()->with('success', 'Patient information updated successfully.');
    }

    /**
     * Display medication management
     */
    public function medications(Request $request)
    {
        $doctor = Auth::user()->doctor()->first();

        if (! $doctor) {
            return redirect()->route('doctor.profile.show')->with('error', 'Doctor profile not found.');
        }

        $search = trim((string) $request->query('search', ''));
        $medicines = Medicine::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('generic_name', 'like', "%{$search}%")
                        ->orWhere('manufacturer', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $stats = Medicine::query()->selectRaw(
            'COUNT(*) AS total,
             SUM(CASE WHEN quantity >= 10 AND (expiration_date IS NULL OR expiration_date > ?) THEN 1 ELSE 0 END) AS available,
             SUM(CASE WHEN quantity < 10 THEN 1 ELSE 0 END) AS low_stock,
             SUM(CASE WHEN expiration_date IS NOT NULL AND expiration_date < ? THEN 1 ELSE 0 END) AS expired',
            [now()->toDateString(), now()->toDateString()]
        )->first();

        return view('doctor.medicines', [
            'medicines' => $medicines,
            'doctor' => $doctor,
            'totalMedicines' => (int) ($stats->total ?? 0),
            'availableMedicines' => (int) ($stats->available ?? 0),
            'lowStockMedicines' => (int) ($stats->low_stock ?? 0),
            'expiredMedicines' => (int) ($stats->expired ?? 0),
        ]);
    }

    /**
     * Display reports and statistics for doctor
     */
    public function reports()
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->with('error', 'Doctor profile not found.');
        }

        // Generate various statistics
        $appointmentStats = [
            'total' => Appointment::where('doc_id', $doctor->id)->count(),
            'this_month' => Appointment::where('doc_id', $doctor->id)
                ->whereMonth('date', Carbon::now()->month)
                ->whereYear('date', Carbon::now()->year)
                ->count(),
            'pending' => Appointment::where('doc_id', $doctor->id)
                ->where('status', 'Pending')
                ->count(),
            'completed' => Appointment::where('doc_id', $doctor->id)
                ->where('status', 'Completed')
                ->count(),
        ];

        $patientStats = [
            'total_patients' => Appointment::where('doc_id', $doctor->id)
                ->distinct('patient_id')
                ->count(),
            'new_this_month' => Appointment::where('doc_id', $doctor->id)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->distinct('patient_id')
                ->count(),
        ];

        // Monthly appointment trends
        $monthlyTrends = Appointment::where('doc_id', $doctor->id)
            ->selectRaw(SqlDateExpressions::monthNumber('date').' as month, '.SqlDateExpressions::yearNumber('date').' as year, COUNT(*) as count')
            ->whereYear('date', '>=', Carbon::now()->subYear()->year)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Top diagnoses/conditions
        $topConditions = HealthRecords::whereHas('patient.appointments', function($query) use ($doctor) {
                $query->where('doc_id', $doctor->id);
            })
            ->whereNotNull('diagnosis')
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
            ->slice(0, 10);

        return view('doctor.reports', compact(
            'doctor',
            'appointmentStats',
            'patientStats',
            'monthlyTrends',
            'topConditions'
        ));
    }

    public function healthRecords()
    {
        $doctor = Doctors::where('user_id', Auth::id())->first();

        if (!$doctor) {
            return redirect()->back()->with('error', 'Doctor profile not found. Please contact administrator.');
        }

        // Get all health records for patients who have appointments with this doctor
        $healthRecords = HealthRecords::whereHas('patient.appointments', function($query) use ($doctor) {
            $query->where('doc_id', $doctor->id);
        })
        ->whereNotNull('patient_id')
        ->with(['patient' => function($query) {
            $query->whereNotNull('user_id')->with('user');
        }, 'patient.appointments' => function($query) use ($doctor) {
            $query->where('doc_id', $doctor->id)->latest();
        }])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        // Get physical examinations
        $physicalExaminations = PhysicalExamination::where('doctor_id', $doctor->id)
            ->with(['patient.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'physical_page');

        // Get dental examinations
        $dentalExaminations = DentalExamination::where('doctor_id', $doctor->id)
            ->with(['patient.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'dental_page');

        // Get immunization records for patients treated by this doctor
        $immunizationRecords = ImmunizationRecords::whereHas('patient.appointments', function($query) use ($doctor) {
            $query->where('doc_id', $doctor->id);
        })
        ->with(['patient.user'])
        ->orderBy('created_at', 'desc')
        ->paginate(10, ['*'], 'immunization_page');

        // Get summary statistics
        $totalHealthRecords = $healthRecords->total();
        $totalPhysicalExams = PhysicalExamination::where('doctor_id', $doctor->id)->count();
        $totalDentalExams = DentalExamination::where('doctor_id', $doctor->id)->count();
        $totalImmunizations = ImmunizationRecords::whereHas('patient.appointments', function($query) use ($doctor) {
            $query->where('doc_id', $doctor->id);
        })->count();
        $totalPrescriptions = PrescriptionRecord::where('doctor_id', $doctor->id)->count();

        $recentRecords = $totalHealthRecords + $totalPhysicalExams + $totalDentalExams + $totalImmunizations + $totalPrescriptions;

        // Get records by diagnosis for this doctor's patients
        $diagnosisStats = HealthRecords::whereHas('patient.appointments', function($query) use ($doctor) {
            $query->where('doc_id', $doctor->id);
        })
        ->whereNotNull('diagnosis')
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
        ->slice(0, 10);

        return view('doctor.health-records', compact(
            'doctor',
            'healthRecords',
            'physicalExaminations',
            'dentalExaminations',
            'immunizationRecords',
            'totalHealthRecords',
            'totalPhysicalExams',
            'totalDentalExams',
            'totalImmunizations',
            'totalPrescriptions',
            'recentRecords',
            'diagnosisStats'
        ));
    }

    /**
     * Print health record for a specific patient
     */
    public function printHealthRecord($patientId)
    {
        $doctor = Doctors::where('user_id', Auth::id())->first();

        if (!$doctor) {
            abort(403, 'Doctor profile not found');
        }

        // Verify patient has appointments with this doctor
        $patient = Patients::whereHas('appointments', function($query) use ($doctor) {
            $query->where('doc_id', $doctor->id);
        })
        ->where('id', $patientId)
        ->with(['user', 'healthRecords', 'physicalExaminations', 'dentalExaminations', 'immunizations'])
        ->first();

        if (!$patient) {
            abort(404, 'Patient not found or no appointments with you');
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
     * Show form for creating a new health record
     */
    public function createHealthRecord()
    {
        $doctor = Doctors::where('user_id', Auth::id())->first();

        if (!$doctor) {
            return redirect()->back()->with('error', 'Doctor profile not found. Please contact administrator.');
        }

        // Get patients who have appointments with this doctor
        $patients = Patients::whereHas('appointments', function($query) use ($doctor) {
            $query->where('doc_id', $doctor->id);
        })
        ->with('user')
        ->orderBy('id', 'desc')
        ->get();

        // Get available immunizations for dropdown
        $immunizations = Immunization::orderBy('name')->get();

        return view('doctor.health-records.create', compact('doctor', 'patients', 'immunizations'));
    }

    /**
     * Store a new health record
     */
    public function storeHealthRecord(Request $request)
    {
        $doctor = Doctors::where('user_id', Auth::id())->first();

        if (!$doctor) {
            return redirect()->back()->with('error', 'Doctor profile not found. Please contact administrator.');
        }

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'record_type' => 'required|in:physical,dental,immunization',
        ]);

        // Enforce allowed record types based on specialization
        $special = strtolower($doctor->specialization ?? '');

        if ($special === 'physician' && !in_array($request->record_type, ['physical', 'immunization'])) {
            return back()
                ->withErrors(['record_type' => 'As a physician, you may only create physical examinations or immunization records.'])
                ->withInput();
        }

        if ($special === 'dentist' && !in_array($request->record_type, ['dental', 'immunization'])) {
            return back()
                ->withErrors(['record_type' => 'As a dentist, you may only create dental examinations or immunization records.'])
                ->withInput();
        }

        // Verify doctor has access to this patient
        $hasAppointment = Appointment::where('doc_id', $doctor->id)
            ->where('patient_id', $request->patient_id)
            ->exists();

        if (!$hasAppointment) {
            return back()->with('error', 'You can only create records for patients you have treated.');
        }

        try {
            switch ($request->record_type) {
                case 'physical':
                    $this->createPhysicalExamination($request, $doctor);
                    break;
                case 'dental':
                    $this->createDentalExamination($request, $doctor);
                    break;
                case 'immunization':
                    $this->createImmunizationRecord($request, $doctor);
                    break;
            }

            return redirect()->route('doctor.health-records')
                ->with('success', 'Health record created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating health record: ' . $e->getMessage());
        }
    }

    /**
     * Create Physical Examination Record
     */
    private function createPhysicalExamination(Request $request, $doctor)
    {
        $request->validate([
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

        // Get the patient's user_id
        $patient = Patients::findOrFail($request->patient_id);

        PhysicalExamination::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
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
    private function createDentalExamination(Request $request, $doctor)
    {
        $request->validate([
            'diagnosis' => 'required|string',
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
            'doctor_id' => $doctor->id,
            'diagnosis' => $request->diagnosis,
            'teeth_status' => $teethStatus,
        ]);
    }

    /**
     * Create Immunization Record
     */
    private function createImmunizationRecord(Request $request, $doctor)
    {
        $request->validate([
            'vaccine_name' => 'required|string|max:255',
            'vaccine_type' => 'required|string|max:255',
            'dosage' => 'required|string|max:255',
            'site_of_administration' => 'required|string|max:255',
            'expiration_date' => 'required|date',
            'notes' => 'required|string',
        ]);

        ImmunizationRecords::create([
            'patient_id' => $request->patient_id,
            'vaccine_name' => $request->vaccine_name,
            'vaccine_type' => $request->vaccine_type,
            'administered_by' => $doctor->name,
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
        $doctor = Doctors::where('user_id', Auth::id())->first();

        if (!$doctor) {
            return redirect()->back()->with('error', 'Doctor profile not found.');
        }

        $record = HealthRecords::findOrFail($id);

        // Verify this record belongs to a patient the doctor has treated
        $hasAccess = Appointment::where('doc_id', $doctor->id)
            ->where('patient_id', $record->patient_id)
            ->exists();

        if (!$hasAccess) {
            return redirect()->back()->with('error', 'You do not have access to edit this record.');
        }

        return view('doctor.edit-health-record', compact('record', 'doctor'));
    }

    /**
     * Update Health Record
     */
    public function updateHealthRecord(Request $request, $id)
    {
        $doctor = Doctors::where('user_id', Auth::id())->first();

        $record = HealthRecords::findOrFail($id);

        $request->validate([
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'symptoms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $record->update([
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'symptoms' => $request->symptoms,
            'notes' => $request->notes,
        ]);

        return redirect()->route('doctor.health-records')
            ->with('success', 'Health record updated successfully.');
    }

    /**
     * Edit Physical Examination
     */
    public function editPhysicalExam($id)
    {
        $doctor = Doctors::where('user_id', Auth::id())->first();

        if (!$doctor) {
            return redirect()->back()->with('error', 'Doctor profile not found.');
        }

        $exam = PhysicalExamination::where('id', $id)
            ->where('doctor_id', $doctor->id)
            ->firstOrFail();

        return view('doctor.edit-physical-exam', compact('exam', 'doctor'));
    }

    /**
     * Update Physical Examination
     */
    public function updatePhysicalExam(Request $request, $id)
    {
        $doctor = Doctors::where('user_id', Auth::id())->first();

        $exam = PhysicalExamination::where('id', $id)
            ->where('doctor_id', $doctor->id)
            ->firstOrFail();

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

        $exam->update($request->only([
            'height', 'weight', 'bp', 'heart', 'lungs',
            'eyes', 'ears', 'nose', 'throat', 'skin', 'remarks'
        ]));

        return redirect()->route('doctor.health-records')
            ->with('success', 'Physical examination updated successfully.');
    }

    /**
     * Edit Dental Examination
     */
    public function editDentalExam($id)
    {
        $doctor = Doctors::where('user_id', Auth::id())->first();

        if (!$doctor) {
            return redirect()->back()->with('error', 'Doctor profile not found.');
        }

        $exam = DentalExamination::where('id', $id)
            ->where('doctor_id', $doctor->id)
            ->firstOrFail();

        return view('doctor.edit-dental-exam', compact('exam', 'doctor'));
    }

    /**
     * Update Dental Examination
     */
    public function updateDentalExam(Request $request, $id)
    {
        $doctor = Doctors::where('user_id', Auth::id())->first();

        $exam = DentalExamination::where('id', $id)
            ->where('doctor_id', $doctor->id)
            ->firstOrFail();

        $validated = $request->validate([
            'diagnosis' => 'sometimes|nullable|string|max:255',
            'teeth_status' => 'sometimes|nullable|array',
        ]);

        $updates = [];
        if (array_key_exists('diagnosis', $validated)) {
            $updates['diagnosis'] = $validated['diagnosis'] ?? '';
        }
        if (array_key_exists('teeth_status', $validated)) {
            $updates['teeth_status'] = $validated['teeth_status'] ?? [];
        }

        if ($updates !== []) {
            $exam->update($updates);
        }

        return redirect()->route('doctor.health-records')
            ->with('success', 'Dental examination updated successfully.');
    }

    /**
     * Edit Immunization Record
     */
    public function editImmunization($id)
    {
        $doctor = Doctors::where('user_id', Auth::id())->first();

        if (!$doctor) {
            return redirect()->back()->with('error', 'Doctor profile not found.');
        }

        $record = ImmunizationRecords::findOrFail($id);

        // Verify access
        $hasAccess = Appointment::where('doc_id', $doctor->id)
            ->whereHas('patient', function($q) use ($record) {
                $q->where('id', $record->patient_id);
            })
            ->exists();

        if (!$hasAccess) {
            return redirect()->back()->with('error', 'You do not have access to edit this record.');
        }

        return view('doctor.edit-immunization', compact('record', 'doctor'));
    }

    /**
     * Update Immunization Record
     */
    public function updateImmunization(Request $request, $id)
    {
        $record = ImmunizationRecords::findOrFail($id);

        $request->validate([
            'vaccine_name' => 'required|string|max:255',
            'vaccine_type' => 'nullable|string|max:255',
            'administered_by' => 'nullable|string|max:255',
            'dosage' => 'nullable|string|max:255',
            'site_of_administration' => 'nullable|string|max:255',
            'expiration_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $record->update($request->only([
            'vaccine_name', 'vaccine_type', 'administered_by',
            'dosage', 'site_of_administration', 'expiration_date', 'notes'
        ]));

        return redirect()->route('doctor.health-records')
            ->with('success', 'Immunization record updated successfully.');
    }

    /**
     * Edit Prescription Record
     */
    public function editPrescription($id)
    {
        $doctor = Doctors::where('user_id', Auth::id())->first();

        if (!$doctor) {
            return redirect()->back()->with('error', 'Doctor profile not found.');
        }

        $prescription = PrescriptionRecord::findOrFail($id);

        // Verify access
        $hasAccess = Appointment::where('doc_id', $doctor->id)
            ->where('patient_id', $prescription->patient_id)
            ->exists();

        if (!$hasAccess) {
            return redirect()->back()->with('error', 'You do not have access to edit this record.');
        }

        $medicines = Medicine::all();

        return view('doctor.edit-prescription', compact('prescription', 'doctor', 'medicines'));
    }

    /**
     * Update Prescription Record
     */
    public function updatePrescription(Request $request, $id)
    {
        $prescription = PrescriptionRecord::findOrFail($id);

        $request->validate([
            'medicine_id' => 'required|exists:medicine,id',
            'dosage' => 'required|string|max:255',
            'frequency' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'instructions' => 'nullable|string',
            'date_prescribed' => 'required|date',
        ]);

        $prescription->update($request->only([
            'medicine_id', 'dosage', 'frequency', 'duration',
            'instructions', 'date_prescribed'
        ]));

        return redirect()->route('doctor.health-records')
            ->with('success', 'Prescription updated successfully.');
    }

    /**
     * Store a new medicine
     */
    public function storeMedicine(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'medicine_type' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'quantity' => 'nullable|integer|min:0',
            'expiration_date' => 'nullable|date',
        ]);

        Medicine::create($request->all());

        return redirect()->route('doctor.medications')
            ->with('success', 'Medicine added successfully.');
    }

    /**
     * Update an existing medicine
     */
    public function updateMedicine(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'medicine_type' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'quantity' => 'nullable|integer|min:0',
            'expiration_date' => 'nullable|date',
        ]);

        $medicine->update($request->all());

        return redirect()->route('doctor.medications')
            ->with('success', 'Medicine updated successfully.');
    }

    /**
     * Display prescriptions page
     */
    public function prescriptions(Request $request)
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->with('error', 'Doctor profile not found.');
        }

        // Build query for prescriptions
        $query = PrescriptionRecord::where('doctor_id', $doctor->id)
            ->with(['patient.user', 'medicine']);

        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('patient.user', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })->orWhere('patient_id', 'like', '%' . $search . '%');
        }

        // Status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Get prescriptions
        $prescriptions = $query->orderBy('date_prescribed', 'desc')->paginate(15);

        // Get statistics
        $totalPrescriptions = PrescriptionRecord::where('doctor_id', $doctor->id)->count();
        $activePrescriptions = PrescriptionRecord::where('doctor_id', $doctor->id)->where('status', 'active')->count();
        $completedPrescriptions = PrescriptionRecord::where('doctor_id', $doctor->id)->where('status', 'completed')->count();
        $discontinuedPrescriptions = PrescriptionRecord::where('doctor_id', $doctor->id)->where('status', 'discontinued')->count();

        // Get patients for dropdown
        $patients = Patients::whereHas('appointments', function($query) use ($doctor) {
            $query->where('doc_id', $doctor->id);
        })->with('user')->get();

        // Get medicines for dropdown
        $medicines = Medicine::orderBy('name', 'asc')->get();

        return view('doctor.prescriptions', compact(
            'doctor',
            'prescriptions',
            'totalPrescriptions',
            'activePrescriptions',
            'completedPrescriptions',
            'discontinuedPrescriptions',
            'patients',
            'medicines'
        ));
    }

    /**
     * Show form to create prescription
     */
    public function createPrescription(Request $request)
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->with('error', 'Doctor profile not found.');
        }

        // Get patient if patient_id is provided
        $patient = null;
        if ($request->has('patient_id')) {
            $patient = Patients::with('user')->find($request->patient_id);
        }

        // Get all patients for dropdown
        $patients = Patients::with('user')->get();

        // Get medicines for dropdown
        $medicines = Medicine::where('quantity', '>', 0)
            ->orderBy('name', 'asc')
            ->get();

        return view('doctor.create-prescription', compact('patient', 'patients', 'medicines'));
    }

    /**
     * Store a new prescription
     */
    public function storePrescription(Request $request)
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->with('error', 'Doctor profile not found.');
        }

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medicine_id' => 'required|exists:medicine,id',
            'dosage' => 'required|string|max:255',
            'frequency' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'instruction' => 'nullable|string',
            'date_prescribed' => 'nullable|date',
        ]);

        PrescriptionRecord::create([
            'patient_id' => $request->patient_id,
            'medicine_id' => $request->medicine_id,
            'doctor_id' => $doctor->id,
            'prescribed_by' => Auth::id(),
            'dosage' => $request->dosage,
            'frequency' => $request->frequency,
            'duration' => $request->duration,
            'instruction' => $request->instruction,
            'date_prescribed' => $request->date_prescribed ?? now(),
            'status' => 'active',
        ]);

        return redirect()->back()->with('success', 'Prescription created successfully.');
    }

    /**
     * Update an existing prescription
     */
    public function updatePrescriptionRecord(Request $request, $id)
    {
        $prescription = PrescriptionRecord::findOrFail($id);

        // Verify doctor owns this prescription
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if ($prescription->doctor_id != $doctor->id) {
            return redirect()->route('doctor.prescriptions')->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'dosage' => 'required|string|max:255',
            'frequency' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'instruction' => 'nullable|string',
        ]);

        $prescription->update($request->only([
            'dosage', 'frequency', 'duration', 'instruction'
        ]));

        return redirect()->route('doctor.prescriptions')
            ->with('success', 'Prescription updated successfully.');
    }

    /**
     * Discontinue a prescription
     */
    public function discontinuePrescription(Request $request, $id)
    {
        $prescription = PrescriptionRecord::findOrFail($id);

        // Verify doctor owns this prescription
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if ($prescription->doctor_id != $doctor->id) {
            return redirect()->route('doctor.prescriptions')->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'discontinuation_reason' => 'required|string',
        ]);

        $prescription->update([
            'status' => 'discontinued',
            'date_discontinued' => now(),
            'discontinuation_reason' => $request->discontinuation_reason,
        ]);

        return redirect()->route('doctor.prescriptions')
            ->with('success', 'Prescription discontinued successfully.');
    }

    /**
     * Get prescription history for a patient
     */
    public function prescriptionHistory($patientId)
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Doctor profile not found.'], 404);
        }

        $prescriptions = PrescriptionRecord::where('patient_id', $patientId)
            ->where('doctor_id', $doctor->id)
            ->with('medicine')
            ->orderBy('date_prescribed', 'desc')
            ->get();

        return response()->json($prescriptions);
    }

    /**
     * AJAX Tab Method for Certificates Shell Navigation
     */
    public function ajaxCertificates()
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        // Fetch certificate requests assigned to or relevant to this doctor
        $certificateRequests = CertificateRequest::with(['patient.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('doctor.partials.certificates', compact('doctor', 'certificateRequests'));
    }









    /**
     * Display certificate requests for the doctor to review
     */
    public function certificateRequests(Request $request)
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->with('error', 'Doctor profile not found.');
        }

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

        return view('doctor.certificate-requests', compact(
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
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->with('error', 'Doctor profile not found.');
        }

        $request = CertificateRequest::with(['patient.user', 'certificateType', 'appointment'])
            ->findOrFail($id);

        return view('doctor.certificate-request-detail', compact('request', 'doctor'));
    }

    /**
     * Approve a certificate request
     */
    public function approveCertificateRequest(Request $request, $id)
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->with('error', 'Doctor profile not found.');
        }

        $certificateRequest = CertificateRequest::findOrFail($id);

        $validated = $request->validate([
            'doctor_notes' => 'nullable|string|max:1000',
        ]);

        $certificateRequest->update([
            'status' => CertificateRequest::STATUS_APPROVED,
            'doctor_id' => $doctor->id,
            'doctor_notes' => $validated['doctor_notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Certificate request approved successfully.');
    }

    /**
     * Reject a certificate request
     */
    public function rejectCertificateRequest(Request $request, $id)
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->with('error', 'Doctor profile not found.');
        }

        $certificateRequest = CertificateRequest::findOrFail($id);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $certificateRequest->update([
            'status' => CertificateRequest::STATUS_REJECTED,
            'doctor_id' => $doctor->id,
            'doctor_notes' => $validated['rejection_reason'],
        ]);

        return redirect()->back()->with('success', 'Certificate request rejected.');
    }

    /**
     * Mark a certificate as issued
     */
    public function issueCertificateRequest($id)
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->with('error', 'Doctor profile not found.');
        }

        $certificateRequest = CertificateRequest::findOrFail($id);

        $certificateRequest->update([
            'status' => CertificateRequest::STATUS_ISSUED,
            'issued_date' => now(),
        ]);

        return redirect()->back()->with('success', 'Certificate marked as issued.');
    }

    /**
     * Show report generation page for doctors
     */
    public function showReportGeneration()
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->with('error', 'Doctor profile not found.');
        }

        $savedReports = GeneratedReport::where('generated_by_type', 'doctor')
            ->where('generated_by_id', $doctor->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('doctor.generate-report', compact('savedReports'));
    }

    /**
     * Generate report based on parameters
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:patient_statistics,appointment_statistics,health_records,prescriptions,medicine_inventory',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'title' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->with('error', 'Doctor profile not found.');
        }

        $reportType = $request->report_type;
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->subYear();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now();

        $reportData = $this->compileDoctorReportData($reportType, $dateFrom, $dateTo, $doctor);

        // Save report to database
        $report = GeneratedReport::create([
            'title' => $request->title,
            'report_type' => $reportType,
            'description' => $request->description,
            'parameters' => [
                'date_from' => $dateFrom->toDateString(),
                'date_to' => $dateTo->toDateString(),
            ],
            'generated_by_type' => 'doctor',
            'generated_by_id' => $doctor->id,
            'status' => 'completed',
        ]);

        return view('doctor.view-report', compact('report', 'reportData', 'dateFrom', 'dateTo'));
    }

    /**
     * View existing report
     */
    public function viewReport($id)
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->with('error', 'Doctor profile not found.');
        }

        $report = GeneratedReport::findOrFail($id);

        // Check authorization
        if ($report->generated_by_id !== $doctor->id || $report->generated_by_type !== 'doctor') {
            abort(403, 'Unauthorized access to this report.');
        }

        $parameters = is_array($report->parameters) ? $report->parameters : [];
        $dateFrom = Carbon::parse($parameters['date_from'] ?? optional($report->created_at)->copy()?->subYear() ?? now()->subYear());
        $dateTo = Carbon::parse($parameters['date_to'] ?? $report->created_at ?? now());

        $reportData = $this->compileDoctorReportData($report->report_type, $dateFrom, $dateTo, $doctor);

        return view('doctor.view-report', compact('report', 'reportData', 'dateFrom', 'dateTo'));
    }

    /**
     * Delete report
     */
    public function deleteReport($id)
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->with('error', 'Doctor profile not found.');
        }

        $report = GeneratedReport::findOrFail($id);

        // Check authorization
        if ($report->generated_by_id !== $doctor->id || $report->generated_by_type !== 'doctor') {
            abort(403, 'Unauthorized to delete this report.');
        }

        $report->delete();

        return redirect()->route('doctor.reports.generate')
            ->with('success', 'Report deleted successfully.');
    }

    /**
     * Export report as CSV
     */
    public function exportReport($id, $format = 'pdf')
    {
        $user = Auth::user();
        $doctor = Doctors::where('user_id', $user->id)->first();

        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->with('error', 'Doctor profile not found.');
        }

        $report = GeneratedReport::findOrFail($id);

        // Check authorization
        if ($report->generated_by_id !== $doctor->id || $report->generated_by_type !== 'doctor') {
            abort(403, 'Unauthorized to export this report.');
        }

        $parameters = is_array($report->parameters) ? $report->parameters : [];
        $dateFrom = Carbon::parse($parameters['date_from'] ?? optional($report->created_at)->copy()?->subYear() ?? now()->subYear());
        $dateTo = Carbon::parse($parameters['date_to'] ?? $report->created_at ?? now());

        $reportData = $this->compileDoctorReportData($report->report_type, $dateFrom, $dateTo, $doctor);

        if ($format === 'csv') {
            return $this->exportDoctorReportAsCSV($report, $reportData);
        }

        return view('doctor.export-report', compact('report', 'reportData', 'dateFrom', 'dateTo'));
    }

    /**
     * Compile doctor-specific report data
     */
    private function compileDoctorReportData($reportType, $dateFrom, $dateTo, $doctor)
    {
        switch ($reportType) {
            case 'patient_statistics':
                return $this->compileDoctorPatientStatistics($dateFrom, $dateTo, $doctor);
            case 'appointment_statistics':
                return $this->compileDoctorAppointmentStatistics($dateFrom, $dateTo, $doctor);
            case 'health_records':
                return $this->compileDoctorHealthRecordsReport($dateFrom, $dateTo, $doctor);
            case 'prescriptions':
                return $this->compileDoctorPrescriptionsReport($dateFrom, $dateTo, $doctor);
            case 'medicine_inventory':
                return $this->compileDoctorMedicineInventoryReport();
            default:
                return [];
        }
    }

    private function compileDoctorPatientStatistics($dateFrom, $dateTo, $doctor)
    {
        return [
            'total_patients' => Appointment::where('doc_id', $doctor->id)
                ->distinct('patient_id')
                ->count(),
            'new_patients' => Appointment::where('doc_id', $doctor->id)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->distinct('patient_id')
                ->count(),
            'gender_distribution' => Appointment::where('appointments.doc_id', $doctor->id)
                ->join('patients', 'appointments.patient_id', '=', 'patients.id')
                ->join('users', 'patients.user_id', '=', 'users.id')
                ->selectRaw('users.gender, COUNT(*) as count')
                ->groupBy('users.gender')
                ->get(),
            'monthly_trend' => Appointment::where('doc_id', $doctor->id)
                ->selectRaw(SqlDateExpressions::monthNumber('date').' as month, '.SqlDateExpressions::yearNumber('date').' as year, COUNT(*) as count')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get(),
            // Detailed patient list
            'recent_patients' => Appointment::where('appointments.doc_id', $doctor->id)
                ->with(['patient.user', 'patient.educationalLevel'])
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->orderBy('date', 'desc')
                ->take(50)
                ->get(),
            'all_patients' => Appointment::where('appointments.doc_id', $doctor->id)
                ->with(['patient.user', 'patient.educationalLevel'])
                ->orderBy('date', 'desc')
                ->take(100)
                ->get(),
        ];
    }

    private function compileDoctorAppointmentStatistics($dateFrom, $dateTo, $doctor)
    {
        return [
            'total_appointments' => Appointment::where('doc_id', $doctor->id)->count(),
            'by_status' => Appointment::where('doc_id', $doctor->id)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get(),
            'completed' => Appointment::where('doc_id', $doctor->id)
                ->where('status', 'Completed')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->count(),
            'pending' => Appointment::where('doc_id', $doctor->id)
                ->where('status', 'Pending')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->count(),
            'cancelled' => Appointment::where('doc_id', $doctor->id)
                ->where('status', 'Cancelled')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->count(),
            'monthly_breakdown' => Appointment::where('doc_id', $doctor->id)
                ->selectRaw(SqlDateExpressions::monthNumber('date').' as month, '.SqlDateExpressions::yearNumber('date').' as year, status, COUNT(*) as count')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->groupBy('year', 'month', 'status')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get(),
            // Detailed appointment lists
            'recent_appointments' => Appointment::where('appointments.doc_id', $doctor->id)
                ->with(['patient.user', 'doctor.user'])
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->orderBy('date', 'desc')
                ->orderBy('time', 'desc')
                ->take(50)
                ->get(),
            'pending_appointments' => Appointment::where('appointments.doc_id', $doctor->id)
                ->with(['patient.user', 'doctor.user'])
                ->where('status', 'Pending')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->orderBy('date', 'asc')
                ->take(50)
                ->get(),
            'completed_appointments' => Appointment::where('appointments.doc_id', $doctor->id)
                ->with(['patient.user', 'doctor.user'])
                ->where('status', 'Completed')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->orderBy('date', 'desc')
                ->take(50)
                ->get(),
            'cancelled_appointments' => Appointment::where('appointments.doc_id', $doctor->id)
                ->with(['patient.user', 'doctor.user'])
                ->where('status', 'Cancelled')
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->orderBy('date', 'desc')
                ->take(50)
                ->get(),
        ];
    }

    private function compileDoctorHealthRecordsReport($dateFrom, $dateTo, $doctor)
    {
        // Base query for doctor-related health records
        $baseQuery = function($query) use ($doctor) {
            $query->whereHas('patient.appointments', function($q) use ($doctor) {
                $q->where('doc_id', $doctor->id);
            });
        };

        return [
            'total_records' => HealthRecords::where($baseQuery)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count(),
            'records_with_diagnosis' => HealthRecords::where($baseQuery)
                ->whereNotNull('diagnosis')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count(),
            'top_conditions' => HealthRecords::whereHas('patient.appointments', function($query) use ($doctor) {
                $query->where('doc_id', $doctor->id);
            })
                ->selectRaw('diagnosis, COUNT(*) as count')
                ->whereNotNull('diagnosis')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->groupBy('diagnosis')
                ->orderBy('count', 'desc')
                ->take(20)
                ->get(),
            // Detailed health records
            'recent_records' => HealthRecords::whereHas('patient.appointments', function($query) use ($doctor) {
                $query->where('doc_id', $doctor->id);
            })
                ->with('patient.user')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get(),
            // Physical examinations detail
            'physical_examinations' => PhysicalExamination::where('doctor_id', $doctor->id)
                ->with(['patient.user', 'doctor.user'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get(),
            // Dental examinations detail
            'dental_examinations' => DentalExamination::where('doctor_id', $doctor->id)
                ->with(['patient.user', 'doctor.user'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get(),
            // Immunization records detail
            'immunization_records' => ImmunizationRecords::whereHas('patient.appointments', function($query) use ($doctor) {
                $query->where('doc_id', $doctor->id);
            })
                ->with(['patient.user'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get(),
            // Prescriptions detail
            'prescriptions_detail' => PrescriptionRecord::where('doctor_id', $doctor->id)
                ->with(['patient.user', 'medicine'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get(),
        ];
    }

    private function compileDoctorPrescriptionsReport($dateFrom, $dateTo, $doctor)
    {
        return [
            'total_prescriptions' => PrescriptionRecord::where('doctor_id', $doctor->id)->count(),
            'active_prescriptions' => PrescriptionRecord::where('doctor_id', $doctor->id)
                ->where('status', 'active')
                ->count(),
            'discontinued_prescriptions' => PrescriptionRecord::where('doctor_id', $doctor->id)
                ->where('status', 'discontinued')
                ->count(),
            'recent_prescriptions' => PrescriptionRecord::where('doctor_id', $doctor->id)
                ->with(['patient.user', 'medicine', 'doctor.user'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get(),
            'most_prescribed' => PrescriptionRecord::where('doctor_id', $doctor->id)
                ->selectRaw('medicine_id, COUNT(*) as count')
                ->whereNotNull('medicine_id')
                ->groupBy('medicine_id')
                ->orderBy('count', 'desc')
                ->take(10)
                ->get(),
        ];
    }

    private function compileDoctorMedicineInventoryReport()
    {
        return [
            'total_medicines' => Medicine::count(),
            'low_stock' => Medicine::where('quantity', '>', 0)->where('quantity', '<=', 10)->get(),
            'out_of_stock' => Medicine::where('quantity', 0)->get(),
            'all_medicines' => Medicine::orderBy('name')->get(),
        ];
    }

    /**
     * Export doctor report as CSV
     */
    private function exportDoctorReportAsCSV($report, $reportData)
    {
        $filename = 'report_' . $report->id . '_' . date('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($report, $reportData) {
            $handle = fopen('php://output', 'w');

            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [$report->title]);
            fputcsv($handle, ['Report Type: ' . ucwords(str_replace('_', ' ', $report->report_type))]);
            fputcsv($handle, ['Date Range: ' . ($report->parameters['date_from'] ?? 'All available') . ' to ' . ($report->parameters['date_to'] ?? 'current')]);
            fputcsv($handle, ['Generated: ' . $report->created_at->format('Y-m-d H:i:s')]);
            fputcsv($handle, []);

            if ($report->report_type === 'patient_statistics') {
                fputcsv($handle, ['Patient Statistics Report']);
                fputcsv($handle, ['Total Patients', $reportData['total_patients'] ?? 0]);
                fputcsv($handle, ['New Patients', $reportData['new_patients'] ?? 0]);
                fputcsv($handle, []);
                fputcsv($handle, ['Gender', 'Count']);
                foreach ($reportData['gender_distribution'] ?? [] as $item) {
                    fputcsv($handle, [$item->gender ?? 'N/A', $item->count]);
                }
            } elseif ($report->report_type === 'appointment_statistics') {
                fputcsv($handle, ['Appointment Statistics Report']);
                fputcsv($handle, ['Total Appointments', $reportData['total_appointments'] ?? 0]);
                fputcsv($handle, ['Completed', $reportData['completed'] ?? 0]);
                fputcsv($handle, ['Pending', $reportData['pending'] ?? 0]);
                fputcsv($handle, ['Cancelled', $reportData['cancelled'] ?? 0]);
                fputcsv($handle, []);
                fputcsv($handle, ['Recent Appointments']);
                fputcsv($handle, ['Date', 'Time', 'Patient', 'Doctor', 'Status', 'Reason']);
                foreach ($reportData['recent_appointments'] ?? [] as $appt) {
                    fputcsv($handle, [
                        $appt->date ?? 'N/A',
                        $appt->time ?? 'N/A',
                        $appt->patient->user->name ?? 'N/A',
                        $appt->doctor->user->name ?? 'N/A',
                        $appt->status ?? 'N/A',
                        $appt->reason ?? 'N/A'
                    ]);
                }
            } elseif ($report->report_type === 'health_records_summary') {
                fputcsv($handle, ['Health Records Summary Report']);
                fputcsv($handle, ['Total Records', $reportData['total_records'] ?? 0]);
                fputcsv($handle, ['Records with Diagnosis', $reportData['records_with_diagnosis'] ?? 0]);
                fputcsv($handle, []);

                // Top Conditions
                fputcsv($handle, ['Top Conditions']);
                fputcsv($handle, ['Condition', 'Cases']);
                foreach ($reportData['top_conditions'] ?? [] as $condition) {
                    fputcsv($handle, [
                        $condition->diagnosis ?? 'N/A',
                        $condition->count ?? 0
                    ]);
                }
                fputcsv($handle, []);

                // Health Records Detail
                fputcsv($handle, ['Health Records Detail']);
                fputcsv($handle, ['Date', 'Patient', 'Diagnosis', 'Symptoms', 'Treatment']);
                foreach ($reportData['recent_records'] ?? [] as $record) {
                    fputcsv($handle, [
                        $record->created_at->format('Y-m-d') ?? 'N/A',
                        $record->patient->user->name ?? 'N/A',
                        $record->diagnosis ?? 'N/A',
                        $record->symptoms ?? 'N/A',
                        $record->treatment ?? 'N/A'
                    ]);
                }
                fputcsv($handle, []);

                // Physical Examinations Detail
                fputcsv($handle, ['Physical Examinations Detail']);
                fputcsv($handle, ['Date', 'Patient', 'Height', 'Weight', 'BP', 'Heart', 'Remarks']);
                foreach ($reportData['physical_examinations'] ?? [] as $exam) {
                    fputcsv($handle, [
                        $exam->created_at->format('Y-m-d') ?? 'N/A',
                        $exam->patient->user->name ?? 'N/A',
                        $exam->height ?? 'N/A',
                        $exam->weight ?? 'N/A',
                        $exam->bp ?? 'N/A',
                        $exam->heart ?? 'N/A',
                        $exam->remarks ?? 'N/A'
                    ]);
                }
                fputcsv($handle, []);

                // Dental Examinations Detail
                fputcsv($handle, ['Dental Examinations Detail']);
                fputcsv($handle, ['Date', 'Patient', 'Doctor', 'Diagnosis']);
                foreach ($reportData['dental_examinations'] ?? [] as $exam) {
                    fputcsv($handle, [
                        $exam->created_at->format('Y-m-d') ?? 'N/A',
                        $exam->patient->user->name ?? 'N/A',
                        $exam->doctor->user->name ?? 'N/A',
                        $exam->diagnosis ?? 'N/A'
                    ]);
                }
                fputcsv($handle, []);

                // Immunization Records Detail
                fputcsv($handle, ['Immunization Records Detail']);
                fputcsv($handle, ['Date', 'Patient', 'Vaccine', 'Dosage', 'Administered By']);
                foreach ($reportData['immunization_records'] ?? [] as $record) {
                    fputcsv($handle, [
                        $record->created_at->format('Y-m-d') ?? 'N/A',
                        $record->patient->user->name ?? 'N/A',
                        $record->vaccine_name ?? 'N/A',
                        $record->dosage ?? 'N/A',
                        $record->administered_by ?? 'N/A'
                    ]);
                }
                fputcsv($handle, []);

                // Prescriptions Detail
                fputcsv($handle, ['Prescriptions Detail']);
                fputcsv($handle, ['Date', 'Patient', 'Doctor', 'Medicine', 'Dosage', 'Status']);
                foreach ($reportData['prescriptions_detail'] ?? [] as $prescription) {
                    fputcsv($handle, [
                        $prescription->created_at->format('Y-m-d') ?? 'N/A',
                        $prescription->patient->user->name ?? 'N/A',
                        $prescription->doctor->user->name ?? 'N/A',
                        $prescription->medicine->name ?? 'N/A',
                        $prescription->dosage ?? 'N/A',
                        $prescription->status ?? 'N/A'
                    ]);
                }
            } elseif ($report->report_type === 'prescriptions_report') {
                fputcsv($handle, ['Prescriptions Report']);
                fputcsv($handle, ['Total Prescriptions', $reportData['total_prescriptions'] ?? 0]);
                fputcsv($handle, ['Active', $reportData['active_prescriptions'] ?? 0]);
                fputcsv($handle, ['Discontinued', $reportData['discontinued_prescriptions'] ?? 0]);
                fputcsv($handle, []);

                // Recent Prescriptions Detail
                fputcsv($handle, ['Recent Prescriptions Detail']);
                fputcsv($handle, ['Date', 'Patient', 'Doctor', 'Medicine', 'Dosage', 'Status']);
                foreach ($reportData['recent_prescriptions'] ?? [] as $prescription) {
                    fputcsv($handle, [
                        $prescription->date_prescribed ?? 'N/A',
                        $prescription->patient->user->name ?? 'N/A',
                        $prescription->doctor->user->name ?? 'N/A',
                        $prescription->medicine->name ?? 'N/A',
                        $prescription->dosage ?? 'N/A',
                        $prescription->status ?? 'N/A'
                    ]);
                }
            } else {
                fputcsv($handle, ['Report data export']);
                fputcsv($handle, ['Note: Export for this report type not fully implemented']);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Print a certificate
     */
    public function printCertificate($id)
    {
        $certificate = CertificateRequest::with(['patient.user', 'certificateType', 'doctor.user', 'appointment'])
            ->findOrFail($id);

        // Only allow printing if certificate is approved or issued
        if (!in_array($certificate->status, ['approved', 'issued'])) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Certificate must be approved before printing.'], 403);
            }
            return redirect()->back()->with('error', 'Certificate must be approved before printing.');
        }

        return view('doctor.certificate-print', compact('certificate'));
    }

    

    private function doctorDashboardData(int $doctorId): array
    {
        $ttl = max(1, config('performance.dashboard_cache_ttl', 60));

        $summary = Cache::remember("dashboard.doctor.{$doctorId}.summary", $ttl, function () use ($doctorId): array {
            $today = Carbon::today()->toDateString();
            $weekStart = Carbon::now()->startOfWeek()->toDateString();
            $weekEnd = Carbon::now()->endOfWeek()->toDateString();

            $counts = DB::table('appointments')
                ->where('doc_id', $doctorId)
                ->selectRaw(
                    'SUM(CASE WHEN date = ? THEN 1 ELSE 0 END) AS today_appointments,
                     SUM(CASE WHEN date BETWEEN ? AND ? THEN 1 ELSE 0 END) AS weekly_appointments,
                     COUNT(DISTINCT patient_id) AS total_patients,
                     SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS pending_appointments',
                    [$today, $weekStart, $weekEnd, Appointment::STATUS_PENDING]
                )
                ->first();

            $monthExpression = SqlDateExpressions::monthNumber('date');
            $monthly = DB::table('appointments')
                ->where('doc_id', $doctorId)
                ->whereYear('date', Carbon::now()->year)
                ->selectRaw("{$monthExpression} AS month, COUNT(*) AS aggregate")
                ->groupByRaw($monthExpression)
                ->pluck('aggregate', 'month');

            $monthlyStats = array_fill(1, 12, 0);
            foreach ($monthly as $month => $count) {
                $monthlyStats[(int) $month] = (int) $count;
            }
            ksort($monthlyStats);

            return [
                'todayAppointments' => (int) ($counts->today_appointments ?? 0),
                'weeklyAppointments' => (int) ($counts->weekly_appointments ?? 0),
                'totalPatients' => (int) ($counts->total_patients ?? 0),
                'pendingAppointments' => (int) ($counts->pending_appointments ?? 0),
                'monthlyStats' => $monthlyStats,
            ];
        });

        $summary['recentAppointments'] = Appointment::query()
            ->select(['id', 'patient_id', 'doc_id', 'date', 'time', 'status'])
            ->where('doc_id', $doctorId)
            ->with(['patient:id,user_id', 'patient.user:id,name'])
            ->latest('date')
            ->latest('time')
            ->limit(5)
            ->get();

        return $summary;
    }

}
