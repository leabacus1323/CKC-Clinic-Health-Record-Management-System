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
<h2 class="text-xl font-semibold text-white">Update Appointment</h2>
<form class="max-w mx-auto" method="POST"  action="{{ route('admin.appointments.update', $appointment->id) }}">
    @csrf
    @method('PUT')
    <div class="grid md:grid-cols-2 md:gap-6">
        <div class="relative z-0 w-full mb-5 group">
            <select name="patient_id" id="patient"
                class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer cursor-pointer"
                required onchange="showSelected('patient', 'patient_selected')">
                <option value="" disabled>Select Patient</option>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}" {{ $appointment->patient_id == $patient->id ? 'selected' : '' }}>
                        {{ $patient->name }} ({{ $patient->patient_type }})
                    </option>
                @endforeach
            </select>
            <label for="patient" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Patient</label>
            <div id="patient_selected" class="mt-2 text-sm text-gray-700 dark:text-gray-300 hidden"></div>
        </div>
    </div>
    <div class="grid md:grid-cols-2 md:gap-6">
        <div class="relative z-0 w-full mb-5 group">
            <select name="doc_id" id="doctor"
                class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer cursor-pointer"
                required onchange="showSelected('doctor', 'doctor_selected'); checkAvailabilityOnChange();">
                <option value="" disabled>Select Doctor</option>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}" {{ (old('doc_id', $appointment->doc_id) == $doctor->id || $selectedDoctorId == $doctor->id) ? 'selected' : '' }}>
                        {{ $doctor->name }} ({{ $doctor->specialization }})
                    </option>
                @endforeach
            </select>
            <label for="doctor" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Doctor</label>
            <div id="doctor_selected" class="mt-2 text-sm text-gray-700 dark:text-gray-300 hidden"></div>
        </div>
    </div>
    <div class="grid md:grid-cols-2 md:gap-6">
        <div class="relative z-0 w-full mb-5 group">
            <input type="date" name="date" id="date"
                class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                placeholder=" "
                value="{{ old('date', $selectedDate ?? $appointment->date) }}"
                onchange="checkAvailability()"
                required />
            <label for="date" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">Date</label>
        </div>
    </div>

    <!-- Time Selection -->
    <div class="mb-5">
        <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">
            Preferred Time <span class="text-red-500">*</span>
        </div>

        @if($selectedDate && $selectedDoctorId)
            <!-- Time Slot Table -->
            <div class="border border-gray-300 rounded-md overflow-hidden bg-white dark:bg-gray-800">
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-2 border-b border-gray-300 dark:border-gray-600">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Available Time Slots for {{ date('F j, Y', strtotime($selectedDate)) }}</h4>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-0">
                    @foreach($timeSlots as $time => $label)
                        <div class="border-r border-b border-gray-200 dark:border-gray-600 last:border-r-0">
                            <label class="block p-3 cursor-pointer transition-colors time-slot-available {{ isset($timeSlotAvailability[$time]) && $timeSlotAvailability[$time] ? 'hover:bg-green-50 dark:hover:bg-green-900' : 'bg-red-50 dark:bg-red-900 cursor-not-allowed time-slot-booked' }}">
                                <input type="radio" name="time" value="{{ $time }}"
                                       {{ (old('time', $appointment->time) == $time) ? 'checked' : '' }}
                                       {{ isset($timeSlotAvailability[$time]) && !$timeSlotAvailability[$time] ? 'disabled' : '' }}
                                       class="sr-only"
                                       onchange="setSelectedTime('{{ $time }}')">
                                <div class="text-center">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $label }}</div>
                                    <div class="text-xs mt-1">
                                        @if(isset($timeSlotAvailability[$time]))
                                            @if($timeSlotAvailability[$time])
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Available
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Booked
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <input type="hidden" name="selected_time" id="selected_time" required>
            <input type="hidden" name="final_time" id="final_time" required>
        @else
            <!-- Instructions -->
            <div class="border border-gray-300 rounded-md p-4 bg-gray-50 dark:bg-gray-700">
                <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                    Please select a doctor and date to view available time slots.
                </p>
            </div>
        @endif
    </div>
    <div class="grid md:grid-cols-2 md:gap-6">
        <div class="relative z-0 w-full mb-5 group">
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                <option value="Pending" {{ $appointment->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Confirmed" {{ $appointment->status == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="Cancelled" {{ $appointment->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
    </div>
    <div class="flex justify-start space-x-2">
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="return validateForm()">
            Save Appointment
        </button>
    </div>
</form>

<style>
.time-slot-available {
    transition: all 0.3s ease;
}

.time-slot-available:hover {
    transform: scale(1.02);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.time-slot-selected {
    background-color: #dbeafe !important;
    border: 2px solid #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.time-slot-booked {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>

<script>
function showSelected(selectId, displayId) {
    const select = document.getElementById(selectId);
    const display = document.getElementById(displayId);

    if (select.value) {
        const selectedOption = select.options[select.selectedIndex];
        display.textContent = 'Selected: ' + selectedOption.text;
        display.classList.remove('hidden');
    } else {
        display.classList.add('hidden');
    }
}

function checkAvailability() {
    const doctorId = document.getElementById('doctor').value;
    const date = document.getElementById('date').value;

    if (doctorId && date) {
        // Check if we already have the current values in URL to avoid unnecessary refresh
        const currentUrl = new URL(window.location.href);
        const currentDoctorId = currentUrl.searchParams.get('doc_id');
        const currentDate = currentUrl.searchParams.get('date');

        if (currentDoctorId !== doctorId || currentDate !== date) {
            // Show loading indicator
            document.body.style.cursor = 'wait';

            // Redirect to same page with parameters to check availability
            const url = new URL(window.location.href);
            url.searchParams.set('doc_id', doctorId);
            url.searchParams.set('date', date);
            window.location.href = url.toString();
        }
    }
}

function checkAvailabilityOnChange() {
    const date = document.getElementById('date').value;
    if (date) {
        // Small delay to ensure the value is set properly
        setTimeout(() => {
            checkAvailability();
        }, 100);
    }
}

function setSelectedTime(time) {
    document.getElementById('selected_time').value = time;
    document.getElementById('final_time').value = time;

    // Update visual selection
    document.querySelectorAll('input[name="time"]').forEach((input) => {
        const label = input.closest('label');
        if (input.value === time) {
            label.classList.add('time-slot-selected');
            label.classList.remove('hover:bg-green-50', 'dark:hover:bg-green-900');
        } else {
            label.classList.remove('time-slot-selected');
            if (!input.disabled) {
                label.classList.add('hover:bg-green-50', 'dark:hover:bg-green-900');
            }
        }
    });
}

function validateForm() {
    const patientId = document.querySelector('select[name="patient_id"]').value;
    const doctorId = document.getElementById('doctor').value;
    const date = document.getElementById('date').value;
    const time = document.getElementById('final_time') ? document.getElementById('final_time').value : document.querySelector('input[name="time"]:checked')?.value;

    if (!patientId) {
        alert('Please select a patient.');
        return false;
    }

    if (!doctorId) {
        alert('Please select a doctor.');
        return false;
    }

    if (!date) {
        alert('Please select a date.');
        return false;
    }

    if (!time) {
        alert('Please select a time slot.');
        return false;
    }

    return true;
}

// Show initially selected values on page load
document.addEventListener('DOMContentLoaded', function() {
    showSelected('patient', 'patient_selected');
    showSelected('doctor', 'doctor_selected');

    // Auto-select time if coming back from server or editing existing appointment
    @if(old('time') || isset($appointment))
        const selectedTime = '{{ old('time', $appointment->time ?? '') }}';
        if (selectedTime) {
            setSelectedTime(selectedTime);
        }
    @endif
});
</script>

@endsection
