@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="clinic-page-heading">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-teal-700">Operational accountability</p>
            <h1 class="clinic-page-title">My Infrastructure Logs</h1>
            <p class="clinic-page-subtitle">Review your own portal activity and request outcomes. Medical record contents, form values, and patient notes are never stored here.</p>
        </div>
        <a href="{{ route('staff.dashboard') }}" class="clinic-button-secondary">
            <x-clinic-nav-icon name="dashboard" class="h-5 w-5" />Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['label' => 'Events Today', 'value' => $summary['today'], 'icon' => 'calendar'],
            ['label' => 'Changes Recorded', 'value' => $summary['writes'], 'icon' => 'records'],
            ['label' => 'Failed Requests', 'value' => $summary['failed'], 'icon' => 'alert'],
            ['label' => 'Total Activity', 'value' => $summary['total'], 'icon' => 'chart'],
        ] as $stat)
            <div class="clinic-stat-card">
                <div class="flex items-start justify-between gap-3">
                    <div><p class="text-xs font-semibold text-slate-500">{{ $stat['label'] }}</p><p class="mt-3 text-3xl font-extrabold text-slate-800">{{ number_format($stat['value']) }}</p></div>
                    <span class="clinic-stat-icon"><x-clinic-nav-icon :name="$stat['icon']" class="h-5 w-5" /></span>
                </div>
            </div>
        @endforeach
    </div>

    <section class="clinic-card">
        <div class="clinic-card-header">
            <div><h2 class="text-lg font-extrabold text-slate-800">Runtime Status</h2><p class="text-xs text-slate-500">Read-only service configuration visible to clinic staff</p></div>
        </div>
        <div class="clinic-card-body grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
            @foreach($system as $label => $value)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $label }}</p><p class="mt-2 break-words text-sm font-extrabold text-slate-800">{{ $value }}</p></div>
            @endforeach
        </div>
    </section>

    <section class="clinic-card">
        <div class="clinic-card-header">
            <div><h2 class="text-lg font-extrabold text-slate-800">Activity History</h2><p class="text-xs text-slate-500">Only events performed through your account are shown.</p></div>
        </div>
        <div class="border-b border-slate-100 p-4">
            <form method="GET" action="{{ route('staff.infrastructure-logs.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <select name="category" class="clinic-input">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" @selected(request('category') === $category)>{{ str($category)->replace('-', ' ')->headline() }}</option>
                    @endforeach
                </select>
                <select name="status" class="clinic-input">
                    <option value="">All outcomes</option>
                    <option value="success" @selected(request('status') === 'success')>Successful</option>
                    <option value="failed" @selected(request('status') === 'failed')>Failed</option>
                </select>
                <div class="flex gap-2"><button class="clinic-button-primary flex-1" type="submit">Apply Filter</button><a href="{{ route('staff.infrastructure-logs.index') }}" class="clinic-button-secondary">Reset</a></div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="clinic-table">
                <thead><tr><th>Date and Time</th><th>Action</th><th>Category</th><th>Description</th><th>Route</th><th>Status</th><th>IP Address</th></tr></thead>
                <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td class="whitespace-nowrap">{{ $event->occurred_at?->timezone(config('app.timezone'))->format('M d, Y h:i:s A') }}</td>
                            <td><span class="clinic-badge {{ $event->action === 'READ' ? 'bg-sky-100 text-sky-800' : 'bg-amber-100 text-amber-800' }}">{{ $event->action }}</span></td>
                            <td>{{ str($event->event_category ?? 'portal')->replace('-', ' ')->headline() }}</td>
                            <td class="min-w-64">{{ $event->description ?? 'Portal activity' }}</td>
                            <td class="font-mono text-xs">{{ $event->route_name }}</td>
                            <td><span class="clinic-badge {{ ($event->response_status ?? 500) < 400 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">{{ $event->response_status }}</span></td>
                            <td class="whitespace-nowrap">{{ $event->ip_address }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-12 text-center text-slate-500">No matching activity has been recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 p-4">{{ $events->links() }}</div>
    </section>
</div>
@endsection
