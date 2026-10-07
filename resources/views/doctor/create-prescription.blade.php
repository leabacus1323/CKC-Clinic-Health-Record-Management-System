@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Create New Prescription</h1>
                    <p class="text-gray-600">Add a new prescription for a patient</p>
                </div>
                <a href="{{ route('doctor.prescriptions') }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">
                    ← Back
                </a>
            </div>
        </div>

        <!-- Create Form -->
        <div class="bg-white shadow-sm rounded-lg">
            <form action="{{ route('doctor.prescription.store') }}" method="POST" class="p-6">
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
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" {{ (isset($patient) && $patient->id == $p->id) ? 'selected' : '' }}>
                                    {{ $p->user->name }}
                                    @if($p->student_id)
                                        ({{ $p->student_id }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Medicine Selection -->
                    <div>
                        <label for="medicine_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Select Medicine *
                        </label>
                        <select id="medicine_id"
                                name="medicine_id"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Select a Medicine --</option>
                            @foreach($medicines as $medicine)
                                <option value="{{ $medicine->id }}">
                                    {{ $medicine->name }}
                                    @if($medicine->generic_name)
                                        ({{ $medicine->generic_name }})
                                    @endif
                                    - {{ $medicine->quantity }} available
                                </option>
                            @endforeach
                        </select>
                        @error('medicine_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dosage -->
                    <div>
                        <label for="dosage" class="block text-sm font-medium text-gray-700 mb-2">
                            Dosage *
                        </label>
                        <input type="text"
                               id="dosage"
                               name="dosage"
                               required
                               value="{{ old('dosage') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g., 500mg, 2 tablets">
                        @error('dosage')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Frequency -->
                    <div>
                        <label for="frequency" class="block text-sm font-medium text-gray-700 mb-2">
                            Frequency *
                        </label>
                        <input type="text"
                               id="frequency"
                               name="frequency"
                               required
                               value="{{ old('frequency') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g., Once daily, Twice daily, Every 8 hours">
                        @error('frequency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Duration -->
                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                            Duration *
                        </label>
                        <input type="text"
                               id="duration"
                               name="duration"
                               required
                               value="{{ old('duration') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g., 7 days, 2 weeks, 1 month">
                        @error('duration')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Instructions -->
                    <div>
                        <label for="instruction" class="block text-sm font-medium text-gray-700 mb-2">
                            Instructions
                        </label>
                        <textarea id="instruction"
                                  name="instruction"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="e.g., Take with food, Take before bedtime">{{ old('instruction') }}</textarea>
                        @error('instruction')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date Prescribed -->
                    <div>
                        <label for="date_prescribed" class="block text-sm font-medium text-gray-700 mb-2">
                            Date Prescribed *
                        </label>
                        <input type="date"
                               id="date_prescribed"
                               name="date_prescribed"
                               required
                               value="{{ old('date_prescribed', date('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('date_prescribed')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status *
                        </label>
                        <select id="status"
                                name="status"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Completed" {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Discontinued" {{ old('status') == 'Discontinued' ? 'selected' : '' }}>Discontinued</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t">
                    <a href="{{ route('doctor.prescriptions') }}"
                       class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        Create Prescription
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
