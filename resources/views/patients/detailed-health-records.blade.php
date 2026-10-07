@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Detailed Health Records</h1>
            <p class="text-sm text-gray-600">Complete health history for {{ optional($patient->user)->name ?? 'your profile' }}.</p>
        </div>
        <a href="{{ route('patients.health.records') }}" class="rounded bg-gray-700 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">Back to health records</a>
    </div>
    @include('patients.partials.health-records')
</div>
@endsection
