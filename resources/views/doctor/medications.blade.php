@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-800">Medications</h1>
            <p class="text-gray-600">Prescriptions and medication management for your patients</p>
        </div>

        <div class="p-6">
            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-md">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 7.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-blue-600">{{ $prescriptions->total() }}</p>
                            <p class="text-blue-600 font-medium">Total Prescriptions</p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-md">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-green-600">
                                {{ $prescriptions->where('date_prescribed', '>=', now()->subMonth())->count() }}
                            </p>
                            <p class="text-green-600 font-medium">This Month</p>
                        </div>
                    </div>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-md">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-purple-600">
                                {{ $prescriptions->pluck('patient_id')->unique()->count() }}
                            </p>
                            <p class="text-purple-600 font-medium">Patients Treated</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mb-8 flex flex-wrap gap-4">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    + New Prescription
                </button>
                <button class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">
                    View Medicine Inventory
                </button>
                <button class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition">
                    Generate Prescription Report
                </button>
            </div>

            <!-- Prescription List -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Prescriptions</h2>

                @if($prescriptions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">Date</th>
                                    <th class="px-6 py-3">Patient</th>
                                    <th class="px-6 py-3">Medicine</th>
                                    <th class="px-6 py-3">Dosage</th>
                                    <th class="px-6 py-3">Instructions</th>
                                    <th class="px-6 py-3">Duration</th>
                                    <th class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prescriptions as $prescription)
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="font-medium">{{ \Carbon\Carbon::parse($prescription->date_prescribed)->format('M j, Y') }}</p>
                                                <p class="text-gray-500 text-xs">{{ \Carbon\Carbon::parse($prescription->date_prescribed)->format('g:i A') }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-xs">
                                                    {{ substr($prescription->patient->user->name, 0, 1) }}
                                                </div>
                                                <div class="ml-3">
                                                    <p class="font-medium text-gray-800">{{ $prescription->patient->user->name }}</p>
                                                    <p class="text-gray-500 text-xs">ID: {{ $prescription->patient->id }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="font-medium text-gray-800">{{ $prescription->medicine->name ?? 'N/A' }}</p>
                                            @if($prescription->medicine && $prescription->medicine->type)
                                                <p class="text-gray-500 text-xs">{{ $prescription->medicine->type }}</p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                {{ $prescription->dosage }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-gray-800">{{ Str::limit($prescription->instructions ?? 'No instructions', 30) }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($prescription->duration)
                                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                    {{ $prescription->duration }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-xs">Not specified</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('doctor.patient-details', $prescription->patient->id) }}"
                                                   class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                                    View Patient
                                                </a>
                                                <button class="text-green-600 hover:text-green-800 text-xs font-medium">
                                                    Refill
                                                </button>
                                                <button class="text-purple-600 hover:text-purple-800 text-xs font-medium">
                                                    Print
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($prescriptions->hasPages())
                        <div class="mt-6">
                            {{ $prescriptions->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 7.172V5L8 4z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Prescriptions Found</h3>
                        <p class="text-gray-500">You haven't prescribed any medications yet.</p>
                        <button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                            Create First Prescription
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
