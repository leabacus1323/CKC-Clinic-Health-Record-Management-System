@extends('admin.layout.navigation')

@section('content')
@php
    $metrics = $report['metrics'];
    $trends = $report['trends'];
    $monthlyMax = max(1, collect($trends['monthly'])->max(fn ($item) => max($item['appointments'], $item['clinical_encounters'])) ?? 1);
    $categoryMax = max(1, collect($trends['visit_categories'])->max('count') ?? 1);
    $changeClass = fn ($value) => $value > 0 ? 'text-amber-700 bg-amber-50' : ($value < 0 ? 'text-emerald-700 bg-emerald-50' : 'text-slate-600 bg-slate-100');
@endphp

<div class="space-y-6">
    <div class="clinic-page-heading">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-teal-700">Automated decision support</p>
            <h1 class="clinic-page-title">Health Analytics & Reports</h1>
            <p class="clinic-page-subtitle">Privacy-safe aggregate statistics for health trends, clinic utilization, resource planning, and evidence-based policy.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.analytics.export', ['period' => $period]) }}" class="clinic-button-secondary"><x-clinic-nav-icon name="upload" class="h-5 w-5 rotate-180" />Export CSV</a>
            <form method="POST" action="{{ route('admin.analytics.generate') }}">
                @csrf
                <input type="hidden" name="period" value="{{ $period }}">
                <button class="clinic-button-primary"><x-clinic-nav-icon name="chart" class="h-5 w-5" />Save snapshot</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="rounded-2xl border border-teal-100 bg-teal-50 p-4">
        <div class="flex items-start gap-3">
            <span class="clinic-stat-icon bg-white"><x-clinic-nav-icon name="privacy" class="h-5 w-5" /></span>
            <div>
                <p class="text-sm font-extrabold text-teal-900">De-identified administrative reporting</p>
                <p class="mt-1 text-xs leading-5 text-teal-800">This dashboard shows aggregate workload, broad visit categories, and inventory indicators. Patient names, diagnoses, symptoms, treatment notes, and prescription instructions are not displayed.</p>
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.analytics.index') }}" class="clinic-card p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label for="period" class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">Reporting period</label>
                <select id="period" name="period" class="clinic-input">
                    @foreach([30 => 'Last 30 days', 90 => 'Last 90 days', 180 => 'Last 180 days', 365 => 'Last 12 months'] as $days => $label)
                        <option value="{{ $days }}" @selected($period === $days)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button class="clinic-button-primary">Apply period</button>
            <p class="text-xs text-slate-500 sm:pb-3">{{ $metrics['period_start'] }} to {{ $metrics['period_end'] }}</p>
        </div>
    </form>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="clinic-stat-card">
            <div class="flex items-start justify-between"><div><p class="text-sm font-semibold text-slate-500">Clinic Appointments</p><p class="mt-3 text-4xl font-extrabold text-slate-800">{{ number_format($metrics['appointments_total']) }}</p><span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $changeClass($metrics['appointment_change_percent']) }}">{{ $metrics['appointment_change_percent'] > 0 ? '+' : '' }}{{ $metrics['appointment_change_percent'] }}% vs previous</span></div><span class="clinic-stat-icon"><x-clinic-nav-icon name="calendar" class="h-6 w-6" /></span></div>
        </div>
        <div class="clinic-stat-card">
            <div class="flex items-start justify-between"><div><p class="text-sm font-semibold text-slate-500">Patients Served</p><p class="mt-3 text-4xl font-extrabold text-slate-800">{{ number_format($metrics['unique_patients_served']) }}</p><p class="mt-2 text-xs text-slate-500">Unique patients with appointments</p></div><span class="clinic-stat-icon"><x-clinic-nav-icon name="users" class="h-6 w-6" /></span></div>
        </div>
        <div class="clinic-stat-card">
            <div class="flex items-start justify-between"><div><p class="text-sm font-semibold text-slate-500">Clinical Service Activity</p><p class="mt-3 text-4xl font-extrabold text-slate-800">{{ number_format($metrics['clinical_encounters_total']) }}</p><span class="mt-2 inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $changeClass($metrics['clinical_change_percent']) }}">{{ $metrics['clinical_change_percent'] > 0 ? '+' : '' }}{{ $metrics['clinical_change_percent'] }}% vs previous</span></div><span class="clinic-stat-icon"><x-clinic-nav-icon name="records" class="h-6 w-6" /></span></div>
        </div>
        <div class="clinic-stat-card">
            <div class="flex items-start justify-between"><div><p class="text-sm font-semibold text-slate-500">Pending Workload</p><p class="mt-3 text-4xl font-extrabold text-slate-800">{{ number_format($metrics['pending_appointments'] + $metrics['pending_certificate_requests']) }}</p><p class="mt-2 text-xs text-slate-500">Appointments and requests</p></div><span class="clinic-stat-icon"><x-clinic-nav-icon name="alert" class="h-6 w-6" /></span></div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.6fr_1fr]">
        <section class="clinic-card">
            <div class="clinic-card-header"><div><h2 class="text-lg font-extrabold text-slate-800">Clinic Activity Trend</h2><p class="text-xs text-slate-500">Appointments compared with recorded clinical services</p></div></div>
            <div class="clinic-card-body space-y-4">
                @forelse($trends['monthly'] as $item)
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3 text-xs"><span class="font-bold text-slate-600">{{ $item['label'] }}</span><span class="text-slate-500">{{ $item['appointments'] }} appointments · {{ $item['clinical_encounters'] }} service entries</span></div>
                        <div class="grid gap-1">
                            <div class="h-3 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-teal-600" style="width: {{ min(100, ($item['appointments'] / $monthlyMax) * 100) }}%"></div></div>
                            <div class="h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-emerald-600" style="width: {{ min(100, ($item['clinical_encounters'] / $monthlyMax) * 100) }}%"></div></div>
                        </div>
                    </div>
                @empty
                    <p class="py-10 text-center text-sm text-slate-500">No activity data is available for this period.</p>
                @endforelse
                <div class="flex flex-wrap gap-4 border-t border-slate-100 pt-3 text-xs font-semibold text-slate-500"><span class="flex items-center gap-2"><i class="h-2.5 w-2.5 rounded-full bg-teal-600"></i>Appointments</span><span class="flex items-center gap-2"><i class="h-2.5 w-2.5 rounded-full bg-emerald-600"></i>Clinical service entries</span></div>
            </div>
        </section>

        <section class="clinic-card">
            <div class="clinic-card-header"><div><h2 class="text-lg font-extrabold text-slate-800">Utilization Indicators</h2><p class="text-xs text-slate-500">Operational load, not patient-level data</p></div></div>
            <div class="clinic-card-body grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                @foreach([
                    ['Average appointments per clinic day', $metrics['average_appointments_per_clinic_day']],
                    ['Appointments per clinician', $metrics['appointments_per_clinician']],
                    ['Active clinic professionals', $metrics['active_clinicians']],
                    ['Busiest weekday', $metrics['busiest_weekday']],
                    ['Pending appointment rate', $metrics['pending_rate'].'%'],
                    ['Cancellation rate', $metrics['cancellation_rate'].'%'],
                ] as [$label, $value])
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $label }}</p><p class="mt-2 text-xl font-extrabold text-slate-800">{{ $value }}</p></div>
                @endforeach
            </div>
        </section>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <section class="clinic-card">
            <div class="clinic-card-header"><div><h2 class="text-lg font-extrabold text-slate-800">Broad Health Trend Categories</h2><p class="text-xs text-slate-500">Aggregated from appointment reasons; small groups are suppressed</p></div></div>
            <div class="clinic-card-body space-y-4">
                @forelse($trends['visit_categories'] as $item)
                    <div><div class="mb-1 flex items-center justify-between text-sm"><span class="font-semibold text-slate-700">{{ $item['label'] }}</span><span class="font-extrabold text-slate-800">{{ $item['count'] }}</span></div><div class="h-2.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-teal-600" style="width: {{ min(100, ($item['count'] / $categoryMax) * 100) }}%"></div></div></div>
                @empty
                    <p class="py-8 text-center text-sm text-slate-500">At least three similar appointment reasons are required before a category is shown.</p>
                @endforelse
            </div>
        </section>

        <section class="clinic-card">
            <div class="clinic-card-header"><div><h2 class="text-lg font-extrabold text-slate-800">Service Mix</h2><p class="text-xs text-slate-500">Volume of major clinic activities</p></div></div>
            <div class="clinic-card-body grid grid-cols-2 gap-3 sm:grid-cols-3">
                @foreach([
                    ['Health records', $metrics['health_records'], 'records'],
                    ['Physical exams', $metrics['physical_examinations'], 'shield'],
                    ['Dental exams', $metrics['dental_examinations'], 'certificate'],
                    ['Prescriptions', $metrics['prescriptions'], 'medicine'],
                    ['Immunizations', $metrics['immunizations'], 'shield'],
                    ['Certificate requests', $metrics['pending_certificate_requests'], 'certificate'],
                ] as [$label, $value, $icon])
                    <div class="rounded-2xl border border-slate-200 p-4"><span class="clinic-stat-icon"><x-clinic-nav-icon :name="$icon" class="h-5 w-5" /></span><p class="mt-3 text-2xl font-extrabold text-slate-800">{{ number_format($value) }}</p><p class="text-xs font-semibold text-slate-500">{{ $label }}</p></div>
                @endforeach
            </div>
        </section>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <section class="clinic-card">
            <div class="clinic-card-header"><div><h2 class="text-lg font-extrabold text-slate-800">Evidence-Based Policy Actions</h2><p class="text-xs text-slate-500">Automatically generated from current indicators</p></div></div>
            <div class="clinic-card-body space-y-3">
                @foreach($report['recommendations'] as $item)
                    @php($priorityClass = match($item['priority']) { 'high' => 'border-rose-200 bg-rose-50 text-rose-800', 'medium' => 'border-amber-200 bg-amber-50 text-amber-800', default => 'border-sky-200 bg-sky-50 text-sky-800' })
                    <div class="rounded-2xl border p-4 {{ $priorityClass }}"><div class="flex flex-wrap items-center justify-between gap-2"><h3 class="text-sm font-extrabold">{{ $item['title'] }}</h3><span class="rounded-full bg-white/70 px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider">{{ $item['priority'] }}</span></div><p class="mt-2 text-xs leading-5">{{ $item['action'] }}</p></div>
                @endforeach
            </div>
        </section>

        <section class="clinic-card">
            <div class="clinic-card-header"><div><h2 class="text-lg font-extrabold text-slate-800">Resource Allocation</h2><p class="text-xs text-slate-500">Medicine inventory and patient population</p></div></div>
            <div class="clinic-card-body space-y-4">
                <div class="grid grid-cols-2 gap-3"><div class="rounded-2xl bg-rose-50 p-4"><p class="text-3xl font-extrabold text-rose-800">{{ $metrics['low_stock_medicines'] }}</p><p class="text-xs font-bold text-rose-700">Low-stock items</p></div><div class="rounded-2xl bg-amber-50 p-4"><p class="text-3xl font-extrabold text-amber-800">{{ $metrics['expiring_medicines'] }}</p><p class="text-xs font-bold text-amber-700">Expiring in 90 days</p></div></div>
                <div class="rounded-2xl border border-slate-200 p-4"><p class="text-xs font-bold uppercase tracking-wider text-slate-400">Registered health profiles</p><div class="mt-3 flex justify-between gap-4 text-sm"><span>Students <b class="text-slate-800">{{ number_format($metrics['student_profiles']) }}</b></span><span>Employees <b class="text-slate-800">{{ number_format($metrics['employee_profiles']) }}</b></span></div></div>
                @if(count($trends['top_medicines']))
                    <div><p class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-400">Frequently prescribed medicines</p><div class="space-y-2">@foreach($trends['top_medicines'] as $item)<div class="flex justify-between rounded-xl bg-slate-50 px-3 py-2 text-xs"><span class="font-semibold text-slate-700">{{ $item['label'] }}</span><span class="font-extrabold text-slate-800">{{ $item['count'] }}</span></div>@endforeach</div></div>
                @endif
            </div>
        </section>
    </div>

    <section class="clinic-card overflow-hidden">
        <div class="clinic-card-header"><div><h2 class="text-lg font-extrabold text-slate-800">Automated Report History</h2><p class="text-xs text-slate-500">Daily snapshots are created when the Laravel scheduler is running</p></div><code class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs text-slate-600">php artisan schedule:work</code></div>
        @if($snapshots->isNotEmpty())
            <div class="overflow-x-auto"><table class="clinic-table"><thead><tr><th>Generated</th><th>Period</th><th>Appointments</th><th>Encounters</th><th>Pending</th><th>Source</th></tr></thead><tbody>@foreach($snapshots as $snapshot)<tr><td>{{ $snapshot->generated_at?->format('M d, Y h:i A') }}</td><td>{{ $snapshot->period_days }} days</td><td>{{ number_format($snapshot->metrics['appointments_total'] ?? 0) }}</td><td>{{ number_format($snapshot->metrics['clinical_encounters_total'] ?? 0) }}</td><td>{{ number_format(($snapshot->metrics['pending_appointments'] ?? 0) + ($snapshot->metrics['pending_certificate_requests'] ?? 0)) }}</td><td><span class="clinic-badge bg-slate-100 text-slate-700">{{ $snapshot->generated_by === 'manual' ? ($snapshot->generatedByUser?->name ?? 'Administrator') : 'Scheduler' }}</span></td></tr>@endforeach</tbody></table></div>
        @else
            <div class="p-10 text-center"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400"><x-clinic-nav-icon name="chart" class="h-6 w-6" /></span><p class="mt-3 text-sm font-semibold text-slate-600">No saved snapshots yet</p><p class="mt-1 text-xs text-slate-500">Use “Save snapshot” or start the Laravel scheduler.</p></div>
        @endif
    </section>
</div>
@endsection
