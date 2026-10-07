@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Staff Mode Indicator -->
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span class="text-green-800 font-medium">Staff Duties Mode</span>
            <span class="text-green-600 text-sm ml-2">- Managing clinic operations</span>
        </div>
    </div>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Staff Dashboard</h1>
        <p class="text-gray-600">Welcome back, {{ Auth::user()->name }}</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
            <div class="flex items-center">
                <div class="p-3 bg-blue-500 rounded-full">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">Today's Appointments</h3>
                    <p class="text-2xl font-bold text-blue-600">{{ $todayAppointments }}</p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 p-6 rounded-lg border border-green-200">
            <div class="flex items-center">
                <div class="p-3 bg-green-500 rounded-full">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">This Week</h3>
                    <p class="text-2xl font-bold text-green-600">{{ $weeklyAppointments }}</p>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 p-6 rounded-lg border border-purple-200">
            <div class="flex items-center">
                <div class="p-3 bg-purple-500 rounded-full">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">Total Patients</h3>
                    <p class="text-2xl font-bold text-purple-600">{{ $totalPatients }}</p>
                </div>
            </div>
        </div>

        <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-500 rounded-full">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-800">Pending</h3>
                    <p class="text-2xl font-bold text-yellow-600">{{ $pendingAppointments }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('staff.appointments') }}"
                   class="block w-full bg-blue-600 text-white text-center py-2 px-4 rounded hover:bg-blue-700 transition">
                    Manage Appointments
                </a>
                <a href="{{ route('staff.patients') }}"
                   class="block w-full bg-green-600 text-white text-center py-2 px-4 rounded hover:bg-green-700 transition">
                    View Patients
                </a>
                <a href="{{ route('staff.reports') }}"
                   class="block w-full bg-purple-600 text-white text-center py-2 px-4 rounded hover:bg-purple-700 transition">
                    View Reports
                </a>
                <a href="{{ route('staff.reports.generate') }}"
                   class="block w-full bg-indigo-600 text-white text-center py-2 px-4 rounded hover:bg-indigo-700 transition">
                    📊 Generate Custom Report
                </a>
                <a href="{{ route('staff.announcements.create') }}"
                   class="block w-full bg-blue-600 text-white text-center py-2 px-4 rounded hover:bg-blue-700 transition">
                    📣 Send Reminder
                </a>
                <a href="javascript:void(0)" onclick="scrollToSection('import-section')"
                   class="block w-full bg-teal-600 text-white text-center py-2 px-4 rounded hover:bg-teal-700 transition">
                    📁 Import Data
                </a>
            </div>
        </div>

        <!-- Recent Appointments -->
        <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-sm border">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Appointments</h3>
            @if($recentAppointments->count() > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($recentAppointments as $appointment)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                            <div>
                                <p class="font-medium text-gray-800">
                                    {{ $appointment->patient->user->name ?? 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($appointment->date)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Doctor: {{ $appointment->doctor->user->name ?? 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <span class="px-3 py-1 text-xs rounded-full
                                    @if($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($appointment->status === 'confirmed') bg-blue-100 text-blue-800
                                    @elseif($appointment->status === 'completed') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No recent appointments</p>
            @endif
        </div>
    </div>

    <!-- Patient Statistics by Course & Educational Level -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- By Course -->
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Patients by Course</h3>
            @if(isset($byCourse) && $byCourse->count() > 0)
                <div class="overflow-x-auto max-h-48 overflow-y-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-3 py-2">Course Name</th>
                                <th class="px-3 py-2 text-center">Total</th>
                                <th class="px-3 py-2 text-center">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($byCourse as $course)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    <a href="{{ route('staff.statistics.course', $course->id) }}" class="text-blue-600 hover:underline">{{ $course->course_name }}</a>
                                </td>
                                <td class="px-3 py-2 text-center">{{ $course->total_patients }}</td>
                                <td class="px-3 py-2 text-center">{{ $totalPatients > 0 ? round(($course->total_patients / $totalPatients) * 100, 1) : 0 }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No course data available.</p>
            @endif
        </div>

        <!-- By Educational Level -->
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Patients by Educational Level</h3>
            @if(isset($byEducationalLevel) && $byEducationalLevel->count() > 0)
                <div class="overflow-x-auto max-h-48 overflow-y-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-3 py-2">Level Name</th>
                                <th class="px-3 py-2 text-center">Total</th>
                                <th class="px-3 py-2 text-center">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($byEducationalLevel as $level)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-3 py-2">
                                    <a href="{{ route('staff.statistics.level', $level->id) }}" class="text-blue-600 hover:underline">{{ $level->level_name }}</a>
                                </td>
                                <td class="px-3 py-2 text-center">{{ $level->total_patients }}</td>
                                <td class="px-3 py-2 text-center">{{ $totalPatients > 0 ? round(($level->total_patients / $totalPatients) * 100, 1) : 0 }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No educational level data available.</p>
            @endif
        </div>
    </div>

    <!-- Import Section -->
    <div id="import-section" class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Import Data</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Import Users -->
            <div class="border rounded-lg p-4">
                <h4 class="font-medium text-gray-700 mb-3">Import Users</h4>
                <form action="{{ route('staff.import.process.users') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <input type="file" name="file" accept=".csv,.xlsx,.xls" required class="w-full text-sm border rounded p-2">
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <label class="text-gray-600">Name Column #</label>
                            <input type="number" name="name_column" value="0" min="0" required class="w-full border rounded px-2 py-1">
                        </div>
                        <div>
                            <label class="text-gray-600">Email Column #</label>
                            <input type="number" name="email_column" value="1" min="0" required class="w-full border rounded px-2 py-1">
                        </div>
                        <div>
                            <label class="text-gray-600">Password #</label>
                            <input type="number" name="password_column" value="2" min="0" class="w-full border rounded px-2 py-1">
                        </div>
                        <div>
                            <label class="text-gray-600">User Type #</label>
                            <input type="number" name="user_type_column" value="3" min="0" class="w-full border rounded px-2 py-1">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded text-sm">
                        Import Users
                    </button>
                    <a href="{{ route('staff.import.sample.users') }}" class="block text-center text-blue-600 hover:underline text-xs">
                        Download Sample CSV
                    </a>
                </form>
            </div>

            <!-- Import Medicines -->
            <div class="border rounded-lg p-4">
                <h4 class="font-medium text-gray-700 mb-3">Import Medicines</h4>
                <form action="{{ route('staff.import.process.medicines') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <input type="file" name="file" accept=".csv,.xlsx,.xls" required class="w-full text-sm border rounded p-2">
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <div>
                            <label class="text-gray-600">Name #</label>
                            <input type="number" name="name_column" value="0" min="0" required class="w-full border rounded px-2 py-1">
                        </div>
                        <div>
                            <label class="text-gray-600">Desc #</label>
                            <input type="number" name="description_column" value="1" min="0" class="w-full border rounded px-2 py-1">
                        </div>
                        <div>
                            <label class="text-gray-600">Qty #</label>
                            <input type="number" name="quantity_column" value="2" min="0" class="w-full border rounded px-2 py-1">
                        </div>
                        <div>
                            <label class="text-gray-600">Expiry #</label>
                            <input type="number" name="expiration_date_column" value="3" min="0" class="w-full border rounded px-2 py-1">
                        </div>
                        <div>
                            <label class="text-gray-600">Type #</label>
                            <input type="number" name="medicine_type_column" value="4" min="0" class="w-full border rounded px-2 py-1">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded text-sm">
                        Import Medicines
                    </button>
                    <a href="{{ route('staff.import.sample.medicines') }}" class="block text-center text-green-600 hover:underline text-xs">
                        Download Sample CSV
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
    {{ session('error') }}
</div>
@endif

<script>
function scrollToSection(id) {
    const section = document.getElementById(id);
    if (section) {
        section.scrollIntoView({ behavior: 'smooth' });
    }
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyChart');
    const months = [
        @for($m = 1; $m <= 12; $m++)
            '{{ date('M', mktime(0, 0, 0, $m, 1)) }}',
        @endfor
    ];
    const data = [
        @for($m = 1; $m <= 12; $m++)
            {{ $monthlyStats->get($m, 0) }},
        @endfor
    ];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Appointments',
                data: data,
                borderColor: 'rgba(59, 130, 246, 1)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 3,
                pointHoverRadius: 5,
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw + ' appointments';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    grid: { display: false }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endsection
