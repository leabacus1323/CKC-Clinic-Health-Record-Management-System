@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('patients.certificates.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-4">
                <span class="mr-1">←</span> Back to Certificates
            </a>
            <h1 class="text-2xl font-semibold text-gray-800">Request Medical Certificate</h1>
            <p class="text-gray-600">Fill out the form below to request a medical certificate</p>
        </div>

        @if(!$hasAppointmentHistory)
            <!-- Blocker for patients without appointment history -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-6 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-amber-800">Appointment History Required</h3>
                        <p class="mt-2 text-sm text-amber-700">
                            You cannot request a medical certificate at this time because you don't have any completed appointments yet.
                        </p>
                        <p class="mt-2 text-sm text-amber-700">
                            Please <a href="{{ route('appointments.create') }}" class="font-medium underline hover:no-underline">book an appointment</a> and complete a visit first before requesting a certificate.
                        </p>
                        <a href="{{ route('patients.certificates.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-md hover:bg-amber-700 transition">
                            <span class="mr-1">←</span> Back to Certificates
                        </a>
                    </div>
                </div>
            </div>
        @else
        <!-- Form -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            <form action="{{ route('patients.certificates.store') }}" method="POST">
                @csrf

                <!-- Patient Info -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Patient Information</h3>
                    <p class="text-gray-900">{{ $patient->user->firstname }} {{ $patient->user->middlename ?? '' }} {{ $patient->user->lastname }}</p>
                    <p class="text-sm text-gray-500">
                        @if($patient->school_id)
                            School ID: {{ $patient->school_id }}
                        @else
                            Patient Type: {{ ucfirst($patient->patient_type) }}
                        @endif
                    </p>
                </div>

                <!-- Related Appointment (Optional) - moved to top -->
                @if($appointments->count() > 0)
                <div class="mb-6">
                    <label for="appointment_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Related Appointment <span class="text-xs text-gray-500">(Optional)</span>
                    </label>
                    <select name="appointment_id" id="appointment_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Select Related Appointment --</option>
                        @foreach($appointments as $appointment)
                            @php
                                $apptDate = \Carbon\Carbon::parse($appointment->date)->format('M d, Y');
                                $doctorName = null;
                                if ($appointment->doctor && $appointment->doctor->user) {
                                    $u = $appointment->doctor->user;
                                    $first = $u->f_name ?? $u->firstname ?? $u->name ?? null;
                                    $last = $u->l_name ?? $u->lastname ?? null;
                                    $doctorName = trim(($first ?? '') . ' ' . ($last ?? '')) ?: null;
                                }
                                $specialization = $appointment->doctor ? ($appointment->doctor->specialization ? ucfirst($appointment->doctor->specialization) : null) : null;
                                $doctorLabel = $doctorName ? 'Dr. ' . $doctorName . ($specialization ? ' (' . $specialization . ')' : '') : '';
                                $concern = $appointment->concern ?? 'General Checkup';
                            @endphp
                            <option value="{{ $appointment->id }}" data-doctor-specialization="{{ strtolower($appointment->doctor->specialization ?? '') }}" {{ old('appointment_id') == $appointment->id ? 'selected' : '' }}>
                                {{ $apptDate }} - {{ $doctorLabel }}{{ $doctorLabel ? ' - ' : '' }}{{ $concern }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Select an appointment if the certificate is related to a specific visit</p>
                </div>
                @endif

                <!-- Certificate Type -->
                <div class="mb-6">
                    <label for="certificate_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Certificate Type <span class="text-red-500">*</span>
                    </label>
                    <select name="certificate_type_id" id="certificate_type_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('certificate_type_id') border-red-500 @enderror">
                        <option value="">-- Select Certificate Type --</option>
                        @foreach($certificateTypes as $type)
                            @php $isDental = in_array($type->id, $dentalTypeIds); @endphp
                            <option value="{{ $type->id }}" data-is-dental="{{ $isDental ? '1' : '0' }}" {{ old('certificate_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('certificate_type_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    @if(old('certificate_type_id'))
                        @php $selectedType = $certificateTypes->firstWhere('id', old('certificate_type_id')) @endphp
                        @if($selectedType && $selectedType->description)
                            <p class="mt-2 text-sm text-gray-500">{{ $selectedType->description }}</p>
                        @endif
                    @endif
                </div>

                <!-- Reason/Purpose -->
                <div class="mb-6">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Purpose/Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reason" id="reason" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('reason') border-red-500 @enderror"
                              placeholder="Please explain why you need this certificate (e.g., for work, school, insurance, etc.)">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Box -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">What happens next?</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Your request will be reviewed by the clinic staff</li>
                                    <li>A doctor will verify and approve your certificate</li>
                                    <li>You'll be notified once your certificate is ready</li>
                                    <li>You can collect your certificate at the clinic</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('patients.certificates.index') }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const appointmentSelect = document.getElementById('appointment_id');
    const certSelect = document.getElementById('certificate_type_id');

    function setOptionVisibility(option, visible) {
        option.hidden = !visible;
        option.disabled = !visible;
        option.style.display = visible ? '' : 'none';
    }

    function toggleDentalOptions() {
        if (!appointmentSelect || !certSelect) return;
        const apptOption = appointmentSelect.options[appointmentSelect.selectedIndex];
        const specialization = apptOption ? apptOption.getAttribute('data-doctor-specialization') : null;

        // If the appointment's doctor is a dentist, show only dental certificate types
        if (specialization === 'dentist') {
            for (let i = 0; i < certSelect.options.length; i++) {
                const opt = certSelect.options[i];
                const isDental = opt.getAttribute('data-is-dental') === '1';
                setOptionVisibility(opt, isDental || opt.value === '');
                if (!isDental && opt.selected) {
                    certSelect.selectedIndex = 0;
                }
            }
        } else {
            // Not a dentist: hide dental options, show non-dental
            for (let i = 0; i < certSelect.options.length; i++) {
                const opt = certSelect.options[i];
                const isDental = opt.getAttribute('data-is-dental') === '1';
                setOptionVisibility(opt, !isDental || opt.value === '');
                if (isDental && opt.selected) {
                    certSelect.selectedIndex = 0;
                }
            }
        }
    }

    if (appointmentSelect) {
        appointmentSelect.addEventListener('change', toggleDentalOptions);
    }

    // Initialize on load
    toggleDentalOptions();
});
</script>
@endpush
