@extends('staff.shells.staff-shell')

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('staff.index') }}" class="text-blue-600 hover:underline flex items-center mb-2">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Dashboard
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Patients in {{ $course->course_name }}</h1>
            <p class="text-gray-600">Total: {{ $patients->count() }} patients</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Patient Name</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Contact</th>
                    <th class="px-4 py-3">Educational Level</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $patient->id }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $patient->user->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3">{{ $patient->user->email ?? 'N/A' }}</td>
                    <td class="px-4 py-3">{{ $patient->user->contact_no ?? 'N/A' }}</td>
                    <td class="px-4 py-3">{{ $patient->educationalLevel->level_name ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-3 text-center text-gray-500">No patients found in this course.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
