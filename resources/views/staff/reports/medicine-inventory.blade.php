<div class="space-y-6">
    <h2 class="text-xl font-semibold text-white">Medicine Inventory Report</h2>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-gray-700 rounded-lg p-4">
            <div class="text-gray-400 text-sm">Total Medicines</div>
            <div class="text-3xl font-bold text-white mt-2">{{ $data['total_medicines'] ?? 0 }}</div>
        </div>
        <div class="bg-gray-700 rounded-lg p-4">
            <div class="text-gray-400 text-sm">In Stock</div>
            <div class="text-3xl font-bold text-green-400 mt-2">{{ $data['in_stock'] ?? 0 }}</div>
        </div>
        <div class="bg-gray-700 rounded-lg p-4">
            <div class="text-gray-400 text-sm">Low Stock</div>
            <div class="text-3xl font-bold text-yellow-400 mt-2">{{ $data['low_stock'] ?? 0 }}</div>
        </div>
        <div class="bg-gray-700 rounded-lg p-4">
            <div class="text-gray-400 text-sm">Out of Stock</div>
            <div class="text-3xl font-bold text-red-400 mt-2">{{ $data['out_of_stock'] ?? 0 }}</div>
        </div>
        <div class="bg-gray-700 rounded-lg p-4">
            <div class="text-gray-400 text-sm">Expired</div>
            <div class="text-3xl font-bold text-red-600 mt-2">{{ $data['expired'] ?? 0 }}</div>
        </div>
    </div>

    <!-- All Medicines List -->
    @if(isset($data['all_medicines']) && count($data['all_medicines']) > 0)
    <div class="bg-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-white mb-4">All Medicines</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-300">
                <thead class="border-b border-gray-600">
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
                    <tr class="border-b border-gray-600 hover:bg-gray-600">
                        <td class="py-2">{{ $medicine->name }}</td>
                        <td class="py-2">{{ $medicine->generic_name ?? 'N/A' }}</td>
                        <td class="py-2">{{ $medicine->manufacturer ?? 'N/A' }}</td>
                        <td class="text-right py-2">
                            @if($medicine->quantity <= 0)
                                <span class="px-2 py-1 bg-red-600 text-white text-xs rounded">{{ $medicine->quantity }}</span>
                            @elseif($medicine->quantity <= 10)
                                <span class="px-2 py-1 bg-yellow-600 text-white text-xs rounded">{{ $medicine->quantity }}</span>
                            @else
                                <span class="px-2 py-1 bg-green-600 text-white text-xs rounded">{{ $medicine->quantity }}</span>
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

    <!-- Low Stock Items -->
    @if(isset($data['low_stock_items']) && count($data['low_stock_items']) > 0)
    <div class="bg-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-white mb-4">Low Stock Items (Quantity ≤ 10)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-300">
                <thead class="border-b border-gray-600">
                    <tr>
                        <th class="text-left py-2">Medicine</th>
                        <th class="text-left py-2">Generic Name</th>
                        <th class="text-right py-2">Quantity</th>
                        <th class="text-left py-2">Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['low_stock_items'] as $medicine)
                    <tr class="border-b border-gray-600">
                        <td class="py-2">{{ $medicine->name }}</td>
                        <td class="py-2">{{ $medicine->generic_name ?? 'N/A' }}</td>
                        <td class="text-right py-2">
                            <span class="px-2 py-1 bg-yellow-600 text-white text-xs rounded">
                                {{ $medicine->quantity }}
                            </span>
                        </td>
                        <td class="py-2">{{ \Carbon\Carbon::parse($medicine->expiration_date)->format('M d, Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Expired Items -->
    @if(isset($data['expired_items']) && count($data['expired_items']) > 0)
    <div class="bg-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-white mb-4">Expired Medicines</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-300">
                <thead class="border-b border-gray-600">
                    <tr>
                        <th class="text-left py-2">Medicine</th>
                        <th class="text-left py-2">Generic Name</th>
                        <th class="text-right py-2">Quantity</th>
                        <th class="text-left py-2">Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['expired_items'] as $medicine)
                    <tr class="border-b border-gray-600">
                        <td class="py-2">{{ $medicine->name }}</td>
                        <td class="py-2">{{ $medicine->generic_name ?? 'N/A' }}</td>
                        <td class="text-right py-2">{{ $medicine->quantity }}</td>
                        <td class="py-2">
                            <span class="px-2 py-1 bg-red-600 text-white text-xs rounded">
                                {{ \Carbon\Carbon::parse($medicine->expiration_date)->format('M d, Y') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
