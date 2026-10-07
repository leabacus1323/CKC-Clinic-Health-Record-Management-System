@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('doctor.medications') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Medications
            </a>
            <h1 class="text-2xl font-semibold text-gray-800">Add New Medicine</h1>
            <p class="text-gray-600 mt-1">Add a new medicine to the inventory</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white shadow-sm rounded-lg p-6">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('doctor.medicines.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Medicine Name *</label>
                        <input type="text" name="name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                               value="{{ old('name') }}">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Generic Name</label>
                        <input type="text" name="generic_name"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="{{ old('generic_name') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Manufacturer</label>
                        <input type="text" name="manufacturer"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="{{ old('manufacturer') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                        <select name="medicine_type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Type</option>
                            <option value="Tablet" {{ old('medicine_type') == 'Tablet' ? 'selected' : '' }}>Tablet</option>
                            <option value="Capsule" {{ old('medicine_type') == 'Capsule' ? 'selected' : '' }}>Capsule</option>
                            <option value="Syrup" {{ old('medicine_type') == 'Syrup' ? 'selected' : '' }}>Syrup</option>
                            <option value="Injection" {{ old('medicine_type') == 'Injection' ? 'selected' : '' }}>Injection</option>
                            <option value="Ointment" {{ old('medicine_type') == 'Ointment' ? 'selected' : '' }}>Ointment</option>
                            <option value="Drops" {{ old('medicine_type') == 'Drops' ? 'selected' : '' }}>Drops</option>
                            <option value="Other" {{ old('medicine_type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                        <input type="number" name="quantity" required min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('quantity') border-red-500 @enderror"
                               value="{{ old('quantity', 0) }}">
                        @error('quantity')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                        <input type="text" name="unit" placeholder="e.g., pieces, bottles"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="{{ old('unit', 'pieces') }}">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expiration Date</label>
                        <input type="date" name="expiration_date"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="{{ old('expiration_date') }}">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Enter any additional details...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                    <a href="{{ route('doctor.medications') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Add Medicine
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
