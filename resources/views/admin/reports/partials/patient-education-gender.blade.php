<div class="space-y-6">
    <h2 class="text-xl font-semibold text-white">Patient Education & Gender Statistics</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gray-700 rounded-lg p-4">
            <div class="text-gray-400 text-sm">Total Patients</div>
            <div class="text-3xl font-bold text-white mt-2">{{ $data['total_patients'] ?? 0 }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-gray-700 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-white mb-4">Patient Count by Gender</h3>
            <table class="w-full text-gray-300">
                <thead class="border-b border-gray-600">
                    <tr>
                        <th class="text-left py-2">Gender</th>
                        <th class="text-right py-2">Count</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['by_gender'] as $item)
                    <tr class="border-b border-gray-600 hover:bg-gray-600">
                        <td class="py-2">{{ $item->gender === 'M' ? 'Male' : ($item->gender === 'F' ? 'Female' : ucfirst($item->gender)) }}</td>
                        <td class="text-right py-2">{{ $item->count }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="py-4 text-center text-gray-400">No gender data available.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-gray-700 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-white mb-4">Patient Count by Educational Level</h3>
            <table class="w-full text-gray-300">
                <thead class="border-b border-gray-600">
                    <tr>
                        <th class="text-left py-2">Educational Level</th>
                        <th class="text-right py-2">Count</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['by_educational_level_and_gender']->groupBy('level_name') as $levelName => $items)
                    <tr class="border-b border-gray-600 hover:bg-gray-600">
                        <td class="py-2">{{ $levelName }}</td>
                        <td class="text-right py-2">{{ $items->sum('count') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="py-4 text-center text-gray-400">No educational level data available.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-white mb-4">Education Level by Gender Matrix</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-300">
                <thead class="border-b border-gray-600">
                    <tr>
                        <th class="text-left py-2">Educational Level</th>
                        <th class="text-right py-2">Male</th>
                        <th class="text-right py-2">Female</th>
                        <th class="text-right py-2">Other</th>
                        <th class="text-right py-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['education_gender_matrix'] as $levelName => $genderCounts)
                    <tr class="border-b border-gray-600 hover:bg-gray-600">
                        <td class="py-2">{{ $levelName }}</td>
                        <td class="text-right py-2">{{ $genderCounts['Male'] ?? 0 }}</td>
                        <td class="text-right py-2">{{ $genderCounts['Female'] ?? 0 }}</td>
                        <td class="text-right py-2">{{ $genderCounts['Other'] ?? 0 }}</td>
                        <td class="text-right py-2">{{ array_sum($genderCounts) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($data['detailed_list']) && count($data['detailed_list']) > 0)
    <div class="bg-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-white mb-4">Detailed Patient List</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-300">
                <thead class="border-b border-gray-600">
                    <tr>
                        <th class="text-left py-2">Name</th>
                        <th class="text-left py-2">Gender</th>
                        <th class="text-left py-2">Education Level</th>
                        <th class="text-left py-2">Registered On</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['detailed_list'] as $patient)
                    <tr class="border-b border-gray-600 hover:bg-gray-600">
                        <td class="py-2">{{ $patient->user->name ?? 'N/A' }}</td>
                        <td class="py-2">{{ $patient->user->gender === 'M' ? 'Male' : ($patient->user->gender === 'F' ? 'Female' : ucfirst($patient->user->gender ?? 'N/A')) }}</td>
                        <td class="py-2">{{ $patient->educationalLevel->level_name ?? 'N/A' }}</td>
                        <td class="py-2">{{ optional($patient->created_at)->format('M d, Y') ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
