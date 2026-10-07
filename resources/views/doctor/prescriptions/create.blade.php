@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('doctor.prescriptions') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Prescriptions
            </a>
            <h1 class="text-2xl font-semibold text-gray-800">Prescribe New Medication</h1>
            <p class="text-gray-600 mt-1">Create a new prescription for a patient</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('doctor.prescription.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Patient *</label>
                        <select name="patient_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('patient_id') border-red-500 @enderror">
                            <option value="">Select Patient</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->user->name ?? 'N/A' }}
                                    ({{ $patient->school_id ?? 'ID: ' . $patient->id }})
                                </option>
                            @endforeach
                        </select>
                        @error('patient_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Medicine *</label>
                        <select name="medicine_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('medicine_id') border-red-500 @enderror">
                            <option value="">Select Medicine</option>
                            @foreach($medicines as $medicine)
                                <option value="{{ $medicine->id }}" {{ old('medicine_id') == $medicine->id ? 'selected' : '' }}>
                                    {{ $medicine->name }} - {{ $medicine->medicine_type ?? '' }}
                                    ({{ $medicine->quantity }} {{ $medicine->unit ?? 'available' }})
                                </option>
                            @endforeach
                        </select>
                        @error('medicine_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dosage *</label>
                        <input type="text" name="dosage" required placeholder="e.g., 500mg"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('dosage') border-red-500 @enderror"
                               value="{{ old('dosage') }}">
                        @error('dosage')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Frequency *</label>
                        <input type="text" name="frequency" required placeholder="e.g., twice daily"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('frequency') border-red-500 @enderror"
                               value="{{ old('frequency') }}">
                        @error('frequency')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duration *</label>
                        <input type="text" name="duration" required placeholder="e.g., 7 days"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('duration') border-red-500 @enderror"
                               value="{{ old('duration') }}">
                        @error('duration')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="discontinued" {{ old('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                        <textarea name="instructions" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Special instructions for the patient...">{{ old('instructions') }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                    <a href="{{ route('doctor.prescriptions') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Prescribe Medication
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
