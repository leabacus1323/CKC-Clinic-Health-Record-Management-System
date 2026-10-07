@extends('admin.layout.navigation')

@section('content')
<div class="relative overflow-x-auto p-6">
    @if (session('success'))
    <div id="alert-border-3" class="flex items-center p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
        </svg>
        <div class="ms-3 text-sm font-medium">
            {{ session('success') }}
        </div>
    </div>
    @endif
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div id="alert-border-4" class="flex items-center p-4 mb-4 text-yellow-800 border-t-4 border-yellow-300 bg-yellow-50 dark:text-yellow-300 dark:bg-gray-800 dark:border-yellow-800" role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <div class="ms-3 text-sm font-medium">
                    {{ $error }}
                </div>
            </div>
        @endforeach
    @endif

    <h2 class="text-xl font-semibold text-white mb-6">New Prescription</h2>

    <form class="max-w-4xl mx-auto" action="{{ route('admin.prescription.store') }}" method="POST">
        @csrf

        <!-- Patient Selection -->
        <div class="mb-6">
            <label for="patient_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Patient</label>
            <select name="patient_id" id="patient_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                <option value="">Select Patient</option>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}">{{ $patient->user->name ?? 'Patient #'.$patient->id }}</option>
                @endforeach
            </select>
        </div>

        <!-- Medicine Selection -->
        <div class="mb-6">
            <label for="medicine_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Medicine</label>
            <select name="medicine_id" id="medicine_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                <option value="">Select Medicine</option>
                @foreach($medicines as $medicine)
                    <option value="{{ $medicine->id }}">{{ $medicine->name }} ({{ $medicine->category ?? 'N/A' }})</option>
                @endforeach
            </select>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mb-6">
            <!-- Dosage -->
            <div>
                <label for="dosage" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Dosage</label>
                <input type="text" name="dosage" id="dosage" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="e.g. 500mg" required>
            </div>

            <!-- Frequency -->
            <div>
                <label for="frequency" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Frequency</label>
                <input type="text" name="frequency" id="frequency" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="e.g. 3x daily">
            </div>

            <!-- Duration -->
            <div>
                <label for="duration" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Duration</label>
                <input type="text" name="duration" id="duration" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="e.g. 7 days">
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <!-- Date Prescribed -->
            <div>
                <label for="date_prescribed" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date Prescribed</label>
                <input type="date" name="date_prescribed" id="date_prescribed" value="{{ date('Y-m-d') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                <select name="status" id="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="discontinued">Discontinued</option>
                </select>
            </div>
        </div>

        <!-- Instructions -->
        <div class="mb-6">
            <label for="instruction" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Instructions</label>
            <textarea name="instruction" id="instruction" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter usage instructions..."></textarea>
        </div>

        <div class="flex justify-start space-x-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                Save Prescription
            </button>
            <a href="{{ route('admin.prescription.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
