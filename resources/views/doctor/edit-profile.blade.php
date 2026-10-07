@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-800">Edit Profile</h1>
            <p class="text-gray-600">Update your professional and personal information</p>
        </div>

        <div class="p-6">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('doctor.profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Personal Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Personal Information</h3>

                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" required
                                   value="{{ old('name', $user->name) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" required
                                   value="{{ old('email', $user->email) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Professional Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">Professional Information</h3>

                    <div class="space-y-4">
                        <div>
                            @php
                                $currentSpecialization = old('specialization', $doctor->specialization);
                                $isCustomSpecialization = $currentSpecialization && !in_array($currentSpecialization, ['Dentist', 'Physician']);
                            @endphp
                            <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">
                                Specialization <span class="text-red-500">*</span>
                            </label>
                            <select name="specialization" id="specialization" onchange="toggleCustomSpecialization()" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" disabled {{ $currentSpecialization ? '' : 'selected' }}>Select specialization</option>
                                <option value="Dentist" {{ $currentSpecialization === 'Dentist' ? 'selected' : '' }}>Dentist</option>
                                <option value="Physician" {{ $currentSpecialization === 'Physician' ? 'selected' : '' }}>Physician</option>
                                <option value="Other" {{ $isCustomSpecialization ? 'selected' : '' }}>Add another</option>
                            </select>
                        </div>

                        <div id="customSpecializationGroup" style="display: {{ $isCustomSpecialization ? 'block' : 'none' }};">
                            <label for="specialization_custom" class="block text-sm font-medium text-gray-700 mb-2">
                                Enter specialization <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="specialization_custom" id="specialization_custom"
                                   value="{{ old('specialization_custom', $isCustomSpecialization ? $currentSpecialization : '') }}"
                                   placeholder="e.g. General Dentistry"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('specialization_custom')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Address <span class="text-red-500">*</span>
                            </label>
                            <textarea name="address" id="address" rows="3" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address', $doctor->address) }}</textarea>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_available" id="is_available" value="1"
                                   {{ old('is_available', $doctor->is_available) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="is_available" class="ml-2 text-sm text-gray-700">Available for appointments</label>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between pt-6">
                    <a href="{{ route('doctor.profile.show') }}"
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        Update Profile
                    </button>
                </div>
            </form>

            <script>
                function toggleCustomSpecialization() {
                    const specializationSelect = document.getElementById('specialization');
                    const customGroup = document.getElementById('customSpecializationGroup');
                    const customInput = document.getElementById('specialization_custom');

                    if (specializationSelect.value === 'Other') {
                        customGroup.style.display = 'block';
                        customInput.required = true;
                    } else {
                        customGroup.style.display = 'none';
                        customInput.required = false;
                    }
                }

                document.addEventListener('DOMContentLoaded', function () {
                    toggleCustomSpecialization();
                });
            </script>

            <!-- Password Change Section -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Change Password</h3>
                <form action="{{ route('doctor.profile.password.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Current Password
                            </label>
                            <input type="password" name="current_password" id="current_password"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                                New Password
                            </label>
                            <input type="password" name="new_password" id="new_password"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm New Password
                            </label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <button type="submit"
                            class="px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-800 transition">
                        Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
