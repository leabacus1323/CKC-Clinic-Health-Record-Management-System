<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\CertificateRequest;
use App\Models\DentalExamination;
use App\Models\HealthAnalyticsSnapshot;
use App\Models\HealthRecords;
use App\Models\ImmunizationRecords;
use App\Models\Medicine;
use App\Models\Patients;
use App\Models\PhysicalExamination;
use App\Models\PrescriptionRecord;
use App\Models\User;
use App\Support\SqlDateExpressions;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class HealthAnalyticsService
{
    public const ALLOWED_PERIODS = [30, 90, 180, 365];

    public function generate(int $periodDays = 30): array
    {
        $periodDays = in_array($periodDays, self::ALLOWED_PERIODS, true) ? $periodDays : 30;
        $end = now()->endOfDay();
        $start = now()->subDays($periodDays - 1)->startOfDay();
        $previousEnd = $start->copy()->subSecond();
        $previousStart = $previousEnd->copy()->subDays($periodDays - 1)->startOfDay();

        $appointments = Appointment::query()
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get(['id', 'patient_id', 'doc_id', 'date', 'time', 'status', 'reason']);

        $previousAppointments = Appointment::query()
            ->whereBetween('date', [$previousStart->toDateString(), $previousEnd->toDateString()])
            ->get(['id', 'patient_id', 'date', 'status']);

        $clinicalCurrent = $this->clinicalCounts($start, $end);
        $clinicalPrevious = $this->clinicalCounts($previousStart, $previousEnd);
        $statusCounts = $appointments->countBy(fn ($item) => strtolower(trim((string) $item->status)));
        $activeClinicians = User::whereIn('user_type', [User::TYPE_STAFF, User::TYPE_DOCTOR])->count();
        $clinicDays = max(1, collect(CarbonPeriod::create($start, $end))->reject(fn (Carbon $date) => $date->isWeekend())->count());
        $totalAppointments = $appointments->count();
        $confirmed = (int) ($statusCounts['confirmed'] ?? 0) + (int) ($statusCounts['completed'] ?? 0);
        $cancelled = (int) ($statusCounts['cancelled'] ?? 0);
        $pending = (int) ($statusCounts['pending'] ?? 0);
        $uniquePatients = $appointments->pluck('patient_id')->filter()->unique()->count();

        $studentProfiles = Patients::query()->where('patient_type', 1)->count();
        $employeeProfiles = Patients::query()->where(function ($query): void {
            $query->whereNull('patient_type')->orWhere('patient_type', '<>', 1);
        })->count();

        $lowStockThreshold = max(1, (int) config('clinic.low_stock_threshold', 10));
        $lowStockMedicines = Medicine::query()
            ->where('quantity', '<=', $lowStockThreshold)
            ->orderBy('quantity')
            ->limit(10)
            ->get(['id', 'name', 'generic_name', 'quantity', 'unit', 'expiration_date']);

        $expiringMedicines = Medicine::query()
            ->whereNotNull('expiration_date')
            ->whereBetween('expiration_date', [today()->toDateString(), today()->addDays(90)->toDateString()])
            ->orderBy('expiration_date')
            ->limit(10)
            ->get(['id', 'name', 'quantity', 'unit', 'expiration_date']);

        $topMedicines = PrescriptionRecord::query()
            ->join('medicine', 'prescription_records.medicine_id', '=', 'medicine.id')
            ->whereBetween('prescription_records.created_at', [$start, $end])
            ->selectRaw('medicine.name, COUNT(*) as aggregate')
            ->groupBy('medicine.name')
            ->orderByDesc('aggregate')
            ->limit(8)
            ->get()
            ->map(fn ($row) => ['label' => $row->name, 'count' => (int) $row->aggregate])
            ->values()
            ->all();

        $visitCategories = $this->groupVisitReasons($appointments);
        $monthly = $this->monthlySeries($start, $end, $appointments);
        $busiestWeekday = $this->busiestWeekday($appointments);
        $appointmentChange = $this->percentChange($totalAppointments, $previousAppointments->count());
        $clinicalChange = $this->percentChange($clinicalCurrent['total'], $clinicalPrevious['total']);
        $cancellationRate = $totalAppointments > 0 ? round(($cancelled / $totalAppointments) * 100, 1) : 0.0;
        $pendingRate = $totalAppointments > 0 ? round(($pending / $totalAppointments) * 100, 1) : 0.0;

        $metrics = [
            'appointments_total' => $totalAppointments,
            'appointments_previous' => $previousAppointments->count(),
            'appointment_change_percent' => $appointmentChange,
            'pending_appointments' => $pending,
            'confirmed_appointments' => $confirmed,
            'cancelled_appointments' => $cancelled,
            'cancellation_rate' => $cancellationRate,
            'pending_rate' => $pendingRate,
            'unique_patients_served' => $uniquePatients,
            'average_appointments_per_clinic_day' => round($totalAppointments / $clinicDays, 1),
            'appointments_per_clinician' => $activeClinicians > 0 ? round($totalAppointments / $activeClinicians, 1) : 0,
            'active_clinicians' => $activeClinicians,
            'busiest_weekday' => $busiestWeekday,
            'clinical_encounters_total' => $clinicalCurrent['total'],
            'clinical_encounters_previous' => $clinicalPrevious['total'],
            'clinical_change_percent' => $clinicalChange,
            'health_records' => $clinicalCurrent['health_records'],
            'physical_examinations' => $clinicalCurrent['physical_examinations'],
            'dental_examinations' => $clinicalCurrent['dental_examinations'],
            'prescriptions' => $clinicalCurrent['prescriptions'],
            'immunizations' => $clinicalCurrent['immunizations'],
            'pending_certificate_requests' => CertificateRequest::where('status', CertificateRequest::STATUS_PENDING)->count(),
            'low_stock_medicines' => $lowStockMedicines->count(),
            'expiring_medicines' => $expiringMedicines->count(),
            'student_profiles' => $studentProfiles,
            'employee_profiles' => $employeeProfiles,
            'period_days' => $periodDays,
            'period_start' => $start->toDateString(),
            'period_end' => $end->toDateString(),
        ];

        $trends = [
            'monthly' => $monthly,
            'visit_categories' => $visitCategories,
            'top_medicines' => $topMedicines,
            'low_stock_items' => $lowStockMedicines->map(fn (Medicine $medicine) => [
                'name' => $medicine->name,
                'generic_name' => $medicine->generic_name,
                'quantity' => (int) $medicine->quantity,
                'unit' => $medicine->unit,
            ])->values()->all(),
            'expiring_items' => $expiringMedicines->map(fn (Medicine $medicine) => [
                'name' => $medicine->name,
                'quantity' => (int) $medicine->quantity,
                'unit' => $medicine->unit,
                'expiration_date' => $medicine->expiration_date ? Carbon::parse($medicine->expiration_date)->format('Y-m-d') : null,
            ])->values()->all(),
        ];

        return [
            'metrics' => $metrics,
            'trends' => $trends,
            'recommendations' => $this->recommendations($metrics, $trends),
            'generated_at' => now()->toIso8601String(),
        ];
    }

    public function createSnapshot(int $periodDays = 30, string $source = 'scheduler', ?int $userId = null): HealthAnalyticsSnapshot
    {
        $report = $this->generate($periodDays);

        return HealthAnalyticsSnapshot::create([
            'period_days' => $periodDays,
            'period_start' => $report['metrics']['period_start'],
            'period_end' => $report['metrics']['period_end'],
            'metrics' => $report['metrics'],
            'trends' => $report['trends'],
            'recommendations' => $report['recommendations'],
            'generated_by' => $source,
            'generated_by_user_id' => $userId,
            'generated_at' => now(),
        ]);
    }

    private function clinicalCounts(Carbon $start, Carbon $end): array
    {
        $counts = [
            'health_records' => HealthRecords::whereBetween('created_at', [$start, $end])->count(),
            'physical_examinations' => PhysicalExamination::whereBetween('created_at', [$start, $end])->count(),
            'dental_examinations' => DentalExamination::whereBetween('created_at', [$start, $end])->count(),
            'prescriptions' => PrescriptionRecord::whereBetween('created_at', [$start, $end])->count(),
            'immunizations' => ImmunizationRecords::whereBetween('created_at', [$start, $end])->count(),
        ];
        $counts['total'] = array_sum($counts);

        return $counts;
    }

    private function monthlySeries(Carbon $start, Carbon $end, Collection $appointments): array
    {
        $months = collect(CarbonPeriod::create($start->copy()->startOfMonth(), '1 month', $end->copy()->startOfMonth()))
            ->map(fn (Carbon $date) => $date->format('Y-m'))
            ->values();

        $appointmentCounts = $appointments->countBy(fn ($item) => Carbon::parse($item->date)->format('Y-m'));
        $encounters = collect();
        $monthExpression = SqlDateExpressions::yearMonth('created_at');

        foreach ([HealthRecords::class, PhysicalExamination::class, DentalExamination::class, PrescriptionRecord::class, ImmunizationRecords::class] as $model) {
            $model::query()
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw($monthExpression.' as month, COUNT(*) as aggregate')
                ->groupBy('month')
                ->pluck('aggregate', 'month')
                ->each(function ($count, $month) use (&$encounters): void {
                    $encounters[$month] = (int) ($encounters[$month] ?? 0) + (int) $count;
                });
        }

        return $months->map(fn (string $month) => [
            'month' => $month,
            'label' => Carbon::createFromFormat('Y-m', $month)->format('M Y'),
            'appointments' => (int) ($appointmentCounts[$month] ?? 0),
            'clinical_encounters' => (int) ($encounters[$month] ?? 0),
        ])->all();
    }

    private function busiestWeekday(Collection $appointments): string
    {
        if ($appointments->isEmpty()) {
            return 'No data';
        }

        return (string) $appointments
            ->countBy(fn ($item) => Carbon::parse($item->date)->format('l'))
            ->sortDesc()
            ->keys()
            ->first();
    }

    private function groupVisitReasons(Collection $appointments): array
    {
        $groups = [
            'Routine checkup' => ['checkup', 'check-up', 'physical', 'clearance', 'routine'],
            'Respiratory concern' => ['cough', 'cold', 'flu', 'asthma', 'breath', 'respiratory', 'sore throat'],
            'Fever or headache' => ['fever', 'headache', 'migraine', 'dizziness'],
            'Injury or pain' => ['injury', 'wound', 'sprain', 'fracture', 'pain', 'accident'],
            'Digestive concern' => ['stomach', 'abdominal', 'diarrhea', 'vomit', 'nausea', 'digestive'],
            'Dental concern' => ['dental', 'tooth', 'teeth', 'gum'],
            'Immunization' => ['vaccine', 'vaccination', 'immunization', 'booster'],
        ];

        $counts = [];
        foreach ($appointments as $appointment) {
            $reason = strtolower(trim((string) $appointment->reason));
            $category = 'Other clinic concerns';
            foreach ($groups as $label => $keywords) {
                if (collect($keywords)->contains(fn (string $keyword) => str_contains($reason, $keyword))) {
                    $category = $label;
                    break;
                }
            }
            $counts[$category] = ($counts[$category] ?? 0) + 1;
        }

        arsort($counts);

        return collect($counts)
            ->filter(fn (int $count) => $count >= 3)
            ->map(fn (int $count, string $label) => ['label' => $label, 'count' => $count])
            ->values()
            ->take(8)
            ->all();
    }

    private function recommendations(array $metrics, array $trends): array
    {
        $recommendations = [];

        if ($metrics['appointments_total'] >= 5 && $metrics['pending_rate'] >= 25) {
            $recommendations[] = $this->recommendation('high', 'Reduce appointment backlog', 'Pending appointments represent '.$metrics['pending_rate'].'% of clinic activity. Assign a daily triage block or additional reviewer during peak days.');
        }
        if ($metrics['appointments_total'] >= 10 && $metrics['appointment_change_percent'] >= 15) {
            $recommendations[] = $this->recommendation('medium', 'Prepare for rising clinic demand', 'Appointments increased by '.$metrics['appointment_change_percent'].'% compared with the previous period. Review staffing coverage on '.$metrics['busiest_weekday'].'s.');
        }
        if ($metrics['appointments_total'] >= 5 && $metrics['cancellation_rate'] >= 15) {
            $recommendations[] = $this->recommendation('medium', 'Improve appointment attendance', 'The cancellation rate is '.$metrics['cancellation_rate'].'%. Use reminder announcements and confirm appointments one day before the schedule.');
        }
        if ($metrics['low_stock_medicines'] > 0) {
            $recommendations[] = $this->recommendation('high', 'Replenish low-stock medicines', $metrics['low_stock_medicines'].' medicine item(s) are at or below the reorder threshold. Prioritize frequently prescribed items.');
        }
        if ($metrics['expiring_medicines'] > 0) {
            $recommendations[] = $this->recommendation('medium', 'Use or rotate expiring stock', $metrics['expiring_medicines'].' medicine item(s) expire within 90 days. Apply first-expire, first-out rotation and avoid over-ordering.');
        }
        if ($metrics['clinical_encounters_total'] >= 10 && $metrics['clinical_change_percent'] >= 20) {
            $recommendations[] = $this->recommendation('medium', 'Strengthen preventive health activities', 'Recorded clinical service activity rose by '.$metrics['clinical_change_percent'].'%. Use the visit-category trend to target health education and screening.');
        }

        if ($metrics['appointments_total'] < 5 && $metrics['clinical_encounters_total'] < 5) {
            $recommendations[] = $this->recommendation('info', 'Collect more data before major policy changes', 'The selected period contains limited activity. Use a longer reporting period and combine the indicators with professional review before changing staffing or health-service policies.');
        }

        $topCategory = $trends['visit_categories'][0] ?? null;
        if ($topCategory) {
            $recommendations[] = $this->recommendation('info', 'Target the leading health concern', $topCategory['label'].' is the most frequent aggregated visit category with '.$topCategory['count'].' appointment(s). Consider a focused information campaign or screening activity.');
        }

        if ($recommendations === []) {
            $recommendations[] = $this->recommendation('info', 'Maintain current service capacity', 'Clinic demand and inventory indicators are stable. Continue monthly review and retain current staffing and stock controls.');
        }

        return $recommendations;
    }

    private function recommendation(string $priority, string $title, string $action): array
    {
        return compact('priority', 'title', 'action');
    }

    private function percentChange(int $current, int $previous): float
    {
        if ($previous === 0) {
            return $current === 0 ? 0.0 : 100.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
