@extends('admin.layout.navigation')

@section('content')
<div class="space-y-6">
    <div class="clinic-page-heading">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-teal-700">System accountability</p>
            <h1 class="clinic-page-title">Infrastructure and Clinical Activity Logs</h1>
            <p class="clinic-page-subtitle">Retained operational metadata from administrators, clinic staff, and doctors. Patient medical text, request payloads, and treatment notes are excluded.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="clinic-button-secondary"><x-clinic-nav-icon name="dashboard" class="h-5 w-5" />Dashboard</a>
    </div>

    <div class="rounded-2xl border border-teal-100 bg-teal-50 p-4">
        <div class="flex items-start gap-3">
            <span class="clinic-stat-icon bg-white"><x-clinic-nav-icon name="privacy" class="h-5 w-5" /></span>
            <div><p class="text-sm font-extrabold text-teal-900">Staff and doctor logs are retained centrally</p><p class="mt-1 text-xs leading-5 text-teal-800">The default view shows clinic staff and doctor activity. Use the role filter to review staff, doctors, administrators, or all retained events.</p></div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['label' => 'Staff Events', 'value' => $summary['staff'], 'icon' => 'users'],
            ['label' => 'Doctor Events', 'value' => $summary['doctors'], 'icon' => 'shield'],
            ['label' => 'Clinical Events Today', 'value' => $summary['today'], 'icon' => 'calendar'],
            ['label' => 'Failed Clinical Requests', 'value' => $summary['failed'], 'icon' => 'alert'],
        ] as $stat)
            <div class="clinic-stat-card"><div class="flex items-start justify-between gap-3"><div><p class="text-xs font-semibold text-slate-500">{{ $stat['label'] }}</p><p class="mt-3 text-3xl font-extrabold text-slate-800">{{ number_format($stat['value']) }}</p></div><span class="clinic-stat-icon"><x-clinic-nav-icon :name="$stat['icon']" class="h-5 w-5" /></span></div></div>
        @endforeach
    </div>

    <section class="clinic-card">
        <div class="clinic-card-header"><div><h2 class="text-lg font-extrabold text-slate-800">Runtime Configuration</h2><p class="text-xs text-slate-500">Sanitized application and security status</p></div></div>
        <div class="clinic-card-body grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
            @foreach($system as $label => $value)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ str_replace('_', ' ', $label) }}</p><p class="mt-2 break-words text-sm font-extrabold text-slate-800">{{ is_bool($value) ? ($value ? 'Enabled' : 'Disabled') : $value }}</p></div>
            @endforeach
        </div>
    </section>

    <section class="clinic-card">
        <div class="clinic-card-header"><div><h2 class="text-lg font-extrabold text-slate-800">Retained Activity</h2><p class="text-xs text-slate-500">Filter by role, module, outcome, date, account, route, or IP address.</p></div></div>
        <div class="border-b border-slate-100 p-4">
            <form method="GET" action="{{ route('admin.system.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-6">
                <select name="role" class="clinic-input">
                    <option value="clinical" @selected($role === 'clinical')>Staff and doctors</option>
                    <option value="staff" @selected($role === 'staff')>Staff only</option>
                    <option value="doctor" @selected($role === 'doctor')>Doctors only</option>
                    <option value="admin" @selected($role === 'admin')>Administrators only</option>
                    <option value="all" @selected($role === 'all')>All roles</option>
                </select>
                <select name="category" class="clinic-input">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ str($category)->replace('-', ' ')->headline() }}</option>
                    @endforeach
                </select>
                <select name="status" class="clinic-input"><option value="">All outcomes</option><option value="success" @selected(request('status') === 'success')>Successful</option><option value="failed" @selected(request('status') === 'failed')>Failed</option></select>
                <input type="date" name="from" value="{{ request('from') }}" class="clinic-input" aria-label="From date">
                <input type="date" name="to" value="{{ request('to') }}" class="clinic-input" aria-label="To date">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Name, route, or IP" class="clinic-input">
                <div class="flex gap-2 md:col-span-2 xl:col-span-6 xl:justify-end"><button class="clinic-button-primary" type="submit">Apply Filters</button><a href="{{ route('admin.system.index') }}" class="clinic-button-secondary">Reset</a></div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="clinic-table">
                <thead><tr><th>Date and Time</th><th>User</th><th>Role</th><th>Action</th><th>Category</th><th>Description</th><th>Route</th><th>Status</th><th>IP Address</th></tr></thead>
                <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td class="whitespace-nowrap">{{ $event->occurred_at?->timezone(config('app.timezone'))->format('M d, Y h:i:s A') }}</td>
                            <td><p class="font-bold text-slate-800">{{ $event->actor?->name ?? 'Deleted/System Account' }}</p><p class="text-xs text-slate-500">{{ $event->actor?->email }}</p></td>
                            <td class="min-w-40">{{ $event->actor_role ?? $event->actor?->roleLabel() ?? 'System' }}</td>
                            <td><span class="clinic-badge {{ $event->action === 'READ' ? 'bg-sky-100 text-sky-800' : 'bg-amber-100 text-amber-800' }}">{{ $event->action }}</span></td>
                            <td>{{ str($event->event_category ?? 'portal')->replace('-', ' ')->headline() }}</td>
                            <td class="min-w-64">{{ $event->description ?? 'Portal activity' }}</td>
                            <td class="font-mono text-xs">{{ $event->route_name }}</td>
                            <td><span class="clinic-badge {{ ($event->response_status ?? 500) < 400 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">{{ $event->response_status }}</span></td>
                            <td class="whitespace-nowrap">{{ $event->ip_address }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-4 py-12 text-center text-slate-500">No matching infrastructure activity was found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 p-4">{{ $events->links() }}</div>
    </section>
</div>
@endsection
