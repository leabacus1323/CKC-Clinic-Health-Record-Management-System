@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Staff Mode Indicator -->
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span class="text-green-800 font-medium">Staff Duties Mode</span>
            <span class="text-green-600 text-sm ml-2">- Managing clinic operations</span>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">My Patients</h1>
                <p class="text-gray-600">Patients who have appointments with you</p>
            </div>
            <a href="{{ route('staff.patients.create') }}"
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                + Add Patient
            </a>
        </div>

        <div class="p-6">
            <!-- Search and Filter -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <input type="text"
                               id="searchPatients"
                               placeholder="Search patients by name, ID, or email... (live search)"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-10">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <div class="sm:w-48">
                        <select id="filterType"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Patient Types</option>
                            <option value="1">Students</option>
                            <option value="2">Faculty & Staff</option>
                        </select>
                    </div>
                </div>
            </div>

<!-- Patients count display -->
    <div class="patients-count mb-4 text-sm text-gray-600">
        Showing <strong>{{ $patients->count() }}</strong> of <strong>{{ $patients->total() }}</strong> patients
    </div>

    <!-- Patients Grid -->
    @if($patients->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="patientsGrid">
            @foreach($patients as $patient)
                <div class="patient-card bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition"
                     data-name="{{ strtolower($patient->user->name ?? '') }}"
                     data-email="{{ strtolower($patient->user->email ?? '') }}"
                     data-id="{{ $patient->id }}"
                     data-type="{{ $patient->patient_type }}">

                    <!-- Patient Header -->
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-lg">
                            {{ substr($patient->user->name ?? 'N', 0, 1) }}
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="font-semibold text-gray-800">{{ $patient->user->name ?? 'N/A' }}</h3>
                                    <p class="text-sm text-gray-500">ID: {{ $patient->id }}</p>
                                </div>
                            </div>

                            <!-- Patient Info -->
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Type:</span>
                                    <span class="font-medium">
                                        @if($patient->patient_type == 1) Student
                                        @elseif($patient->patient_type == 3) Faculty
                                        @elseif($patient->patient_type == 2) Staff
                                        @else Other
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Email:</span>
                                    <span class="font-medium">{{ $patient->user->email }}</span>
                                </div>
                                @if($patient->school_id)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">School ID:</span>
                                        <span class="font-medium">{{ $patient->school_id }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Appointment Stats -->
                            <div class="bg-gray-50 rounded-lg p-3 mb-4">
                                <div class="grid grid-cols-2 gap-4 text-center">
                                    <div>
                                        <p class="text-2xl font-bold text-blue-600">{{ $patient->appointments_count ?? 0 }}</p>
                                        <p class="text-xs text-gray-600">Total Visits</p>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-bold text-green-600">
                                            {{ $patient->appointments->where('created_at', '>=', now()->subMonth())->count() }}
                                        </p>
                                        <p class="text-xs text-gray-600">This Month</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Latest Appointment -->
                            @if($patient->appointments->first())
                                @php $lastAppointment = $patient->appointments->first(); @endphp
                                <div class="border-t border-gray-200 pt-3 mb-4">
                                    <p class="text-sm font-medium text-gray-800 mb-1">Latest Appointment</p>
                                    <p class="text-xs text-gray-600">
                                        {{ \Carbon\Carbon::parse($lastAppointment->date)->format('M j, Y') }} at
                                        {{ \Carbon\Carbon::parse($lastAppointment->time)->format('g:i A') }}
                                    </p>
                                    <span class="inline-block mt-1 px-2 py-1 text-xs rounded-full
                                        @if($lastAppointment->status === 'Pending') bg-yellow-100 text-yellow-800
                                        @elseif($lastAppointment->status === 'Confirmed') bg-green-100 text-green-800
                                        @elseif($lastAppointment->status === 'Completed') bg-blue-100 text-blue-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ $lastAppointment->status }}
                                    </span>
                                </div>
                            @endif

                            <!-- Medical Alerts -->
                            @if($patient->allergies || $patient->medical_condition)
                                <div class="border-t border-gray-200 pt-3 mb-4">
                                    <p class="text-sm font-medium text-red-600 mb-1">⚠️ Medical Alerts</p>
                                    @if($patient->allergies)
                                        <p class="text-xs text-gray-600"><strong>Allergies:</strong> {{ Str::limit($patient->allergies, 30) }}</p>
                                    @endif
                                    @if($patient->medical_condition)
                                        <p class="text-xs text-gray-600"><strong>Conditions:</strong> {{ Str::limit($patient->medical_condition, 30) }}</p>
                                    @endif
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="flex space-x-2">
                                <a href="{{ route('staff.patient-details', $patient->id) }}"
                                   class="flex-1 bg-blue-700 hover:bg-blue-700 text-white text-center py-2 px-3 rounded-md text-sm font-medium shadow-sm border border-blue-700 transition-all duration-200">
                                    View Details
                                </a>
                                <a href="{{ route('staff.print.health-record', $patient->id) }}" target="_blank"
                                   class="flex-1 bg-purple-600 hover:bg-purple-600 text-white text-center py-2 px-3 rounded-md text-sm font-medium shadow-sm border border-purple-600 transition-all duration-200">
                                    🖨️ Print Record
                                </a>
                                <button class="bg-gray-300 hover:bg-gray-300 text-gray-700 py-2 px-3 rounded-md text-sm font-medium transition-all duration-200"
                                        onclick="quickContact('{{ $patient->user->email }}')">
                                    Contact
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($patients->hasPages())
                    <div class="mt-8">
                        {{ $patients->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Patients Found</h3>
                    <p class="text-gray-500">You don't have any patients with appointments yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchPatients');
    const typeFilter = document.getElementById('filterType');
    const patientCards = document.querySelectorAll('.patient-card');
    const countDisplay = document.querySelector('.patients-count');

    if (!searchInput || !typeFilter || patientCards.length === 0) return;

    let searchTimeout;

    function filterPatients() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedType = typeFilter.value;
        let visibleCount = 0;

        patientCards.forEach(card => {
            const name = card.dataset.name || '';
            const email = card.dataset.email || '';
            const id = card.dataset.id || '';
            const type = card.dataset.type || '';

            const matchesSearch = searchTerm === '' ||
                name.includes(searchTerm) ||
                email.includes(searchTerm) ||
                id.includes(searchTerm);
            const matchesType = selectedType === '' || type === selectedType;

            if (matchesSearch && matchesType) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (countDisplay) {
            const totalCount = patientCards.length;
            let countText = `Showing <strong>${visibleCount}</strong> of <strong>${totalCount}</strong> patients`;
            if (searchTerm || selectedType) {
                if (searchTerm) countText += ` matching "<strong>${searchTerm}</strong>"`;
                if (selectedType) countText += selectedType === '1' ? ' (<strong>Students</strong>)' : ' (<strong>Faculty & Staff</strong>)';
            }
            countDisplay.innerHTML = countText;
        }
    }

    searchInput.oninput = function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(filterPatients, 200);
    };

    typeFilter.onchange = function() {
        filterPatients();
    };

    filterPatients();
});
</script>
@endpush

<!-- Inline script for AJAX loaded content -->
<script>
(function() {
    document.addEventListener('input', function(e) {
        if (e.target.id === 'searchPatients') {
            const searchInput = document.getElementById('searchPatients');
            const typeFilter = document.getElementById('filterType');
            const patientCards = document.querySelectorAll('.patient-card');
            const countDisplay = document.querySelector('.patients-count');

            if (!searchInput || !typeFilter || patientCards.length === 0) return;

            let searchTimeout;

            function filterPatients() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedType = typeFilter.value;
                let visibleCount = 0;

                patientCards.forEach(card => {
                    const name = card.dataset.name || '';
                    const email = card.dataset.email || '';
                    const id = card.dataset.id || '';
                    const type = card.dataset.type || '';

                    const matchesSearch = searchTerm === '' ||
                        name.includes(searchTerm) ||
                        email.includes(searchTerm) ||
                        id.includes(searchTerm);
                    const matchesType = selectedType === '' || type === selectedType;

                    if (matchesSearch && matchesType) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (countDisplay) {
                    const totalCount = patientCards.length;
                    let countText = `Showing <strong>${visibleCount}</strong> of <strong>${totalCount}</strong> patients`;
                    if (searchTerm || selectedType) {
                        if (searchTerm) countText += ` matching "<strong>${searchTerm}</strong>"`;
                        if (selectedType) countText += selectedType === '1' ? ' (<strong>Students</strong>)' : ' (<strong>Faculty & Staff</strong>)';
                    }
                    countDisplay.innerHTML = countText;
                }
            }

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(filterPatients, 200);
        }
    });

    document.addEventListener('change', function(e) {
        if (e.target.id === 'filterType') {
            const searchInput = document.getElementById('searchPatients');
            const typeFilter = document.getElementById('filterType');
            const patientCards = document.querySelectorAll('.patient-card');
            const countDisplay = document.querySelector('.patients-count');

            if (!searchInput || !typeFilter || patientCards.length === 0) return;

            let visibleCount = 0;
            const searchTerm = searchInput.value.toLowerCase();
            const selectedType = typeFilter.value;

            patientCards.forEach(card => {
                const name = card.dataset.name || '';
                const email = card.dataset.email || '';
                const id = card.dataset.id || '';
                const type = card.dataset.type || '';

                const matchesSearch = searchTerm === '' ||
                    name.includes(searchTerm) ||
                    email.includes(searchTerm) ||
                    id.includes(searchTerm);
                const matchesType = selectedType === '' || type === selectedType;

                if (matchesSearch && matchesType) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (countDisplay) {
                const totalCount = patientCards.length;
                let countText = `Showing <strong>${visibleCount}</strong> of <strong>${totalCount}</strong> patients`;
                if (searchTerm || selectedType) {
                    if (searchTerm) countText += ` matching "<strong>${searchTerm}</strong>"`;
                    if (selectedType) countText += selectedType === '1' ? ' (<strong>Students</strong>)' : ' (<strong>Faculty & Staff</strong>)';
                }
                countDisplay.innerHTML = countText;
            }
        }
    });
})();
</script>

@endsection
