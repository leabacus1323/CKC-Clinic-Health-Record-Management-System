@extends('admin.layout.navigation')
@section('content')
<div class="space-y-6">
    <div class="clinic-page-heading">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-teal-700">System administration</p>
            <h1 class="clinic-page-title">Administration Dashboard</h1>
            <p class="clinic-page-subtitle">Manage accounts, review automated health statistics, allocate resources, and monitor infrastructure status.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="clinic-button-primary"><x-clinic-nav-icon name="users" class="h-5 w-5" />Add account</a>
    </div>

    <div class="rounded-2xl border border-teal-100 bg-teal-50 p-4">
        <div class="flex items-start gap-3">
            <span class="clinic-stat-icon bg-white"><x-clinic-nav-icon name="privacy" class="h-5 w-5" /></span>
            <div><p class="text-sm font-extrabold text-teal-900">Administrative privacy boundary</p><p class="mt-1 text-xs leading-5 text-teal-800">This role manages system configuration and accounts. Patient medical text, clinical notes, individual prescriptions, and treatment records remain unavailable.</p></div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">
        @foreach([
            ['label' => 'Total Accounts', 'value' => $summary['total_users'], 'icon' => 'users'],
            ['label' => 'Administrators', 'value' => $summary['administrators'], 'icon' => 'settings'],
            ['label' => 'School Nurses', 'value' => $summary['nurses'], 'icon' => 'records'],
            ['label' => 'Doctors', 'value' => $summary['doctors'], 'icon' => 'shield'],
            ['label' => 'Teachers', 'value' => $summary['teachers'], 'icon' => 'users'],
            ['label' => 'Guardians', 'value' => $summary['guardians'], 'icon' => 'privacy'],
        ] as $stat)
            <div class="clinic-stat-card">
                <div class="flex items-start justify-between gap-3"><div><p class="text-xs font-semibold text-slate-500">{{ $stat['label'] }}</p><p class="mt-3 text-3xl font-extrabold text-slate-800">{{ $stat['value'] }}</p></div><span class="clinic-stat-icon"><x-clinic-nav-icon :name="$stat['icon']" class="h-5 w-5" /></span></div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1fr_1.8fr]">
        <section class="clinic-card">
            <div class="clinic-card-header"><h2 class="text-lg font-extrabold text-slate-800">Quick Administration</h2></div>
            <div class="clinic-card-body space-y-3">
                <a href="{{ route('admin.analytics.index') }}" class="flex items-center gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-teal-200 hover:bg-teal-50"><span class="clinic-stat-icon"><x-clinic-nav-icon name="chart" class="h-5 w-5" /></span><span><span class="block text-sm font-bold text-slate-800">Health analytics</span><span class="text-xs text-slate-500">Automated trends, utilization, resources, and policy indicators</span></span></a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-teal-200 hover:bg-teal-50"><span class="clinic-stat-icon"><x-clinic-nav-icon name="users" class="h-5 w-5" /></span><span><span class="block text-sm font-bold text-slate-800">User accounts</span><span class="text-xs text-slate-500">Students, staff, teachers, and guardians</span></span></a>
                <a href="{{ route('guardian-approvals.index') }}" class="flex items-center gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-teal-200 hover:bg-teal-50"><span class="clinic-stat-icon"><x-clinic-nav-icon name="shield" class="h-5 w-5" /></span><span><span class="block text-sm font-bold text-slate-800">Guardian verification</span><span class="text-xs text-slate-500">Approve legal guardian relationships</span></span></a>
                <a href="{{ route('immunization-requirements.index') }}" class="flex items-center gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-teal-200 hover:bg-teal-50"><span class="clinic-stat-icon"><x-clinic-nav-icon name="settings" class="h-5 w-5" /></span><span><span class="block text-sm font-bold text-slate-800">Vaccine requirements</span><span class="text-xs text-slate-500">Configure compliance rules</span></span></a>
                <a href="{{ route('admin.system.index') }}" class="flex items-center gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-teal-200 hover:bg-teal-50"><span class="clinic-stat-icon"><x-clinic-nav-icon name="chart" class="h-5 w-5" /></span><span><span class="block text-sm font-bold text-slate-800">Infrastructure logs</span><span class="text-xs text-slate-500">Review staff, doctor, administrator, and system activity</span></span></a>
            </div>
        </section>

        <section class="clinic-card">
            <div class="clinic-card-header"><div><h2 class="text-lg font-extrabold text-slate-800">Infrastructure Status</h2><p class="text-xs text-slate-500">Current application security and runtime configuration</p></div><a href="{{ route('admin.system.index') }}" class="text-sm font-bold text-teal-700 hover:underline">Open logs</a></div>
            <div class="clinic-card-body grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach([
                    'Environment' => $system['environment'],
                    'Database' => $system['database'],
                    'Cache store' => $system['cache'],
                    'Session driver' => $system['session'],
                    'Queue connection' => $system['queue'],
                    'Idle lock' => $system['idleLock'].' seconds',
                    'Audit sink' => $system['auditDriver'],
                    'Audit failure mode' => $system['auditFailClosed'] ? 'Fail closed' : 'Fail open',
                    'External audit' => $system['auditExternalRequired'] ? 'Required' : 'Not enforced',
                    'Audit separation' => $system['auditSeparationConfirmed'] ? 'Confirmed' : 'Not confirmed',
                ] as $label => $value)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $label }}</p><p class="mt-2 break-words text-sm font-extrabold text-slate-800">{{ $value }}</p></div>
                @endforeach
            </div>
        </section>
    </div>
</div>
@endsection
