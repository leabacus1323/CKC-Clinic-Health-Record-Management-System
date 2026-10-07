<?php

namespace App\Http\Controllers;

use App\Support\SqlDateExpressions;

use Illuminate\Http\Request;
use App\Models\Patients;
use App\Models\Course;
use App\Models\EducationalLevel;
use App\Models\Appointment;
use App\Models\Doctors;
use App\Models\User;
use App\Models\HealthRecords;
use App\Models\PrescriptionRecord;
use App\Models\Medicine;
use App\Models\PhysicalExamination;
use App\Models\DentalExamination;
use App\Models\ImmunizationRecords;
use App\Models\GeneratedReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminReports extends Controller
{
    /**
     * Display the reports dashboard
     */
    public function index()
    {
        return view('admin.reports.index');
    }

    /**
     * Show report generation form
     */
    public function showReportGeneration()
    {
        $savedReports = GeneratedReport::where('generated_by_type', 'admin')
            ->where('generated_by_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.reports.generate', compact('savedReports'));
    }

    /**
     * Generate report based on parameters
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:patient_statistics,patient_education_gender,appointment_statistics,health_records,prescriptions,medicine_inventory',
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
            'generated_by_type' => 'admin',
            'generated_by_id' => Auth::id(),
            'status' => 'completed',
        ]);

        return view('admin.reports.view', compact('report', 'reportData', 'dateFrom', 'dateTo'));
    }

    /**
     * View existing report
     */
    public function viewReport($id)
    {
        $report = GeneratedReport::findOrFail($id);

        [$dateFrom, $dateTo] = $this->resolveReportPeriod($report);

        $reportData = $this->compileReportData($report->report_type, $dateFrom, $dateTo);

        return view('admin.reports.view', compact('report', 'reportData', 'dateFrom', 'dateTo'));
    }

    /**
     * Delete report
     */
    public function deleteReport($id)
    {
        $report = GeneratedReport::findOrFail($id);

        if ($report->generated_by_id !== Auth::id()) {
            abort(403, 'Unauthorized to delete this report.');
        }

        $report->delete();

        return redirect()->route('admin.reports.generate')
            ->with('success', 'Report deleted successfully.');
    }

    /**
     * Export report as CSV
     */
    public function exportReport($id, $format = 'csv')
    {
        $report = GeneratedReport::findOrFail($id);

        [$dateFrom, $dateTo] = $this->resolveReportPeriod($report);

        $reportData = $this->compileReportData($report->report_type, $dateFrom, $dateTo);

        return $this->exportAsCSV($report, $reportData);
    }

    /**
     * Resolve a saved report period safely. Older/imported reports may not
     * contain date_from/date_to parameters.
     */
    private function resolveReportPeriod(GeneratedReport $report): array
    {
        $parameters = $report->parameters;

        if (! is_array($parameters)) {
            $parameters = json_decode((string) $parameters, true) ?: [];
        }

        $dateFrom = ! empty($parameters['date_from'])
            ? Carbon::parse($parameters['date_from'])->startOfDay()
            : Carbon::parse($report->created_at ?? now())->subYear()->startOfDay();

        $dateTo = ! empty($parameters['date_to'])
            ? Carbon::parse($parameters['date_to'])->endOfDay()
            : Carbon::parse($report->created_at ?? now())->endOfDay();

        if ($dateTo->lt($dateFrom)) {
            $dateTo = $dateFrom->copy()->endOfDay();
        }

        return [$dateFrom, $dateTo];
    }

    /**
     * Compile report data based on type
     */
    private function compileReportData($reportType, $dateFrom, $dateTo)
    {
        switch ($reportType) {
            case 'patient_statistics':
                return $this->compilePatientStatistics($dateFrom, $dateTo);
            case 'patient_education_gender':
                return $this->compilePatientEducationGenderStatistics();
            case 'appointment_statistics':
                return $this->compileAppointmentStatistics($dateFrom, $dateTo);
            case 'health_records':
                return $this->compileHealthRecordsReport($dateFrom, $dateTo);
            case 'prescriptions':
                return $this->compilePrescriptionsReport($dateFrom, $dateTo);
            case 'medicine_inventory':
                return $this->compileMedicineInventoryReport($dateFrom, $dateTo);
            default:
                return [];
        }
    }

    /**
     * Compile Patient Statistics Report
     */
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
            'by_course' => Patients::join('courses', 'patients.course_id', '=', 'courses.id')
                ->join('users', 'patients.user_id', '=', 'users.id')
                ->selectRaw('courses.course_name, COUNT(*) as count')
                ->whereIn('users.user_type', [1, 2])
                ->groupBy('courses.course_name')
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

    /**
     * Compile Patient Statistics by Educational Level and Gender
     */
    private function compilePatientEducationGenderStatistics()
    {
        $total = Patients::whereHas('user', function($q) {
            $q->whereIn('user_type', [1, 2]);
        })->count();

        // Get statistics by educational level
        $byEducationalLevel = Patients::join('educational_level', 'patients.edulvl_id', '=', 'educational_level.id')
            ->join('users', 'patients.user_id', '=', 'users.id')
            ->selectRaw('educational_level.level_name, users.gender, COUNT(*) as count')
            ->whereIn('users.user_type', [1, 2])
            ->groupBy('educational_level.level_name', 'users.gender')
            ->orderBy('educational_level.level_name')
            ->get();

        // Get statistics by gender
        $byGender = Patients::join('users', 'patients.user_id', '=', 'users.id')
            ->selectRaw('users.gender, COUNT(*) as count')
            ->whereIn('users.user_type', [1, 2])
            ->groupBy('users.gender')
            ->get();

        // Get detailed patient list with educational level and gender
        $detailedList = Patients::with(['user', 'educationalLevel'])
            ->whereHas('user', function($q) {
                $q->whereIn('user_type', [1, 2]);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Create a matrix of education level + gender
        $educationGenderMatrix = [];
        $educationalLevels = EducationalLevel::all();
        $genders = ['Male', 'Female', 'Other'];

        foreach ($educationalLevels as $level) {
            $educationGenderMatrix[$level->level_name] = [];
            foreach ($genders as $gender) {
                $count = Patients::join('educational_level', 'patients.edulvl_id', '=', 'educational_level.id')
                    ->join('users', 'patients.user_id', '=', 'users.id')
                    ->where('educational_level.id', $level->id)
                    ->where('users.gender', $gender)
                    ->whereIn('users.user_type', [1, 2])
                    ->count();
                $educationGenderMatrix[$level->level_name][$gender] = $count;
            }
        }

        return [
            'total_patients' => $total,
            'by_educational_level_and_gender' => $byEducationalLevel,
            'by_gender' => $byGender,
            'education_gender_matrix' => $educationGenderMatrix,
            'detailed_list' => $detailedList,
        ];
    }

    /**
     * Compile Appointment Statistics Report
     */
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
            'pending' => Appointment::where('status', 'Pending')
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

    /**
     * Compile Health Records Report
     */
    private function compileHealthRecordsReport($dateFrom, $dateTo)
    {
        return [
            'total_records' => HealthRecords::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'physical_exams' => PhysicalExamination::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'dental_exams' => DentalExamination::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'immunizations' => ImmunizationRecords::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
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
            // Top diagnoses (group in PHP to operate on decrypted values)
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

    /**
     * Compile Prescriptions Report
     */
    private function compilePrescriptionsReport($dateFrom, $dateTo)
    {
        return [
            'total_prescriptions' => PrescriptionRecord::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'by_medicine' => PrescriptionRecord::join('medicine', 'prescription_records.medicine_id', '=', 'medicine.id')
                ->selectRaw('medicine.name, COUNT(*) as count')
                ->whereBetween('prescription_records.created_at', [$dateFrom, $dateTo])
                ->groupBy('medicine.name')
                ->orderBy('count', 'desc')
                ->get(),
            // Detailed prescription list
            'prescriptions' => PrescriptionRecord::with(['patient.user', 'patient.educationalLevel', 'medicine', 'doctor.user'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->orderBy('created_at', 'desc')
                ->take(100)
                ->get(),
        ];
    }

    /**
     * Compile Medicine Inventory Report
     */
    private function compileMedicineInventoryReport($dateFrom, $dateTo)
    {
        $allMedicines = Medicine::orderBy('name')->get();
        $lowStock = Medicine::where('quantity', '<=', 10)->orderBy('quantity')->get();
        $expiredMedicines = Medicine::where('expiration_date', '<', Carbon::now())->get();
        $expiringSoon = Medicine::where('expiration_date', '>=', Carbon::now())
            ->where('expiration_date', '<=', Carbon::now()->addMonth())
            ->get();

        return [
            'total_medicines' => Medicine::count(),
            'total_quantity' => Medicine::sum('quantity'),
            'low_stock_count' => $lowStock->count(),
            'expired_count' => $expiredMedicines->count(),
            'expiring_soon_count' => $expiringSoon->count(),
            // Detailed lists
            'all_medicines' => $allMedicines,
            'low_stock' => $lowStock,
            'expired' => $expiredMedicines,
            'expiring_soon' => $expiringSoon,
        ];
    }

    /**
     * Export report as CSV
     */
    private function exportAsCSV($report, $data)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . str_replace(' ', '_', $report->title) . '_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($report, $data) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [$report->title]);
            fputcsv($handle, ['Generated: ' . $report->created_at->format('Y-m-d H:i:s')]);
            fputcsv($handle, ['Period: ' . ($report->parameters['date_from'] ?? 'N/A') . ' to ' . ($report->parameters['date_to'] ?? 'N/A')]);
            fputcsv($handle, []);

            switch ($report->report_type) {
                case 'patient_statistics':
                    fputcsv($handle, ['Patient Statistics Report']);
                    fputcsv($handle, ['Total Patients', $data['total_patients'] ?? 0]);
                    fputcsv($handle, ['New Patients (Period)', $data['new_patients'] ?? 0]);
                    fputcsv($handle, []);
                    fputcsv($handle, ['Patient Name', 'Gender', 'Email', 'Educational Level', 'Date']);
                    if (isset($data['recent_patients'])) {
                        foreach ($data['recent_patients'] as $patient) {
                            fputcsv($handle, [
                                $patient->user->name ?? 'N/A',
                                $patient->user->gender ?? 'N/A',
                                $patient->user->email ?? 'N/A',
                                $patient->educationalLevel->level_name ?? 'N/A',
                                $patient->created_at->format('Y-m-d'),
                            ]);
                        }
                    }
                    break;

                case 'appointment_statistics':
                    fputcsv($handle, ['Appointment Statistics Report']);
                    fputcsv($handle, ['Total Appointments', $data['total_appointments'] ?? 0]);
                    fputcsv($handle, ['Completed', $data['completed'] ?? 0]);
                    fputcsv($handle, ['Pending', $data['pending'] ?? 0]);
                    fputcsv($handle, ['Cancelled', $data['cancelled'] ?? 0]);
                    fputcsv($handle, []);
                    fputcsv($handle, ['Date', 'Time', 'Patient', 'Doctor', 'Status']);
                    if (isset($data['recent_appointments'])) {
                        foreach ($data['recent_appointments'] as $apt) {
                            fputcsv($handle, [
                                $apt->date,
                                $apt->time,
                                $apt->patient->user->name ?? 'N/A',
                                $apt->doctor->user->name ?? 'N/A',
                                $apt->status,
                            ]);
                        }
                    }
                    break;

                case 'health_records':
                    fputcsv($handle, ['Health Records Report']);
                    fputcsv($handle, ['Total Health Records', $data['total_records'] ?? 0]);
                    fputcsv($handle, ['Total Physical Exams', $data['physical_exams'] ?? 0]);
                    fputcsv($handle, ['Total Dental Exams', $data['dental_exams'] ?? 0]);
                    fputcsv($handle, ['Total Immunizations', $data['immunizations'] ?? 0]);
                    fputcsv($handle, []);
                    fputcsv($handle, ['Health Records']);
                    fputcsv($handle, ['Date', 'Patient', 'Diagnosis', 'Symptoms', 'Treatment']);
                    if (isset($data['recent_records'])) {
                        foreach ($data['recent_records'] as $record) {
                            fputcsv($handle, [
                                $record->created_at->format('Y-m-d'),
                                $record->patient->user->name ?? 'N/A',
                                $record->diagnosis ?? 'N/A',
                                $record->symptoms ?? 'N/A',
                                $record->treatment ?? 'N/A',
                            ]);
                        }
                    }
                    fputcsv($handle, []);
                    fputcsv($handle, ['Physical Examinations']);
                    fputcsv($handle, ['Date', 'Patient', 'Height', 'Weight', 'BP', 'Heart', 'Remarks']);
                    if (isset($data['physical_examinations'])) {
                        foreach ($data['physical_examinations'] as $exam) {
                            fputcsv($handle, [
                                $exam->created_at->format('Y-m-d'),
                                $exam->patient->user->name ?? 'N/A',
                                $exam->height ?? 'N/A',
                                $exam->weight ?? 'N/A',
                                $exam->bp ?? 'N/A',
                                $exam->heart ?? 'N/A',
                                $exam->remarks ?? 'N/A',
                            ]);
                        }
                    }
                    fputcsv($handle, []);
                    fputcsv($handle, ['Dental Examinations']);
                    fputcsv($handle, ['Date', 'Patient', 'Doctor', 'Diagnosis']);
                    if (isset($data['dental_examinations'])) {
                        foreach ($data['dental_examinations'] as $exam) {
                            fputcsv($handle, [
                                $exam->created_at->format('Y-m-d'),
                                $exam->patient->user->name ?? 'N/A',
                                $exam->doctor->user->name ?? 'N/A',
                                $exam->diagnosis ?? 'N/A',
                            ]);
                        }
                    }
                    fputcsv($handle, []);
                    fputcsv($handle, ['Immunization Records']);
                    fputcsv($handle, ['Date', 'Patient', 'Vaccine', 'Dosage', 'Administered By']);
                    if (isset($data['immunization_records'])) {
                        foreach ($data['immunization_records'] as $record) {
                            fputcsv($handle, [
                                $record->created_at->format('Y-m-d'),
                                $record->patient->user->name ?? 'N/A',
                                $record->vaccine_name ?? 'N/A',
                                $record->dosage ?? 'N/A',
                                $record->administered_by ?? 'N/A',
                            ]);
                        }
                    }
                    break;

                case 'prescriptions':
                    fputcsv($handle, ['Prescriptions Report']);
                    fputcsv($handle, ['Total Prescriptions', $data['total_prescriptions'] ?? 0]);
                    fputcsv($handle, []);
                    fputcsv($handle, ['Date', 'Patient', 'Medicine', 'Dosage', 'Doctor']);
                    if (isset($data['prescriptions'])) {
                        foreach ($data['prescriptions'] as $rx) {
                            fputcsv($handle, [
                                $rx->created_at->format('Y-m-d'),
                                $rx->patient->user->name ?? 'N/A',
                                $rx->medicine->name ?? 'N/A',
                                $rx->dosage ?? 'N/A',
                                $rx->doctor->user->name ?? 'N/A',
                            ]);
                        }
                    }
                    break;

                case 'medicine_inventory':
                    fputcsv($handle, ['Medicine Inventory Report']);
                    fputcsv($handle, ['Total Medicines', $data['total_medicines'] ?? 0]);
                    fputcsv($handle, ['Low Stock Items', $data['low_stock_count'] ?? 0]);
                    fputcsv($handle, ['Expired Items', $data['expired_count'] ?? 0]);
                    fputcsv($handle, []);
                    fputcsv($handle, ['Medicine Name', 'Quantity', 'Expiration Date', 'Unit']);
                    if (isset($data['all_medicines'])) {
                        foreach ($data['all_medicines'] as $med) {
                            fputcsv($handle, [
                                $med->name,
                                $med->quantity,
                                $med->expiration_date,
                                $med->unit ?? 'N/A',
                            ]);
                        }
                    }
                    break;
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
