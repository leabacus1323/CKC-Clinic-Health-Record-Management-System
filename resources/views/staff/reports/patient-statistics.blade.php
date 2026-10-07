<div class="space-y-6">
    <h2 class="text-xl font-semibold text-white">Patient Statistics</h2>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gray-700 rounded-lg p-4">
            <div class="text-gray-400 text-sm">Total Patients</div>
            <div class="text-3xl font-bold text-white mt-2">{{ $data['total_patients'] ?? 0 }}</div>
        </div>
        <div class="bg-gray-700 rounded-lg p-4">
            <div class="text-gray-400 text-sm">New Patients (Period)</div>
            <div class="text-3xl font-bold text-green-400 mt-2">{{ $data['new_patients'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Gender Distribution -->
    @if(isset($data['gender_distribution']) && count($data['gender_distribution']) > 0)
    <div class="bg-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-white mb-4">Gender Distribution</h3>
        <table class="w-full text-gray-300">
            <thead class="border-b border-gray-600">
                <tr>
                    <th class="text-left py-2">Gender</th>
                    <th class="text-right py-2">Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['gender_distribution'] as $item)
                <tr class="border-b border-gray-600">
                    <td class="py-2">{{ $item->gender === 'M' ? 'Male' : 'Female' }}</td>
                    <td class="text-right py-2">{{ $item->count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Educational Level Distribution -->
    @if(isset($data['by_educational_level']) && count($data['by_educational_level']) > 0)
    <div class="bg-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-white mb-4">Distribution by Educational Level</h3>
        <table class="w-full text-gray-300">
            <thead class="border-b border-gray-600">
                <tr>
                    <th class="text-left py-2">Educational Level</th>
                    <th class="text-right py-2">Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['by_educational_level'] as $item)
                <tr class="border-b border-gray-600">
                    <td class="py-2">{{ $item->level_name }}</td>
                    <td class="text-right py-2">{{ $item->count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Recent Patients Detail -->
    @if(isset($data['recent_patients']) && count($data['recent_patients']) > 0)
    <div class="bg-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-white mb-4">Recent Patients (This Period)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-300">
                <thead class="border-b border-gray-600">
                    <tr>
                        <th class="text-left py-2">Date Registered</th>
                        <th class="text-left py-2">Name</th>
                        <th class="text-left py-2">Gender</th>
                        <th class="text-left py-2">Email</th>
                        <th class="text-left py-2">Level</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['recent_patients'] as $patient)
                    <tr class="border-b border-gray-600 hover:bg-gray-600">
                        <td class="py-2">{{ $patient->created_at->format('M d, Y') }}</td>
                        <td class="py-2">{{ $patient->user->name ?? 'N/A' }}</td>
                        <td class="py-2">
                            @if($patient->user && $patient->user->gender)
                                {{ $patient->user->gender === 'M' ? 'Male' : 'Female' }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="py-2">{{ $patient->user->email ?? 'N/A' }}</td>
                        <td class="py-2">{{ $patient->educationalLevel->level_name ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- All Patients Detail -->
    @if(isset($data['all_patients']) && count($data['all_patients']) > 0)
    <div class="bg-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-white mb-4">All Patients</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-300">
                <thead class="border-b border-gray-600">
                    <tr>
                        <th class="text-left py-2">Date Registered</th>
                        <th class="text-left py-2">Name</th>
                        <th class="text-left py-2">Gender</th>
                        <th class="text-left py-2">Email</th>
                        <th class="text-left py-2">Contact</th>
                        <th class="text-left py-2">Level</th>
                        <th class="text-left py-2">Blood Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['all_patients'] as $patient)
                    <tr class="border-b border-gray-600 hover:bg-gray-600">
                        <td class="py-2">{{ $patient->created_at->format('M d, Y') }}</td>
                        <td class="py-2">{{ $patient->user->name ?? 'N/A' }}</td>
                        <td class="py-2">
                            @if($patient->user && $patient->user->gender)
                                {{ $patient->user->gender === 'M' ? 'Male' : 'Female' }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="py-2">{{ $patient->user->email ?? 'N/A' }}</td>
                        <td class="py-2">{{ $patient->user->contact_no ?? 'N/A' }}</td>
                        <td class="py-2">{{ $patient->educationalLevel->level_name ?? 'N/A' }}</td>
                        <td class="py-2">{{ $patient->bloodtype ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
