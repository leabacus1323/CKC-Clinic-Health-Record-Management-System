@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8 sm:px-6">
    <a href="{{ route('teacher.safety.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">← Back to safety alerts</a>
    <div class="mt-4 overflow-hidden rounded-2xl border-2 border-red-200 bg-white shadow-lg">
        <div class="bg-red-700 px-6 py-5 text-white">
            <p class="text-sm font-semibold uppercase tracking-wide">Emergency life-safety information</p>
            <h1 class="mt-1 text-3xl font-bold">{{ $patient->user?->name }}</h1>
            <p class="mt-1">School ID: {{ $patient->school_id }} · {{ $patient->course?->course_name ?? $patient->educationalLevel?->level_name ?? 'Student' }}</p>
        </div>
        <div class="space-y-6 p-6">
            <section><h2 class="text-sm font-bold uppercase text-gray-500">Immediate alert</h2><p class="mt-2 text-xl font-semibold text-red-700">{{ $patient->life_safety_alert ?: 'Follow the emergency instructions below.' }}</p></section>
            <section><h2 class="text-sm font-bold uppercase text-gray-500">Emergency medication</h2><p class="mt-2 text-lg text-gray-900">{{ $patient->emergency_medication ?: 'No emergency medication listed.' }}</p></section>
            <section><h2 class="text-sm font-bold uppercase text-gray-500">What to do</h2><div class="mt-2 whitespace-pre-line rounded-lg bg-amber-50 p-4 text-gray-900">{{ $patient->life_safety_instructions ?: 'Contact the school clinic immediately and follow the school emergency plan.' }}</div></section>
            <p class="text-xs text-gray-500">Last clinical review: {{ $patient->life_safety_reviewed_at?->format('M d, Y h:i A') ?? 'Not recorded' }}</p>
        </div>
    </div>
</div>
@endsection
