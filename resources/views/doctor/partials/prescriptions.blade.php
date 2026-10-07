<!-- Doctor Prescriptions Partial View -->
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Prescriptions</h2>
    <a href="{{ route('doctor.prescriptions.create-page') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Prescribe New Medication
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border p-6">
    <p class="text-gray-600">Prescriptions content loaded via AJAX.</p>
</div>
