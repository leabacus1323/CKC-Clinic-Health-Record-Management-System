@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="rounded-lg border bg-white shadow-sm">
        <div class="border-b px-6 py-4"><h1 class="text-2xl font-semibold text-gray-900">Prescription History</h1></div>
        <div class="divide-y">
            @forelse($prescriptionRecords as $record)
                <article class="p-6">
                    <div class="flex flex-wrap justify-between gap-3">
                        <div><h2 class="font-semibold text-gray-900">{{ optional($record->medicine)->medicine_name ?? optional($record->medicine)->name ?? 'Medication' }}</h2><p class="text-sm text-gray-600">{{ $record->dosage ?? 'Dosage not specified' }} · {{ $record->frequency ?? 'Frequency not specified' }}</p></div>
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">{{ ucfirst($record->status ?? 'recorded') }}</span>
                    </div>
                    @if($record->instruction)<p class="mt-3 text-sm text-gray-700"><strong>Instructions:</strong> {{ $record->instruction }}</p>@endif
                    <p class="mt-2 text-xs text-gray-500">Prescribed {{ optional($record->date_prescribed ? \Carbon\Carbon::parse($record->date_prescribed) : $record->created_at)->format('M d, Y') }}</p>
                </article>
            @empty
                <p class="p-8 text-center text-gray-500">No prescription history is available.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
