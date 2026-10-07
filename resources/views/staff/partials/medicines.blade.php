<!-- Staff Medicines Partial View -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Medicines Management</h2>
            <p class="text-gray-600 text-sm mt-1">Monitor and manage clinic medicines inventory</p>
        </div>
        <a href="{{ route('staff.medicines.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm flex items-center whitespace-nowrap">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Medicine
        </a>
    </div>

    <!-- Statistics Cards -->
    @php
        $medicines = \App\Models\Medicine::all();
        $totalMedicines = $medicines->count();
        $availableMedicines = $medicines->where('quantity', '>', 0)->count();
        $lowStockMedicines = $medicines->whereBetween('quantity', [1, 10])->count();
        $expiredMedicines = $medicines->filter(function($m) {
            return $m->expiration_date && \Carbon\Carbon::parse($m->expiration_date)->isPast();
        })->count();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total Medicines Card -->
        <div class="bg-white rounded-lg border p-4 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5.5m0 0H9m3.5 0H5.5m0 0H2"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600 text-sm">Total Medicines</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $totalMedicines }}</p>
                </div>
            </div>
        </div>

        <!-- Available Medicines Card -->
        <div class="bg-white rounded-lg border p-4 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600 text-sm">In Stock</p>
                    <p class="text-2xl font-bold text-green-600">{{ $availableMedicines }}</p>
                </div>
            </div>
        </div>

        <!-- Low Stock Card -->
        <div class="bg-white rounded-lg border p-4 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M7.08 6.06L8.5 7.5m5 5l1.42 1.42M7.08 17.94l1.42-1.42m5-5l1.42 1.42"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600 text-sm">Low Stock</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $lowStockMedicines }}</p>
                </div>
            </div>
        </div>

        <!-- Expired Medicines Card -->
        <div class="bg-white rounded-lg border p-4 shadow-sm">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600 text-sm">Expired</p>
                    <p class="text-2xl font-bold text-red-600">{{ $expiredMedicines }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Medicines List -->
    <div class="bg-white rounded-lg border shadow-sm">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Medicines Inventory</h3>

            @if($medicines->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs font-semibold text-gray-700 bg-gray-50 uppercase border-b">
                            <tr>
                                <th class="px-4 py-3">Medicine Name</th>
                                <th class="px-4 py-3">Generic Name</th>
                                <th class="px-4 py-3">Dosage</th>
                                <th class="px-4 py-3">Quantity</th>
                                <th class="px-4 py-3">Unit</th>
                                <th class="px-4 py-3">Expiration</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicines as $medicine)
                                @php
                                    $isExpired = $medicine->expiration_date && \Carbon\Carbon::parse($medicine->expiration_date)->isPast();
                                    $isLowStock = $medicine->quantity > 0 && $medicine->quantity <= 10;
                                    $isOutOfStock = $medicine->quantity <= 0;
                                @endphp
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $medicine->name }}</td>
                                    <td class="px-4 py-3">{{ $medicine->generic_name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $medicine->dosage ?? '-' }}</td>
                                    <td class="px-4 py-3 font-semibold">{{ $medicine->quantity ?? 0 }}</td>
                                    <td class="px-4 py-3">{{ $medicine->unit ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        @if($medicine->expiration_date)
                                            {{ \Carbon\Carbon::parse($medicine->expiration_date)->format('M d, Y') }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($isExpired)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Expired
                                            </span>
                                        @elseif($isOutOfStock)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Out of Stock
                                            </span>
                                        @elseif($isLowStock)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Low Stock
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Available
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('staff.medicines.edit', $medicine->id) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-600 text-sm font-medium mt-2">No medicines in inventory</p>
                    <p class="text-gray-500 text-xs mt-1">Start by adding your first medicine using the button above</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Refresh medicines data every 60 seconds
    setInterval(function() {
        fetch(window.location.pathname)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const newDoc = parser.parseFromString(html, 'text/html');
                const newContent = newDoc.querySelector('#tab-content').innerHTML;
                document.getElementById('tab-content').innerHTML = newContent;
            })
            .catch(error => console.error('Error refreshing medicines:', error));
    }, 60000);
</script>

