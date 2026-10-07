@extends('layouts.app')

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
                <a href="{{ route('doctor.health-records') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition">
                    ← Back
                </a>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white shadow-sm rounded-lg">
            <form action="{{ route('doctor.physical-exams.update', $exam->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <!-- Vital Signs -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Vital Signs</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="height" class="block text-sm font-medium text-gray-700 mb-2">
                                Height (cm)
                            </label>
                            <input type="number" 
                                   id="height" 
                                   name="height" 
                                   step="0.1"
                                   value="{{ old('height', $exam->height) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="e.g., 170">
                            @error('height')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                                Weight (kg)
                            </label>
                            <input type="number" 
                                   id="weight" 
                                   name="weight" 
                                   step="0.1"
                                   value="{{ old('weight', $exam->weight) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="e.g., 65">
                            @error('weight')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="bp" class="block text-sm font-medium text-gray-700 mb-2">
                                Blood Pressure
                            </label>
                            <input type="text" 
                                   id="bp" 
                                   name="bp" 
                                   value="{{ old('bp', $exam->bp) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="e.g., 120/80">
                            @error('bp')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Physical Examination Details -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Physical Examination</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="heart" class="block text-sm font-medium text-gray-700 mb-2">
                                Heart
                            </label>
                            <input type="text" 
                                   id="heart" 
                                   name="heart" 
                                   value="{{ old('heart', $exam->heart) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="e.g., Normal, regular rhythm">
                            @error('heart')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="lungs" class="block text-sm font-medium text-gray-700 mb-2">
                                Lungs
                            </label>
                            <input type="text" 
                                   id="lungs" 
                                   name="lungs" 
                                   value="{{ old('lungs', $exam->lungs) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="e.g., Clear, no wheezing">
                            @error('lungs')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="eyes" class="block text-sm font-medium text-gray-700 mb-2">
                                Eyes
                            </label>
                            <input type="text" 
                                   id="eyes" 
                                   name="eyes" 
                                   value="{{ old('eyes', $exam->eyes) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="e.g., Normal vision">
                            @error('eyes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="ears" class="block text-sm font-medium text-gray-700 mb-2">
                                Ears
                            </label>
                            <input type="text" 
                                   id="ears" 
                                   name="ears" 
                                   value="{{ old('ears', $exam->ears) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="e.g., Clear">
                            @error('ears')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="nose" class="block text-sm font-medium text-gray-700 mb-2">
                                Nose
                            </label>
                            <input type="text" 
                                   id="nose" 
                                   name="nose" 
                                   value="{{ old('nose', $exam->nose) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="e.g., No obstruction">
                            @error('nose')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="throat" class="block text-sm font-medium text-gray-700 mb-2">
                                Throat
                            </label>
                            <input type="text" 
                                   id="throat" 
                                   name="throat" 
                                   value="{{ old('throat', $exam->throat) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="e.g., No inflammation">
                            @error('throat')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="skin" class="block text-sm font-medium text-gray-700 mb-2">
                                Skin
                            </label>
                            <input type="text" 
                                   id="skin" 
                                   name="skin" 
                                   value="{{ old('skin', $exam->skin) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                   placeholder="e.g., Normal, no rashes">
                            @error('skin')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Remarks -->
                <div class="mb-6">
                    <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">
                        Remarks
                    </label>
                    <textarea id="remarks" 
                              name="remarks" 
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                              placeholder="Additional remarks or observations...">{{ old('remarks', $exam->remarks) }}</textarea>
                    @error('remarks')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('doctor.health-records') }}" 
                       class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 transition">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-purple-600 text-white px-6 py-2 rounded-md hover:bg-purple-700 transition">
                        Update Examination
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
