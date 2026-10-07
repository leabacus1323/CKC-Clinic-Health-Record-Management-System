<?php

namespace App\Http\Controllers;

use App\Models\Patients;
use App\Models\Appointment;
use App\Models\HealthRecords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientsController extends Controller
{
    /**
     * Display patient dashboard with appointments and health records
     */
    public function index()
    {
        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->route('patients.profile.create')
                ->with('info', 'Please complete your patient profile first.');
        }

        $appointments = Appointment::where('patient_id', $patient->id)
            ->with('doctor')
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        $healthRecords = HealthRecords::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('patients.dashboard', compact('patient', 'appointments', 'healthRecords'));
    }

    // AJAX Tab Methods for Shell Navigation
    public function ajaxDashboard()
    {
        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        if (!$patient) {
            return '<div class="p-6 text-center"><p class="text-gray-600">Please complete your patient profile first.</p></div>';
        }

        $appointments = Appointment::where('patient_id', $patient->id)
            ->with('doctor')
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        $healthRecords = HealthRecords::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('patients.partials.dashboard', compact('patient', 'appointments', 'healthRecords'));
    }

    public function ajaxAppointments()
    {
        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        if (!$patient) {
            return '<div class="p-6 text-center"><p class="text-gray-600">Please complete your patient profile first.</p></div>';
        }

        $appointments = Appointment::where('patient_id', $patient->id)
            ->with('doctor.user')
            ->orderBy('date', 'desc')
            ->get();

        return view('patients.partials.appointments', compact('appointments'));
    }

    public function ajaxHealthRecords()
    {
        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        if (!$patient) {
            return '<div class="p-6 text-center"><p class="text-gray-600">Please complete your patient profile first.</p></div>';
        }

        // Get all health records for the patient
        $healthRecords = \App\Models\HealthRecords::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get dental examinations
        $dentalExaminations = \App\Models\DentalExamination::where('patient_id', $patient->id)
            ->with('doctor.user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get physical examinations
        $physicalExaminations = \App\Models\PhysicalExamination::where('patient_id', $patient->id)
            ->with('doctor.user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get immunization records
        $immunizationRecords = \App\Models\ImmunizationRecords::where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get prescription records
        $prescriptionRecords = \App\Models\PrescriptionRecord::where('patient_id', $patient->id)
            ->with(['doctor.user', 'medicine'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('patients.partials.health-records', compact(
            'healthRecords', 'dentalExaminations', 'physicalExaminations',
            'immunizationRecords', 'prescriptionRecords'
        ));
    }

    public function ajaxProfile()
    {
        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        if (!$patient) {
            return '<div class="p-6 text-center"><p class="text-gray-600">Please complete your patient profile first.</p></div>';
        }

        return view('patients.partials.profile', compact('patient', 'user'));
    }

    public function ajaxCertificates()
    {
        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        if (!$patient) {
            return '<div class="p-6 text-center"><p class="text-gray-600">Please complete your patient profile first.</p></div>';
        }

        $certificates = \App\Models\CertificateRequest::where('patient_id', $patient->id)
            ->with('certificateType')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('patients.partials.certificates', compact('certificates'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Patients $patients)
    {
          $patients = Patients::all();
        return view('patients.show', compact('patients'));
    }

   

}
