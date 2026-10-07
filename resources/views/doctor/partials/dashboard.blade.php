<div class="space-y-6">
    <div class="clinic-page-heading">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-teal-700">Clinical care</p>
            <h1 class="clinic-page-title">Doctor Dashboard</h1>
            <p class="clinic-page-subtitle">Welcome back, Dr. {{ $doctor->user->name }}. Review appointments and patient activity.</p>
        </div>
        <a href="{{ route('doctor.health-records.create') }}" class="clinic-button-primary"><x-clinic-nav-icon name="records" class="h-5 w-5" />New health record</a>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['label' => "Today's Appointments", 'value' => $todayAppointments, 'icon' => 'calendar', 'caption' => 'Scheduled consultations'],
            ['label' => 'This Week', 'value' => $weeklyAppointments, 'icon' => 'chart', 'caption' => 'Weekly appointments'],
            ['label' => 'Total Patients', 'value' => $totalPatients, 'icon' => 'users', 'caption' => 'Accessible profiles'],
            ['label' => 'Pending', 'value' => $pendingAppointments, 'icon' => 'alert', 'caption' => 'Awaiting action'],
        ] as $stat)
            <div class="clinic-stat-card">
                <div class="flex items-start justify-between gap-4">
                    <div><p class="text-sm font-semibold text-slate-500">{{ $stat['label'] }}</p><p class="mt-3 text-4xl font-extrabold tracking-tight text-slate-800">{{ $stat['value'] }}</p><p class="mt-1 text-xs text-slate-400">{{ $stat['caption'] }}</p></div>
                    <span class="clinic-stat-icon"><x-clinic-nav-icon :name="$stat['icon']" class="h-6 w-6" /></span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[0.8fr_2.2fr]">
        <section class="clinic-card">
            <div class="clinic-card-header"><h2 class="text-lg font-extrabold text-slate-800">Quick Actions</h2></div>
            <div class="clinic-card-body space-y-3">
                <a href="{{ route('doctor.appointments') }}" class="clinic-button-primary w-full"><x-clinic-nav-icon name="calendar" class="h-5 w-5" />Manage appointments</a>
                <a href="{{ route('doctor.patients') }}" class="clinic-button-secondary w-full"><x-clinic-nav-icon name="users" class="h-5 w-5" />View patients</a>
                <a href="{{ route('doctor.prescriptions.create-page') }}" class="clinic-button-secondary w-full"><x-clinic-nav-icon name="medicine" class="h-5 w-5" />Create prescription</a>
                <a href="{{ route('doctor.certificate-requests') }}" class="clinic-button-secondary w-full"><x-clinic-nav-icon name="certificate" class="h-5 w-5" />Certificate requests</a>
            </div>
        </section>

        <section class="clinic-card overflow-hidden">
            <div class="clinic-card-header">
                <div><h2 class="text-lg font-extrabold text-slate-800">Recent Appointments</h2><p class="text-xs text-slate-500">Latest patient consultations</p></div>
                <a href="{{ route('doctor.appointments') }}" class="text-sm font-bold text-teal-700 hover:underline">View schedule</a>
            </div>
            @if($recentAppointments->isEmpty())
                <div class="p-10 text-center"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400"><x-clinic-nav-icon name="calendar" class="h-6 w-6" /></span><p class="mt-3 text-sm font-semibold text-slate-600">No recent appointments</p></div>
            @else
                <div class="overflow-x-auto">
                    <table class="clinic-table">
                        <thead><tr><th>Patient</th><th>Date</th><th>Time</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($recentAppointments as $appointment)
                                @php($status = strtolower((string) $appointment->status))
                                <tr>
                                    <td class="font-semibold text-slate-800">{{ $appointment->patient->user->name ?? 'N/A' }}</td>
                                    <td>{{ $appointment->date }}</td>
                                    <td>{{ $appointment->time }}</td>
                                    <td><span class="clinic-badge {{ in_array($status, ['confirmed', 'completed']) ? 'bg-emerald-100 text-emerald-800' : ($status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">{{ ucfirst($status ?: 'unknown') }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>

    <section class="clinic-card">
        <div class="clinic-card-header"><div><h2 class="text-lg font-extrabold text-slate-800">Monthly Appointments</h2><p class="text-xs text-slate-500">Appointment volume by month</p></div></div>
        <div class="clinic-card-body grid grid-cols-3 gap-3 sm:grid-cols-6 xl:grid-cols-12">
            @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $index => $month)
                <div class="rounded-xl bg-slate-50 p-3 text-center"><p class="text-xl font-extrabold text-teal-700">{{ $monthlyStats[$index + 1] ?? 0 }}</p><p class="mt-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ $month }}</p></div>
            @endforeach
        </div>
    </section>
</div>
