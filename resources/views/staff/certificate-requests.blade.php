@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Certificate Requests</h1>
            <p class="text-gray-600">Review and manage patient certificate requests</p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Status Tabs -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="flex border-b border-gray-200 overflow-x-auto">
                <a href="{{ route('staff.certificate-requests', ['status' => 'pending']) }}"
                   class="px-6 py-3 text-sm font-medium whitespace-nowrap border-b-2 {{ request('status') === 'pending' || !request('status') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Pending
                    <span class="ml-1 bg-yellow-100 text-yellow-800 py-0.5 px-2 rounded-full text-xs">{{ $pendingCount }}</span>
                </a>
                <a href="{{ route('staff.certificate-requests', ['status' => 'approved']) }}"
                   class="px-6 py-3 text-sm font-medium whitespace-nowrap border-b-2 {{ request('status') === 'approved' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Approved
                    <span class="ml-1 bg-blue-100 text-blue-800 py-0.5 px-2 rounded-full text-xs">{{ $approvedCount }}</span>
                </a>
                <a href="{{ route('staff.certificate-requests', ['status' => 'issued']) }}"
                   class="px-6 py-3 text-sm font-medium whitespace-nowrap border-b-2 {{ request('status') === 'issued' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Issued
                    <span class="ml-1 bg-green-100 text-green-800 py-0.5 px-2 rounded-full text-xs">{{ $issuedCount }}</span>
                </a>
                <a href="{{ route('staff.certificate-requests', ['status' => 'rejected']) }}"
                   class="px-6 py-3 text-sm font-medium whitespace-nowrap border-b-2 {{ request('status') === 'rejected' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Rejected
                    <span class="ml-1 bg-red-100 text-red-800 py-0.5 px-2 rounded-full text-xs">{{ $rejectedCount }}</span>
                </a>
            </div>
        </div>

        <!-- Search Form -->
        <div class="bg-white shadow-sm rounded-lg p-4 mb-6">
            <form method="GET" action="{{ route('staff.certificate-requests') }}" class="flex gap-4 items-center">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="flex-1">
                    <input type="text" name="search" placeholder="Search by patient name..."
                           value="{{ request('search') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('staff.certificate-requests', ['status' => request('status')]) }}"
                       class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Requests List -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            @if($requests->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Certificate Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($requests as $certRequest)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $certRequest->patient->user->firstname ?? '' }} {{ $certRequest->patient->user->middlename ?? '' }} {{ $certRequest->patient->user->lastname ?? '' }}
                                </div>
                                @if($certRequest->patient->school_id)
                                    <div class="text-xs text-gray-500">ID: {{ $certRequest->patient->school_id }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $certRequest->certificateType->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 truncate max-w-xs" title="{{ $certRequest->reason }}">
                                    {{ Str::limit($certRequest->reason, 40) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $certRequest->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php $statusClass = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-blue-100 text-blue-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'issued' => 'bg-green-100 text-green-800'
                                ][$certRequest->status] ?? 'bg-gray-100 text-gray-800'; @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ ucfirst($certRequest->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex flex-col gap-1">
                                    <a href="{{ route('staff.certificate-requests.show', $certRequest->id) }}"
                                       class="text-blue-600 hover:text-blue-900 font-medium">
                                        View Details
                                    </a>
                                    @if(in_array($certRequest->status, ['approved', 'issued']))
                                    <a href="{{ route('staff.certificate-requests.print', $certRequest->id) }}" target="_blank"
                                       class="text-green-600 hover:text-green-900 font-medium text-xs">
                                        🖨️ Print Certificate
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    {{ $requests->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No certificate requests</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request('status') === 'pending')
                            No pending certificate requests at the moment.
                        @elseif(request('status') === 'approved')
                            No approved certificate requests.
                        @elseif(request('status') === 'issued')
                            No issued certificates yet.
                        @elseif(request('status') === 'rejected')
                            No rejected certificate requests.
                        @else
                            No certificate requests found.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
