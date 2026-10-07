@extends('admin.layout.navigation')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Import Data</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Users Import Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <div class="bg-blue-100 p-3 rounded-lg dark:bg-blue-900">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white ml-4">Import Users</h2>
            </div>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Bulk upload users from CSV or Excel file. Supports column mapping for flexible file formats.</p>
            <div class="flex space-x-3">
                <a href="{{ route('admin.import.users') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                    Import Users
                </a>
                <a href="{{ route('admin.import.sample.users') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-medium py-2 px-4 rounded">
                    Download Sample
                </a>
            </div>
        </div>

        <!-- Medicines Import Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 p-3 rounded-lg dark:bg-green-900">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white ml-4">Import Medicines</h2>
            </div>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Bulk upload medicines inventory from CSV or Excel file. Duplicate entries will update existing records.</p>
            <div class="flex space-x-3">
                <a href="{{ route('admin.import.medicines') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded">
                    Import Medicines
                </a>
                <a href="{{ route('admin.import.sample.medicines') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-medium py-2 px-4 rounded">
                    Download Sample
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mt-6 p-4 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mt-6 p-4 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-lg">
        {{ session('error') }}
    </div>
    @endif
</div>
@endsection
