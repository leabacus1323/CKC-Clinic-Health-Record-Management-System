@extends('admin.layout.navigation')

@section('content')
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
<h2 class="text-xl font-semibold text-white">New Doctors</h2>
<form class="max-w mx-auto" action="{{ route('admin.doctors.store') }}" method="POST">
    @csrf
    <div class="grid md:grid-cols-2 md:gap-6">
        <div class="relative z-0 w-full mb-6 group">
            <input type="number" id="searchValue" name="user_id" hidden>
            <!-- Search Input -->
            <input
                type="text"
                placeholder=""
                id="searchInput"
                class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                oninput="filterOptions()"
                onclick="toggleDropdown()"
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
            <label for="searchInput" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">User Account</label>
        </div>
    </div>
    <div class="grid md:grid-cols-2 md:gap-6">
        <div class="relative z-0 w-full mb-5 group">
            <label for="specialization" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Specialization</label>
            <select name="specialization" id="specialization" onchange="toggleCustomSpecialization()" class="block py-2.5 px-3 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" required>
                <option value="" disabled {{ old('specialization') ? '' : 'selected' }}>Select specialization</option>
                <option value="Dentist" {{ old('specialization') == 'Dentist' ? 'selected' : '' }}>Dentist</option>
                <option value="Physician" {{ old('specialization') == 'Physician' ? 'selected' : '' }}>Physician</option>
                <option value="Other" {{ old('specialization') && !in_array(old('specialization'), ['Dentist', 'Physician']) ? 'selected' : '' }}>Add another</option>
            </select>
            @error('specialization')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div class="grid md:grid-cols-2 md:gap-6" id="customSpecializationGroup" style="display: {{ old('specialization') && !in_array(old('specialization'), ['Dentist', 'Physician']) ? 'block' : 'none' }};">
        <div class="relative z-0 w-full mb-5 group">
            <label for="specialization_custom" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Enter specialization</label>
            <input type="text" name="specialization_custom" id="specialization_custom" value="{{ old('specialization') && !in_array(old('specialization'), ['Dentist', 'Physician']) ? old('specialization') : '' }}" class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
            @error('specialization_custom')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div class="grid md:grid-cols-2 md:gap-6">
        <div class="relative z-0 w-full mb-5 group">
            <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Clinic Address</label>
            <textarea name="address" id="address" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder=" "></textarea>
        </div>
    </div>
    <div class="flex justify-start space-x-2">
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Save Doctors
        </button>
    </div>
</form>
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

    function toggleCustomSpecialization() {
        const specializationSelect = document.getElementById('specialization');
        const customGroup = document.getElementById('customSpecializationGroup');
        const customInput = document.getElementById('specialization_custom');

        if (specializationSelect.value === 'Other') {
            customGroup.style.display = 'block';
            customInput.setAttribute('required', 'required');
        } else {
            customGroup.style.display = 'none';
            customInput.removeAttribute('required');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleCustomSpecialization();
    });

    // Hide dropdown if clicked outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('dropdown');
        const input = document.getElementById('searchInput');

        if (!dropdown.contains(event.target) && event.target !== input) {
            dropdown.classList.add('hidden');
        }
    });
</script>
@endsection
