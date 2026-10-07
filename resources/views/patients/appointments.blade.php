@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">My Appointments</h1>
                <p class="text-gray-600">View and manage your medical appointments</p>
            </div>
            <a href="{{ route('patients.schedule.appointment') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                Schedule New Appointment
            </a>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if($appointments->count() > 0)
                <div class="space-y-4">
                    @foreach($appointments as $appointment)
                        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4 mb-2">
                                        <h3 class="text-lg font-medium text-gray-800">
                                            Dr. {{ $appointment->doctor->user->name ?? 'N/A' }}
                                        </h3>
                                        <span class="inline-block px-3 py-1 text-sm rounded-full
                                            @if($appointment->status === 'Pending') bg-yellow-100 text-yellow-800
                                            @elseif($appointment->status === 'Confirmed') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $appointment->status }}
                                        </span>
                                    </div>

                                    <div class="text-sm text-gray-600 space-y-1">
                                        <p><strong>Specialization:</strong> {{ $appointment->doctor->specialization ?? 'General Practice' }}</p>
                                        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->date)->format('F j, Y') }}</p>
                                        <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($appointment->time)->format('g:i A') }}</p>
                                        @if($appointment->reason)
                                            <p><strong>Reason:</strong> {{ $appointment->reason }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-right">
                                    <div class="text-sm text-gray-500 mb-2">
                                        Scheduled: {{ $appointment->created_at->format('M j, Y') }}
                                    </div>
                                    @if($appointment->status === 'Pending' && $appointment->date > now()->toDateString())
                                        <button class="text-red-600 hover:text-red-800 text-sm">
                                            Cancel
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination or load more if needed -->
                <div class="mt-6 text-center">
                    <p class="text-gray-500 text-sm">Showing {{ $appointments->count() }} appointment(s)</p>
                </div>
            @else
                <!-- No appointments -->
                <div class="text-center py-12">
                    <div class="bg-gray-50 p-8 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-800 mb-2">No Appointments Yet</h3>
                        <p class="text-gray-600 mb-4">You haven't scheduled any appointments yet.</p>
                        <a href="{{ route('patients.schedule.appointment') }}"
                           class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                            Schedule Your First Appointment
                        </a>
                    </div>
                </div>
            @endif

            <!-- Back to Dashboard -->
            <div class="mt-8 text-center border-t border-gray-200 pt-6">
                <a href="{{ route('patients.dashboard') }}"
                   class="text-blue-600 hover:text-blue-800">
                    ← Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
