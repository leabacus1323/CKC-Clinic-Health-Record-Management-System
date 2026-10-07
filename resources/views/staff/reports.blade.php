@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Reports & Statistics</h1>
            <p class="text-gray-600">View your practice statistics and performance metrics</p>
        </div>
        <a href="{{ route('staff.reports.generate') }}" 
           class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50">
            📊 Generate Custom Report
        </a>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">Total Appointments</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $appointmentStats['total'] }}</p>
            <p class="text-sm text-blue-600 mt-2">All time</p>
        </div>

        <div class="bg-green-50 p-6 rounded-lg border border-green-200">
            <h3 class="text-lg font-semibold text-green-800 mb-2">This Month</h3>
            <p class="text-3xl font-bold text-green-600">{{ $appointmentStats['this_month'] }}</p>
            <p class="text-sm text-green-600 mt-2">{{ \Carbon\Carbon::now()->format('F Y') }}</p>
        </div>

        <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200">
            <h3 class="text-lg font-semibold text-yellow-800 mb-2">Pending</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ $appointmentStats['pending'] }}</p>
            <p class="text-sm text-yellow-600 mt-2">Awaiting confirmation</p>
        </div>

        <div class="bg-purple-50 p-6 rounded-lg border border-purple-200">
            <h3 class="text-lg font-semibold text-purple-800 mb-2">Completed</h3>
            <p class="text-3xl font-bold text-purple-600">{{ $appointmentStats['completed'] }}</p>
            <p class="text-sm text-purple-600 mt-2">Successfully treated</p>
        </div>
    </div>

    <!-- Patient Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Patient Statistics</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Patients Treated</span>
                    <span class="font-bold text-gray-800">{{ $patientStats['total_patients'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">New Patients This Month</span>
                    <span class="font-bold text-green-600">{{ $patientStats['new_this_month'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Average per Month</span>
                    <span class="font-bold text-blue-600">
                        {{ $appointmentStats['total'] > 0 ? round($appointmentStats['total'] / 12, 1) : 0 }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Appointment Status Breakdown</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-500 rounded mr-3"></div>
                        <span class="text-gray-600">Completed</span>
                    </div>
                    <span class="font-bold">{{ $appointmentStats['completed'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-yellow-500 rounded mr-3"></div>
                        <span class="text-gray-600">Pending</span>
                    </div>
                    <span class="font-bold">{{ $appointmentStats['pending'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-blue-500 rounded mr-3"></div>
                        <span class="text-gray-600">Total</span>
                    </div>
                    <span class="font-bold">{{ $appointmentStats['total'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Trends -->
    <div class="bg-white p-6 rounded-lg shadow-sm border mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Monthly Appointment Trends</h3>
        @if($monthlyTrends->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Month</th>
                            <th class="text-left py-2">Year</th>
                            <th class="text-left py-2">Appointments</th>
                            <th class="text-left py-2">Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyTrends as $trend)
                            <tr class="border-b">
                                <td class="py-2">{{ DateTime::createFromFormat('!m', $trend->month)->format('F') }}</td>
                                <td class="py-2">{{ $trend->year }}</td>
                                <td class="py-2 font-bold">{{ $trend->count }}</td>
                                <td class="py-2">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, ($trend->count / max($monthlyTrends->max('count'), 1)) * 100) }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No monthly data available yet</p>
        @endif
    </div>

    <!-- Top Conditions -->
    <div class="bg-white p-6 rounded-lg shadow-sm border">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Most Common Diagnoses</h3>
        @if(count($topConditions) > 0)
            <div class="space-y-3">
                @foreach($topConditions as $condition)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                        <span class="text-gray-800">{{ $condition->diagnosis }}</span>
                        <div class="flex items-center">
                            <span class="font-bold text-gray-600 mr-3">{{ $condition->count }} cases</span>
                            <div class="w-24 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($condition->count / $topConditions[0]->count) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No diagnosis data available yet</p>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="mt-8 flex space-x-4">
        <a href="{{ route('staff.dashboard') }}"
           class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition">
            ← Back to Dashboard
        </a>
        <button onclick="window.print()"
                class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
            Print Report
        </button>
    </div>
</div>

@section('scripts')
<script>
    // Simple print styles
    document.addEventListener('DOMContentLoaded', function() {
        const style = document.createElement('style');
        style.textContent = `
            @media print {
                .no-print { display: none !important; }
                body { background: white !important; }
                .bg-blue-50, .bg-green-50, .bg-yellow-50, .bg-purple-50 {
                    background: #f8f9fa !important;
                }
            }
        `;
        document.head.appendChild(style);
    });
</script>
@endsection
@endsection
