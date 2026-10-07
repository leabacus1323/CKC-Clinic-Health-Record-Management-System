@extends('admin.layout.navigation')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Edit Physical Examination</h1>
                    <p class="text-gray-600">Update physical examination details</p>
                </div>
                <a href="{{ route('admin.physical.index') }}"
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">
                    ← Back
                </a>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white shadow-sm rounded-lg">
            <form action="{{ route('admin.physical.update', $exam->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <!-- Patient Info (Read-only) -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold mb-2">Patient Information</h3>
                    <p><strong>Patient:</strong> {{ $exam->patient && $exam->patient->user ? $exam->patient->user->name : 'N/A' }}</p>
                    <p><strong>Doctor:</strong> {{ $exam->doctor && $exam->doctor->user ? $exam->doctor->user->name : 'N/A' }}</p>
                </div>

                <!-- Vital Signs -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Vital Signs</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="height" class="block text-sm font-medium text-gray-700 mb-2">Height (cm)</label>
                            <input type="text"
                                   id="height"
                                   name="height"
                                   value="{{ old('height', $exam->height) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="e.g., 170">
                        </div>
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Weight (kg)</label>
                            <input type="text"
                                   id="weight"
                                   name="weight"
                                   value="{{ old('weight', $exam->weight) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="e.g., 65">
                        </div>
                        <div>
                            <label for="bp" class="block text-sm font-medium text-gray-700 mb-2">Blood Pressure</label>
                            <input type="text"
                                   id="bp"
                                   name="bp"
                                   value="{{ old('bp', $exam->bp) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="e.g., 120/80">
                        </div>
                    </div>
                </div>

                <!-- Physical Examination Details -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Physical Examination Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="heart" class="block text-sm font-medium text-gray-700 mb-2">Heart</label>
                            <input type="text"
                                   id="heart"
                                   name="heart"
                                   value="{{ old('heart', $exam->heart) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="Heart examination findings">
                        </div>
                        <div>
                            <label for="lungs" class="block text-sm font-medium text-gray-700 mb-2">Lungs</label>
                            <input type="text"
                                   id="lungs"
                                   name="lungs"
                                   value="{{ old('lungs', $exam->lungs) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="Lungs examination findings">
                        </div>
                        <div>
                            <label for="eyes" class="block text-sm font-medium text-gray-700 mb-2">Eyes</label>
                            <input type="text"
                                   id="eyes"
                                   name="eyes"
                                   value="{{ old('eyes', $exam->eyes) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="Eyes examination findings">
                        </div>
                        <div>
                            <label for="ears" class="block text-sm font-medium text-gray-700 mb-2">Ears</label>
                            <input type="text"
                                   id="ears"
                                   name="ears"
                                   value="{{ old('ears', $exam->ears) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="Ears examination findings">
                        </div>
                        <div>
                            <label for="nose" class="block text-sm font-medium text-gray-700 mb-2">Nose</label>
                            <input type="text"
                                   id="nose"
                                   name="nose"
                                   value="{{ old('nose', $exam->nose) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="Nose examination findings">
                        </div>
                        <div>
                            <label for="throat" class="block text-sm font-medium text-gray-700 mb-2">Throat</label>
                            <input type="text"
                                   id="throat"
                                   name="throat"
                                   value="{{ old('throat', $exam->throat) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="Throat examination findings">
                        </div>
                        <div class="md:col-span-2">
                            <label for="skin" class="block text-sm font-medium text-gray-700 mb-2">Skin</label>
                            <input type="text"
                                   id="skin"
                                   name="skin"
                                   value="{{ old('skin', $exam->skin) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="Skin examination findings">
                        </div>
                    </div>
                </div>

                <!-- Remarks -->
                <div class="mb-6">
                    <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
                    <textarea id="remarks"
                              name="remarks"
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                              placeholder="Additional remarks or observations...">{{ old('remarks', $exam->remarks) }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('admin.physical.index') }}"
                       class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-purple-600 text-white px-6 py-2 rounded-md hover:bg-purple-700 transition">
                        Update Physical Exam
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
