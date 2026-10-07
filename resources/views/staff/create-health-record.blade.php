@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Staff Mode Indicator -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-blue-700 font-medium">Staff Mode - Creating Health Record</p>
            </div>
        </div>

        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Create New Health Record</h1>
                    <p class="text-gray-600">Add a new health record for a patient</p>
                </div>
                <a href="{{ route('staff.health-records') }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">
                    ← Back
                </a>
            </div>
        </div>

        <!-- Create Form -->
        <div class="bg-white shadow-sm rounded-lg">
            <form action="{{ route('staff.health-records.store') }}" method="POST" class="p-6">
                @csrf

                <div class="grid grid-cols-1 gap-6">
                    <!-- Patient Selection -->
                    <div>
                        <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Select Patient *
                        </label>
                        <select id="patient_id"
                                name="patient_id"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Select a Patient --</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->user->name }}
                                    @if($patient->student_id)
                                        ({{ $patient->student_id }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Record Type Selection -->
                    <div>
                        <label for="record_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Record Type *
                        </label>
                        <select id="record_type"
                                name="record_type"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Select Record Type --</option>
                            <option value="physical" {{ old('record_type') == 'physical' ? 'selected' : '' }}>Physical Examination</option>
                            <option value="dental" {{ old('record_type') == 'dental' ? 'selected' : '' }}>Dental Examination</option>
                            <option value="immunization" {{ old('record_type') == 'immunization' ? 'selected' : '' }}>Immunization Record</option>
                        </select>
                        @error('record_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Attending Doctor Selection -->
                    <div>
                        <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Attending Doctor
                        </label>
                        <select id="doctor_id"
                                name="doctor_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Select a Doctor --</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    {{ optional($doctor->user)->name ?: 'Doctor #' . $doctor->id }}
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Physical Examination Fields (hidden by default) -->
                    <div id="physical_fields" class="hidden border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">Physical Examination Details</h3>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="height" class="block text-sm font-medium text-gray-700 mb-2">Height (cm) *</label>
                                <input type="number" step="0.01" id="height" name="height" value="{{ old('height') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="e.g., 170" min="0">
                                @error('height')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Weight (kg) *</label>
                                <input type="number" step="0.1" id="weight" name="weight" value="{{ old('weight') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="e.g., 65" min="0">
                                @error('weight')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="bp" class="block text-sm font-medium text-gray-700 mb-2">Blood Pressure *</label>
                                <input type="text" id="bp" name="bp" value="{{ old('bp') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="e.g., 120/80">
                            </div>
                            <div>
                                <label for="heart" class="block text-sm font-medium text-gray-700 mb-2">Heart *</label>
                                <input type="text" id="heart" name="heart" value="{{ old('heart') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Normal/Abnormal">
                            </div>
                            <div>
                                <label for="lungs" class="block text-sm font-medium text-gray-700 mb-2">Lungs *</label>
                                <input type="text" id="lungs" name="lungs" value="{{ old('lungs') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="eyes" class="block text-sm font-medium text-gray-700 mb-2">Eyes *</label>
                                <input type="text" id="eyes" name="eyes" value="{{ old('eyes') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="ears" class="block text-sm font-medium text-gray-700 mb-2">Ears *</label>
                                <input type="text" id="ears" name="ears" value="{{ old('ears') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="nose" class="block text-sm font-medium text-gray-700 mb-2">Nose *</label>
                                <input type="text" id="nose" name="nose" value="{{ old('nose') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="throat" class="block text-sm font-medium text-gray-700 mb-2">Throat *</label>
                                <input type="text" id="throat" name="throat" value="{{ old('throat') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="skin" class="block text-sm font-medium text-gray-700 mb-2">Skin *</label>
                                <input type="text" id="skin" name="skin" value="{{ old('skin') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="physical_remarks" class="block text-sm font-medium text-gray-700 mb-2">Remarks *</label>
                            <textarea id="physical_remarks" name="remarks" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('remarks') }}</textarea>
                        </div>
                    </div>

                    <!-- Dental Examination Fields (hidden by default) -->
                    <div id="dental_fields" class="hidden border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">Dental Examination Details</h3>

                        <div class="mb-4">
                            <label for="dental_diagnosis" class="block text-sm font-medium text-gray-700 mb-2">Diagnosis *</label>
                            <textarea id="dental_diagnosis" name="dental_diagnosis" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Dental diagnosis and findings...">{{ old('dental_diagnosis') }}</textarea>
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

                    <!-- Immunization Fields (hidden by default) -->
                    <div id="immunization_fields" class="hidden border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-800 mb-4">Immunization Details</h3>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="vaccine_name" class="block text-sm font-medium text-gray-700 mb-2">Vaccine Name *</label>
                                <input type="text" id="vaccine_name" name="vaccine_name" value="{{ old('vaccine_name') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="COVID-19, Hepatitis B, etc.">
                            </div>
                            <div>
                                <label for="vaccine_type" class="block text-sm font-medium text-gray-700 mb-2">Vaccine Type *</label>
                                <input type="text" id="vaccine_type" name="vaccine_type" value="{{ old('vaccine_type') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="mRNA, Inactivated, etc.">
                            </div>
                            <div>
                                <label for="dosage" class="block text-sm font-medium text-gray-700 mb-2">Dosage *</label>
                                <input type="text" id="dosage" name="dosage" value="{{ old('dosage') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="0.5 mL, 1st dose, etc.">
                            </div>
                            <div>
                                <label for="site_of_administration" class="block text-sm font-medium text-gray-700 mb-2">Site of Administration *</label>
                                <select id="site_of_administration" name="site_of_administration"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="" disabled selected>Select site</option>
                                    <option value="Left arm" {{ old('site_of_administration') == 'Left arm' ? 'selected' : '' }}>Left arm</option>
                                    <option value="Right arm" {{ old('site_of_administration') == 'Right arm' ? 'selected' : '' }}>Right arm</option>
                                    <option value="Left thigh" {{ old('site_of_administration') == 'Left thigh' ? 'selected' : '' }}>Left thigh</option>
                                    <option value="Right thigh" {{ old('site_of_administration') == 'Right thigh' ? 'selected' : '' }}>Right thigh</option>
                                    <option value="Oral" {{ old('site_of_administration') == 'Oral' ? 'selected' : '' }}>Oral</option>
                                </select>
                            </div>
                            <div>
                                <label for="expiration_date" class="block text-sm font-medium text-gray-700 mb-2">Expiration Date *</label>
                                <input type="date" id="expiration_date" name="expiration_date" value="{{ old('expiration_date') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="immunization_notes" class="block text-sm font-medium text-gray-700 mb-2">Notes *</label>
                            <textarea id="immunization_notes" name="immunization_notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Additional notes about the immunization...">{{ old('immunization_notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                    <a href="{{ route('staff.health-records') }}"
                       class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        Create Record
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
    // Show/hide fields based on record type
    document.getElementById('record_type').addEventListener('change', function() {
        toggleRecordFields();
    });

    function toggleRecordFields() {
        const recordType = document.getElementById('record_type').value;
        const physicalFields = document.getElementById('physical_fields');
        const dentalFields = document.getElementById('dental_fields');
        const immunizationFields = document.getElementById('immunization_fields');

        // Hide all
        physicalFields.classList.add('hidden');
        dentalFields.classList.add('hidden');
        immunizationFields.classList.add('hidden');

        // Remove required from all conditional fields
        const immunizationFieldIds = ['vaccine_name', 'vaccine_type', 'dosage', 'site_of_administration', 'expiration_date', 'immunization_notes'];
        immunizationFieldIds.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.removeAttribute('required');
        });
        const physicalFieldIds = ['height', 'weight', 'bp', 'heart', 'lungs', 'eyes', 'ears', 'nose', 'throat', 'skin', 'physical_remarks'];
        physicalFieldIds.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.removeAttribute('required');
        });
        const dentalFieldIds = ['dental_diagnosis'];
        dentalFieldIds.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.removeAttribute('required');
        });

        // Show selected and set required
        if (recordType === 'physical') {
            physicalFields.classList.remove('hidden');
            physicalFieldIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.setAttribute('required', 'required');
            });
        } else if (recordType === 'dental') {
            dentalFields.classList.remove('hidden');
            dentalFieldIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.setAttribute('required', 'required');
            });
        } else if (recordType === 'immunization') {
            immunizationFields.classList.remove('hidden');
            immunizationFieldIds.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.setAttribute('required', 'required');
            });
        }
    }

    // Client-side form validation
    document.querySelector('form').addEventListener('submit', function(e) {
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
            const doctorId = document.getElementById('doctor_id');
            if (doctorId && !doctorId.value) {
                errors.push('Please select an attending doctor');
                highlightField('doctor_id');
            } else if (doctorId) {
                unhighlightField('doctor_id');
            }
        }

        // Check dental diagnosis
        if (recordType === 'dental') {
            const diagnosis = document.getElementById('dental_diagnosis');
            if (diagnosis && !diagnosis.value.trim()) {
                errors.push('Please enter a diagnosis');
                highlightField('dental_diagnosis');
            } else if (diagnosis) {
                unhighlightField('dental_diagnosis');
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
                { id: 'physical_remarks', msg: 'Please enter remarks' }
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

        // Check immunization fields
        if (recordType === 'immunization') {
            const immunizationFieldNames = [
                { id: 'vaccine_name', msg: 'Please enter a vaccine name' },
                { id: 'vaccine_type', msg: 'Please enter a vaccine type' },
                { id: 'dosage', msg: 'Please enter a dosage' },
                { id: 'expiration_date', msg: 'Please enter an expiration date' },
                { id: 'immunization_notes', msg: 'Please enter notes' }
            ];
            immunizationFieldNames.forEach(field => {
                const el = document.getElementById(field.id);
                if (el && !el.value.trim()) {
                    errors.push(field.msg);
                    highlightField(field.id);
                } else if (el) {
                    unhighlightField(field.id);
                }
            });

            // Check site of administration (select dropdown)
            const siteOfAdmin = document.getElementById('site_of_administration');
            if (siteOfAdmin && !siteOfAdmin.value) {
                errors.push('Please select a site of administration');
                highlightField('site_of_administration');
            } else if (siteOfAdmin) {
                unhighlightField('site_of_administration');
            }
        }

        // If there are errors, prevent submission and show alert
        if (errors.length > 0) {
            e.preventDefault();
            e.stopImmediatePropagation();
            showValidationAlert(errors);
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
