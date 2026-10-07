@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <div><h1 class="text-2xl font-bold text-gray-900">Teacher Life-Safety Alerts</h1><p class="text-sm text-gray-600">Only school nurses and doctors can publish or change these minimum-necessary alerts.</p></div>
    </div>
    @if(session('success'))<div class="mb-4 rounded-lg bg-green-100 p-3 text-green-800">{{ session('success') }}</div>@endif
    <form method="GET" class="mb-5 flex gap-2"><input name="search" value="{{ $search }}" class="w-full rounded-lg border-gray-300" placeholder="Search patient"><button class="rounded-lg bg-blue-600 px-5 py-2 font-semibold text-white">Search</button></form>
    <div class="overflow-hidden rounded-xl bg-white shadow"><div class="overflow-x-auto"><table class="min-w-full divide-y divide-gray-200 text-sm"><thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Patient</th><th class="px-4 py-3 text-left">Registry</th><th class="px-4 py-3 text-left">Teacher visibility</th><th class="px-4 py-3 text-left">Alert</th><th class="px-4 py-3"></th></tr></thead><tbody class="divide-y divide-gray-100">
    @foreach($patients as $patient)<tr><td class="px-4 py-3"><div class="font-semibold">{{ $patient->user?->name }}</div><div class="text-xs text-gray-500">{{ $patient->school_id }}</div></td><td class="px-4 py-3">{{ ucfirst(str_replace('_',' ', $patient->registry_type)) }}</td><td class="px-4 py-3"><span class="rounded-full px-2 py-1 text-xs font-semibold {{ $patient->life_safety_visible_to_teachers ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">{{ $patient->life_safety_visible_to_teachers ? 'Visible' : 'Hidden' }}</span></td><td class="max-w-md truncate px-4 py-3">{{ $patient->life_safety_alert ?: '—' }}</td><td class="px-4 py-3 text-right"><a href="{{ route('clinical.life-safety.edit', $patient) }}" class="font-semibold text-blue-600 hover:underline">Review</a></td></tr>@endforeach
    </tbody></table></div></div><div class="mt-5">{{ $patients->links() }}</div>
</div>
@endsection
