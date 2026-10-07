@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="rounded-lg border bg-white shadow-sm">
        <div class="border-b px-6 py-4"><h1 class="text-2xl font-semibold text-gray-900">Immunization Records</h1></div>
        <div class="divide-y">
            @forelse($immunizationRecords as $record)
                <article class="p-6">
                    <div class="flex flex-wrap justify-between gap-3">
                        <div><h2 class="font-semibold text-gray-900">{{ $record->vaccine_name ?? $record->vaccine ?? 'Immunization' }}</h2><p class="text-sm text-gray-600">{{ $record->vaccine_type ?? 'Vaccination record' }}</p></div>
                        <time class="text-sm text-gray-500">{{ optional($record->created_at)->format('M d, Y') }}</time>
                    </div>
                    @if($record->dosage)<p class="mt-3 text-sm text-gray-700"><strong>Dosage:</strong> {{ $record->dosage }}</p>@endif
                    @if($record->notes)<p class="mt-2 text-sm text-gray-700"><strong>Notes:</strong> {{ $record->notes }}</p>@endif
                </article>
            @empty
                <p class="p-8 text-center text-gray-500">No immunization records are available.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
