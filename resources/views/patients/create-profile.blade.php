@extends('layouts.app')

@section('content')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const patientTypeSelect = document.getElementById('patient_type');
    const schoolIdField = document.getElementById('school_id_field');
    const edulvlField = document.getElementById('edulvl_field');
    const courseField = document.getElementById('course_field');
    const courseSelect = document.getElementById('course_id');
    const levelSelect = document.getElementById('edulvl_id');

    function toggleStudentFields() {
        const isStudent = patientTypeSelect && patientTypeSelect.value === 'student';
        if (schoolIdField) schoolIdField.style.display = isStudent ? 'block' : 'none';
        if (edulvlField) edulvlField.style.display = isStudent ? 'block' : 'none';
        toggleCourseRequirement(isStudent);
    }


    function toggleCourseRequirement(isStudent = true) {
        const selected = levelSelect ? levelSelect.options[levelSelect.selectedIndex] : null;
        const requiresCourse = Boolean(isStudent && selected && selected.dataset.requiresCourse === '1');
        const indicators = document.querySelectorAll('.course-required-indicator');

        if (!courseField || !courseSelect) return;

        courseField.style.display = requiresCourse ? 'block' : 'none';
        courseSelect.required = requiresCourse;
        indicators.forEach((indicator) => indicator.style.display = requiresCourse ? 'inline' : 'none');

        if (!requiresCourse) {
            courseSelect.value = '';
        }
    }

    if (patientTypeSelect) {
        patientTypeSelect.addEventListener('change', toggleStudentFields);
        if (levelSelect) levelSelect.addEventListener('change', () => toggleCourseRequirement(patientTypeSelect.value === 'student'));
        toggleStudentFields();
    }
});
</script>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-800">Create Patient Profile</h1>
            <p class="text-gray-600">Complete your profile to access all patient features</p>
        </div>

        <div class="p-6">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('patients.profile.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Patient Type -->
                <div>
                    <label for="patient_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Patient Type <span class="text-red-500">*</span>
                    </label>
                    <select name="patient_type" id="patient_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select patient type...</option>
                        <option value="student" {{ old('patient_type') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="staff" {{ old('patient_type') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="faculty" {{ old('patient_type') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                        <option value="external" {{ old('patient_type') == 'external' ? 'selected' : '' }}>External</option>
                    </select>
                </div>

                <!-- School ID (for students) -->
                <div id="school_id_field" style="display: none;">
                    <label for="school_id" class="block text-sm font-medium text-gray-700 mb-2">
                        School ID
                    </label>
                    <input type="text" name="school_id" id="school_id"
                           value="{{ old('school_id') }}"
                           placeholder="Enter your School ID"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Educational Level (for students) -->
                <div id="edulvl_field" style="display: none;">
                    <label for="edulvl_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Educational Level
                    </label>
                    <select name="edulvl_id" id="edulvl_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select educational level...</option>
                        @if(isset($edulvl))
                            @foreach($edulvl as $level)
                                <option value="{{ $level->id }}" data-requires-course="{{ \App\Services\PatientEducationRequirement::levelRequiresCourse($level) ? '1' : '0' }}" {{ old('edulvl_id') == $level->id ? 'selected' : '' }}>
                                    {{ $level->level_name }} - {{ $level->year_level }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>


                <!-- Course (required for College levels 1-4) -->
                <div id="course_field" style="display: none;">
                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Course <span class="text-red-500 course-required-indicator" style="display: none;">*</span>
                    </label>
                    <select name="course_id" id="course_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select course...</option>
                        @foreach($courses ?? [] as $course)
                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->course_name }}</option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Required for College levels 1, 2, 3, and 4.</p>
                </div>

                <!-- Blood Type -->
                <div>
                    <label for="bloodtype" class="block text-sm font-medium text-gray-700 mb-2">
                        Blood Type
                    </label>
                    <select name="bloodtype" id="bloodtype"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select blood type...</option>
                        <option value="A+" {{ old('bloodtype') == 'A+' ? 'selected' : '' }}>A+</option>
                        <option value="A-" {{ old('bloodtype') == 'A-' ? 'selected' : '' }}>A-</option>
                        <option value="B+" {{ old('bloodtype') == 'B+' ? 'selected' : '' }}>B+</option>
                        <option value="B-" {{ old('bloodtype') == 'B-' ? 'selected' : '' }}>B-</option>
                        <option value="O+" {{ old('bloodtype') == 'O+' ? 'selected' : '' }}>O+</option>
                        <option value="O-" {{ old('bloodtype') == 'O-' ? 'selected' : '' }}>O-</option>
                        <option value="AB+" {{ old('bloodtype') == 'AB+' ? 'selected' : '' }}>AB+</option>
                        <option value="AB-" {{ old('bloodtype') == 'AB-' ? 'selected' : '' }}>AB-</option>
                    </select>
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Address <span class="text-red-500">*</span>
                    </label>
                    <textarea name="address" id="address" rows="3" required
                              placeholder="Enter your complete address..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
                </div>

                <!-- Medical Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Medical Information</h3>

                    <div class="space-y-4">
                        <div>
                            <label for="medical_condition" class="block text-sm font-medium text-gray-700 mb-2">
                                Medical Conditions (Optional)
                            </label>
                            <textarea name="medical_condition" id="medical_condition" rows="2"
                                      placeholder="List any ongoing medical conditions..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('medical_condition') }}</textarea>
                        </div>

                        <div>
                            <label for="medical_illness" class="block text-sm font-medium text-gray-700 mb-2">
                                Medical Illness History (Optional)
                            </label>
                            <textarea name="medical_illness" id="medical_illness" rows="2"
                                      placeholder="List any past illnesses..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('medical_illness') }}</textarea>
                        </div>

                        <div>
                            <label for="operations" class="block text-sm font-medium text-gray-700 mb-2">
                                Previous Operations (Optional)
                            </label>
                            <textarea name="operations" id="operations" rows="2"
                                      placeholder="List any previous operations or surgeries..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('operations') }}</textarea>
                        </div>

                        <div>
                            <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">
                                Allergies (Optional)
                            </label>
                            <textarea name="allergies" id="allergies" rows="2"
                                      placeholder="List any known allergies (food, medication, environmental)..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('allergies') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Emergency Contact</h3>

                    <div class="space-y-4">
                        <div>
                            <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="emergency_contact_name" id="emergency_contact_name" required
                                   value="{{ old('emergency_contact_name') }}"
                                   placeholder="Full name of emergency contact"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="emergency_contact_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="emergency_contact_number" id="emergency_contact_number" required
                                   value="{{ old('emergency_contact_number') }}"
                                   placeholder="Phone number of emergency contact"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="emergency_relationship" class="block text-sm font-medium text-gray-700 mb-2">
                                Relationship <span class="text-red-500">*</span>
                            </label>
                            <select name="emergency_relationship" id="emergency_relationship" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select relationship...</option>
                                <option value="Parent" {{ old('emergency_relationship') == 'Parent' ? 'selected' : '' }}>Parent</option>
                                <option value="Guardian" {{ old('emergency_relationship') == 'Guardian' ? 'selected' : '' }}>Guardian</option>
                                <option value="Spouse" {{ old('emergency_relationship') == 'Spouse' ? 'selected' : '' }}>Spouse</option>
                                <option value="Sibling" {{ old('emergency_relationship') == 'Sibling' ? 'selected' : '' }}>Sibling</option>
                                <option value="Relative" {{ old('emergency_relationship') == 'Relative' ? 'selected' : '' }}>Relative</option>
                                <option value="Friend" {{ old('emergency_relationship') == 'Friend' ? 'selected' : '' }}>Friend</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between pt-6">
                    <a href="{{ route('patients.dashboard') }}"
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        Create Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
