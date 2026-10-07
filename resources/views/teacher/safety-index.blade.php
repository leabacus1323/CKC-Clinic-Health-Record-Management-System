@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-5">
        <h1 class="text-2xl font-bold text-gray-900">Student Life-Safety Alerts</h1>
        <p class="mt-1 text-sm text-gray-700">Read-only minimum-necessary information for immediate classroom safety. Clinical notes, diagnoses, visits, and prescriptions are not available here.</p>
    </div>

    <form method="GET" class="mb-5 flex gap-2">
        <input name="search" value="{{ $search }}" class="w-full rounded-lg border-gray-300" placeholder="Search student name or school ID">
        <button class="rounded-lg bg-blue-600 px-5 py-2 font-semibold text-white">Search</button>
    </form>

    <div class="overflow-hidden rounded-xl bg-white shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50"><tr><th class="px-4 py-3 text-left">Student</th><th class="px-4 py-3 text-left">Class</th><th class="px-4 py-3 text-left">Life-safety alert</th><th class="px-4 py-3 text-left">Emergency medication</th><th class="px-4 py-3"></th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                @forelse($students as $student)
                    <tr>
                        <td class="px-4 py-3"><div class="font-semibold text-gray-900">{{ $student->user?->name }}</div><div class="text-xs text-gray-500">{{ $student->school_id }}</div></td>
                        <td class="px-4 py-3">{{ $student->course?->course_name ?? $student->educationalLevel?->level_name ?? '—' }}</td>
                        <td class="px-4 py-3 font-medium text-red-700">{{ $student->life_safety_alert ?: 'See instructions' }}</td>
                        <td class="px-4 py-3">{{ $student->emergency_medication ?: 'None listed' }}</td>
                        <td class="px-4 py-3 text-right"><a class="font-semibold text-blue-600 hover:underline" href="{{ route('teacher.safety.show', $student) }}">Open</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">No active teacher-visible safety alerts found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-5">{{ $students->links() }}</div>
</div>
@endsection
