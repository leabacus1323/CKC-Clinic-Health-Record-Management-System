@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Report Header -->
    <div class="bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $report->title }}</h1>
                <p class="text-gray-400 mt-1">{{ $report->description }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('staff.reports.export', ['id' => $report->id, 'format' => 'csv']) }}" 
                   class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    Export CSV
                </a>
                <a href="{{ route('staff.reports.generate') }}" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    Back to Reports
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 text-gray-300">
            <div>
                <span class="text-gray-400">Report Type:</span>
                <span class="ml-2 px-2 py-1 bg-blue-600 text-white text-sm rounded">
                    {{ ucwords(str_replace('_', ' ', $report->report_type)) }}
                </span>
            </div>
            <div>
                <span class="text-gray-400">Date Range:</span>
                <span class="ml-2">{{ $dateFrom->format('M d, Y') }} - {{ $dateTo->format('M d, Y') }}</span>
            </div>
            <div>
                <span class="text-gray-400">Generated:</span>
                <span class="ml-2">{{ $report->created_at->format('M d, Y H:i') }}</span>
            </div>
        </div>
    </div>

    <!-- Report Content -->
    <div class="bg-gray-800 rounded-lg shadow-md p-6">
        @if($report->report_type === 'patient_statistics')
            @include('staff.reports.patient-statistics', ['data' => $reportData])
        @elseif($report->report_type === 'appointment_statistics')
            @include('staff.reports.appointment-statistics', ['data' => $reportData])
        @elseif($report->report_type === 'health_records')
            @include('staff.reports.health-records', ['data' => $reportData])
        @elseif($report->report_type === 'patient_education_levels')
            @include('staff.reports.patient-education-levels', ['data' => $reportData])
        @elseif($report->report_type === 'prescriptions')
            @include('staff.reports.prescriptions', ['data' => $reportData])
        @elseif($report->report_type === 'medicine_inventory')
            @include('staff.reports.medicine-inventory', ['data' => $reportData])
        @endif
    </div>
</div>
@endsection
