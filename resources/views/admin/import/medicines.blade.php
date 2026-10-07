@extends('admin.layout.navigation')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('admin.import.index') }}" class="text-blue-600 hover:underline dark:text-blue-400 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Import
        </a>
    </div>

    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Import Medicines</h1>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('admin.import.process.medicines') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload CSV or Excel File</label>
                <input type="file" name="file" accept=".csv,.xlsx,.xls" required
                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Map Columns</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Select which column number (0-based index) corresponds to each field. Column numbers start from 0 on the left.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Name Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name Column <span class="text-red-500">*</span></label>
                        <input type="number" name="name_column" value="0" min="0" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <!-- Generic Name Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Generic Name Column</label>
                        <input type="number" name="generic_name_column" value="1" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <!-- Manufacturer Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Manufacturer Column</label>
                        <input type="number" name="manufacturer_column" value="2" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <!-- Dosage Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dosage Column</label>
                        <input type="number" name="dosage_column" value="3" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <!-- Description Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description Column</label>
                        <input type="number" name="description_column" value="4" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <!-- Quantity Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity Column</label>
                        <input type="number" name="quantity_column" value="5" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <!-- Unit Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Unit Column</label>
                        <input type="number" name="unit_column" value="6" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="text-xs text-gray-500 mt-1">e.g., mg, ml, tablet, capsule</p>
                    </div>

                    <!-- Expiration Date Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Expiration Date Column</label>
                        <input type="number" name="expiration_date_column" value="7" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="text-xs text-gray-500 mt-1">Format: YYYY-MM-DD</p>
                    </div>

                    <!-- Medicine Type Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Medicine Type Column</label>
                        <input type="number" name="medicine_type_column" value="8" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <!-- Status Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Column</label>
                        <input type="number" name="status_column" value="9" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="text-xs text-gray-500 mt-1">active or inactive</p>
                    </div>
                </div>
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg">
                    Import Medicines
                </button>
                <a href="{{ route('admin.import.sample.medicines') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg">
                    Download Sample CSV
                </a>
            </div>
        </form>
    </div>

    <div class="mt-6 bg-green-50 dark:bg-green-900/30 rounded-lg p-4">
        <h4 class="font-semibold text-green-800 dark:text-green-300 mb-2">Important Notes</h4>
        <ul class="text-sm text-green-700 dark:text-green-400 space-y-1">
            <li>• Duplicate medicine names will update existing records (adds to quantity)</li>
            <li>• Date format should be: YYYY-MM-DD (e.g., 2026-12-31)</li>
            <li>• Medicine type examples: General, Antibiotic, Analgesic, Vitamin</li>
        </ul>
    </div>
</div>
@endsection
