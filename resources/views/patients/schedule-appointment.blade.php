@extends('layouts.app')

@section('content')
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

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-800">Schedule Doctor Appointment</h1>
            <p class="text-gray-600">Book an appointment with one of our qualified doctors</p>
        </div>

        <div class="p-6">
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('patients.store.appointment') }}" method="POST" class="space-y-6" onsubmit="return validateForm()">
                @csrf

                <!-- Doctor Selection -->
                <div>
                    <label for="doctor_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Doctor <span class="text-red-500">*</span>
                    </label>
                    <select name="doctor_id" id="doctor_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Choose a doctor...</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ (old('doctor_id') == $doctor->id || $selectedDoctorId == $doctor->id) ? 'selected' : '' }}>
                                Dr. {{ $doctor->user->name }} - {{ $doctor->specialization }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Selection -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                        Appointment Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date" id="date" required
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                           value="{{ old('date', $selectedDate) }}"
                           onchange="checkAvailability()"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Time Selection -->
                <div>
                    <div class="block text-sm font-medium text-gray-700 mb-2">
                        Preferred Time <span class="text-red-500">*</span>
                    </div>

                    @if($selectedDate && $selectedDoctorId)
                        <!-- Time Slot Table -->
                        <div class="border border-gray-300 rounded-md overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2 border-b border-gray-300">
                                <h4 class="text-sm font-medium text-gray-700">Available Time Slots for {{ date('F j, Y', strtotime($selectedDate)) }}</h4>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-0">
                                @foreach($timeSlots as $time => $label)
                                    <div class="border-r border-b border-gray-200 last:border-r-0">
                                        <label class="block p-3 cursor-pointer transition-colors time-slot-available {{ isset($timeSlotAvailability[$time]) && $timeSlotAvailability[$time] ? 'hover:bg-green-50' : 'bg-red-50 cursor-not-allowed time-slot-booked' }}">
                                            <input type="radio" name="time" value="{{ $time }}"
                                                   {{ old('time') == $time ? 'checked' : '' }}
                                                   {{ isset($timeSlotAvailability[$time]) && !$timeSlotAvailability[$time] ? 'disabled' : '' }}
                                                   class="sr-only"
                                                   onchange="setSelectedTime('{{ $time }}')">
                                            <div class="text-center">
                                                <div class="text-sm font-medium text-gray-900">{{ $label }}</div>
                                                <div class="text-xs mt-1">
                                                    @if(isset($timeSlotAvailability[$time]))
                                                        @if($timeSlotAvailability[$time])
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                Available
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
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
                        <input type="hidden" name="time" id="final_time" required>
                    @else
                        <!-- Instructions -->
                        <div class="border border-gray-300 rounded-md p-4 bg-gray-50">
                            <p class="text-sm text-gray-600 text-center">
                                Please select a doctor and date to view available time slots.
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Reason for Visit -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for Visit (Optional)
                    </label>
                    <textarea name="reason" id="reason" rows="4" placeholder="Briefly describe your symptoms or reason for visit..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('reason') }}</textarea>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between pt-6">
                    <a href="{{ route('patients.dashboard') }}"
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        Schedule Appointment
                    </button>
                </div>
            </form>

            <!-- Available Doctors Info -->
            <div class="mt-8 bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-800 mb-3">Available Doctors</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($doctors as $doctor)
                        <div class="bg-white p-3 rounded border">
                            <h4 class="font-medium">Dr. {{ $doctor->user->name }}</h4>
                            <p class="text-sm text-gray-600">{{ $doctor->specialization }}</p>
                            @if($doctor->address)
                                <p class="text-xs text-gray-500">{{ $doctor->address }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function checkAvailability() {
    const doctorId = document.getElementById('doctor_id').value;
    const date = document.getElementById('date').value;

    if (doctorId && date) {
        // Check if we already have the current values in URL to avoid unnecessary refresh
        const currentUrl = new URL(window.location.href);
        const currentDoctorId = currentUrl.searchParams.get('doctor_id');
        const currentDate = currentUrl.searchParams.get('date');

        if (currentDoctorId !== doctorId || currentDate !== date) {
            // Show loading indicator
            document.body.style.cursor = 'wait';

            // Redirect to same page with parameters to check availability
            const url = new URL(window.location.href);
            url.searchParams.set('doctor_id', doctorId);
            url.searchParams.set('date', date);
            window.location.href = url.toString();
        }
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
            label.classList.remove('hover:bg-green-50');
        } else {
            label.classList.remove('time-slot-selected');
            if (!input.disabled) {
                label.classList.add('hover:bg-green-50');
            }
        }
    });
}

function validateForm() {
    const doctorId = document.getElementById('doctor_id').value;
    const date = document.getElementById('date').value;
    const time = document.getElementById('final_time').value;

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

// Handle doctor selection change
document.getElementById('doctor_id').addEventListener('change', function() {
    const date = document.getElementById('date').value;
    if (date) {
        // Small delay to ensure the value is set properly
        setTimeout(() => {
            checkAvailability();
        }, 100);
    }
});

// Auto-select time if coming back from server
@if(old('time'))
    setSelectedTime('{{ old('time') }}');
@endif
</script>
@endsection
