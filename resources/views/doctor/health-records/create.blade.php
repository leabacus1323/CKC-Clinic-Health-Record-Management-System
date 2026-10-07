@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Create Health Record</h1>
                    <p class="text-gray-600">Add a new health record for your patient</p>
                </div>
                <a href="{{ route('doctor.health-records') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm">
                    Back to Health Records
                </a>
            </div>
        </div>

        <div class="p-6">
            <form action="{{ route('doctor.health-records.store') }}" method="POST" id="healthRecordForm">
                @csrf

                <!-- Patient Selection -->
                <div class="mb-6">
                    <label for="patient_id" class="block mb-2 text-sm font-medium text-gray-900">Patient *</label>
                    <select name="patient_id" id="patient_id"
                            class="block py-2.5 px-3 w-full text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            required>
                        <option value="" disabled selected>Select a patient</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->user->name }} (ID: {{ $patient->id }})
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Record Type Selection -->
                @php
                    $special = strtolower($doctor->specialization ?? '');
                    $isDentist = $special === 'dentist';
                    $isPhysician = $special === 'physician';
                @endphp
                <div class="mb-6">
                    <label for="record_type" class="block mb-2 text-sm font-medium text-gray-900">Record Type *</label>
                    <select name="record_type" id="record_type"
                            class="block py-2.5 px-3 w-full text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            required onchange="toggleRecordFields()">
                        <option value="" disabled {{ !($isDentist || $isPhysician) && !old('record_type') ? 'selected' : '' }}>Select record type</option>
                        @if($isDentist)
                            <option value="dental" {{ old('record_type') == 'dental' ? 'selected' : '' }}>Dental Examination</option>
                            <option value="immunization" {{ old('record_type') == 'immunization' ? 'selected' : '' }}>Immunization Record</option>
                        @elseif($isPhysician)
                            <option value="physical" {{ old('record_type') == 'physical' ? 'selected' : '' }}>Physical Examination</option>
                            <option value="immunization" {{ old('record_type') == 'immunization' ? 'selected' : '' }}>Immunization Record</option>
                        @else
                            <option value="physical" {{ old('record_type') == 'physical' ? 'selected' : '' }}>Physical Examination</option>
                            <option value="dental" {{ old('record_type') == 'dental' ? 'selected' : '' }}>Dental Examination</option>
                            <option value="immunization" {{ old('record_type') == 'immunization' ? 'selected' : '' }}>Immunization Record</option>
                        @endif
                    </select>
                    @error('record_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($isDentist)
                        <p class="mt-2 text-sm text-yellow-700">As a dentist, you can create dental and immunization records.</p>
                    @endif
                    @if($isPhysician)
                        <p class="mt-2 text-sm text-yellow-700">As a physician, you can create physical and immunization records.</p>
                    @endif
                </div>

                <!-- Physical Examination Fields -->
                <div id="physical_fields" class="record-fields" style="display: none;">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Physical Examination Details</h3>

                    <!-- Vital Signs -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div>
                            <label for="height" class="block mb-2 text-sm font-medium text-gray-900">Height (cm) *</label>
                            <input type="number" step="0.1" name="height" id="height"
                                   class="block w-full p-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('height') }}" placeholder="170.5">
                        </div>
                        <div>
                            <label for="weight" class="block mb-2 text-sm font-medium text-gray-900">Weight (kg) *</label>
                            <input type="number" step="0.1" name="weight" id="weight"
                                   class="block w-full p-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('weight') }}" placeholder="65.0">
                        </div>
                        <div>
                            <label for="bp" class="block mb-2 text-sm font-medium text-gray-900">Blood Pressure *</label>
                            <input type="text" name="bp" id="bp"
                                   class="block w-full p-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('bp') }}" placeholder="120/80">
                        </div>
                    </div>

                    <!-- Examination Areas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="heart" class="block mb-2 text-sm font-medium text-gray-900">Heart *</label>
                            <input type="text" name="heart" id="heart"
                                   class="block w-full p-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('heart') }}" placeholder="Regular rhythm, no murmurs">
                        </div>
                        <div>
                            <label for="lungs" class="block mb-2 text-sm font-medium text-gray-900">Lungs *</label>
                            <input type="text" name="lungs" id="lungs"
                                   class="block w-full p-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('lungs') }}" placeholder="Clear to auscultation">
                        </div>
                        <div>
                            <label for="eyes" class="block mb-2 text-sm font-medium text-gray-900">Eyes *</label>
                            <input type="text" name="eyes" id="eyes"
                                   class="block w-full p-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('eyes') }}" placeholder="Normal appearance">
                        </div>
                        <div>
                            <label for="ears" class="block mb-2 text-sm font-medium text-gray-900">Ears *</label>
                            <input type="text" name="ears" id="ears"
                                   class="block w-full p-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('ears') }}" placeholder="Normal hearing">
                        </div>
                        <div>
                            <label for="nose" class="block mb-2 text-sm font-medium text-gray-900">Nose *</label>
                            <input type="text" name="nose" id="nose"
                                   class="block w-full p-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('nose') }}" placeholder="No congestion">
                        </div>
                        <div>
                            <label for="throat" class="block mb-2 text-sm font-medium text-gray-900">Throat *</label>
                            <input type="text" name="throat" id="throat"
                                   class="block w-full p-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('throat') }}" placeholder="No inflammation">
                        </div>
                        <div>
                            <label for="skin" class="block mb-2 text-sm font-medium text-gray-900">Skin *</label>
                            <input type="text" name="skin" id="skin"
                                   class="block w-full p-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('skin') }}" placeholder="Normal color and texture">
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div class="mb-6">
                        <label for="remarks" class="block mb-2 text-sm font-medium text-gray-900">Remarks *</label>
                        <textarea name="remarks" id="remarks" rows="4"
                                  class="block w-full p-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Additional notes and observations..."></textarea>
                    </div>
                </div>

                <!-- Dental Examination Fields -->
                <div id="dental_fields" class="record-fields" style="display: none;">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Dental Examination Details</h3>

                    <div class="mb-6">
                        <label for="diagnosis" class="block mb-2 text-sm font-medium text-gray-900">Diagnosis *</label>
                        <textarea name="diagnosis" id="diagnosis" rows="4"
                                  class="block w-full p-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Dental diagnosis and findings...">{{ old('diagnosis') }}</textarea>
                    </div>

                    <!-- Dental Chart -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Dental Chart</label>

                            <!-- Legend -->
                            <div class="flex flex-wrap gap-4 mb-4 p-3 bg-gray-50 rounded">
                                <div class="flex items-center">
                                    <span class="w-6 h-6 bg-green-500 rounded mr-2"></span>
                                    <span class="text-sm">Healthy</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-6 h-6 bg-red-500 rounded mr-2"></span>
                                    <span class="text-sm">Cavity</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-6 h-6 bg-blue-500 rounded mr-2"></span>
                                    <span class="text-sm">Filled</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-6 h-6 bg-gray-500 rounded mr-2"></span>
                                    <span class="text-sm">Missing</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="w-6 h-6 bg-yellow-500 rounded mr-2"></span>
                                    <span class="text-sm">Other</span>
                                </div>
                            </div>

                            <!-- Upper Teeth -->
                            <div class="mb-6">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Upper Teeth</h4>
                                <div class="grid grid-cols-8 gap-2">
                                    @for($i = 1; $i <= 16; $i++)
                                        <div class="text-center">
                                            <div class="tooth-selector w-12 h-12 border-2 border-gray-300 rounded cursor-pointer hover:border-gray-500 transition flex items-center justify-center bg-white"
                                                 data-tooth="upper-{{ $i }}"
                                                 data-status="healthy"
                                                 onclick="toggleToothStatus(this)">
                                                <span class="text-xs font-medium">{{ $i }}</span>
                                            </div>
                                            <input type="hidden" name="teeth_status[upper][{{ $i }}]" value="healthy">
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <!-- Lower Teeth -->
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Lower Teeth</h4>
                                <div class="grid grid-cols-8 gap-2">
                                    @for($i = 1; $i <= 16; $i++)
                                        <div class="text-center">
                                            <div class="tooth-selector w-12 h-12 border-2 border-gray-300 rounded cursor-pointer hover:border-gray-500 transition flex items-center justify-center bg-white"
                                                 data-tooth="lower-{{ $i }}"
                                                 data-status="healthy"
                                                 onclick="toggleToothStatus(this)">
                                                <span class="text-xs font-medium">{{ $i }}</span>
                                            </div>
                                            <input type="hidden" name="teeth_status[lower][{{ $i }}]" value="healthy">
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                </div>

                <!-- Immunization Fields -->
                <div id="immunization_fields" class="record-fields" style="display: none;">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Immunization Details</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="vaccine_name" class="block mb-2 text-sm font-medium text-gray-900">Vaccine Name *</label>
                            <input type="text" name="vaccine_name" id="vaccine_name"
                                   class="block w-full p-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('vaccine_name') }}" placeholder="COVID-19, Hepatitis B, etc.">
                        </div>
                        <div>
                            <label for="vaccine_type" class="block mb-2 text-sm font-medium text-gray-900">Vaccine Type *</label>
                            <input type="text" name="vaccine_type" id="vaccine_type"
                                   class="block w-full p-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('vaccine_type') }}" placeholder="mRNA, Inactivated, etc.">
                        </div>
                        <div>
                            <label for="dosage" class="block mb-2 text-sm font-medium text-gray-900">Dosage *</label>
                            <input type="text" name="dosage" id="dosage"
                                   class="block w-full p-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('dosage') }}" placeholder="0.5 mL, 1st dose, etc.">
                        </div>
                        <div>
                            <label for="site_of_administration" class="block mb-2 text-sm font-medium text-gray-900">Site of Administration *</label>
                            <select name="site_of_administration" id="site_of_administration"
                                    class="block w-full p-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="" disabled {{ old('site_of_administration') ? '' : 'selected' }}>Select site</option>
                                <option value="Left arm" {{ old('site_of_administration') == 'Left arm' ? 'selected' : '' }}>Left arm</option>
                                <option value="Right arm" {{ old('site_of_administration') == 'Right arm' ? 'selected' : '' }}>Right arm</option>
                                <option value="Left thigh" {{ old('site_of_administration') == 'Left thigh' ? 'selected' : '' }}>Left thigh</option>
                                <option value="Right thigh" {{ old('site_of_administration') == 'Right thigh' ? 'selected' : '' }}>Right thigh</option>
                                <option value="Oral" {{ old('site_of_administration') == 'Oral' ? 'selected' : '' }}>Oral</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="expiration_date" class="block mb-2 text-sm font-medium text-gray-900">Expiration Date *</label>
                            <input type="date" name="expiration_date" id="expiration_date"
                                   class="block w-full p-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('expiration_date') }}">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="notes" class="block mb-2 text-sm font-medium text-gray-900">Notes *</label>
                        <textarea name="notes" id="notes" rows="4"
                                  class="block w-full p-2.5 text-sm text-gray-900 bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Additional notes about the immunization...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('doctor.health-records') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                        Create Health Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Validation Error Alert -->
<div id="validationAlert" class="hidden fixed top-4 right-4 z-50 max-w-md">
    <div class="bg-red-50 border border-red-200 rounded-lg shadow-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h4 class="text-sm font-medium text-red-800">Please fix the following errors:</h4>
                <ul id="validationErrors" class="mt-2 text-sm text-red-700 list-disc list-inside"></ul>
            </div>
            <button onclick="document.getElementById('validationAlert').classList.add('hidden')" class="ml-auto text-red-400 hover:text-red-600">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
function toggleRecordFields() {
    const recordType = document.getElementById('record_type').value;
    const allFields = document.querySelectorAll('.record-fields');

    // Hide all field groups
    allFields.forEach(field => {
        field.style.display = 'none';
    });

    // Remove required from all conditional fields
    const immunizationFields = ['vaccine_name', 'vaccine_type', 'dosage', 'site_of_administration', 'expiration_date', 'notes'];
    immunizationFields.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.removeAttribute('required');
    });
    const physicalFields = ['height', 'weight', 'bp', 'heart', 'lungs', 'eyes', 'ears', 'nose', 'throat', 'skin', 'remarks'];
    physicalFields.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.removeAttribute('required');
    });
    const dentalFields = ['diagnosis'];
    dentalFields.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.removeAttribute('required');
    });

    // Show selected field group and set required attributes
    if (recordType) {
        const targetFields = document.getElementById(recordType + '_fields');
        if (targetFields) {
            targetFields.style.display = 'block';
        }

        // Add required to immunization fields when immunization is selected
        if (recordType === 'immunization') {
            immunizationFields.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.setAttribute('required', 'required');
            });
        }

        // Add required to physical fields when physical is selected
        if (recordType === 'physical') {
            physicalFields.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.setAttribute('required', 'required');
            });
        }

        // Add required to dental fields when dental is selected
        if (recordType === 'dental') {
            dentalFields.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.setAttribute('required', 'required');
            });
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleRecordFields();

    // Client-side form validation
    const form = document.getElementById('healthRecordForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const errors = [];
            const recordType = document.getElementById('record_type').value;

            // Check patient selection
            const patientId = document.getElementById('patient_id').value;
            if (!patientId) {
                errors.push('Please select a patient');
                highlightField('patient_id');
            } else {
                unhighlightField('patient_id');
            }

            // Check record type
            if (!recordType) {
                errors.push('Please select a record type');
                highlightField('record_type');
            } else {
                unhighlightField('record_type');
            }

            // Check doctor for physical and dental
            if ((recordType === 'physical' || recordType === 'dental')) {
                const doctorSelect = document.getElementById('doctor_id');
                if (doctorSelect && !doctorSelect.value) {
                    errors.push('Please select a doctor');
                    highlightField('doctor_id');
                } else if (doctorSelect) {
                    unhighlightField('doctor_id');
                }
            }

            // Check dental diagnosis
            if (recordType === 'dental') {
                const diagnosis = document.getElementById('diagnosis');
                if (diagnosis && !diagnosis.value.trim()) {
                    errors.push('Please enter a diagnosis');
                    highlightField('diagnosis');
                } else if (diagnosis) {
                    unhighlightField('diagnosis');
                }
            }

            // Check physical examination fields
            if (recordType === 'physical') {
                const physicalFieldNames = [
                    { id: 'height', msg: 'Please enter height' },
                    { id: 'weight', msg: 'Please enter weight' },
                    { id: 'bp', msg: 'Please enter blood pressure' },
                    { id: 'heart', msg: 'Please enter heart status' },
                    { id: 'lungs', msg: 'Please enter lungs status' },
                    { id: 'eyes', msg: 'Please enter eyes status' },
                    { id: 'ears', msg: 'Please enter ears status' },
                    { id: 'nose', msg: 'Please enter nose status' },
                    { id: 'throat', msg: 'Please enter throat status' },
                    { id: 'skin', msg: 'Please enter skin status' },
                    { id: 'remarks', msg: 'Please enter remarks' }
                ];
                physicalFieldNames.forEach(field => {
                    const el = document.getElementById(field.id);
                    if (el && !el.value.trim()) {
                        errors.push(field.msg);
                        highlightField(field.id);
                    } else if (el) {
                        unhighlightField(field.id);
                    }
                });
            }

            // Check vaccine name for immunization
            if (recordType === 'immunization') {
                const vaccineName = document.getElementById('vaccine_name');
                if (vaccineName && !vaccineName.value.trim()) {
                    errors.push('Please enter a vaccine name');
                    highlightField('vaccine_name');
                } else if (vaccineName) {
                    unhighlightField('vaccine_name');
                }

                const vaccineType = document.getElementById('vaccine_type');
                if (vaccineType && !vaccineType.value.trim()) {
                    errors.push('Please enter a vaccine type');
                    highlightField('vaccine_type');
                } else if (vaccineType) {
                    unhighlightField('vaccine_type');
                }

                const dosage = document.getElementById('dosage');
                if (dosage && !dosage.value.trim()) {
                    errors.push('Please enter a dosage');
                    highlightField('dosage');
                } else if (dosage) {
                    unhighlightField('dosage');
                }

                const siteOfAdmin = document.getElementById('site_of_administration');
                if (siteOfAdmin && !siteOfAdmin.value) {
                    errors.push('Please select a site of administration');
                    highlightField('site_of_administration');
                } else if (siteOfAdmin) {
                    unhighlightField('site_of_administration');
                }

                const expDate = document.getElementById('expiration_date');
                if (expDate && !expDate.value) {
                    errors.push('Please enter an expiration date');
                    highlightField('expiration_date');
                } else if (expDate) {
                    unhighlightField('expiration_date');
                }

                const notes = document.getElementById('notes');
                if (notes && !notes.value.trim()) {
                    errors.push('Please enter notes');
                    highlightField('notes');
                } else if (notes) {
                    unhighlightField('notes');
                }
            }

            // If there are errors, prevent submission and show alert
            if (errors.length > 0) {
                e.preventDefault();
                e.stopImmediatePropagation();
                showValidationAlert(errors);
            }
        });
    }
});

function highlightField(fieldId) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.classList.add('border-red-500', 'ring-2', 'ring-red-200');
    }
}

function unhighlightField(fieldId) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
    }
}

function showValidationAlert(errors) {
    const alertDiv = document.getElementById('validationAlert');
    const errorList = document.getElementById('validationErrors');
    errorList.innerHTML = '';
    errors.forEach(error => {
        const li = document.createElement('li');
        li.textContent = error;
        errorList.appendChild(li);
    });
    alertDiv.classList.remove('hidden');

    // Auto-hide after 5 seconds
    setTimeout(() => {
        alertDiv.classList.add('hidden');
    }, 5000);
}

// Tooth status colors
const statusColors = {
    'healthy': 'bg-green-500',
    'cavity': 'bg-red-500',
    'filled': 'bg-blue-500',
    'missing': 'bg-gray-500',
    'other': 'bg-yellow-500'
};

const statusOrder = ['healthy', 'cavity', 'filled', 'missing', 'other'];

// Toggle tooth status
function toggleToothStatus(element) {
    const currentStatus = element.getAttribute('data-status');
    const currentIndex = statusOrder.indexOf(currentStatus);
    const nextIndex = (currentIndex + 1) % statusOrder.length;
    const nextStatus = statusOrder[nextIndex];

    // Remove all status color classes
    Object.values(statusColors).forEach(colorClass => {
        element.classList.remove(colorClass);
    });

    // Add new status color
    element.classList.add(statusColors[nextStatus]);
    element.setAttribute('data-status', nextStatus);

    // Update hidden input
    const hiddenInput = element.parentElement.querySelector('input[type="hidden"]');
    hiddenInput.value = nextStatus;

    // Update border to white when not healthy for better visibility
    if (nextStatus !== 'healthy') {
        element.classList.remove('bg-white');
    } else {
        element.classList.add('bg-white');
    }
}
</script>
@endsection
