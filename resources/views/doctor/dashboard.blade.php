@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Doctor Dashboard</h1>
        <p class="text-gray-600">Welcome back, Dr. {{ $doctor->user->name }}</p>
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
                <a href="{{ route('doctor.appointments') }}"
                   class="block w-full bg-blue-600 text-white text-center py-2 px-4 rounded hover:bg-blue-700 transition">
                    Manage Appointments
                </a>
                <a href="{{ route('doctor.patients') }}"
                   class="block w-full bg-green-600 text-white text-center py-2 px-4 rounded hover:bg-green-700 transition">
                    View Patients
                </a>
                <a href="{{ route('doctor.reports') }}"
                   class="block w-full bg-purple-600 text-white text-center py-2 px-4 rounded hover:bg-purple-700 transition">
                    View Reports
                </a>
                <a href="{{ route('doctor.reports.generate') }}"
                   class="block w-full bg-indigo-600 text-white text-center py-2 px-4 rounded hover:bg-indigo-700 transition">
                    📊 Generate Custom Report
                </a>
            </div>
        </div>

        <!-- Recent Appointments -->
        <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-sm border">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Appointments</h3>
            @if($recentAppointments->count() > 0)
                <div class="space-y-3">
                    @foreach($recentAppointments as $appointment)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                            <div>
                                <p class="font-medium text-gray-800">
                                    {{ $appointment->patient->user->name ?? 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($appointment->date)->format('M j, Y') }} at
                                    {{ \Carbon\Carbon::parse($appointment->time)->format('g:i A') }}
                                </p>
                            </div>
                            <span class="px-3 py-1 text-sm rounded-full
                                @if($appointment->status === 'Pending') bg-yellow-100 text-yellow-800
                                @elseif($appointment->status === 'Confirmed') bg-green-100 text-green-800
                                @elseif($appointment->status === 'Completed') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $appointment->status }}
                            </span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('doctor.appointments') }}"
                       class="text-blue-600 hover:text-blue-800 text-sm">
                        View all appointments →
                    </a>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No recent appointments</p>
            @endif
        </div>
    </div>

    <!-- Monthly Statistics Chart -->
    <div class="bg-white p-6 rounded-lg shadow-sm border">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Monthly Appointment Trends</h3>
        <div style="position: relative; height: 100px; max-height: 100px; max-width: 100%;">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>
</div>

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
            {{ is_array($monthlyStats) ? ($monthlyStats[$m] ?? 0) : ($monthlyStats->get($m, 0)) }},
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
