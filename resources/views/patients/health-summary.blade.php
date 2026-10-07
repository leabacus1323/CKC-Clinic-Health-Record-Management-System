@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6"><h1 class="text-2xl font-semibold text-gray-900">Health Summary</h1><p class="text-sm text-gray-600">A concise overview of your recorded clinic information.</p></div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([['Health records',$totalHealthRecords],['Immunizations',$totalImmunizations],['Prescriptions',$totalPrescriptions],['Examinations',$totalExaminations]] as [$label,$count])
            <div class="rounded-lg border bg-white p-5 shadow-sm"><p class="text-sm text-gray-600">{{ $label }}</p><p class="mt-2 text-3xl font-bold text-blue-700">{{ $count }}</p></div>
        @endforeach
    </div>
    <section class="mt-6 rounded-lg border bg-white shadow-sm">
        <h2 class="border-b px-6 py-4 text-lg font-semibold">Recent Health Records</h2>
        <div class="divide-y">
            @forelse($recentHealthRecords as $record)
                <article class="p-6"><p class="font-medium text-gray-900">{{ $record->complaint ?? $record->diagnosis ?? 'Clinic record' }}</p><p class="mt-1 text-sm text-gray-600">{{ optional($record->created_at)->format('M d, Y g:i A') }}</p></article>
            @empty <p class="p-6 text-gray-500">No recent records found.</p> @endforelse
        </div>
    </section>
</div>
@endsection
