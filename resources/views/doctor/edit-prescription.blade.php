@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Edit Prescription</h1>
                    <p class="text-gray-600">Update prescription details</p>
                </div>
                <a href="{{ route('doctor.health-records') }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">
                    ← Back
                </a>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white shadow-sm rounded-lg">
            <form action="{{ route('doctor.prescriptions.update', $prescription->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Medicine -->
                    <div class="md:col-span-2">
                        <label for="medicine_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Medicine <span class="text-red-500">*</span>
                        </label>
                        <select id="medicine_id"
                                name="medicine_id"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="">Select medicine...</option>
                            @foreach($medicines as $medicine)
                                <option value="{{ $medicine->id }}"
                                        {{ old('medicine_id', $prescription->medicine_id) == $medicine->id ? 'selected' : '' }}>
                                    {{ $medicine->name }}
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
                            Dosage <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="dosage"
                               name="dosage"
                               value="{{ old('dosage', $prescription->dosage) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="e.g., 500mg, 2 tablets">
                        @error('dosage')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Frequency -->
                    <div>
                        <label for="frequency" class="block text-sm font-medium text-gray-700 mb-2">
                            Frequency <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="frequency"
                               name="frequency"
                               value="{{ old('frequency', $prescription->frequency) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="e.g., 3 times daily, Every 8 hours">
                        @error('frequency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Duration -->
                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                            Duration
                        </label>
                        <input type="text"
                               id="duration"
                               name="duration"
                               value="{{ old('duration', $prescription->duration) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="e.g., 7 days, 2 weeks">
                        @error('duration')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date Prescribed -->
                    <div>
                        <label for="date_prescribed" class="block text-sm font-medium text-gray-700 mb-2">
                            Date Prescribed
                        </label>
                        <input type="date"
                               id="date_prescribed"
                               name="date_prescribed"
                               value="{{ old('date_prescribed', $prescription->date_prescribed ? \Carbon\Carbon::parse($prescription->date_prescribed)->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        @error('date_prescribed')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Instructions -->
                    <div class="md:col-span-2">
                        <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">
                            Instructions
                        </label>
                        <textarea id="instructions"
                                  name="instructions"
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                  placeholder="Special instructions for the patient (e.g., Take with food, Avoid alcohol...)">{{ old('instructions', $prescription->instructions) }}</textarea>
                        @error('instructions')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('doctor.health-records') }}"
                       class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700 transition">
                        Update Prescription
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
