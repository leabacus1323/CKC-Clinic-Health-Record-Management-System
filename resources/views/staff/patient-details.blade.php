@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Patient Details</h1>
                <p class="text-gray-600">Complete medical record for {{ $patient->user->name }}</p>
            </div>
            <div class="flex space-x-2">
                <button onclick="openEditModal()"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    Edit Patient Info
                </button>
                <a href="{{ route('staff.patients') }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">
                    ← Back to Patients
                </a>
            </div>
        </div>

        <!-- Edit Blood Type Modal -->
        <div id="editModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Edit Patient Information</h3>
                <form action="{{ route('staff.patients.update', $patient->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label for="bloodtype" class="block text-sm font-medium text-gray-700 mb-2">Blood Type</label>
                            <select name="bloodtype" id="bloodtype"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Blood Type...</option>
                                <option value="A+" {{ $patient->bloodtype == 'A+' ? 'selected' : '' }}>A+</option>
                                <option value="A-" {{ $patient->bloodtype == 'A-' ? 'selected' : '' }}>A-</option>
                                <option value="B+" {{ $patient->bloodtype == 'B+' ? 'selected' : '' }}>B+</option>
                                <option value="B-" {{ $patient->bloodtype == 'B-' ? 'selected' : '' }}>B-</option>
                                <option value="AB+" {{ $patient->bloodtype == 'AB+' ? 'selected' : '' }}>AB+</option>
                                <option value="AB-" {{ $patient->bloodtype == 'AB-' ? 'selected' : '' }}>AB-</option>
                                <option value="O+" {{ $patient->bloodtype == 'O+' ? 'selected' : '' }}>O+</option>
                                <option value="O-" {{ $patient->bloodtype == 'O-' ? 'selected' : '' }}>O-</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                            <input type="text" name="address" id="address" value="{{ $patient->address }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="mb-4 md:col-span-2">
                            <label for="medical_condition" class="block text-sm font-medium text-gray-700 mb-2">Medical Conditions</label>
                            <textarea name="medical_condition" id="medical_condition" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $patient->medical_condition }}</textarea>
                        </div>
                        <div class="mb-4 md:col-span-2">
                            <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">Allergies</label>
                            <textarea name="allergies" id="allergies" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $patient->allergies }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Name</label>
                            <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ $patient->emergency_contact_name }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label for="emergency_contact_number" class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Number</label>
                            <input type="text" name="emergency_contact_number" id="emergency_contact_number" value="{{ $patient->emergency_contact_number }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="mb-4">
                            <label for="emergency_relationship" class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact Relationship</label>
                            <input type="text" name="emergency_relationship" id="emergency_relationship" value="{{ $patient->emergency_relationship }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" onclick="closeEditModal()"
                                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition">
                            Cancel
                        </button>
                        <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openEditModal() {
                document.getElementById('editModal').classList.remove('hidden');
            }
            function closeEditModal() {
                document.getElementById('editModal').classList.add('hidden');
            }
        </script>

        <div class="p-6">
            <!-- Patient Basic Information -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Patient Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Full Name</label>
                        <p class="text-gray-800 font-medium">{{ $patient->user->name }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Patient ID</label>
                        <p class="text-gray-800 font-medium">{{ $patient->id }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                        <p class="text-gray-800">{{ $patient->user->email }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-1">School ID</label>
                        <p class="text-gray-800">{{ $patient->school_id ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Patient Type</label>
                        <p class="text-gray-800">
                            @if($patient->patient_type == 1) Student
                            @elseif($patient->patient_type == 3) Faculty
                            @elseif($patient->patient_type == 2) Staff
                            @else Other
                            @endif
                        </p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Address</label>
                        <p class="text-gray-800">{{ $patient->address ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-red-100 p-4 rounded-lg border border-red-200">
                        <label class="block text-sm font-medium text-red-700 mb-2">Blood Type</label>
                        <p class="text-gray-800 font-medium">{{ $patient->bloodtype ?? 'Not specified' }}</p>
                    </div>
                </div>
            </div>

            <!-- Medical History -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Medical History</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                        <label class="block text-sm font-medium text-red-700 mb-2">Medical Conditions</label>
                        <p class="text-gray-800">{{ $patient->medical_condition ?: 'None reported' }}</p>
                    </div>
                    <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                        <label class="block text-sm font-medium text-orange-700 mb-2">Allergies</label>
                        <p class="text-gray-800">{{ $patient->allergies ?: 'None reported' }}</p>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <label class="block text-sm font-medium text-blue-700 mb-2">Medical Illness History</label>
                        <p class="text-gray-800">{{ $patient->medical_illness ?: 'None reported' }}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                        <label class="block text-sm font-medium text-purple-700 mb-2">Previous Operations</label>
                        <p class="text-gray-800">{{ $patient->operations ?: 'None reported' }}</p>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Emergency Contact</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Contact Name</label>
                        <p class="text-gray-800">{{ $patient->emergency_contact_name ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Phone Number</label>
                        <p class="text-gray-800">{{ $patient->emergency_contact_number ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Relationship</label>
                        <p class="text-gray-800">{{ $patient->emergency_relationship ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Appointment History -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Appointment History with You</h2>
                @if($appointments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Date</th>
                                    <th class="px-6 py-3">Time</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Reason</th>
                                    <th class="px-6 py-3">Scheduled</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments as $appointment)
                                    <tr class="bg-white border-b">
                                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($appointment->date)->format('M j, Y') }}</td>
                                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($appointment->time)->format('g:i A') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                @if($appointment->status === 'Pending') bg-yellow-100 text-yellow-800
                                                @elseif($appointment->status === 'Confirmed') bg-green-100 text-green-800
                                                @elseif($appointment->status === 'Completed') bg-blue-100 text-blue-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ $appointment->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">{{ $appointment->reason ?: 'General consultation' }}</td>
                                        <td class="px-6 py-4">{{ $appointment->created_at->format('M j, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No appointment history with this patient.</p>
                @endif
            </div>

            <!-- Medical Records Tabs -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Medical Records</h2>

                <!-- Tabs Navigation -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8">
                        <button onclick="showRecordTab('health')"
                                class="record-tab active border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600">
                            Health Records ({{ $healthRecords->count() }})
                        </button>
                        <button onclick="showRecordTab('physical')"
                                class="record-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Physical Exams ({{ $physicalExams->count() }})
                        </button>
                        <button onclick="showRecordTab('dental')"
                                class="record-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Dental Exams ({{ $dentalExams->count() }})
                        </button>
                        <button onclick="showRecordTab('immunization')"
                                class="record-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Immunizations ({{ $immunizations->count() }})
                        </button>
                        <button onclick="showRecordTab('prescription')"
                                class="record-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Prescriptions ({{ $prescriptions->count() }})
                        </button>
                    </nav>
                </div>

                <!-- Health Records Tab -->
                <div id="health-tab" class="record-tab-content">
                    @if($healthRecords->count() > 0)
                        <div class="space-y-4">
                            @foreach($healthRecords as $record)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="font-medium text-gray-800">Record #{{ $record->id }}</h3>
                                            <span class="text-sm text-gray-500">{{ $record->created_at->format('M j, Y g:i A') }}</span>
                                        </div>
                                        <button onclick="editHealthRecord({{ $record->id }})"
                                                class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition">
                                            Edit
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                                        @if($record->diagnosis)
                                            <div class="bg-blue-50 p-3 rounded">
                                                <p class="text-xs font-medium text-blue-700 mb-1">Diagnosis</p>
                                                <p class="text-sm text-gray-800">{{ $record->diagnosis }}</p>
                                            </div>
                                        @endif
                                        @if($record->treatment)
                                            <div class="bg-green-50 p-3 rounded">
                                                <p class="text-xs font-medium text-green-700 mb-1">Treatment</p>
                                                <p class="text-sm text-gray-800">{{ $record->treatment }}</p>
                                            </div>
                                        @endif
                                        @if($record->notes)
                                            <div class="bg-gray-50 p-3 rounded md:col-span-2">
                                                <p class="text-xs font-medium text-gray-700 mb-1">Notes</p>
                                                <p class="text-sm text-gray-800">{{ $record->notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No health records found.</p>
                    @endif
                </div>

                <!-- Physical Examinations Tab -->
                <div id="physical-tab" class="record-tab-content hidden">
                    @if($physicalExams->count() > 0)
                        <div class="space-y-4">
                            @foreach($physicalExams as $exam)
                                <div class="border border-purple-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="font-medium text-gray-800">Physical Exam #{{ $exam->id }}</h3>
                                            <span class="text-sm text-gray-500">{{ $exam->created_at->format('M j, Y g:i A') }}</span>
                                        </div>
                                        <button onclick="editPhysicalExam({{ $exam->id }})"
                                                class="bg-purple-600 text-white px-3 py-1 rounded text-sm hover:bg-purple-700 transition">
                                            Edit
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3">
                                        @if($exam->height)
                                            <div class="bg-purple-50 p-3 rounded">
                                                <p class="text-xs font-medium text-purple-700 mb-1">Height</p>
                                                <p class="text-sm text-gray-800">{{ $exam->height }} cm</p>
                                            </div>
                                        @endif
                                        @if($exam->weight)
                                            <div class="bg-purple-50 p-3 rounded">
                                                <p class="text-xs font-medium text-purple-700 mb-1">Weight</p>
                                                <p class="text-sm text-gray-800">{{ $exam->weight }} kg</p>
                                            </div>
                                        @endif
                                        @if($exam->bp)
                                            <div class="bg-red-50 p-3 rounded">
                                                <p class="text-xs font-medium text-red-700 mb-1">Blood Pressure</p>
                                                <p class="text-sm text-gray-800">{{ $exam->bp }}</p>
                                            </div>
                                        @endif
                                        @if($exam->heart)
                                            <div class="bg-pink-50 p-3 rounded">
                                                <p class="text-xs font-medium text-pink-700 mb-1">Heart</p>
                                                <p class="text-sm text-gray-800">{{ $exam->heart }}</p>
                                            </div>
                                        @endif
                                        @if($exam->lungs)
                                            <div class="bg-blue-50 p-3 rounded">
                                                <p class="text-xs font-medium text-blue-700 mb-1">Lungs</p>
                                                <p class="text-sm text-gray-800">{{ $exam->lungs }}</p>
                                            </div>
                                        @endif
                                        @if($exam->eyes)
                                            <div class="bg-green-50 p-3 rounded">
                                                <p class="text-xs font-medium text-green-700 mb-1">Eyes</p>
                                                <p class="text-sm text-gray-800">{{ $exam->eyes }}</p>
                                            </div>
                                        @endif
                                        @if($exam->ears)
                                            <div class="bg-yellow-50 p-3 rounded">
                                                <p class="text-xs font-medium text-yellow-700 mb-1">Ears</p>
                                                <p class="text-sm text-gray-800">{{ $exam->ears }}</p>
                                            </div>
                                        @endif
                                        @if($exam->nose)
                                            <div class="bg-orange-50 p-3 rounded">
                                                <p class="text-xs font-medium text-orange-700 mb-1">Nose</p>
                                                <p class="text-sm text-gray-800">{{ $exam->nose }}</p>
                                            </div>
                                        @endif
                                        @if($exam->throat)
                                            <div class="bg-indigo-50 p-3 rounded">
                                                <p class="text-xs font-medium text-indigo-700 mb-1">Throat</p>
                                                <p class="text-sm text-gray-800">{{ $exam->throat }}</p>
                                            </div>
                                        @endif
                                        @if($exam->skin)
                                            <div class="bg-teal-50 p-3 rounded">
                                                <p class="text-xs font-medium text-teal-700 mb-1">Skin</p>
                                                <p class="text-sm text-gray-800">{{ $exam->skin }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    @if($exam->remarks)
                                        <div class="bg-gray-50 p-3 rounded mt-3">
                                            <p class="text-xs font-medium text-gray-700 mb-1">Remarks</p>
                                            <p class="text-sm text-gray-800">{{ $exam->remarks }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No physical examination records found.</p>
                    @endif
                </div>

                <!-- Dental Examinations Tab -->
                <div id="dental-tab" class="record-tab-content hidden">
                    @if($dentalExams->count() > 0)
                        <div class="space-y-4">
                            @foreach($dentalExams as $exam)
                                <div class="border border-green-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="font-medium text-gray-800">Dental Exam #{{ $exam->id }}</h3>
                                            <span class="text-sm text-gray-500">{{ $exam->created_at->format('M j, Y g:i A') }}</span>
                                        </div>
                                        <button onclick="editDentalExam({{ $exam->id }})"
                                                class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition">
                                            Edit
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 gap-3 mt-3">
                                        @if($exam->diagnosis)
                                            <div class="bg-green-50 p-3 rounded">
                                                <p class="text-xs font-medium text-green-700 mb-1">Diagnosis</p>
                                                <p class="text-sm text-gray-800">{{ $exam->diagnosis }}</p>
                                            </div>
                                        @endif

                                        @if($exam->teeth_status)
                                            <div class="bg-blue-50 p-3 rounded">
                                                <p class="text-xs font-medium text-blue-700 mb-2">Teeth Status</p>
                                                <div class="grid grid-cols-4 md:grid-cols-8 gap-2">
                                                    @php
                                                        $teethStatus = is_string($exam->teeth_status) ? json_decode($exam->teeth_status, true) : $exam->teeth_status;
                                                    @endphp
                                                    @foreach($teethStatus as $toothNum => $status)
                                                        @if(is_string($status) && $status !== 'healthy')
                                                            <div class="text-center p-2 rounded
                                                                @if($status === 'cavity') bg-red-100 text-red-700
                                                                @elseif($status === 'missing') bg-gray-200 text-gray-700
                                                                @elseif($status === 'filled') bg-yellow-100 text-yellow-700
                                                                @else bg-orange-100 text-orange-700
                                                                @endif">
                                                                <div class="text-xs font-bold">#{{ $toothNum }}</div>
                                                                <div class="text-xs">{{ ucfirst($status) }}</div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                @php
                                                    $healthyCount = collect($teethStatus)->filter(fn($s) => $s === 'healthy')->count();
                                                    $totalTeeth = count($teethStatus);
                                                @endphp
                                                <p class="text-xs text-gray-600 mt-2">{{ $healthyCount }}/{{ $totalTeeth }} teeth healthy</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No dental examination records found.</p>
                    @endif
                </div>

                <!-- Immunization Records Tab -->
                <div id="immunization-tab" class="record-tab-content hidden">
                    @if($immunizations->count() > 0)
                        <div class="space-y-4">
                            @foreach($immunizations as $immunization)
                                <div class="border border-yellow-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="font-medium text-gray-800">{{ $immunization->vaccine_name }}</h3>
                                            <span class="text-sm text-gray-500">{{ $immunization->created_at->format('M j, Y g:i A') }}</span>
                                        </div>
                                        <button onclick="editImmunization({{ $immunization->id }})"
                                                class="bg-yellow-600 text-white px-3 py-1 rounded text-sm hover:bg-yellow-700 transition">
                                            Edit
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                                        @if($immunization->vaccine_type)
                                            <div class="bg-yellow-50 p-3 rounded">
                                                <p class="text-xs font-medium text-yellow-700 mb-1">Vaccine Type</p>
                                                <p class="text-sm text-gray-800">{{ $immunization->vaccine_type }}</p>
                                            </div>
                                        @endif
                                        @if($immunization->dosage)
                                            <div class="bg-blue-50 p-3 rounded">
                                                <p class="text-xs font-medium text-blue-700 mb-1">Dosage</p>
                                                <p class="text-sm text-gray-800">{{ $immunization->dosage }}</p>
                                            </div>
                                        @endif
                                        @if($immunization->site_of_administration)
                                            <div class="bg-green-50 p-3 rounded">
                                                <p class="text-xs font-medium text-green-700 mb-1">Administration Site</p>
                                                <p class="text-sm text-gray-800">{{ $immunization->site_of_administration }}</p>
                                            </div>
                                        @endif
                                        @if($immunization->administered_by)
                                            <div class="bg-purple-50 p-3 rounded">
                                                <p class="text-xs font-medium text-purple-700 mb-1">Administered By</p>
                                                <p class="text-sm text-gray-800">{{ $immunization->administered_by }}</p>
                                            </div>
                                        @endif
                                        @if($immunization->expiration_date)
                                            <div class="bg-red-50 p-3 rounded">
                                                <p class="text-xs font-medium text-red-700 mb-1">Expiration Date</p>
                                                <p class="text-sm text-gray-800">{{ \Carbon\Carbon::parse($immunization->expiration_date)->format('M j, Y') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    @if($immunization->notes)
                                        <div class="bg-gray-50 p-3 rounded mt-3">
                                            <p class="text-xs font-medium text-gray-700 mb-1">Notes</p>
                                            <p class="text-sm text-gray-800">{{ $immunization->notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No immunization records found.</p>
                    @endif
                </div>

                <!-- Prescription Records Tab -->
                <div id="prescription-tab" class="record-tab-content hidden">
                    @if($prescriptions->count() > 0)
                        <div class="space-y-4">
                            @foreach($prescriptions as $prescription)
                                <div class="border border-red-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="font-medium text-gray-800">{{ $prescription->medicine->name ?? 'Medicine' }}</h3>
                                            <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($prescription->date_prescribed)->format('M j, Y') }}</span>
                                        </div>
                                        <button onclick="editPrescription({{ $prescription->id }})"
                                                class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition">
                                            Edit
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                                        @if($prescription->dosage)
                                            <div class="bg-red-50 p-3 rounded">
                                                <p class="text-xs font-medium text-red-700 mb-1">Dosage</p>
                                                <p class="text-sm text-gray-800">{{ $prescription->dosage }}</p>
                                            </div>
                                        @endif
                                        @if($prescription->frequency)
                                            <div class="bg-orange-50 p-3 rounded">
                                                <p class="text-xs font-medium text-orange-700 mb-1">Frequency</p>
                                                <p class="text-sm text-gray-800">{{ $prescription->frequency }}</p>
                                            </div>
                                        @endif
                                        @if($prescription->duration)
                                            <div class="bg-yellow-50 p-3 rounded">
                                                <p class="text-xs font-medium text-yellow-700 mb-1">Duration</p>
                                                <p class="text-sm text-gray-800">{{ $prescription->duration }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    @if($prescription->instructions)
                                        <div class="bg-gray-50 p-3 rounded mt-3">
                                            <p class="text-xs font-medium text-gray-700 mb-1">Instructions</p>
                                            <p class="text-sm text-gray-800">{{ $prescription->instructions }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No prescription records found.</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                <div class="flex flex-wrap gap-4">
                    <button onclick="addHealthRecord({{ $patient->id }})"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                        Add Health Record
                    </button>
                    <button onclick="addPrescription({{ $patient->id }})"
                            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition">
                        Prescribe Medication
                    </button>
                    <button onclick="addDentalRecord({{ $patient->id }})"
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">
                        Add Dental Record
                    </button>
                    <button onclick="addPhysicalExam({{ $patient->id }})"
                            class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition">
                        Add Physical Exam
                    </button>
                    <button onclick="addImmunization({{ $patient->id }})"
                            class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 transition">
                        Add Immunization
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showRecordTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.record-tab-content');
    tabContents.forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active class from all tabs
    const tabs = document.querySelectorAll('.record-tab');
    tabs.forEach(tab => {
        tab.classList.remove('active', 'border-blue-500', 'text-blue-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab content
    const selectedContent = document.getElementById(tabName + '-tab');
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
    }

    // Add active class to clicked tab
    event.target.classList.remove('border-transparent', 'text-gray-500');
    event.target.classList.add('active', 'border-blue-500', 'text-blue-600');
}

function editHealthRecord(id) {
    window.location.href = `/doctor/health-records/${id}/edit`;
}

function editPhysicalExam(id) {
    window.location.href = `/doctor/physical-exams/${id}/edit`;
}

function editDentalExam(id) {
    window.location.href = `/doctor/dental-exams/${id}/edit`;
}

function editImmunization(id) {
    window.location.href = `/doctor/immunizations/${id}/edit`;
}

function editPrescription(id) {
    window.location.href = `/doctor/prescriptions/${id}/edit`;
}

// Quick Actions
function addHealthRecord(patientId) {
    window.location.href = `/doctor/health-records/create?patient_id=${patientId}`;
}

function addPrescription(patientId) {
    window.location.href = `/doctor/prescriptions/create?patient_id=${patientId}`;
}

function addDentalRecord(patientId) {
    window.location.href = `/doctor/dental-exams/create?patient_id=${patientId}`;
}

function addPhysicalExam(patientId) {
    window.location.href = `/doctor/physical-exams/create?patient_id=${patientId}`;
}

function addImmunization(patientId) {
    window.location.href = `/doctor/immunizations/create?patient_id=${patientId}`;
}
</script>

@endsection
