@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6"><h1 class="text-2xl font-bold text-gray-900 dark:text-white">Immunization Review Queue</h1><p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Verify or reject portal submissions. Every decision is audited.</p></div>
    @if(session('success')) <div class="mb-5 rounded-lg bg-green-50 p-4 text-green-800">{{ session('success') }}</div> @endif
    <div class="space-y-4">
        @forelse($records as $record)
            <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div><h2 class="font-semibold text-gray-900 dark:text-white">{{ $record->patient?->user?->name }}</h2><p class="text-sm text-gray-500">{{ $record->requirement?->name ?? $record->vaccine_name }} · Dose {{ $record->dose_number }} · {{ optional($record->administered_at)->format('M d, Y') }}</p><p class="mt-1 text-xs text-gray-500">Uploaded file: {{ $record->document_original_name }}</p></div>
                    <a href="{{ route('privacy-center.immunizations.download', [$record->patient_id, $record]) }}" class="rounded-lg bg-gray-900 px-4 py-2 text-center text-sm font-semibold text-white">Review document</a>
                </div>
                <form method="POST" action="{{ route('immunizations.review.update', $record) }}" class="mt-4 grid gap-3 md:grid-cols-[180px_1fr_auto]">
                    @csrf @method('PATCH')
                    <select name="decision" required class="rounded-lg border-gray-300"><option value="verified">Verify</option><option value="rejected">Reject</option></select>
                    <input name="rejection_reason" placeholder="Reason required when rejecting" class="rounded-lg border-gray-300">
                    <button class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white">Submit decision</button>
                </form>
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center text-gray-500">No pending submissions.</div>
        @endforelse
    </div>
    <div class="mt-6">{{ $records->links() }}</div>
</div>
@endsection
