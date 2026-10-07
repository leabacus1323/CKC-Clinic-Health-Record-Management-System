@extends('admin.layout.navigation')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-4">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Dashboard
    </a>

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Patients in {{ $level->level_name }}</h2>
        <span class="bg-purple-100 text-purple-800 text-sm font-medium px-3 py-1 rounded dark:bg-purple-900 dark:text-purple-300">
            {{ $patients->count() }} Patient(s)
        </span>
    </div>

    @if($patients->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Patient Name</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Contact</th>
                        <th class="px-6 py-3">Course</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $patient)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $patient->id }}</td>
                        <td class="px-6 py-4">{{ $patient->user->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $patient->user->email ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $patient->user->contact_no ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $patient->course->course_name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($patient->patient_type == 'student') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                @else bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                @endif">
                                {{ ucfirst($patient->patient_type ?? 'N/A') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
        <p class="text-gray-500 dark:text-gray-400">No patients found in this educational level.</p>
    </div>
    @endif
</div>
@endsection
