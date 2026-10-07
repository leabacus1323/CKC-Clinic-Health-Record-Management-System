@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Back Link -->
        <a href="{{ route('doctor.certificate-requests') }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-6">
            <span class="mr-1">←</span> Back to Certificate Requests
        </a>

        <!-- Certificate Request Details Card -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-xl font-semibold text-gray-800">{{ $request->certificateType->name }}</h1>
                        <p class="text-sm text-gray-500 mt-1">Request ID: #{{ str_pad($request->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    @php $statusClass = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'approved' => 'bg-blue-100 text-blue-800',
                        'rejected' => 'bg-red-100 text-red-800',
                        'issued' => 'bg-green-100 text-green-800'
                    ][$request->status] ?? 'bg-gray-100 text-gray-800'; @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">
                        {{ ucfirst($request->status) }}
                    </span>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- Patient Info -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Patient Information</h3>
                    <div class="grid grid-cols-2 gap-4 bg-gray-50 rounded-lg p-4">
                        <div>
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Name</h4>
                            <p class="text-gray-900">{{ $request->patient->user->firstname ?? '' }} {{ $request->patient->user->middlename ?? '' }} {{ $request->patient->user->lastname ?? '' }}</p>
                        </div>
                        @if($request->patient->school_id)
                        <div>
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">School ID</h4>
                            <p class="text-gray-900">{{ $request->patient->school_id }}</p>
                        </div>
                        @endif
                        <div>
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Course</h4>
                            <p class="text-gray-900">{{ $request->patient->course->course ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</h4>
                            <p class="text-gray-900">{{ $request->patient->user->contact ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Request Details -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Request Details</h3>
                    <div class="space-y-3">
                        <div>
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Certificate Type</h4>
                            <p class="text-gray-900 font-medium">{{ $request->certificateType->name }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $request->certificateType->description }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Requested On</h4>
                            <p class="text-gray-900">{{ $request->created_at->format('F d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose/Reason</h4>
                            <div class="bg-gray-50 rounded-lg p-4 mt-2">
                                <p class="text-gray-800">{{ $request->reason }}</p>
                            </div>
                        </div>
                        @if($request->appointment)
                        <div>
                            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Related Appointment</h4>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($request->appointment->date)->format('F d, Y') }} - {{ $request->appointment->concern ?? 'General' }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Doctor Notes (if exists) -->
                @if($request->doctor_notes)
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Doctor's Notes</h3>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-gray-800">{{ $request->doctor_notes }}</p>
                    </div>
                </div>
                @endif

                <!-- Action Forms -->
                @if($request->status === 'pending')
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>

                    <!-- Approve Form -->
                    <form action="{{ route('doctor.certificate-requests.approve', $request->id) }}" method="POST" class="mb-6">
                        @csrf
                        <div class="mb-4">
                            <label for="doctor_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Add Notes (Optional)
                            </label>
                            <textarea name="doctor_notes" id="doctor_notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Add any notes about this certificate..."></textarea>
                        </div>
                        <button type="submit" class="w-full sm:w-auto bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition">
                            ✓ Approve Request
                        </button>
                    </form>

                    <!-- Reject Form -->
                    <form action="{{ route('doctor.certificate-requests.reject', $request->id) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to reject this request?');">
                        @csrf
                        <div class="mb-4">
                            <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                                Reason for Rejection <span class="text-red-500">*</span>
                            </label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="3" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                      placeholder="Please provide a reason for rejection..."></textarea>
                        </div>
                        <button type="submit" class="w-full sm:w-auto bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700 transition">
                            ✕ Reject Request
                        </button>
                    </form>
                </div>
                @endif

                <!-- Issue Button (for approved requests) -->
                @if($request->status === 'approved')
                <div class="border-t border-gray-200 pt-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <svg class="h-5 w-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium text-blue-800">Certificate Approved</span>
                        </div>
                        <p class="text-sm text-blue-700 mb-4">
                            The certificate has been approved. Mark as issued when the patient collects it.
                        </p>
                        <form action="{{ route('doctor.certificate-requests.issue', $request->id) }}" method="POST" 
                              onsubmit="return confirm('Mark this certificate as issued?');">
                            @csrf
                            <div class="flex gap-3">
                                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                                    📄 Mark as Issued
                                </button>
                                <a href="{{ route('doctor.certificate-requests.print', $request->id) }}" target="_blank"
                                   class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition inline-flex items-center">
                                    🖨️ Print Certificate
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection