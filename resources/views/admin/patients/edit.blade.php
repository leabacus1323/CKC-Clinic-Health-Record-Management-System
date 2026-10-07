@extends('admin.layout.navigation')

@section('content')
<div class="relative overflow-x-auto">
    @if (session('success'))
    <div id="alert-border-3" class="flex items-center p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
        </svg>
        <div class="ms-3 text-sm font-medium">
            {{ session('success') }}
        </div>
    </div>
    @endif
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div id="alert-border-4" class="flex items-center p-4 mb-4 text-yellow-800 border-t-4 border-yellow-300 bg-yellow-50 dark:text-yellow-300 dark:bg-gray-800 dark:border-yellow-800" role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <div class="ms-3 text-sm font-medium">
                    {{ $error }}
                </div>
            </div>
        @endforeach
    @endif
    <h2 class="text-xl font-semibold text-white">Update Patient</h2>
    <form class="max-w mx-auto" action="{{ route('admin.patients.update', $patients->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="relative z-0 w-full mb-6 group">
                <input type="number" id="searchValue" name="user_id" hidden value="{{ $patients->user_id }}">
                <!-- Search Input -->
                <input
                    type="text"
                    placeholder=""
                    id="searchInput"
                    class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                    oninput="filterOptions()"
                    onclick="toggleDropdown()"
                    value="{{ $patients->name }}"
                />

                <!-- Dropdown Options -->
                <div id="dropdown" class="block w-full p-2.5 bg-white border border-gray-300 rounded-lg shadow-lg hidden">
                    <ul id="optionsList" class="max-h-40 overflow-y-auto">
                    <!-- Loop through options from the database -->
                    @foreach($users as $user)
                        <li class="px-4 py-2 cursor-pointer hover:bg-blue-100" onclick="selectOption('{{ $user['name'] }}','{{ $user['id'] }}', event)">
                        {{ $user['name'] }}
                        </li>
                    @endforeach
                    </ul>
                </div>
                <label for="searchInput" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Patient</label>
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="relative z-0 w-full mb-5 group">
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="relative z-0 w-full mb-5 group">
                <label for="edulvl_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Educational Level</label>
                <select id="edulvl_id" name="edulvl_id" onchange="toggleCourseRequirement()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    @foreach ($edulvl as $level)
                        @if ($patients->edulvl_id == $level->id)
                            <option selected value="{{ $level->id }}" data-requires-course="{{ \App\Services\PatientEducationRequirement::levelRequiresCourse($level) ? '1' : '0' }}">{{ $level->level_name }} {{ $level->year_level }}</option>
                        @else
                            <option value="{{ $level->id }}" data-requires-course="{{ \App\Services\PatientEducationRequirement::levelRequiresCourse($level) ? '1' : '0' }}" {{ old('edulvl_id') == $level->id ? 'selected' : '' }}>{{ $level->level_name }} {{ $level->year_level }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="relative z-0 w-full mb-5 group">
                <input type="text" name="school_id" id="school_id" required class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " value="{{ $patients->school_id }}"/>
                <label for="school_id" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">School ID</label>
            </div>
        </div>
        <div id="course_field" class="grid md:grid-cols-2 md:gap-6 hidden">
            <div class="relative z-0 w-full mb-5 group">
                <label for="course_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Course <span class="text-red-500 course-required-indicator hidden">*</span></label>
                <select id="course_id" name="course_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Select Course</option>
                    @foreach ($courses ?? [] as $course)
                        <option value="{{ $course->id }}" {{ old('course_id', $patients->course_id ?? null) == $course->id ? 'selected' : '' }}>{{ $course->course_name }}</option>
                    @endforeach
                </select>
                @error('course_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Required for College levels 1, 2, 3, and 4.</p>
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="relative z-0 w-full mb-5 group">
                <label for="patient_type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Patient Type</label>
                <select id="patient_type" name="patient_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    @if ($patients->patient_type == 1)
                        <option selected value="1">Student</option>
                    @elseif ($patients->patient_type == 2)
                        <option value="2">Faculty & Staff</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="relative z-0 w-full mb-5 group">
                <label for="bloodtype" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Blood Type</label>
                <select id="bloodtype" name="bloodtype" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">Select Blood Type</option>
                    <option value="A+" {{ $patients->bloodtype == 'A+' ? 'selected' : '' }}>A+</option>
                    <option value="A-" {{ $patients->bloodtype == 'A-' ? 'selected' : '' }}>A-</option>
                    <option value="B+" {{ $patients->bloodtype == 'B+' ? 'selected' : '' }}>B+</option>
                    <option value="B-" {{ $patients->bloodtype == 'B-' ? 'selected' : '' }}>B-</option>
                    <option value="AB+" {{ $patients->bloodtype == 'AB+' ? 'selected' : '' }}>AB+</option>
                    <option value="AB-" {{ $patients->bloodtype == 'AB-' ? 'selected' : '' }}>AB-</option>
                    <option value="O+" {{ $patients->bloodtype == 'O+' ? 'selected' : '' }}>O+</option>
                    <option value="O-" {{ $patients->bloodtype == 'O-' ? 'selected' : '' }}>O-</option>
                </select>
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="relative z-0 w-full mb-5 group">
                <label for="date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Address</label>
                <textarea id="address" name="address" rows="3" class="mt-1 p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $patients->address }}</textarea>
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="relative z-0 w-full mb-5 group">
                <label for="medical_condition" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Medical Condition</label>
                <textarea id="medical_condition" name="medical_condition" rows="3" class="mt-1 p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $patients->medical_condition }}</textarea>
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="relative z-0 w-full mb-5 group">
                <label for="medical_illness" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Medical Illness</label>
                <textarea id="medical_illness" name="medical_illness" rows="3" class="mt-1 p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"> {{ $patients->medical_illness }} </textarea>
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="relative z-0 w-full mb-5 group">
                <label for="operations" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Operations</label>
                <textarea id="operations" name="operations" rows="3" class="mt-1 p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"> {{ $patients->operations }} </textarea>
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="relative z-0 w-full mb-5 group">
                <label for="allergies" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Allergies</label>
                <textarea id="allergies" name="allergies" rows="3" class="mt-1 p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $patients->allergies }}</textarea>
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="relative z-0 w-full mb-5 group">
                <input type="text" name="emergency_contact_name" id="emergency_contact_name" required class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " value="{{ $patients->emergency_contact_name }}"/>
                <label for="emergency_contact_name" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Emergency Contact Name</label>
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="relative z-0 w-full mb-5 group">
                <input type="text" name="emergency_contact_number" id="emergency_contact_number" required class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " value="{{ $patients->emergency_contact_number }}"/>
                <label for="emergency_contact_number" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Emergency Contact Number</label>
            </div>
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="relative z-0 w-full mb-5 group">
                <input type="text" name="emergency_relationship" id="emergency_relationship" required class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " value="{{ $patients->emergency_relationship }}"/>
                <label for="emergency_relationship" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Emergency Relationship</label>
            </div>
        </div>
        <div class="flex justify-start space-x-2">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Save Patient
            </button>
        </div>
    </form>
</div>
<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('dropdown');
        dropdown.classList.toggle('hidden');
    }

    function filterOptions() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const options = document.getElementById('optionsList').getElementsByTagName('li');

        Array.from(options).forEach(option => {
            const text = option.textContent || option.innerText;
            option.style.display = text.toLowerCase().includes(input) ? "" : "none";
        });
    }

    function selectOption(value,id) {
        const input = document.getElementById('searchInput');
        const inputVal = document.querySelector("#searchValue");
        const dropdown = document.getElementById('dropdown');

        input.value = value; // Set the selected option in the input
        inputVal.value = id; //
        dropdown.classList.add('hidden'); // Hide the dropdown
    }

    // Hide dropdown if clicked outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('dropdown');
        const input = document.getElementById('searchInput');

        if (!dropdown.contains(event.target) && event.target !== input) {
            dropdown.classList.add('hidden');
        }
    });

    //
</script>

<script>
    function toggleCourseRequirement() {
        const select = document.getElementById('edulvl_id');
        const selected = select ? select.options[select.selectedIndex] : null;
        const requiresCourse = selected && selected.dataset.requiresCourse === '1';
        const courseField = document.getElementById('course_field');
        const courseSelect = document.getElementById('course_id');
        const indicators = document.querySelectorAll('.course-required-indicator');

        if (!courseField || !courseSelect) return;

        courseField.classList.toggle('hidden', !requiresCourse);
        courseSelect.required = requiresCourse;
        indicators.forEach((indicator) => indicator.classList.toggle('hidden', !requiresCourse));

        if (!requiresCourse) {
            courseSelect.value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleCourseRequirement();
        const levelSelect = document.getElementById('edulvl_id');
        if (levelSelect) {
            levelSelect.addEventListener('change', toggleCourseRequirement);
        }
    });
</script>
@endsection
