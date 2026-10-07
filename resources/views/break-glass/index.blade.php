@extends('admin.layout.navigation')
@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div><h1 class="text-2xl font-bold text-gray-900 dark:text-white">Emergency Break-Glass Access</h1><p class="text-sm text-gray-600 dark:text-gray-300">Use only for an actual or imminent health/safety emergency. Access is time-limited and permanently audited.</p></div>
        <a href="{{ route('break-glass.audit') }}" class="rounded-lg bg-gray-900 px-4 py-2 text-center text-sm font-semibold text-white">View immutable audit</a>
    </div>
    <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-900"><strong>Warning:</strong> Routine convenience, preparedness drills, discipline, and general curiosity are not valid reasons for emergency access.</div>

    <form method="GET" class="flex gap-2"><input name="search" value="{{ $search }}" placeholder="Search patient name, email, or school ID" class="min-w-0 flex-1 rounded-lg border-gray-300"><button class="rounded-lg bg-blue-700 px-5 py-2 text-sm font-semibold text-white">Search</button></form>

    <section class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200 text-sm"><thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Patient</th><th class="px-4 py-3 text-left">Registry</th><th class="px-4 py-3 text-left">School ID</th><th class="px-4 py-3 text-right">Action</th></tr></thead><tbody class="divide-y divide-gray-100">
        @forelse($patients as $patient)<tr><td class="px-4 py-3"><p class="font-medium text-gray-900">{{ $patient->user?->name }}</p><p class="text-xs text-gray-500">{{ $patient->user?->email }}</p></td><td class="px-4 py-3 capitalize">{{ str_replace('_',' ',$patient->registry_type) }}</td><td class="px-4 py-3">{{ $patient->school_id }}</td><td class="px-4 py-3 text-right"><a href="{{ route('break-glass.create', $patient) }}" class="rounded-lg bg-red-700 px-4 py-2 text-xs font-semibold text-white">Activate emergency access</a></td></tr>@empty<tr><td colspan="4" class="px-4 py-10 text-center text-gray-500">No matching records.</td></tr>@endforelse
        </tbody></table></div>
        <div class="p-4">{{ $patients->links() }}</div>
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="font-semibold text-gray-900">Recent activations</h2>
        <div class="mt-4 space-y-3">@forelse($recentEvents as $event)<div class="rounded-lg border border-gray-200 p-4"><div class="flex flex-col gap-1 sm:flex-row sm:justify-between"><p class="font-medium text-gray-900">{{ $event->patient?->user?->name }} · {{ $event->actor?->name }}</p><p class="text-xs text-gray-500">{{ $event->created_at?->format('M d, Y h:i A') }}</p></div><p class="mt-2 text-sm text-gray-600">{{ $event->threat_description }}</p><p class="mt-1 text-xs text-gray-500">Event: {{ $event->public_id }}</p></div>@empty<p class="text-sm text-gray-500">No emergency access events recorded.</p>@endforelse</div>
    </section>
</div>
@endsection
