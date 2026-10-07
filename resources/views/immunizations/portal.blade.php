@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('privacy-center.show', $patient) }}" class="text-sm font-medium text-blue-600 hover:underline">← Back to profile controls</a>
            <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Immunization Compliance</h1>
            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $patient->user?->name }} · Secure record portal</p>
        </div>
    </div>

    @if(session('success')) <div class="rounded-lg bg-green-50 p-4 text-green-800">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="rounded-lg bg-red-50 p-4 text-red-800"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif

    <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Compliance status</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead><tr class="text-left text-xs uppercase tracking-wide text-gray-500"><th class="px-3 py-3">Requirement</th><th class="px-3 py-3">Status</th><th class="px-3 py-3">Verified</th><th class="px-3 py-3">Pending</th><th class="px-3 py-3">Next deadline</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($compliance as $item)
                        <tr><td class="px-3 py-3 font-medium text-gray-900">{{ $item['requirement']->name }}</td><td class="px-3 py-3 capitalize">{{ str_replace('_', ' ', $item['status']) }}</td><td class="px-3 py-3">{{ $item['verified_doses'] }} / {{ $item['required_doses'] }}</td><td class="px-3 py-3">{{ $item['pending_submissions'] }}</td><td class="px-3 py-3">{{ $item['next_due_date']?->format('M d, Y') ?? '—' }}</td></tr>
                    @empty
                        <tr><td colspan="5" class="px-3 py-8 text-center text-gray-500">No active requirements are configured.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if($canUpload)
        <section class="rounded-xl border border-emerald-200 bg-emerald-50 p-6">
            <h2 class="text-lg font-semibold text-emerald-950">Upload immunization evidence</h2>
            <p class="mt-1 text-sm text-emerald-800">Accepted files: PDF, JPG, JPEG, or PNG, up to 10 MB. Files remain in private storage until an authorized reviewer opens them.</p>
            <form method="POST" action="{{ route('privacy-center.immunizations.store', $patient) }}" enctype="multipart/form-data" class="mt-5 grid gap-4 md:grid-cols-2">
                @csrf
                <label class="text-sm font-medium text-gray-800">Requirement<select name="immunization_requirement_id" required class="mt-1 block w-full rounded-lg border-gray-300"><option value="">Select</option>@foreach($requirements as $requirement)<option value="{{ $requirement->id }}">{{ $requirement->name }}</option>@endforeach</select></label>
                <label class="text-sm font-medium text-gray-800">Date administered<input name="administered_at" type="date" required class="mt-1 block w-full rounded-lg border-gray-300"></label>
                <label class="text-sm font-medium text-gray-800">Dose number<input name="dose_number" type="number" min="1" max="20" value="1" required class="mt-1 block w-full rounded-lg border-gray-300"></label>
                <label class="text-sm font-medium text-gray-800">Administered by<input name="administered_by" class="mt-1 block w-full rounded-lg border-gray-300"></label>
                <label class="text-sm font-medium text-gray-800">Expiration date, when shown<input name="expiration_date" type="date" class="mt-1 block w-full rounded-lg border-gray-300"></label>
                <label class="text-sm font-medium text-gray-800">Document<input name="document" type="file" accept=".pdf,.jpg,.jpeg,.png" required class="mt-1 block w-full rounded-lg border border-gray-300 bg-white p-2"></label>
                <label class="md:col-span-2 text-sm font-medium text-gray-800">Notes<textarea name="notes" rows="3" class="mt-1 block w-full rounded-lg border-gray-300"></textarea></label>
                <div class="md:col-span-2"><button class="rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800">Upload for review</button></div>
            </form>
        </section>
    @endif

    <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Submitted records</h2>
        <div class="mt-4 space-y-3">
            @forelse($patient->immunizations->sortByDesc('id') as $record)
                <div class="flex flex-col gap-3 rounded-lg border border-gray-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div><p class="font-medium text-gray-900">{{ $record->requirement?->name ?? $record->vaccine_name }}</p><p class="text-sm text-gray-500">Dose {{ $record->dose_number }} · {{ optional($record->administered_at)->format('M d, Y') ?: 'Date unavailable' }}</p><p class="mt-1 text-xs capitalize {{ $record->verification_status === 'verified' ? 'text-green-700' : ($record->verification_status === 'rejected' ? 'text-red-700' : 'text-amber-700') }}">{{ $record->verification_status }}</p>@if($record->rejection_reason)<p class="mt-1 text-xs text-red-700">{{ $record->rejection_reason }}</p>@endif</div>
                    @if($record->document_path)<a href="{{ route('privacy-center.immunizations.download', [$patient, $record]) }}" class="rounded-lg bg-gray-900 px-4 py-2 text-center text-sm font-semibold text-white">Download evidence</a>@endif
                </div>
            @empty
                <p class="text-sm text-gray-500">No immunization records have been submitted.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
