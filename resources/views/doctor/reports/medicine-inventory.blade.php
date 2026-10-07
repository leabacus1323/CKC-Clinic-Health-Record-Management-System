<div class="space-y-6">
    <h2 class="text-xl font-semibold text-gray-800">Medicine Inventory</h2>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <div class="text-blue-600 text-sm">Total Medicines</div>
            <div class="text-3xl font-bold text-blue-700 mt-2">{{ $data['total_medicines'] ?? 0 }}</div>
        </div>
        <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
            <div class="text-yellow-600 text-sm">Low Stock</div>
            <div class="text-3xl font-bold text-yellow-700 mt-2">{{ count($data['low_stock'] ?? []) }}</div>
        </div>
        <div class="bg-red-50 rounded-lg p-4 border border-red-200">
            <div class="text-red-600 text-sm">Out of Stock</div>
            <div class="text-3xl font-bold text-red-700 mt-2">{{ count($data['out_of_stock'] ?? []) }}</div>
        </div>
    </div>

    <!-- All Medicines -->
    @if(isset($data['all_medicines']) && count($data['all_medicines']) > 0)
    <div class="bg-gray-50 rounded-lg p-4 border">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Medicine List</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-600">
                <thead class="border-b border-gray-300">
                    <tr>
                        <th class="text-left py-2">Medicine</th>
                        <th class="text-left py-2">Generic Name</th>
                        <th class="text-left py-2">Manufacturer</th>
                        <th class="text-right py-2">Quantity</th>
                        <th class="text-left py-2">Dosage</th>
                        <th class="text-left py-2">Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['all_medicines'] as $medicine)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-2">{{ $medicine->name }}</td>
                        <td class="py-2">{{ $medicine->generic_name ?? 'N/A' }}</td>
                        <td class="py-2">{{ $medicine->manufacturer ?? 'N/A' }}</td>
                        <td class="text-right py-2">
                            @if($medicine->quantity <= 0)
                                <span class="px-2 py-1 bg-red-500 text-white text-xs rounded">{{ $medicine->quantity }}</span>
                            @elseif($medicine->quantity <= 10)
                                <span class="px-2 py-1 bg-yellow-500 text-white text-xs rounded">{{ $medicine->quantity }}</span>
                            @else
                                <span class="px-2 py-1 bg-green-500 text-white text-xs rounded">{{ $medicine->quantity }}</span>
                            @endif
                        </td>
                        <td class="py-2">{{ $medicine->dosage ?? 'N/A' }}</td>
                        <td class="py-2">{{ \Carbon\Carbon::parse($medicine->expiration_date)->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
