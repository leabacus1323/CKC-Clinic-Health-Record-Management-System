@extends('admin.layout.navigation')
@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div><a href="{{ route('break-glass.index') }}" class="text-sm font-medium text-blue-600 hover:underline">← Emergency access</a><h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Append-Only Access Audit</h1><p class="text-sm text-gray-600 dark:text-gray-300">Hashes chain each entry to the previous entry, making unauthorized alteration detectable.</p></div>
    <section class="grid gap-4 md:grid-cols-2">
        @foreach(['access_audit_logs' => 'Access audit chain', 'break_glass_events' => 'Emergency-event chain'] as $key => $label)
            @php($result = $integrity[$key])
            <div class="rounded-xl border p-4 {{ $result['valid'] ? 'border-green-200 bg-green-50 text-green-900' : 'border-red-300 bg-red-50 text-red-900' }}">
                <p class="font-semibold">{{ $label }}: {{ $result['valid'] ? 'Integrity verified' : 'Integrity failure detected' }}</p>
                <p class="mt-1 text-sm">Entries checked: {{ $result['checked'] }}</p>
                @unless($result['valid'])<p class="mt-1 text-sm">Failed record ID: {{ $result['failed_id'] }} · {{ str_replace('_', ' ', $result['reason']) }}</p>@endunless
            </div>
        @endforeach
    </section>
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"><div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200 text-sm"><thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Time</th><th class="px-4 py-3 text-left">Actor</th><th class="px-4 py-3 text-left">Patient</th><th class="px-4 py-3 text-left">Action</th><th class="px-4 py-3 text-left">Type</th><th class="px-4 py-3 text-left">Hash</th></tr></thead><tbody class="divide-y divide-gray-100">@forelse($logs as $log)<tr><td class="px-4 py-3 whitespace-nowrap">{{ $log->created_at?->format('M d, Y h:i:s A') }}</td><td class="px-4 py-3">{{ $log->actor?->name ?? 'System' }}</td><td class="px-4 py-3">{{ $log->patient?->user?->name ?? 'N/A' }}</td><td class="px-4 py-3">{{ str_replace('_',' ',$log->action) }}</td><td class="px-4 py-3">{{ $log->access_type }}</td><td class="px-4 py-3 font-mono text-xs">{{ Str::limit($log->entry_hash, 18) }}</td></tr>@empty<tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">No audit entries.</td></tr>@endforelse</tbody></table></div><div class="p-4">{{ $logs->links() }}</div></div>
</div>
@endsection
