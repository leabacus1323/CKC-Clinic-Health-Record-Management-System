<?php

namespace App\Http\Controllers;

use App\Models\Patients;
use App\Models\HealthRecords;
use App\Models\Immunization;
use App\Models\ImmunizationRecords;
use App\Models\PhysicalExamination;
use App\Models\DentalExamination;
use App\Models\PrescriptionRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientsViewPersonalHealthRecord extends Controller
{
    /**
     * Display patient's health records
     */
    public function healthRecords()
    {
        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->route('patients.profile.create')
                ->with('info', 'Please complete your patient profile first.');
        }

        // Get all health-related records
        $healthRecords = HealthRecords::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $dentalExaminations = DentalExamination::where('patient_id', $patient->id)
            ->with('doctor')
            ->orderBy('created_at', 'desc')
            ->get();

        $physicalExaminations = PhysicalExamination::where('patient_id', $patient->id)
            ->with('doctor')
            ->orderBy('created_at', 'desc')
            ->get();

        $immunizationRecords = ImmunizationRecords::where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $prescriptionRecords = PrescriptionRecord::where('patient_id', $patient->id)
            ->with('medicine', 'doctor')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('patients.health-records', compact(
            'healthRecords',
            'dentalExaminations',
            'physicalExaminations',
            'immunizationRecords',
            'prescriptionRecords'
        ));
    }

    /**
     * Display detailed health records with all medical information
     */
    public function detailedHealthRecords()
    {
        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->route('patients.profile.create')
                ->with('info', 'Please complete your patient profile first.');
        }

        // Get all health-related records
        $healthRecords = HealthRecords::where('user_id', $user->id)->get();
        $immunizationRecords = ImmunizationRecords::where('patient_id', $patient->id)
            ->get();
        $physicalExaminations = PhysicalExamination::where('patient_id', $patient->id)->get();
        $dentalExaminations = DentalExamination::where('patient_id', $patient->id)->get();
        $prescriptionRecords = PrescriptionRecord::where('patient_id', $patient->id)
            ->with('medicine')
            ->get();

        return view('patients.detailed-health-records', compact(
            'patient',
            'healthRecords',
            'immunizationRecords',
            'physicalExaminations',
            'dentalExaminations',
            'prescriptionRecords'
        ));
    }

    /**
     * Display immunization records
     */
    public function immunizationRecords()
    {
        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->route('patients.profile.create');
        }

        $immunizationRecords = ImmunizationRecords::where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('patients.immunization-records', compact('immunizationRecords'));
    }

    /**
     * Display prescription history
     */
    public function prescriptionHistory()
    {
        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->route('patients.profile.create');
        }

        $prescriptionRecords = PrescriptionRecord::where('patient_id', $patient->id)
            ->with('medicine')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('patients.prescription-history', compact('prescriptionRecords'));
    }

    /**
     * Display examination history (physical and dental)
     */
    public function examinationHistory()
    {
        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->route('patients.profile.create');
        }

        $physicalExaminations = PhysicalExamination::where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $dentalExaminations = DentalExamination::where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('patients.examination-history', compact('physicalExaminations', 'dentalExaminations'));
    }

    /**
     * Generate health summary report
     */
    public function healthSummary()
    {
        $user = Auth::user();
        $patient = Patients::where('user_id', $user->id)->first();

        if (!$patient) {
            return redirect()->route('patients.profile.create');
        }

        // Generate summary statistics
        $totalHealthRecords = HealthRecords::where('user_id', $user->id)->count();
        $totalImmunizations = ImmunizationRecords::where('patient_id', $patient->id)->count();
        $totalPrescriptions = PrescriptionRecord::where('patient_id', $patient->id)->count();
        $totalExaminations = PhysicalExamination::where('patient_id', $patient->id)->count() +
                           DentalExamination::where('patient_id', $patient->id)->count();

        // Recent records
        $recentHealthRecords = HealthRecords::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('patients.health-summary', compact(
            'patient',
            'totalHealthRecords',
            'totalImmunizations',
            'totalPrescriptions',
            'totalExaminations',
            'recentHealthRecords'
        ));
    }
}
