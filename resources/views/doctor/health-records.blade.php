@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Health Records</h1>
                    <p class="text-gray-600">Health records for patients with appointments with you</p>
                </div>
                <a href="{{ route('doctor.health-records.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                    Create Health Record
                </a>
            </div>
        </div>

        <div class="p-6">
            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200 tab-card active-tab"
                     onclick="showTab('health-records')" data-tab="health-records">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-md">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-blue-600">{{ $totalHealthRecords + $totalPhysicalExams + $totalDentalExams + $totalImmunizations + $totalPrescriptions }}</p>
                            <p class="text-blue-600 font-medium">All Health Records</p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200 tab-card"
                     onclick="showTab('physical-exams')" data-tab="physical-exams">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-md">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-green-600">{{ $totalPhysicalExams }}</p>
                            <p class="text-green-600 font-medium">Physical Exams</p>
                        </div>
                    </div>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200 tab-card"
                     onclick="showTab('dental-exams')" data-tab="dental-exams">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-md">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-purple-600">{{ $totalDentalExams }}</p>
                            <p class="text-purple-600 font-medium">Dental Exams</p>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 cursor-pointer hover:shadow-lg transition-shadow duration-200 tab-card"
                     onclick="showTab('immunizations')" data-tab="immunizations">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-md">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-yellow-600">{{ $totalImmunizations }}</p>
                            <p class="text-yellow-600 font-medium">Immunizations</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Diagnoses -->
            @if($diagnosisStats->count() > 0)
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Most Common Diagnoses</h2>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($diagnosisStats->take(6) as $diagnosis)
                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                    <p class="font-medium text-gray-800">{{ $diagnosis->diagnosis }}</p>
                                    <p class="text-sm text-gray-600">{{ $diagnosis->count }} {{ $diagnosis->count == 1 ? 'case' : 'cases' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- General Health Records Tab -->
            <div id="health-records-content" class="tab-content">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">All Health Records</h2>

                @php
                    $allRecords = collect();
                    $hasAnyRecords = false;

                    // Add general health records
                    if($healthRecords->count() > 0) {
                        foreach($healthRecords as $record) {
                            if($record->patient && $record->patient->user) {
                                $allRecords->push([
                                    'type' => 'health',
                                    'data' => $record,
                                    'date' => $record->created_at,
                                    'patient' => $record->patient
                                ]);
                                $hasAnyRecords = true;
                            }
                        }
                    }

                    // Add physical examinations
                    if($physicalExaminations->count() > 0) {
                        foreach($physicalExaminations as $exam) {
                            if($exam->patient && $exam->patient->user) {
                                $allRecords->push([
                                    'type' => 'physical',
                                    'data' => $exam,
                                    'date' => $exam->created_at,
                                    'patient' => $exam->patient
                                ]);
                                $hasAnyRecords = true;
                            }
                        }
                    }

                    // Add dental examinations
                    if($dentalExaminations->count() > 0) {
                        foreach($dentalExaminations as $exam) {
                            if($exam->patient && $exam->patient->user) {
                                $allRecords->push([
                                    'type' => 'dental',
                                    'data' => $exam,
                                    'date' => $exam->created_at,
                                    'patient' => $exam->patient
                                ]);
                                $hasAnyRecords = true;
                            }
                        }
                    }

                    // Add immunization records
                    if($immunizationRecords->count() > 0) {
                        foreach($immunizationRecords as $record) {
                            if($record->patient && $record->patient->user) {
                                $allRecords->push([
                                    'type' => 'immunization',
                                    'data' => $record,
                                    'date' => $record->created_at,
                                    'patient' => $record->patient
                                ]);
                                $hasAnyRecords = true;
                            }
                        }
                    }

                    // Sort all records by date (newest first)
                    $allRecords = $allRecords->sortByDesc('date');
                @endphp

                @if($hasAnyRecords)
                    <div class="space-y-4">
                        @foreach($allRecords as $recordItem)
                            @php
                                $record = $recordItem['data'];
                                $type = $recordItem['type'];
                                $patient = $recordItem['patient'];
                            @endphp

                            @if($patient && $patient->user)
                            <div class="border border-gray-200 rounded-lg">
                                <div class="p-6">
                                    <!-- Record Header -->
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center">
                                            @if($type === 'health')
                                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold">
                                                    {{ substr($patient->user->name, 0, 1) }}
                                                </div>
                                            @elseif($type === 'physical')
                                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-semibold">
                                                    {{ substr($patient->user->name, 0, 1) }}
                                                </div>
                                            @elseif($type === 'dental')
                                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 font-semibold">
                                                    {{ substr($patient->user->name, 0, 1) }}
                                                </div>
                                            @elseif($type === 'immunization')
                                                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600 font-semibold">
                                                    {{ substr($patient->user->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div class="ml-3">
                                                <h3 class="font-semibold text-gray-800">{{ $patient->user->name }}</h3>
                                                <p class="text-sm text-gray-600">Patient ID: {{ $patient->id }}</p>
                                                @if($type === 'health')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">General Health Record</span>
                                                @elseif($type === 'physical')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Physical Examination</span>
                                                @elseif($type === 'dental')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Dental Examination</span>
                                                @elseif($type === 'immunization')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Immunization Record</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <div class="text-right mr-4">
                                                <p class="text-sm font-medium text-gray-800">{{ $record->created_at->format('M j, Y') }}</p>
                                                <p class="text-sm text-gray-600">{{ $record->created_at->format('g:i A') }}</p>
                                            </div>
                                            @if($type === 'health')
                                                <a href="{{ route('doctor.health-records.edit', $record->id) }}"
                                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                                                    Edit
                                                </a>
                                            @elseif($type === 'physical')
                                                <a href="{{ route('doctor.physical-exams.edit', $record->id) }}"
                                                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                                                    Edit
                                                </a>
                                            @elseif($type === 'dental')
                                                <a href="{{ route('doctor.dental-exams.edit', $record->id) }}"
                                                   class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                                                    Edit
                                                </a>
                                            @elseif($type === 'immunization')
                                                <a href="{{ route('doctor.immunizations.edit', $record->id) }}"
                                                   class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                                                    Edit
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    @if($type === 'health')
                                        <!-- General Health Record Content -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            @if($record->diagnosis)
                                                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                                    <h4 class="font-medium text-red-800 mb-2">Diagnosis</h4>
                                                    <p class="text-gray-800">{{ $record->diagnosis }}</p>
                                                </div>
                                            @endif

                                            @if($record->treatment)
                                                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                                    <h4 class="font-medium text-blue-800 mb-2">Treatment</h4>
                                                    <p class="text-gray-800">{{ $record->treatment }}</p>
                                                </div>
                                            @endif

                                            @if($record->symptoms)
                                                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                                    <h4 class="font-medium text-yellow-800 mb-2">Symptoms</h4>
                                                    <p class="text-gray-800">{{ $record->symptoms }}</p>
                                                </div>
                                            @endif

                                            @if($record->notes)
                                                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                                    <h4 class="font-medium text-green-800 mb-2">Notes</h4>
                                                    <p class="text-gray-800">{{ $record->notes }}</p>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Vital Signs -->
                                        @if($record->blood_pressure || $record->heart_rate || $record->temperature || $record->weight)
                                            <div class="mt-4 pt-4 border-t border-gray-200">
                                                <h4 class="font-medium text-gray-800 mb-3">Vital Signs</h4>
                                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                                    @if($record->blood_pressure)
                                                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                                                            <p class="text-sm text-gray-600">Blood Pressure</p>
                                                            <p class="font-semibold text-gray-800">{{ $record->blood_pressure }}</p>
                                                        </div>
                                                    @endif
                                                    @if($record->heart_rate)
                                                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                                                            <p class="text-sm text-gray-600">Heart Rate</p>
                                                            <p class="font-semibold text-gray-800">{{ $record->heart_rate }} bpm</p>
                                                        </div>
                                                    @endif
                                                    @if($record->temperature)
                                                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                                                            <p class="text-sm text-gray-600">Temperature</p>
                                                            <p class="font-semibold text-gray-800">{{ $record->temperature }}°C</p>
                                                        </div>
                                                    @endif
                                                    @if($record->weight)
                                                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                                                            <p class="text-sm text-gray-600">Weight</p>
                                                            <p class="font-semibold text-gray-800">{{ $record->weight }} kg</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                    @elseif($type === 'physical')
                                        <!-- Physical Examination Content -->
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                            @if($record->height)
                                                <div class="text-center p-3 bg-green-50 rounded-lg border border-green-200">
                                                    <p class="text-sm text-green-600">Height</p>
                                                    <p class="font-semibold text-gray-800">{{ $record->height }} cm</p>
                                                </div>
                                            @endif
                                            @if($record->weight)
                                                <div class="text-center p-3 bg-green-50 rounded-lg border border-green-200">
                                                    <p class="text-sm text-green-600">Weight</p>
                                                    <p class="font-semibold text-gray-800">{{ $record->weight }} kg</p>
                                                </div>
                                            @endif
                                            @if($record->bp)
                                                <div class="text-center p-3 bg-green-50 rounded-lg border border-green-200">
                                                    <p class="text-sm text-green-600">Blood Pressure</p>
                                                    <p class="font-semibold text-gray-800">{{ $record->bp }}</p>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach(['heart', 'lungs', 'eyes', 'ears', 'nose', 'throat', 'skin'] as $field)
                                                @if($record->$field)
                                                    <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                                                        <h4 class="font-medium text-green-800 mb-1">{{ ucfirst($field) }}</h4>
                                                        <p class="text-gray-800">{{ $record->$field }}</p>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        @if($record->remarks)
                                            <div class="mt-4 bg-green-50 p-3 rounded-lg border border-green-200">
                                                <h4 class="font-medium text-green-800 mb-1">Remarks</h4>
                                                <p class="text-gray-800">{{ $record->remarks }}</p>
                                            </div>
                                        @endif

                                    @elseif($type === 'dental')
                                        <!-- Dental Examination Content -->
                                        @if($record->diagnosis)
                                            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 mb-4">
                                                <h4 class="font-medium text-purple-800 mb-2">Dental Diagnosis</h4>
                                                <p class="text-gray-800">{{ $record->diagnosis }}</p>
                                            </div>
                                        @endif

                                        @if($record->teeth_status && is_array($record->teeth_status) && count($record->teeth_status) > 0)
                                            <div class="mb-4">
                                                <h4 class="font-medium text-gray-800 mb-3">Teeth Status</h4>

                                                <!-- Legend -->
                                                <div class="flex flex-wrap gap-4 mb-4 p-3 bg-gray-50 rounded">
                                                    <div class="flex items-center">
                                                        <span class="w-5 h-5 bg-green-500 rounded mr-2"></span>
                                                        <span class="text-xs">Healthy</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <span class="w-5 h-5 bg-red-500 rounded mr-2"></span>
                                                        <span class="text-xs">Cavity</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <span class="w-5 h-5 bg-blue-500 rounded mr-2"></span>
                                                        <span class="text-xs">Filled</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <span class="w-5 h-5 bg-gray-500 rounded mr-2"></span>
                                                        <span class="text-xs">Missing</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <span class="w-5 h-5 bg-yellow-500 rounded mr-2"></span>
                                                        <span class="text-xs">Other</span>
                                                    </div>
                                                </div>

                                                @php
                                                    // Handle both flat array [1 => 'healthy', ...] and nested array ['upper' => [...], 'lower' => [...]]
                                                    $teethStatus = $record->teeth_status;
                                                    $flatStatus = [];
                                                    foreach ($teethStatus as $key => $value) {
                                                        if (is_array($value)) {
                                                            foreach ($value as $subKey => $subValue) {
                                                                $flatStatus[$subKey] = $subValue;
                                                            }
                                                        } else {
                                                            $flatStatus[$key] = $value;
                                                        }
                                                    }
                                                    $statusColors = [
                                                        'healthy' => 'bg-green-500 text-white',
                                                        'cavity' => 'bg-red-500 text-white',
                                                        'filled' => 'bg-blue-500 text-white',
                                                        'missing' => 'bg-gray-500 text-white',
                                                        'other' => 'bg-yellow-500 text-white'
                                                    ];
                                                @endphp

                                                <!-- Upper Teeth (1-16) -->
                                                <div class="mb-4">
                                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Upper Teeth</h5>
                                                    <div class="grid grid-cols-8 gap-2">
                                                        @for($i = 1; $i <= 16; $i++)
                                                            @php
                                                                $status = $flatStatus[$i] ?? 'healthy';
                                                                $colorClass = $statusColors[$status] ?? 'bg-gray-100 text-gray-400';
                                                            @endphp
                                                            <div class="text-center">
                                                                <div class="w-10 h-10 {{ $colorClass }} rounded flex items-center justify-center text-xs font-semibold cursor-default">
                                                                    {{ $i }}
                                                                </div>
                                                            </div>
                                                        @endfor
                                                    </div>
                                                </div>

                                                <!-- Lower Teeth (17-32) -->
                                                <div>
                                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Lower Teeth</h5>
                                                    <div class="grid grid-cols-8 gap-2">
                                                        @for($i = 17; $i <= 32; $i++)
                                                            @php
                                                                $status = $flatStatus[$i] ?? 'healthy';
                                                                $colorClass = $statusColors[$status] ?? 'bg-gray-100 text-gray-400';
                                                            @endphp
                                                            <div class="text-center">
                                                                <div class="w-10 h-10 {{ $colorClass }} rounded flex items-center justify-center text-xs font-semibold cursor-default">
                                                                    {{ $i }}
                                                                </div>
                                                            </div>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    @elseif($type === 'immunization')
                                        <!-- Immunization Record Content -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                                <h4 class="font-medium text-yellow-800 mb-2">Vaccine</h4>
                                                <p class="text-gray-800 font-semibold">{{ $record->vaccine_name }}</p>
                                                @if($record->vaccine_type)
                                                    <p class="text-gray-600 text-sm">Type: {{ $record->vaccine_type }}</p>
                                                @endif
                                            </div>

                                            @if($record->dosage)
                                                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                                    <h4 class="font-medium text-blue-800 mb-2">Dosage</h4>
                                                    <p class="text-gray-800">{{ $record->dosage }}</p>
                                                </div>
                                            @endif

                                            @if($record->site_of_administration)
                                                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                                    <h4 class="font-medium text-green-800 mb-2">Administration Site</h4>
                                                    <p class="text-gray-800">{{ $record->site_of_administration }}</p>
                                                </div>
                                            @endif

                                            @if($record->administered_by)
                                                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                                    <h4 class="font-medium text-purple-800 mb-2">Administered By</h4>
                                                    <p class="text-gray-800">{{ $record->administered_by }}</p>
                                                </div>
                                            @endif
                                        </div>

                                        @if($record->expiration_date)
                                            <div class="mt-4 bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                                                <h4 class="font-medium text-yellow-800 mb-1">Expiration Date</h4>
                                                <p class="text-gray-800">{{ \Carbon\Carbon::parse($record->expiration_date)->format('M j, Y') }}</p>
                                            </div>
                                        @endif

                                        @if($record->notes)
                                            <div class="mt-4 bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                                                <h4 class="font-medium text-yellow-800 mb-1">Notes</h4>
                                                <p class="text-gray-800">{{ $record->notes }}</p>
                                            </div>
                                        @endif
                                    @endif

                                    <!-- Quick Actions -->
                                    <div class="mt-4 pt-4 border-t border-gray-200 flex space-x-3">
                                        <a href="{{ route('doctor.patient-details', $patient->id) }}"
                                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            View Patient Details
                                        </a>
                                        <span class="text-gray-300">|</span>
                                        <a href="{{ route('doctor.print.health-record', $patient->id) }}" target="_blank"
                                           class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                            Print Record
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Health Records Found</h3>
                        <p class="text-gray-500">No health records available for your patients yet.</p>
                    </div>
                @endif
            </div>

            <!-- Physical Examinations Tab -->
            <div id="physical-exams-content" class="tab-content" style="display: none;">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Physical Examinations</h2>

                @if($physicalExaminations->count() > 0)
                    <div class="space-y-4">
                        @foreach($physicalExaminations as $exam)
                            @if($exam->patient && $exam->patient->user)
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-semibold">
                                            {{ substr($exam->patient->user->name, 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="font-semibold text-gray-800">{{ $exam->patient->user->name }}</h3>
                                            <p class="text-sm text-gray-600">Patient ID: {{ $exam->patient->id }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-800">{{ $exam->created_at->format('M j, Y') }}</p>
                                        <p class="text-sm text-gray-600">{{ $exam->created_at->format('g:i A') }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                    @if($exam->height)
                                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                                            <p class="text-sm text-gray-600">Height</p>
                                            <p class="font-semibold text-gray-800">{{ $exam->height }} cm</p>
                                        </div>
                                    @endif
                                    @if($exam->weight)
                                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                                            <p class="text-sm text-gray-600">Weight</p>
                                            <p class="font-semibold text-gray-800">{{ $exam->weight }} kg</p>
                                        </div>
                                    @endif
                                    @if($exam->bp)
                                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                                            <p class="text-sm text-gray-600">Blood Pressure</p>
                                            <p class="font-semibold text-gray-800">{{ $exam->bp }}</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach(['heart', 'lungs', 'eyes', 'ears', 'nose', 'throat', 'skin'] as $field)
                                        @if($exam->$field)
                                            <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                                <h4 class="font-medium text-blue-800 mb-1">{{ ucfirst($field) }}</h4>
                                                <p class="text-gray-800">{{ $exam->$field }}</p>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                @if($exam->remarks)
                                    <div class="mt-4 bg-gray-50 p-3 rounded-lg">
                                        <h4 class="font-medium text-gray-800 mb-1">Remarks</h4>
                                        <p class="text-gray-800">{{ $exam->remarks }}</p>
                                    </div>
                                @endif
                            </div>
                            @endif
                        @endforeach
                    </div>

                    @if($physicalExaminations->hasPages())
                        <div class="mt-8">
                            {{ $physicalExaminations->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Physical Examinations Found</h3>
                        <p class="text-gray-500">No physical examination records available yet.</p>
                    </div>
                @endif
            </div>

            <!-- Dental Examinations Tab -->
            <div id="dental-exams-content" class="tab-content" style="display: none;">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Dental Examinations</h2>

                @if($dentalExaminations->count() > 0)
                    <div class="space-y-4">
                        @foreach($dentalExaminations as $exam)
                            @if($exam->patient && $exam->patient->user)
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 font-semibold">
                                            {{ substr($exam->patient->user->name, 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="font-semibold text-gray-800">{{ $exam->patient->user->name }}</h3>
                                            <p class="text-sm text-gray-600">Patient ID: {{ $exam->patient->id }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-800">{{ $exam->created_at->format('M j, Y') }}</p>
                                        <p class="text-sm text-gray-600">{{ $exam->created_at->format('g:i A') }}</p>
                                    </div>
                                </div>

                                @if($exam->diagnosis)
                                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 mb-4">
                                        <h4 class="font-medium text-purple-800 mb-2">Diagnosis</h4>
                                        <p class="text-gray-800">{{ $exam->diagnosis }}</p>
                                    </div>
                                @endif

                                @if($exam->teeth_status && is_array($exam->teeth_status) && count($exam->teeth_status) > 0)
                                    <div class="mb-4">
                                        <h4 class="font-medium text-gray-800 mb-3">Teeth Status</h4>
                                        <div class="grid grid-cols-8 gap-2 p-4 bg-gray-50 rounded-lg">
                                            @for($i = 1; $i <= 32; $i++)
                                                @php
                                                    $status = $exam->teeth_status[$i] ?? null;

                                                    if ($status === 'healthy') {
                                                        $bgColor = 'bg-green-100 text-green-800';
                                                        $symbol = '✓';
                                                    } elseif ($status === 'cavity') {
                                                        $bgColor = 'bg-red-100 text-red-800';
                                                        $symbol = 'C';
                                                    } elseif ($status === 'filled') {
                                                        $bgColor = 'bg-blue-100 text-blue-800';
                                                        $symbol = 'F';
                                                    } elseif ($status === 'missing') {
                                                        $bgColor = 'bg-gray-100 text-gray-800';
                                                        $symbol = 'X';
                                                    } elseif ($status === 'crown') {
                                                        $bgColor = 'bg-yellow-100 text-yellow-800';
                                                        $symbol = 'Cr';
                                                    } else {
                                                        $bgColor = 'bg-white text-gray-400';
                                                        $symbol = '-';
                                                    }
                                                @endphp
                                                <div class="text-center">
                                                    <div class="text-xs text-gray-600 mb-1">{{ $i }}</div>
                                                    <div class="w-8 h-8 {{ $bgColor }} rounded flex items-center justify-center text-xs font-semibold">
                                                        {{ $symbol }}
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @endif
                        @endforeach
                    </div>

                    @if($dentalExaminations->hasPages())
                        <div class="mt-8">
                            {{ $dentalExaminations->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Dental Examinations Found</h3>
                        <p class="text-gray-500">No dental examination records available yet.</p>
                    </div>
                @endif
            </div>

            <!-- Immunization Records Tab -->
            <div id="immunizations-content" class="tab-content" style="display: none;">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Immunization Records</h2>

                @if($immunizationRecords->count() > 0)
                    <div class="space-y-4">
                        @foreach($immunizationRecords as $record)
                            @if($record->patient && $record->patient->user)
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600 font-semibold">
                                            {{ substr($record->patient->user->name, 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="font-semibold text-gray-800">{{ $record->patient->user->name }}</h3>
                                            <p class="text-sm text-gray-600">Patient ID: {{ $record->patient->id }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-800">{{ $record->created_at->format('M j, Y') }}</p>
                                        <p class="text-sm text-gray-600">{{ $record->created_at->format('g:i A') }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                        <h4 class="font-medium text-yellow-800 mb-2">Vaccine</h4>
                                        <p class="text-gray-800 font-semibold">{{ $record->vaccine_name }}</p>
                                        @if($record->vaccine_type)
                                            <p class="text-gray-600 text-sm">Type: {{ $record->vaccine_type }}</p>
                                        @endif
                                    </div>

                                    @if($record->dosage)
                                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                            <h4 class="font-medium text-blue-800 mb-2">Dosage</h4>
                                            <p class="text-gray-800">{{ $record->dosage }}</p>
                                        </div>
                                    @endif

                                    @if($record->site_of_administration)
                                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                            <h4 class="font-medium text-green-800 mb-2">Administration Site</h4>
                                            <p class="text-gray-800">{{ $record->site_of_administration }}</p>
                                        </div>
                                    @endif

                                    @if($record->administered_by)
                                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                            <h4 class="font-medium text-purple-800 mb-2">Administered By</h4>
                                            <p class="text-gray-800">{{ $record->administered_by }}</p>
                                        </div>
                                    @endif
                                </div>

                                @if($record->expiration_date)
                                    <div class="mt-4 bg-gray-50 p-3 rounded-lg">
                                        <h4 class="font-medium text-gray-800 mb-1">Expiration Date</h4>
                                        <p class="text-gray-800">{{ \Carbon\Carbon::parse($record->expiration_date)->format('M j, Y') }}</p>
                                    </div>
                                @endif

                                @if($record->notes)
                                    <div class="mt-4 bg-gray-50 p-3 rounded-lg">
                                        <h4 class="font-medium text-gray-800 mb-1">Notes</h4>
                                        <p class="text-gray-800">{{ $record->notes }}</p>
                                    </div>
                                @endif
                            </div>
                            @endif
                        @endforeach
                    </div>

                    @if($immunizationRecords->hasPages())
                        <div class="mt-8">
                            {{ $immunizationRecords->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Immunization Records Found</h3>
                        <p class="text-gray-500">No immunization records available yet.</p>
                    </div>
                @endif
            </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.style.display = 'none';
    });

    // Remove active class from all tab cards
    const tabCards = document.querySelectorAll('.tab-card');
    tabCards.forEach(card => {
        card.classList.remove('active-tab');
        // Reset shadow to default hover state
        card.classList.remove('shadow-lg');
    });

    // Show selected tab content
    const selectedContent = document.getElementById(tabName + '-content');
    if (selectedContent) {
        selectedContent.style.display = 'block';
    }

    // Add active class to clicked tab card
    const activeCard = document.querySelector(`[data-tab="${tabName}"]`);
    if (activeCard) {
        activeCard.classList.add('active-tab', 'shadow-lg');
    }
}

// Initialize first tab as active on page load
document.addEventListener('DOMContentLoaded', function() {
    showTab('health-records');
});
</script>

<style>
.tab-card {
    transition: all 0.3s ease;
}

.tab-card:hover {
    transform: translateY(-2px);
}

.tab-card.active-tab {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    border-width: 2px;
}

.tab-card.active-tab.bg-blue-50 {
    border-color: #3b82f6;
    background-color: #dbeafe;
}

.tab-card.active-tab.bg-green-50 {
    border-color: #10b981;
    background-color: #d1fae5;
}

.tab-card.active-tab.bg-purple-50 {
    border-color: #8b5cf6;
    background-color: #e9d5ff;
}

.tab-card.active-tab.bg-yellow-50 {
    border-color: #f59e0b;
    background-color: #fef3c7;
}
</style>

@endsection
