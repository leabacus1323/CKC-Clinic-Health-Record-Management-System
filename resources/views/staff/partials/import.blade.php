<!-- Import Partial -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Import Data</h2>
    <p class="text-gray-600">Bulk upload users and medicines from CSV or Excel files</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Users Import Card -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center mb-4">
            <div class="bg-blue-100 p-3 rounded-lg">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 ml-4">Import Users</h3>
        </div>
        <p class="text-gray-600 mb-4">Bulk upload users from CSV or Excel file.</p>
        <form action="{{ route('staff.import.process.users') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload File</label>
                <input type="file" name="file" accept=".csv,.xlsx,.xls" required
                    class="w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name Column #</label>
                    <input type="number" name="name_column" value="0" min="0" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Column #</label>
                    <input type="number" name="email_column" value="1" min="0" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Column #</label>
                    <input type="number" name="password_column" value="2" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Default: password123</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User Type Column #</label>
                    <input type="number" name="user_type_column" value="3" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">1=Admin, 2=Patient, 3=Doctor</p>
                </div>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                Import Users
            </button>
            <a href="{{ route('staff.import.sample.users') }}" class="block text-center text-blue-600 hover:underline text-sm">
                Download Sample CSV
            </a>
        </form>
    </div>

    <!-- Medicines Import Card -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="flex items-center mb-4">
            <div class="bg-green-100 p-3 rounded-lg">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 ml-4">Import Medicines</h3>
        </div>
        <p class="text-gray-600 mb-4">Bulk upload medicines inventory from CSV or Excel file.</p>
        <form action="{{ route('staff.import.process.medicines') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload File</label>
                <input type="file" name="file" accept=".csv,.xlsx,.xls" required
                    class="w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50">
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name #</label>
                    <input type="number" name="name_column" value="0" min="0" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description #</label>
                    <input type="number" name="description_column" value="1" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity #</label>
                    <input type="number" name="quantity_column" value="2" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date #</label>
                    <input type="number" name="expiration_date_column" value="3" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Format: YYYY-MM-DD</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type #</label>
                    <input type="number" name="medicine_type_column" value="4" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg">
                Import Medicines
            </button>
            <a href="{{ route('staff.import.sample.medicines') }}" class="block text-center text-green-600 hover:underline text-sm">
                Download Sample CSV
            </a>
        </form>
    </div>
</div>

@if(session('success'))
<div class="mt-6 p-4 bg-green-100 text-green-800 rounded-lg">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mt-6 p-4 bg-red-100 text-red-800 rounded-lg">
    {{ session('error') }}
</div>
@endif
