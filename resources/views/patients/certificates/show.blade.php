@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Back Link -->
        <a href="{{ route('patients.certificates.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-6">
            <span class="mr-1">←</span> Back to Certificates
        </a>

        <!-- Certificate Details Card -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-800">{{ $certificate->certificateType->name }}</h1>
                        <p class="text-sm text-gray-500 mt-1">Request ID: #{{ str_pad($certificate->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    @include('patients.certificates.partials.status-badge', ['status' => $certificate->status])
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- Status Timeline -->
                <div class="mb-8">
                    <h3 class="text-sm font-medium text-gray-700 mb-4">Request Status</h3>
                    <div class="flex items-center justify-between">
                        @php
                            $statuses = ['pending', 'approved', 'issued'];
                            $currentIndex = array_search($certificate->status, $statuses);
                        @endphp
                        
                        <div class="flex items-center space-x-2">
                            @foreach($statuses as $index => $status)
                                <div class="flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center 
                                        @if($index <= $currentIndex) 
                                            bg-blue-600 text-white 
                                        @elseif($status === 'rejected') 
                                            bg-red-600 text-white
                                        @else 
                                            bg-gray-200 text-gray-500 
                                        @endif">
                                        @if($index === 0) ⏳ @elseif($index === 1) ✓ @elseif($index === 2) 📄 @endif
                                    </div>
                                    <span class="text-xs mt-1 capitalize">{{ $status }}</span>
                                </div>
                                @if($index < count($statuses) - 1)
                                    <div class="w-12 h-0.5 
                                        @if($index < $currentIndex) bg-blue-600 @else bg-gray-200 @endif"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Requested On</h4>
                        <p class="text-gray-900">{{ $certificate->created_at->format('F d, Y h:i A') }}</p>
                    </div>
                    @if($certificate->issued_date)
                    <div>
                        <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Issued On</h4>
                        <p class="text-gray-900">{{ $certificate->issued_date->format('F d, Y') }}</p>
                    </div>
                    @endif
                    @if($certificate->doctor)
                    <div>
                        <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Processed By</h4>
                        <p class="text-gray-900">Dr. {{ $certificate->doctor->user->firstname ?? '' }} {{ $certificate->doctor->user->lastname ?? '' }}</p>
                    </div>
                    @endif
                    @if($certificate->appointment)
                    <div>
                        <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Related Appointment</h4>
                        <p class="text-gray-900">{{ \Carbon\Carbon::parse($certificate->appointment->date)->format('F d, Y') }}</p>
                    </div>
                    @endif
                </div>

                <!-- Reason -->
                <div class="mb-6">
                    <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Purpose / Reason</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-800">{{ $certificate->reason }}</p>
                    </div>
                </div>

                <!-- Doctor Notes -->
                @if($certificate->doctor_notes)
                <div class="mb-6">
                    <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Doctor's Notes</h4>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-gray-800">{{ $certificate->doctor_notes }}</p>
                    </div>
                </div>
                @endif

                <!-- Certificate Description -->
                @if($certificate->certificateType->description)
                <div class="mb-6">
                    <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">About This Certificate</h4>
                    <p class="text-sm text-gray-600">{{ $certificate->certificateType->description }}</p>
                </div>
                @endif

                <!-- Action for Issued Certificates -->
                @if($certificate->status === 'issued')
                <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium text-green-800">Your certificate is ready for pickup!</span>
                    </div>
                    <p class="mt-2 text-sm text-green-700">
                        Please visit the clinic with a valid ID to collect your certificate during clinic hours.
                    </p>
                </div>
                @endif

                <!-- Rejection Reason -->
                @if($certificate->status === 'rejected' && $certificate->doctor_notes)
                <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium text-red-800">Request Rejected</span>
                    </div>
                    <p class="mt-2 text-sm text-red-700">{{ $certificate->doctor_notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection