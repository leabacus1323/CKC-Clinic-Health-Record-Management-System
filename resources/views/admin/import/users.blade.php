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

    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Import Users</h1>

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
        <form action="{{ route('admin.import.process.users') }}" method="POST" enctype="multipart/form-data">
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- First Name Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name Column <span class="text-red-500">*</span></label>
                        <input type="number" name="f_name_column" value="0" min="0" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <!-- Middle Name Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Middle Name Column</label>
                        <input type="number" name="m_name_column" value="1" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <!-- Last Name Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name Column <span class="text-red-500">*</span></label>
                        <input type="number" name="l_name_column" value="2" min="0" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <!-- Email Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Column <span class="text-red-500">*</span></label>
                        <input type="number" name="email_column" value="3" min="0" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <!-- Password Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password Column</label>
                        <input type="number" name="password_column" value="4" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="text-xs text-gray-500 mt-1">Default password "password123" will be used if not specified</p>
                    </div>

                    <!-- User Type Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">User Type Column</label>
                        <input type="number" name="user_type_column" value="5" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="text-xs text-gray-500 mt-1">0=Admin, 1=Student, 2=Nurse, 3=Doctor, 4=Faculty, 5=Guardian, 6=Principal, 7=Teacher</p>
                    </div>

                    <!-- DOB Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date of Birth Column</label>
                        <input type="number" name="dob_column" value="6" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="text-xs text-gray-500 mt-1">Format: YYYY-MM-DD</p>
                    </div>

                    <!-- Address Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address Column</label>
                        <input type="number" name="address_column" value="7" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <!-- Gender Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gender Column</label>
                        <input type="number" name="gender_column" value="8" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <!-- Contact No Column -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact No Column</label>
                        <input type="number" name="contact_no_column" value="9" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                </div>
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg">
                    Import Users
                </button>
                <a href="{{ route('admin.import.sample.users') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-medium py-2 px-6 rounded-lg">
                    Download Sample CSV
                </a>
            </div>
        </form>
    </div>

    <div class="mt-6 bg-blue-50 dark:bg-blue-900/30 rounded-lg p-4">
        <h4 class="font-semibold text-blue-800 dark:text-blue-300 mb-2">User Type Reference</h4>
        <ul class="text-sm text-blue-700 dark:text-blue-400 space-y-1">
            <li><strong>1</strong> - Admin</li>
            <li><strong>2</strong> - Patient</li>
            <li><strong>3</strong> - Doctor</li>
        </ul>
    </div>
</div>
@endsection
