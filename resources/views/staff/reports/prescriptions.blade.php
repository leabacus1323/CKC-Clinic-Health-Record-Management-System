<div class="space-y-6">
    <h2 class="text-xl font-semibold text-white">Prescription Report</h2>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-gray-700 rounded-lg p-4">
            <div class="text-gray-400 text-sm">Total Prescriptions</div>
            <div class="text-3xl font-bold text-white mt-2">{{ $data['total_prescriptions'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Status Distribution -->
    @if(isset($data['by_status']) && count($data['by_status']) > 0)
    <div class="bg-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-white mb-4">Prescriptions by Status</h3>
        <table class="w-full text-gray-300">
            <thead class="border-b border-gray-600">
                <tr>
                    <th class="text-left py-2">Status</th>
                    <th class="text-right py-2">Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['by_status'] as $item)
                <tr class="border-b border-gray-600">
                    <td class="py-2">{{ $item->status }}</td>
                    <td class="text-right py-2">{{ $item->count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Most Prescribed Medicines -->
    @if(isset($data['most_prescribed']) && count($data['most_prescribed']) > 0)
    <div class="bg-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-white mb-4">Top 10 Most Prescribed Medicines</h3>
        <table class="w-full text-gray-300">
            <thead class="border-b border-gray-600">
                <tr>
                    <th class="text-left py-2">Medicine</th>
                    <th class="text-right py-2">Times Prescribed</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['most_prescribed'] as $item)
                <tr class="border-b border-gray-600">
                    <td class="py-2">{{ $item->name }}</td>
                    <td class="text-right py-2">{{ $item->count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Recent Prescriptions -->
    @if(isset($data['recent_prescriptions']) && count($data['recent_prescriptions']) > 0)
    <div class="bg-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-white mb-4">Recent Prescriptions</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-300">
                <thead class="border-b border-gray-600">
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
                    <tr class="border-b border-gray-600 hover:bg-gray-600">
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
