@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <!-- Back Link -->
        <a href="{{ route('staff.certificate-requests') }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-6">
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

                <!-- Staff Actions -->
                @if($request->status === 'approved' || $request->status === 'issued')
                <div class="border-t border-gray-200 pt-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <svg class="h-5 w-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium text-blue-800">
                                {{ $request->status === 'approved' ? 'Certificate Approved' : 'Certificate Issued' }}
                            </span>
                        </div>
                        @if($request->status === 'approved')
                        <p class="text-sm text-blue-700 mb-4">
                            The certificate has been approved by a doctor. You may print it or mark it as issued when the patient collects it.
                        </p>
                        @else
                        <p class="text-sm text-blue-700 mb-4">
                            This certificate has already been issued. You can reprint a copy if needed.
                        </p>
                        @if($request->issued_date)
                        <p class="text-sm text-blue-700 mb-4">
                            Issued on {{ \Carbon\Carbon::parse($request->issued_date)->format('F d, Y h:i A') }}.
                        </p>
                        @endif
                        @endif

                        <div class="flex flex-col sm:flex-row gap-3">
                            @if($request->status === 'approved')
                            <form action="{{ route('staff.certificate-requests.issue', $request->id) }}" method="POST" 
                                  onsubmit="return confirm('Mark this certificate as issued?');" class="sm:mr-3">
                                @csrf
                                <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
                                    📄 Mark as Issued
                                </button>
                            </form>
                            @endif

                            <a href="{{ route('staff.certificate-requests.print', $request->id) }}" target="_blank"
                               class="w-full sm:w-auto bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition inline-flex items-center justify-center">
                                🖨️ Print Certificate
                            </a>
                        </div>
                    </div>
                </div>
                @elseif($request->status === 'pending')
                <div class="border-t border-gray-200 pt-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-yellow-800 mb-2">Awaiting Doctor Approval</h3>
                        <p class="text-sm text-yellow-700">
                            This request is still pending doctor approval. Staff can view the request details, and printing will become available once a doctor approves it.
                        </p>
                    </div>
                </div>
                @elseif($request->status === 'rejected')
                <div class="border-t border-gray-200 pt-6">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-red-800 mb-2">Request Rejected</h3>
                        <p class="text-sm text-red-700">
                            This certificate request has been rejected by the doctor and cannot be printed or issued.
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection