<div class="space-y-6">
    <h2 class="text-xl font-semibold text-gray-800">Prescription Report</h2>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <div class="text-blue-600 text-sm">Total Prescriptions</div>
            <div class="text-3xl font-bold text-blue-700 mt-2">{{ $data['total_prescriptions'] ?? 0 }}</div>
        </div>
        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <div class="text-green-600 text-sm">Active</div>
            <div class="text-3xl font-bold text-green-700 mt-2">{{ $data['active_prescriptions'] ?? 0 }}</div>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <div class="text-gray-600 text-sm">Discontinued</div>
            <div class="text-3xl font-bold text-gray-700 mt-2">{{ $data['discontinued_prescriptions'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Most Prescribed Medicines -->
    @if(isset($data['most_prescribed']) && count($data['most_prescribed']) > 0)
    <div class="bg-gray-50 rounded-lg p-4 border">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Most Prescribed Medicines</h3>
        <table class="w-full text-gray-600">
            <thead class="border-b border-gray-300">
                <tr>
                    <th class="text-left py-2">Medicine ID</th>
                    <th class="text-right py-2">Times Prescribed</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['most_prescribed'] as $item)
                <tr class="border-b border-gray-200">
                    <td class="py-2">{{ $item->medicine_id }}</td>
                    <td class="text-right py-2">{{ $item->count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Recent Prescriptions -->
    @if(isset($data['recent_prescriptions']) && count($data['recent_prescriptions']) > 0)
    <div class="bg-gray-50 rounded-lg p-4 border">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Prescriptions</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-600">
                <thead class="border-b border-gray-300">
                    <tr>
                        <th class="text-left py-2">Date</th>
                        <th class="text-left py-2">Patient</th>
                        <th class="text-left py-2">Doctor</th>
                        <th class="text-left py-2">Medicine</th>
                        <th class="text-left py-2">Dosage</th>
                        <th class="text-left py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['recent_prescriptions'] as $prescription)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-2">{{ \Carbon\Carbon::parse($prescription->date_prescribed)->format('M d, Y') }}</td>
                        <td class="py-2">{{ $prescription->patient->user->name ?? 'N/A' }}</td>
                        <td class="py-2">
                            @if($prescription->doctor && $prescription->doctor->user)
                                Dr. {{ $prescription->doctor->user->name }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="py-2">{{ $prescription->medicine->name ?? 'N/A' }}</td>
                        <td class="py-2">{{ $prescription->dosage ?? 'N/A' }}</td>
                        <td class="py-2">
                            <span class="px-2 py-1 bg-blue-600 text-white text-xs rounded">
                                {{ $prescription->status }}
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
