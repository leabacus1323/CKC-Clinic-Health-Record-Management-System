<?php

namespace App\Http\Controllers;

use App\Models\CertificateRequest;
use App\Models\CertificateType;
use App\Models\Patients;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientCertificateController extends Controller
{
    /**
     * Display a listing of certificate requests for the current patient.
     */
    public function index()
    {
        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient profile not found.');
        }

        $certificates = CertificateRequest::with(['certificateType', 'doctor'])
            ->where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('patients.certificates.index', compact('certificates'));
    }

    /**
     * Show the form for creating a new certificate request.
     */
    public function create()
    {
        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient profile not found.');
        }

        // Check if patient has any completed appointments
        $hasAppointmentHistory = Appointment::where('patient_id', $patient->id)
            ->whereIn('status', ['completed', 'done', 'Completed', 'Confirmed'])
            ->exists();

        $certificateTypes = CertificateType::active()->get();

        // Identify dental-related certificate types by name (for conditional visibility)
        $dentalNames = [
            'Dental Fit-to-Work',
            'Sports Dental Clearance',
            'Dental Excuse Note',
        ];

        $dentalTypeIds = $certificateTypes->filter(function($t) use ($dentalNames) {
            $name = $t->name ?? '';
            if (in_array($name, $dentalNames)) return true;
            if (stripos($name, 'dental') !== false) return true;
            return false;
        })->pluck('id')->toArray();

        // Get patient's appointments for reference (include doctor and user)
        $appointments = Appointment::where('patient_id', $patient->id)
            ->whereIn('status', ['completed', 'done', 'Completed', 'Confirmed'])
            ->with(['doctor.user'])
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        return view('patients.certificates.create', compact(
            'certificateTypes',
            'appointments',
            'patient',
            'hasAppointmentHistory',
            'dentalTypeIds'
        ));
    }

    /**
     * Store a newly created certificate request.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient profile not found.');
        }

        $validated = $request->validate([
            'certificate_type_id' => 'required|exists:certificate_types,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'reason' => 'required|string|max:1000',
        ]);

        $appointment = null;
        if (!empty($validated['appointment_id'])) {
            $appointment = Appointment::where('id', $validated['appointment_id'])
                ->where('patient_id', $patient->id)
                ->with('doctor')
                ->first();

            if (!$appointment) {
                return redirect()->back()->withInput()->withErrors([
                    'appointment_id' => 'Selected appointment could not be found for your profile.',
                ]);
            }
        }

        $certificateTypes = CertificateType::active()->get();
        $dentalNames = [
            'Dental Fit-to-Work',
            'Sports Dental Clearance',
            'Dental Excuse Note',
        ];
        $dentalTypeIds = $certificateTypes->filter(function($t) use ($dentalNames) {
            $name = $t->name ?? '';
            if (in_array($name, $dentalNames)) return true;
            if (stripos($name, 'dental') !== false) return true;
            return false;
        })->pluck('id')->toArray();

        if (in_array($validated['certificate_type_id'], $dentalTypeIds, true)) {
            if (!$appointment || strtolower($appointment->doctor->specialization ?? '') !== 'dentist') {
                return redirect()->back()->withInput()->withErrors([
                    'certificate_type_id' => 'Dental certificates may only be requested for dentist appointments.',
                ]);
            }
        }

        // Get the doctor from the appointment
        $doctorId = null;
        if ($appointment) {
            $doctorId = $appointment->doc_id;
        }

        CertificateRequest::create([
            'patient_id' => $patient->id,
            'certificate_type_id' => $validated['certificate_type_id'],
            'doctor_id' => $doctorId,
            'appointment_id' => $validated['appointment_id'] ?? null,
            'reason' => $validated['reason'],
            'status' => CertificateRequest::STATUS_PENDING,
        ]);

        return redirect()->route('patients.certificates.index')
            ->with('success', 'Certificate request submitted successfully. You will be notified once a doctor reviews it.');
    }

    /**
     * Display the specified certificate request.
     */
    public function show($id)
    {
        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->back()->with('error', 'Patient profile not found.');
        }

        $certificate = CertificateRequest::with(['certificateType', 'doctor', 'appointment'])
            ->where('id', $id)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$certificate) {
            return redirect()->back()->with('error', 'Certificate request not found.');
        }

        return view('patients.certificates.show', compact('certificate'));
    }
}
