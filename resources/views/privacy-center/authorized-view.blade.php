@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div>
        <a href="{{ route('privacy-center.index') }}" class="text-sm font-medium text-blue-600 hover:underline">← Privacy & Compliance Center</a>
        <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Authorized Health Information</h1>
        <p class="text-sm text-gray-600 dark:text-gray-300">Only categories covered by your active digital authorization are displayed. This view has been audited.</p>
    </div>

    <div class="flex flex-wrap gap-2">
        @foreach($authorizedScopes as $scope)
            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">{{ config('health_privacy.scopes.'.$scope, $scope) }}</span>
        @endforeach
    </div>

    <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <h2 class="font-semibold text-gray-900">Authorizations granting this access</h2>
        <div class="mt-3 flex flex-wrap gap-2">
            @foreach($authorizations as $authorization)
                <a href="{{ route('privacy-center.authorizations.show', [$patient, $authorization]) }}" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-800 hover:bg-blue-100">
                    {{ $authorization->public_id }} · expires {{ $authorization->expires_at?->format('M d, Y') }}
                </a>
            @endforeach
        </div>
    </section>

    @if(in_array('identity', $authorizedScopes, true))
        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="font-semibold text-gray-900">Identity</h2>
            <dl class="mt-4 grid gap-4 sm:grid-cols-2 text-sm">
                <div><dt class="text-gray-500">Name</dt><dd class="font-medium text-gray-900">{{ $patient->user?->name }}</dd></div>
                <div><dt class="text-gray-500">Date of birth</dt><dd class="font-medium text-gray-900">{{ $patient->user?->dob?->format('M d, Y') }}</dd></div>
                <div><dt class="text-gray-500">Registry</dt><dd class="font-medium text-gray-900">{{ str_replace('_', ' ', $patient->registry_type) }}</dd></div>
                <div><dt class="text-gray-500">Contact</dt><dd class="font-medium text-gray-900">{{ $patient->user?->contact_no }}</dd></div>
            </dl>
        </section>
    @endif

    @if(in_array('emergency_contact', $authorizedScopes, true))
        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm"><h2 class="font-semibold text-gray-900">Emergency contact</h2><dl class="mt-4 grid gap-4 sm:grid-cols-3 text-sm"><div><dt class="text-gray-500">Name</dt><dd class="font-medium">{{ $patient->emergency_contact_name }}</dd></div><div><dt class="text-gray-500">Relationship</dt><dd class="font-medium">{{ $patient->emergency_relationship }}</dd></div><div><dt class="text-gray-500">Telephone</dt><dd class="font-medium">{{ $patient->emergency_contact_number }}</dd></div></dl></section>
    @endif

    @if(in_array('allergies', $authorizedScopes, true))
        <section class="rounded-xl border border-red-200 bg-red-50 p-6"><h2 class="font-semibold text-red-950">Allergies and blood type</h2><p class="mt-3 text-sm text-red-900"><strong>Blood type:</strong> {{ $patient->bloodtype ?: 'Not recorded' }}</p><p class="mt-2 text-sm text-red-900"><strong>Allergies:</strong> {{ $patient->allergies ?: 'None recorded' }}</p></section>
    @endif

    @if(in_array('conditions', $authorizedScopes, true))
        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm"><h2 class="font-semibold text-gray-900">Medical conditions and history</h2><dl class="mt-4 space-y-4 text-sm"><div><dt class="text-gray-500">Conditions</dt><dd class="font-medium">{{ $patient->medical_condition ?: 'None recorded' }}</dd></div><div><dt class="text-gray-500">Illness/history</dt><dd class="font-medium">{{ $patient->medical_illness ?: 'None recorded' }}</dd></div><div><dt class="text-gray-500">Operations</dt><dd class="font-medium">{{ $patient->operations ?: 'None recorded' }}</dd></div></dl></section>
    @endif

    @if(in_array('medications', $authorizedScopes, true))
        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm"><h2 class="font-semibold text-gray-900">Medications</h2><div class="mt-4 space-y-3">@forelse($patient->prescriptions->sortByDesc('date_prescribed') as $prescription)<div class="rounded-lg border p-3 text-sm"><p class="font-medium">{{ $prescription->medicine?->name ?? 'Medication record' }}</p><p class="text-gray-600">{{ $prescription->dosage }} · {{ $prescription->frequency }} · {{ $prescription->status }}</p></div>@empty<p class="text-sm text-gray-500">No prescription records.</p>@endforelse</div></section>
    @endif

    @if(in_array('immunizations', $authorizedScopes, true))
        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm"><div class="flex items-center justify-between gap-3"><div><h2 class="font-semibold text-gray-900">Immunizations</h2><p class="text-sm text-gray-500">{{ $patient->immunizations->count() }} record(s)</p></div><a href="{{ route('privacy-center.immunizations.index', $patient) }}" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white">Open immunization view</a></div></section>
    @endif

    @if(in_array('visits', $authorizedScopes, true))
        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm"><h2 class="font-semibold text-gray-900">Clinic visits</h2><div class="mt-4 grid gap-4 sm:grid-cols-2"><div class="rounded-lg bg-gray-50 p-4"><p class="text-2xl font-bold">{{ $patient->appointments->count() }}</p><p class="text-sm text-gray-500">Appointments</p></div><div class="rounded-lg bg-gray-50 p-4"><p class="text-2xl font-bold">{{ $patient->healthRecords->count() }}</p><p class="text-sm text-gray-500">Health record entries</p></div></div></section>
    @endif
</div>
@endsection
