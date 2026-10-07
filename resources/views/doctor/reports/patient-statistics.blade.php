<div class="space-y-6">
    <h2 class="text-xl font-semibold text-gray-800">Patient Statistics</h2>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <div class="text-blue-600 text-sm">Total Patients</div>
            <div class="text-3xl font-bold text-blue-700 mt-2">{{ $data['total_patients'] ?? 0 }}</div>
        </div>
        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <div class="text-green-600 text-sm">New Patients (Period)</div>
            <div class="text-3xl font-bold text-green-700 mt-2">{{ $data['new_patients'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Gender Distribution -->
    @if(isset($data['gender_distribution']) && count($data['gender_distribution']) > 0)
    <div class="bg-gray-50 rounded-lg p-4 border">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Gender Distribution</h3>
        <table class="w-full text-gray-600">
            <thead class="border-b border-gray-300">
                <tr>
                    <th class="text-left py-2">Gender</th>
                    <th class="text-right py-2">Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['gender_distribution'] as $item)
                <tr class="border-b border-gray-200">
                    <td class="py-2">{{ $item->gender === 'M' ? 'Male' : 'Female' }}</td>
                    <td class="text-right py-2">{{ $item->count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Monthly Trend -->
    @if(isset($data['monthly_trend']) && count($data['monthly_trend']) > 0)
    <div class="bg-gray-50 rounded-lg p-4 border">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Monthly Trend</h3>
        <table class="w-full text-gray-600">
            <thead class="border-b border-gray-300">
                <tr>
                    <th class="text-left py-2">Month</th>
                    <th class="text-left py-2">Year</th>
                    <th class="text-right py-2">Appointments</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['monthly_trend'] as $item)
                <tr class="border-b border-gray-200">
                    <td class="py-2">{{ date('F', mktime(0, 0, 0, $item->month, 1)) }}</td>
                    <td class="py-2">{{ $item->year }}</td>
                    <td class="text-right py-2">{{ $item->count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Recent Patients Detail -->
    @if(isset($data['recent_patients']) && count($data['recent_patients']) > 0)
    <div class="bg-gray-50 rounded-lg p-4 border">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Patients (This Period)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-600">
                <thead class="border-b border-gray-300">
                    <tr>
                        <th class="text-left py-2">Date</th>
                        <th class="text-left py-2">Patient Name</th>
                        <th class="text-left py-2">Gender</th>
                        <th class="text-left py-2">Level</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['recent_patients'] as $appointment)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-2">{{ \Carbon\Carbon::parse($appointment->date)->format('M d, Y') }}</td>
                        <td class="py-2">{{ $appointment->patient->user->name ?? 'N/A' }}</td>
                        <td class="py-2">
                            @if($appointment->patient->user && $appointment->patient->user->gender)
                                {{ $appointment->patient->user->gender === 'M' ? 'Male' : 'Female' }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="py-2">{{ $appointment->patient->educationalLevel->level_name ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- All Patients Detail -->
    @if(isset($data['all_patients']) && count($data['all_patients']) > 0)
    <div class="bg-gray-50 rounded-lg p-4 border">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">All Patients</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-600">
                <thead class="border-b border-gray-300">
                    <tr>
                        <th class="text-left py-2">Last Visit</th>
                        <th class="text-left py-2">Patient Name</th>
                        <th class="text-left py-2">Gender</th>
                        <th class="text-left py-2">Email</th>
                        <th class="text-left py-2">Level</th>
                        <th class="text-left py-2">Blood Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['all_patients'] as $appointment)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-2">{{ \Carbon\Carbon::parse($appointment->date)->format('M d, Y') }}</td>
                        <td class="py-2">{{ $appointment->patient->user->name ?? 'N/A' }}</td>
                        <td class="py-2">
                            @if($appointment->patient->user && $appointment->patient->user->gender)
                                {{ $appointment->patient->user->gender === 'M' ? 'Male' : 'Female' }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="py-2">{{ $appointment->patient->user->email ?? 'N/A' }}</td>
                        <td class="py-2">{{ $appointment->patient->educationalLevel->level_name ?? 'N/A' }}</td>
                        <td class="py-2">{{ $appointment->patient->bloodtype ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
