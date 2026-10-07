<div class="space-y-6">
    <div class="clinic-page-heading">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-teal-700">Clinic operations</p>
            <h1 class="clinic-page-title">School Nurse Dashboard</h1>
            <p class="clinic-page-subtitle">Welcome back, {{ Auth::user()->name }}. Here is today’s clinic overview.</p>
        </div>
        <a href="{{ route('staff.appointments') }}" class="clinic-button-primary">
            <x-clinic-nav-icon name="calendar" class="h-5 w-5" />Manage appointments
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['label' => "Today's Appointments", 'value' => $todayAppointments, 'icon' => 'calendar', 'caption' => 'Scheduled for today'],
            ['label' => 'Total Patients', 'value' => $totalPatients, 'icon' => 'users', 'caption' => 'Registered profiles'],
            ['label' => 'Pending Requests', 'value' => $pendingAppointments, 'icon' => 'alert', 'caption' => 'Need clinic action'],
            ['label' => 'Health Records', 'value' => $healthRecordsCount, 'icon' => 'records', 'caption' => 'Clinical entries'],
        ] as $stat)
            <div class="clinic-stat-card">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-500">{{ $stat['label'] }}</p>
                        <p class="mt-3 text-4xl font-extrabold tracking-tight text-slate-800">{{ $stat['value'] }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ $stat['caption'] }}</p>
                    </div>
                    <span class="clinic-stat-icon"><x-clinic-nav-icon :name="$stat['icon']" class="h-6 w-6" /></span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.05fr_1.95fr]">
        <section class="clinic-card">
            <div class="clinic-card-header"><h2 class="text-lg font-extrabold text-slate-800">Quick Actions</h2></div>
            <div class="clinic-card-body grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                <a href="{{ route('staff.patients') }}" class="group flex items-center gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-teal-200 hover:bg-teal-50">
                    <span class="clinic-stat-icon"><x-clinic-nav-icon name="users" class="h-5 w-5" /></span>
                    <span><span class="block text-sm font-bold text-slate-800">Patient registry</span><span class="text-xs text-slate-500">Search and manage patient profiles</span></span>
                </a>
                <a href="{{ route('staff.health-records.create') }}" class="group flex items-center gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-teal-200 hover:bg-teal-50">
                    <span class="clinic-stat-icon"><x-clinic-nav-icon name="records" class="h-5 w-5" /></span>
                    <span><span class="block text-sm font-bold text-slate-800">New clinic record</span><span class="text-xs text-slate-500">Document a consultation or treatment</span></span>
                </a>
                <a href="{{ route('staff.certificate-requests') }}" class="group flex items-center gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-teal-200 hover:bg-teal-50">
                    <span class="clinic-stat-icon"><x-clinic-nav-icon name="certificate" class="h-5 w-5" /></span>
                    <span><span class="block text-sm font-bold text-slate-800">Certificate requests</span><span class="text-xs text-slate-500">Review student medical requests</span></span>
                </a>
                <a href="{{ route('staff.reports') }}" class="group flex items-center gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-teal-200 hover:bg-teal-50">
                    <span class="clinic-stat-icon"><x-clinic-nav-icon name="chart" class="h-5 w-5" /></span>
                    <span><span class="block text-sm font-bold text-slate-800">Clinic reports</span><span class="text-xs text-slate-500">Review statistics and exports</span></span>
                </a>
            </div>
        </section>

        <section class="clinic-card">
            <div class="clinic-card-header">
                <div><h2 class="text-lg font-extrabold text-slate-800">Operational Priorities</h2><p class="text-xs text-slate-500">Items requiring attention today</p></div>
                <a href="{{ route('staff.appointments') }}" class="text-sm font-bold text-teal-700 hover:underline">View all</a>
            </div>
            <div class="clinic-card-body">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl bg-teal-50 p-5"><p class="text-xs font-bold uppercase tracking-wider text-teal-700">Appointments</p><p class="mt-2 text-3xl font-extrabold text-teal-900">{{ $todayAppointments }}</p><p class="mt-1 text-xs text-teal-700">Today’s schedule</p></div>
                    <div class="rounded-2xl bg-amber-50 p-5"><p class="text-xs font-bold uppercase tracking-wider text-amber-700">Pending</p><p class="mt-2 text-3xl font-extrabold text-amber-900">{{ $pendingAppointments }}</p><p class="mt-1 text-xs text-amber-700">Awaiting confirmation</p></div>
                    <div class="rounded-2xl bg-slate-50 p-5"><p class="text-xs font-bold uppercase tracking-wider text-slate-600">Security</p><p class="mt-2 text-base font-extrabold text-slate-800">3-minute lock</p><p class="mt-1 text-xs text-slate-500">Automatic privacy protection</p></div>
                </div>
                <div class="mt-5 rounded-2xl border border-slate-200 p-4">
                    <div class="flex items-start gap-3"><span class="clinic-stat-icon"><x-clinic-nav-icon name="shield" class="h-5 w-5" /></span><div><p class="text-sm font-bold text-slate-800">Minimum-necessary access is active</p><p class="mt-1 text-xs leading-5 text-slate-500">Clinical views, changes, deletions, and exports are recorded in the permanent external audit stream.</p></div></div>
                </div>
            </div>
        </section>
    </div>
</div>
