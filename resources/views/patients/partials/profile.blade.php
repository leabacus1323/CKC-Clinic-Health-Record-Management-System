{{-- Patient Profile Partial for Shell --}}
<div class="max-w-4xl mx-auto bg-white shadow-sm rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <h1 class="text-2xl font-semibold text-gray-800">My Profile</h1>
        <p class="text-gray-600">View and manage your personal information</p>
    </div>

    <div class="p-6">
        @if(!$patient)
            <!-- No Patient Profile -->
            <div class="text-center py-8">
                <div class="bg-yellow-50 p-6 rounded-lg">
                    <h3 class="text-lg font-medium text-yellow-800 mb-2">Complete Your Patient Profile</h3>
                    <p class="text-yellow-600 mb-4">You need to complete your patient profile to access all features.</p>
                    <a href="{{ route('patients.profile.create') }}"
                       class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 transition">
                        Create Profile
                    </a>
                </div>
            </div>
        @else
            <!-- Profile Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Personal Information -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">Personal Information</h2>
                        <a href="{{ route('patients.profile.edit') }}"
                           class="text-blue-600 hover:text-blue-800 text-sm">
                            Edit →
                        </a>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Full Name</label>
                            <p class="text-gray-800">{{ $user->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Email</label>
                            <p class="text-gray-800">{{ $user->email }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Patient Type</label>
                            <p class="text-gray-800">@if($patient->patient_type == 1) Student @elseif($patient->patient_type == 3) Faculty @elseif($patient->patient_type == 2) Staff @else {{ $patient->patient_type }} @endif</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Address</label>
                            <p class="text-gray-800">{{ $patient->address }}</p>
                        </div>
                    </div>
                </div>

                <!-- Medical Information -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Medical Information</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Medical Conditions</label>
                            <p class="text-gray-800">{{ $patient->medical_condition ?: 'None reported' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Medical Illness</label>
                            <p class="text-gray-800">{{ $patient->medical_illness ?: 'None reported' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Previous Operations</label>
                            <p class="text-gray-800">{{ $patient->operations ?: 'None reported' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Allergies</label>
                            <p class="text-gray-800">{{ $patient->allergies ?: 'None reported' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Blood Type</label>
                            <p class="text-gray-800">{{ $patient->bloodtype ?: 'Not specified' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="bg-gray-50 p-6 rounded-lg lg:col-span-2">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Emergency Contact</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Contact Name</label>
                            <p class="text-gray-800">{{ $patient->emergency_contact_name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Phone Number</label>
                            <p class="text-gray-800">{{ $patient->emergency_contact_number }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Relationship</label>
                            <p class="text-gray-800">{{ $patient->emergency_relationship }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-center space-x-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('patients.profile.edit') }}"
                   class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    Edit Profile
                </a>
            </div>
        @endif
    </div>
</div>
