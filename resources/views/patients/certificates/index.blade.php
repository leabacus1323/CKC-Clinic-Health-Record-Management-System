@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">My Certificate Requests</h1>
                <p class="text-gray-600">View and track your medical certificate requests</p>
            </div>
            <a href="{{ route('patients.certificates.create') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition flex items-center">
                <span class="mr-2">+</span> New Request
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <!-- Certificates List -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            @if($certificates->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Certificate Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($certificates as $certificate)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $certificate->certificateType->name }}</div>
                                @if($certificate->reason)
                                    <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($certificate->reason, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @include('patients.certificates.partials.status-badge', ['status' => $certificate->status])
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $certificate->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('patients.certificates.show', $certificate->id) }}"
                                   class="text-blue-600 hover:text-blue-900 font-medium">
                                    View Details
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    {{ $certificates->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No certificate requests yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new certificate request.</p>
                    <div class="mt-6">
                        <a href="{{ route('patients.certificates.create') }}"
                           class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition inline-block">
                            + New Certificate Request
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection