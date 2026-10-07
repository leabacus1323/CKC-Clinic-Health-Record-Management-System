@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Privacy & Compliance Center</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Manage guardian access, digital authorizations, and immunization compliance.</p>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green-50 p-4 text-green-800">{{ session('success') }}</div>
    @endif

    @if($pendingGuardianLinks->isNotEmpty())
        <section class="mb-8 rounded-xl border border-amber-200 bg-amber-50 p-5">
            <h2 class="font-semibold text-amber-900">Pending guardian invitations</h2>
            <div class="mt-3 space-y-3">
                @foreach($pendingGuardianLinks as $link)
                    <div class="flex flex-col gap-3 rounded-lg bg-white p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-medium text-gray-900">{{ $link->patient->user->name }}</p>
                            <p class="text-sm text-gray-600">Relationship: {{ $link->relationship }}</p>
                        </div>
                        @if($link->verified_at === null)
                            <form method="POST" action="{{ route('privacy-center.guardians.accept', $link) }}">
                                @csrf
                                <button class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">Accept invitation</button>
                            </form>
                        @else
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">Awaiting administrator verification</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @forelse($patients as $patient)
            <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-white">{{ $patient->user?->name ?? 'Patient #'.$patient->id }}</h2>
                        <p class="text-sm text-gray-500">{{ $patient->school_id ?: 'No school ID' }}</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $patient->isStudentRegistry() ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                        {{ $patient->isStudentRegistry() ? 'Student registry' : 'Non-student registry' }}
                    </span>
                </div>
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between gap-4"><dt class="text-gray-500">Privacy regime</dt><dd class="font-medium text-gray-800 dark:text-gray-200">{{ str_replace('_', ' ', $patient->privacy_regime) }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-gray-500">Guardians</dt><dd class="font-medium text-gray-800 dark:text-gray-200">{{ $patient->guardians->where('is_active', true)->count() }}</dd></div>
                </dl>
                <a href="{{ route('privacy-center.show', $patient) }}" class="mt-5 inline-flex w-full justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Open profile controls</a>
            </article>
        @empty
            <div class="col-span-full rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center text-gray-600">
                No health profiles are currently linked to this account.
            </div>
        @endforelse
    </div>
</div>
@endsection
