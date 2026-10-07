@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Prescription Management</h1>
                    <p class="text-gray-600">Manage patient prescriptions and medication records</p>
                </div>
                <a href="{{ route('doctor.prescriptions.create-page') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Prescribe New Medication
                </a>
            </div>
        </div>

        <div class="p-6">
            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-md">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-blue-600">{{ $totalPrescriptions }}</p>
                            <p class="text-blue-600 font-medium">Total Prescriptions</p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-md">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-green-600">{{ $activePrescriptions }}</p>
                            <p class="text-green-600 font-medium">Active</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-gray-100 rounded-md">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-gray-600">{{ $completedPrescriptions }}</p>
                            <p class="text-gray-600 font-medium">Completed</p>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-md">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-red-600">{{ $discontinuedPrescriptions }}</p>
                            <p class="text-red-600 font-medium">Discontinued</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Search -->
            <div class="mb-6">
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="text" id="prescriptionSearch" placeholder="Search by patient name, medicine, or dosage..." class="w-full ps-10 p-4 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Prescriptions Table -->
            @if($prescriptions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medicine</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dosage</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frequency</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Prescribed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="prescriptionsTable" class="divide-y divide-gray-200">
                            @foreach($prescriptions as $prescription)
                                <tr class="prescription-row hover:bg-gray-50" data-search="{{ strtolower(($prescription->patient && $prescription->patient->user ? $prescription->patient->user->name : '') . ' ' . ($prescription->medicine ? $prescription->medicine->name : '') . ' ' . $prescription->dosage) }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($prescription->patient && $prescription->patient->user)
                                            <div class="font-medium text-gray-900">{{ $prescription->patient->user->name }}</div>
                                            <div class="text-sm text-gray-500">ID: {{ $prescription->patient->id }}</div>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($prescription->medicine)
                                            <div class="font-medium text-gray-900">{{ $prescription->medicine->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $prescription->medicine->medicine_type }}</div>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $prescription->dosage }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $prescription->frequency ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $prescription->duration ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $prescription->date_prescribed ? \Carbon\Carbon::parse($prescription->date_prescribed)->format('M j, Y') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($prescription->status == 'active')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                        @elseif($prescription->status == 'completed')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Completed</span>
                                        @elseif($prescription->status == 'discontinued')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Discontinued</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ ucfirst($prescription->status ?? 'Unknown') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                        <button onclick='viewPrescriptionHistory({{ $prescription->patient_id }})' class="text-blue-600 hover:text-blue-900" title="View History">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                        @if($prescription->status == 'active')
                                            <button onclick='openUpdateModal(@json($prescription))' class="text-green-600 hover:text-green-900" title="Update">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button onclick='openDiscontinueModal(@json($prescription))' class="text-red-600 hover:text-red-900" title="Discontinue">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($prescriptions->hasPages())
                    <div class="mt-8">
                        {{ $prescriptions->links() }}
                    </div>
                @endif
            @else
                <div id="noPrescriptionResults" class="hidden text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Prescriptions Found</h3>
                    <p class="text-gray-500 mb-4">Start prescribing medications to patients.</p>
                    <button onclick="openPrescribeModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                        Prescribe First Medication
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Prescribe New Medication Modal -->
<div id="prescribeModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Prescribe New Medication</h3>
            <button onclick="closePrescribeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form action="{{ route('doctor.prescription.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Patient *</label>
                    <select name="patient_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Patient</option>
                        @foreach($patients as $patient)
                            @if($patient->user)
                                <option value="{{ $patient->id }}">{{ $patient->user->name }} (ID: {{ $patient->id }})</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Medicine *</label>
                    <select name="medicine_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Medicine</option>
                        @foreach($medicines as $medicine)
                            <option value="{{ $medicine->id }}">{{ $medicine->name }} ({{ $medicine->medicine_type }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dosage *</label>
                    <input type="text" name="dosage" required placeholder="e.g., 500mg" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Frequency</label>
                    <input type="text" name="frequency" placeholder="e.g., 3 times daily" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duration</label>
                    <input type="text" name="duration" placeholder="e.g., 7 days" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Prescribed *</label>
                    <input type="date" name="date_prescribed" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                    <textarea name="instruction" rows="3" placeholder="e.g., Take after meals" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closePrescribeModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Prescribe Medication
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Update Prescription Modal -->
<div id="updateModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Update Prescription</h3>
            <button onclick="closeUpdateModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="updateForm" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Patient</label>
                    <input type="text" id="update_patient" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Medicine</label>
                    <input type="text" id="update_medicine" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Dosage *</label>
                    <input type="text" name="dosage" id="update_dosage" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Frequency</label>
                    <input type="text" name="frequency" id="update_frequency" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duration</label>
                    <input type="text" name="duration" id="update_duration" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                    <textarea name="instruction" id="update_instruction" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeUpdateModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Update Prescription
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Discontinue Prescription Modal -->
<div id="discontinueModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Discontinue Prescription</h3>
            <button onclick="closeDiscontinueModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="discontinueForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <p class="text-gray-700 mb-4">Are you sure you want to discontinue this prescription?</p>
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <p><strong>Patient:</strong> <span id="disc_patient"></span></p>
                    <p><strong>Medicine:</strong> <span id="disc_medicine"></span></p>
                    <p><strong>Dosage:</strong> <span id="disc_dosage"></span></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Discontinuation *</label>
                    <textarea name="discontinuation_reason" required rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Please provide a reason..."></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeDiscontinueModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Discontinue
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Medication History Modal -->
<div id="historyModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Medication History</h3>
            <button onclick="closeHistoryModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="historyContent" class="max-h-96 overflow-y-auto">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

<script>
function openPrescribeModal() {
    document.getElementById('prescribeModal').classList.remove('hidden');
}

function closePrescribeModal() {
    document.getElementById('prescribeModal').classList.add('hidden');
}

function openUpdateModal(prescription) {
    const patientName = prescription.patient && prescription.patient.user ? prescription.patient.user.name : 'N/A';
    const medicineName = prescription.medicine ? prescription.medicine.name : 'N/A';

    document.getElementById('update_patient').value = patientName + ' (ID: ' + prescription.patient_id + ')';
    document.getElementById('update_medicine').value = medicineName;
    document.getElementById('update_dosage').value = prescription.dosage;
    document.getElementById('update_frequency').value = prescription.frequency || '';
    document.getElementById('update_duration').value = prescription.duration || '';
    document.getElementById('update_instruction').value = prescription.instruction || '';

    const form = document.getElementById('updateForm');
    form.action = '/doctor/prescription/' + prescription.id;

    document.getElementById('updateModal').classList.remove('hidden');
}

function closeUpdateModal() {
    document.getElementById('updateModal').classList.add('hidden');
}

function openDiscontinueModal(prescription) {
    const patientName = prescription.patient && prescription.patient.user ? prescription.patient.user.name : 'N/A';
    const medicineName = prescription.medicine ? prescription.medicine.name : 'N/A';

    document.getElementById('disc_patient').textContent = patientName;
    document.getElementById('disc_medicine').textContent = medicineName;
    document.getElementById('disc_dosage').textContent = prescription.dosage;

    const form = document.getElementById('discontinueForm');
    form.action = '/doctor/prescription/' + prescription.id + '/discontinue';

    document.getElementById('discontinueModal').classList.remove('hidden');
}

function closeDiscontinueModal() {
    document.getElementById('discontinueModal').classList.add('hidden');
}

function viewPrescriptionHistory(patientId) {
    document.getElementById('historyModal').classList.remove('hidden');
    document.getElementById('historyContent').innerHTML = '<div class="text-center py-4"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><p class="mt-2 text-gray-600">Loading history...</p></div>';

    fetch('/doctor/prescription/history/' + patientId)
        .then(response => response.json())
        .then(data => {
            let html = '<div class="space-y-4">';
            if (data.length > 0) {
                data.forEach(prescription => {
                    const statusColor = prescription.status === 'active' ? 'green' : (prescription.status === 'discontinued' ? 'red' : 'gray');
                    html += `
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-semibold text-gray-900">${prescription.medicine ? prescription.medicine.name : 'N/A'}</h4>
                                    <p class="text-sm text-gray-600">${prescription.dosage} ${prescription.frequency ? '- ' + prescription.frequency : ''}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-${statusColor}-100 text-${statusColor}-800">${prescription.status}</span>
                            </div>
                            <div class="text-sm text-gray-600">
                                <p><strong>Duration:</strong> ${prescription.duration || 'N/A'}</p>
                                <p><strong>Instructions:</strong> ${prescription.instruction || 'N/A'}</p>
                                <p><strong>Date Prescribed:</strong> ${prescription.date_prescribed || 'N/A'}</p>
                                ${prescription.date_discontinued ? `<p><strong>Date Discontinued:</strong> ${prescription.date_discontinued}</p>` : ''}
                                ${prescription.discontinuation_reason ? `<p><strong>Discontinuation Reason:</strong> ${prescription.discontinuation_reason}</p>` : ''}
                            </div>
                        </div>
                    `;
                });
            } else {
                html += '<p class="text-center text-gray-500 py-8">No medication history found for this patient.</p>';
            }
            html += '</div>';
            document.getElementById('historyContent').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('historyContent').innerHTML = '<p class="text-center text-red-500 py-8">Error loading medication history.</p>';
        });
}

function closeHistoryModal() {
    document.getElementById('historyModal').classList.add('hidden');
}
</script>

@push('scripts')
<script>
(function() {
    document.addEventListener('input', function(e) {
        if (e.target.id === 'prescriptionSearch') {
            var searchValue = e.target.value.toLowerCase();
            var rows = document.querySelectorAll('.prescription-row');
            var visibleCount = 0;
            rows.forEach(function(row) {
                var searchText = row.getAttribute('data-search') || '';
                var show = searchText.includes(searchValue);
                row.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });
            var noResults = document.getElementById('noPrescriptionResults');
            if (noResults) noResults.classList.toggle('hidden', visibleCount > 0);
        }
    });
})();
</script>
@endpush

<!-- Inline script for AJAX loaded content -->
<script>
(function() {
    document.addEventListener('input', function(e) {
        if (e.target.id === 'prescriptionSearch') {
            var searchValue = e.target.value.toLowerCase();
            var rows = document.querySelectorAll('.prescription-row');
            var visibleCount = 0;
            rows.forEach(function(row) {
                var searchText = row.getAttribute('data-search') || '';
                var show = searchText.includes(searchValue);
                row.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });
            var noResults = document.getElementById('noPrescriptionResults');
            if (noResults) noResults.classList.toggle('hidden', visibleCount > 0);
        }
    });
})();
</script>

@endsection
