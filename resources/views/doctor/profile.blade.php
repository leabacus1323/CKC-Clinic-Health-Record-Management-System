@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Doctor Profile</h1>
                <p class="text-gray-600">Your professional information</p>
            </div>
            <a href="{{ route('doctor.profile.edit') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                Edit Profile
            </a>
        </div>

        <div class="p-6 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- User Information -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Full Name</label>
                        <p class="text-gray-800">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Email</label>
                        <p class="text-gray-800">{{ $user->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Professional Information -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Professional Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Specialization</label>
                        <p class="text-gray-800">{{ $doctor->specialization }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600">Address</label>
                        <p class="text-gray-800">{{ $doctor->address }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Availability</label>
                        <p class="text-gray-800">
                            @if($doctor->is_available)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Available
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Not Available
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
