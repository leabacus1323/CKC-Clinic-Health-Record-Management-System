<div class="space-y-6">
    <h2 class="text-xl font-semibold text-white">Patient Education Level Distribution</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gray-700 rounded-lg p-4">
            <div class="text-gray-400 text-sm">Total Patients in Range</div>
            <div class="text-3xl font-bold text-white mt-2">{{ $data['total_patients'] ?? 0 }}</div>
        </div>
    </div>

    <div class="bg-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-white mb-4">Patient Count by Educational Level</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-300">
                <thead class="border-b border-gray-600">
                    <tr>
                        <th class="text-left py-2">Educational Level</th>
                        <th class="text-right py-2">Patient Count</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['by_educational_level'] as $item)
                    <tr class="border-b border-gray-600 hover:bg-gray-600">
                        <td class="py-2">{{ $item->level_name ?? 'Unknown' }}</td>
                        <td class="text-right py-2">{{ $item->count }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="py-4 text-center text-gray-400">No patient data available for this range.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
