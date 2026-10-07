@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Edit Health Record</h1>
                    <p class="text-gray-600">Update general health record information</p>
                </div>
                <a href="{{ route('doctor.health-records') }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">
                    ← Back
                </a>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white shadow-sm rounded-lg">
            <form action="{{ route('doctor.health-records.update', $record->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6">
                    <!-- Diagnosis -->
                    <div>
                        <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-2">
                            Diagnosis
                        </label>
                        <textarea id="diagnosis"
                                  name="diagnosis"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Enter diagnosis...">{{ old('diagnosis', $record->diagnosis) }}</textarea>
                        @error('diagnosis')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Treatment -->
                    <div>
                        <label for="treatment" class="block text-sm font-medium text-gray-700 mb-2">
                            Treatment
                        </label>
                        <textarea id="treatment"
                                  name="treatment"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Enter treatment plan...">{{ old('treatment', $record->treatment) }}</textarea>
                        @error('treatment')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Symptoms -->
                    <div>
                        <label for="symptoms" class="block text-sm font-medium text-gray-700 mb-2">
                            Symptoms
                        </label>
                        <textarea id="symptoms"
                                  name="symptoms"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Enter symptoms...">{{ old('symptoms', $record->symptoms) }}</textarea>
                        @error('symptoms')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea id="notes"
                                  name="notes"
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Additional notes...">{{ old('notes', $record->notes) }}</textarea>
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
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                        Update Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
