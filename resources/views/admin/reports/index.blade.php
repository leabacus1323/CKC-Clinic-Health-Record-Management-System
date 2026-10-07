@extends('admin.layout.navigation')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Report Statistics</h2>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Patients</p>
            <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalPatients }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">Courses</p>
            <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $byCourse->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400">Educational Levels</p>
            <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $byEducationalLevel->count() }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- By Course -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Patients by Course</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Course Name</th>
                            <th class="px-6 py-3 text-center">Total Patients</th>
                            <th class="px-6 py-3 text-center">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($byCourse as $course)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $course->course_name }}</td>
                            <td class="px-6 py-4 text-center">{{ $course->total_patients }}</td>
                            <td class="px-6 py-4 text-center">{{ $totalPatients > 0 ? round(($course->total_patients / $totalPatients) * 100, 1) : 0 }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- By Educational Level -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Patients by Educational Level</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Level Name</th>
                            <th class="px-6 py-3 text-center">Total Patients</th>
                            <th class="px-6 py-3 text-center">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($byEducationalLevel as $level)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $level->level_name }}</td>
                            <td class="px-6 py-4 text-center">{{ $level->total_patients }}</td>
                            <td class="px-6 py-4 text-center">{{ $totalPatients > 0 ? round(($level->total_patients / $totalPatients) * 100, 1) : 0 }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if (session('success'))
<div id="alert-border-3" class="flex items-center p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
    <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
    </svg>
    <div class="ms-3 text-sm font-medium">
        {{ session('success') }}
    </div>
</div>
@endif
@if ($errors->any())
    @foreach ($errors->all() as $error)
    <div id="alert-border-4" class="flex items-center p-4 mb-4 text-yellow-800 border-t-4 border-yellow-300 bg-yellow-50 dark:text-yellow-300 dark:bg-gray-800 dark:border-yellow-800" role="alert">
        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
        </svg>
        <div class="ms-3 text-sm font-medium">
            {{ $error }}
        </div>
    </div>
    @endforeach
@endif
