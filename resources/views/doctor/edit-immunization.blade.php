@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Edit Immunization Record</h1>
                    <p class="text-gray-600">Update immunization details</p>
                </div>
                <a href="{{ route('doctor.health-records') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">
                    ← Back
                </a>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white shadow-sm rounded-lg">
            <form action="{{ route('doctor.immunizations.update', $record->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Vaccine Name -->
                    <div class="md:col-span-2">
                        <label for="vaccine_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Vaccine Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="vaccine_name" 
                               name="vaccine_name" 
                               value="{{ old('vaccine_name', $record->vaccine_name) }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                               placeholder="e.g., COVID-19, Influenza, Hepatitis B">
                        @error('vaccine_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Vaccine Type -->
                    <div>
                        <label for="vaccine_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Vaccine Type
                        </label>
                        <input type="text" 
                               id="vaccine_type" 
                               name="vaccine_type" 
                               value="{{ old('vaccine_type', $record->vaccine_type) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                               placeholder="e.g., mRNA, Inactivated">
                        @error('vaccine_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dosage -->
                    <div>
                        <label for="dosage" class="block text-sm font-medium text-gray-700 mb-2">
                            Dosage
                        </label>
                        <input type="text" 
                               id="dosage" 
                               name="dosage" 
                               value="{{ old('dosage', $record->dosage) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                               placeholder="e.g., 0.5 mL, 1st dose">
                        @error('dosage')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Site of Administration -->
                    <div>
                        <label for="site_of_administration" class="block text-sm font-medium text-gray-700 mb-2">
                            Site of Administration
                        </label>
                        <input type="text" 
                               id="site_of_administration" 
                               name="site_of_administration" 
                               value="{{ old('site_of_administration', $record->site_of_administration) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                               placeholder="e.g., Left deltoid, Right arm">
                        @error('site_of_administration')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Administered By -->
                    <div>
                        <label for="administered_by" class="block text-sm font-medium text-gray-700 mb-2">
                            Administered By
                        </label>
                        <input type="text" 
                               id="administered_by" 
                               name="administered_by" 
                               value="{{ old('administered_by', $record->administered_by) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                               placeholder="e.g., Dr. Smith, Nurse Johnson">
                        @error('administered_by')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Expiration Date -->
                    <div>
                        <label for="expiration_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Expiration Date
                        </label>
                        <input type="date" 
                               id="expiration_date" 
                               name="expiration_date" 
                               value="{{ old('expiration_date', $record->expiration_date ? \Carbon\Carbon::parse($record->expiration_date)->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                        @error('expiration_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea id="notes" 
                                  name="notes" 
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                  placeholder="Additional notes or observations...">{{ old('notes', $record->notes) }}</textarea>
                        @error('notes')
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
                            class="bg-yellow-600 text-white px-6 py-2 rounded-md hover:bg-yellow-700 transition">
                        Update Immunization
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
