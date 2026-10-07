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
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">Medicines Inventory</h1>
                    <p class="text-gray-600">Manage your clinic's medicine inventory</p>
                </div>
                <a href="{{ route('staff.medicines.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Medicine
                </a>
            </div>
        </div>

        <div class="p-6">
            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 rounded-md">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-orange-600">{{ $totalMedicines }}</p>
                            <p class="text-orange-600 font-medium">Total Medicines</p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-md">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-green-600">{{ $availableMedicines }}</p>
                            <p class="text-green-600 font-medium">Available</p>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-md">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-yellow-600">{{ $lowStockMedicines }}</p>
                            <p class="text-yellow-600 font-medium">Low Stock</p>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-md">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-2xl font-bold text-red-600">{{ $expiredMedicines }}</p>
                            <p class="text-red-600 font-medium">Expired</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Search -->
            <div class="mb-6">
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="text" id="medicineSearch" placeholder="Search by medicine name or type..." class="w-full ps-10 p-4 border border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                </div>
            </div>

            <!-- Medicines Table -->
            @if($medicines->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiration Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="medicinesTable" class="divide-y divide-gray-200">
                            @foreach($medicines as $medicine)
                                <tr class="medicine-row hover:bg-gray-50" data-search="{{ strtolower($medicine->name . ' ' . ($medicine->medicine_type ?? '') . ' ' . ($medicine->description ?? '')) }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $medicine->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">{{ $medicine->medicine_type ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-600 max-w-xs truncate">{{ $medicine->description ?? 'No description' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold {{ $medicine->quantity < 10 ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ $medicine->quantity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($medicine->expiration_date)
                                            @php
                                                $expirationDate = \Carbon\Carbon::parse($medicine->expiration_date);
                                                $isExpired = $expirationDate->isPast();
                                                $isExpiringSoon = !$isExpired && $expirationDate->diffInDays(now()) < 30;
                                            @endphp
                                            <span class="text-sm {{ $isExpired ? 'text-red-600 font-semibold' : ($isExpiringSoon ? 'text-yellow-600' : 'text-gray-600') }}">
                                                {{ $expirationDate->format('M j, Y') }}
                                                @if($isExpired)
                                                    <br><span class="text-xs">(Expired)</span>
                                                @elseif($isExpiringSoon)
                                                    <br><span class="text-xs">(Expiring Soon)</span>
                                                @endif
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">
                                            {{ $medicine->created_at ? \Carbon\Carbon::parse($medicine->created_at)->format('M j, Y') : 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $expirationDate = $medicine->expiration_date ? \Carbon\Carbon::parse($medicine->expiration_date) : null;
                                            $isExpired = $expirationDate ? $expirationDate->isPast() : false;
                                            $isLowStock = $medicine->quantity < 10;
                                        @endphp
                                        @if($isExpired)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Expired</span>
                                        @elseif($isLowStock)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Low Stock</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button onclick='openEditMedicineModal(@json($medicine))' class="text-blue-600 hover:text-blue-900 mr-3">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($medicines->hasPages())
                    <div class="mt-8">
                        {{ $medicines->links() }}
                    </div>
                @endif
            @else
                <div id="noMedicineResults" class="hidden text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Medicines Found</h3>
                    <p class="text-gray-500 mb-4">Start building your medicine inventory.</p>
                    <button onclick="openAddMedicineModal()" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm">
                        Add First Medicine
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Medicine Modal -->
<div id="addMedicineModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Add New Medicine</h3>
            <button onclick="closeAddMedicineModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form action="{{ route('staff.medicines.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Medicine Name *</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="medicine_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select Type</option>
                        <option value="Tablet">Tablet</option>
                        <option value="Capsule">Capsule</option>
                        <option value="Syrup">Syrup</option>
                        <option value="Injection">Injection</option>
                        <option value="Ointment">Ointment</option>
                        <option value="Drops">Drops</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                    <input type="number" name="quantity" required min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expiration Date</label>
                    <input type="date" name="expiration_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeAddMedicineModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                    Add Medicine
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Medicine Modal -->
<div id="editMedicineModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Edit Medicine</h3>
            <button onclick="closeEditMedicineModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="editMedicineForm" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Medicine Name *</label>
                    <input type="text" name="name" id="edit_name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="medicine_type" id="edit_medicine_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select Type</option>
                        <option value="Tablet">Tablet</option>
                        <option value="Capsule">Capsule</option>
                        <option value="Syrup">Syrup</option>
                        <option value="Injection">Injection</option>
                        <option value="Ointment">Ointment</option>
                        <option value="Drops">Drops</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                    <input type="number" name="quantity" id="edit_quantity" required min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="edit_description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Expiration Date</label>
                    <input type="date" name="expiration_date" id="edit_expiration_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeEditMedicineModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                    Update Medicine
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddMedicineModal() {
    document.getElementById('addMedicineModal').classList.remove('hidden');
}

function closeAddMedicineModal() {
    document.getElementById('addMedicineModal').classList.add('hidden');
}

function openEditMedicineModal(medicine) {
    document.getElementById('edit_name').value = medicine.name;
    document.getElementById('edit_medicine_type').value = medicine.medicine_type || '';
    document.getElementById('edit_quantity').value = medicine.quantity;
    document.getElementById('edit_description').value = medicine.description || '';
    document.getElementById('edit_expiration_date').value = medicine.expiration_date || '';

    const form = document.getElementById('editMedicineForm');
    form.action = '/staff/medicine/' + medicine.id + '?_method=PUT';

    document.getElementById('editMedicineModal').classList.remove('hidden');
}

function closeEditMedicineModal() {
    document.getElementById('editMedicineModal').classList.add('hidden');
}

(function() {
    var searchInput = document.getElementById('medicineSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var searchValue = this.value.toLowerCase();
            var rows = document.querySelectorAll('.medicine-row');
            var visibleCount = 0;
            rows.forEach(function(row) {
                var searchText = (row.getAttribute('data-search') || '').toLowerCase();
                var show = searchText.includes(searchValue);
                row.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });
            var noResults = document.getElementById('noMedicineResults');
            if (noResults) {
                noResults.classList.toggle('hidden', visibleCount > 0);
            }
        });
    }
})();
</script>

@endsection
