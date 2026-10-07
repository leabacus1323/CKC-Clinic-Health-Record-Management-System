<div class="space-y-6">
    <h2 class="text-xl font-semibold text-white">Appointment Statistics</h2>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gray-700 rounded-lg p-4">
            <div class="text-gray-400 text-sm">Total Appointments</div>
            <div class="text-3xl font-bold text-white mt-2">{{ $data['total_appointments'] ?? 0 }}</div>
        </div>
        <div class="bg-gray-700 rounded-lg p-4">
            <div class="text-gray-400 text-sm">Pending</div>
            <div class="text-3xl font-bold text-yellow-400 mt-2">{{ $data['pending'] ?? 0 }}</div>
        </div>
        <div class="bg-gray-700 rounded-lg p-4">
            <div class="text-gray-400 text-sm">Completed</div>
            <div class="text-3xl font-bold text-green-400 mt-2">{{ $data['completed'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Status Distribution -->
    @if(isset($data['by_status']) && count($data['by_status']) > 0)
    <div class="bg-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-white mb-4">Appointments by Status</h3>
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

    <!-- Monthly Trends -->
    @if(isset($data['by_month']) && count($data['by_month']) > 0)
    <div class="bg-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-white mb-4">Monthly Trends</h3>
        <table class="w-full text-gray-300">
            <thead class="border-b border-gray-600">
                <tr>
                    <th class="text-left py-2">Month</th>
                    <th class="text-right py-2">Appointments</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['by_month'] as $item)
                <tr class="border-b border-gray-600">
                    <td class="py-2">{{ $item->month }}</td>
                    <td class="text-right py-2">{{ $item->count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Detailed Appointments -->
    @if(isset($data['recent_appointments']) && count($data['recent_appointments']) > 0)
    <div class="bg-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-white mb-4">Detailed Appointments</h3>
        <table class="w-full text-gray-300">
            <thead class="border-b border-gray-600">
                <tr>
                    <th class="text-left py-2 px-2">Date</th>
                    <th class="text-left py-2 px-2">Time</th>
                    <th class="text-left py-2 px-2">Patient</th>
                    <th class="text-left py-2 px-2">Doctor</th>
                    <th class="text-left py-2 px-2">Status</th>
                    <th class="text-left py-2 px-2">Reason</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['recent_appointments'] as $appointment)
                <tr class="border-b border-gray-600 hover:bg-gray-600">
                    <td class="py-2 px-2">{{ $appointment->date ? \Carbon\Carbon::parse($appointment->date)->format('M d, Y') : 'N/A' }}</td>
                    <td class="py-2 px-2">{{ $appointment->time ? \Carbon\Carbon::parse($appointment->time)->format('h:i A') : 'N/A' }}</td>
                    <td class="py-2 px-2">
                        @if($appointment->patient && $appointment->patient->user)
                            <div class="font-medium">{{ $appointment->patient->user->name }}</div>
                            <div class="text-xs text-gray-400">{{ $appointment->patient->user->email }}</div>
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="py-2 px-2">
                        @if($appointment->doctor && $appointment->doctor->user)
                            Dr. {{ $appointment->doctor->user->name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="py-2 px-2">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($appointment->status === 'Pending') bg-yellow-100 text-yellow-800
                            @elseif($appointment->status === 'Confirmed') bg-blue-100 text-blue-800
                            @elseif($appointment->status === 'Completed') bg-green-100 text-green-800
                            @elseif($appointment->status === 'Cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $appointment->status ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="py-2 px-2">{{ \Illuminate\Support\Str::limit($appointment->reason ?? 'N/A', 50) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Pending Appointments -->
    @if(isset($data['pending_appointments']) && count($data['pending_appointments']) > 0)
    <div class="bg-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-white mb-4">Pending Appointments</h3>
        <table class="w-full text-gray-300">
            <thead class="border-b border-gray-600">
                <tr>
                    <th class="text-left py-2 px-2">Date</th>
                    <th class="text-left py-2 px-2">Time</th>
                    <th class="text-left py-2 px-2">Patient</th>
                    <th class="text-left py-2 px-2">Doctor</th>
                    <th class="text-left py-2 px-2">Reason</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['pending_appointments'] as $appointment)
                <tr class="border-b border-gray-600 hover:bg-gray-600">
                    <td class="py-2 px-2">{{ $appointment->date ? \Carbon\Carbon::parse($appointment->date)->format('M d, Y') : 'N/A' }}</td>
                    <td class="py-2 px-2">{{ $appointment->time ? \Carbon\Carbon::parse($appointment->time)->format('h:i A') : 'N/A' }}</td>
                    <td class="py-2 px-2">
                        @if($appointment->patient && $appointment->patient->user)
                            {{ $appointment->patient->user->name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="py-2 px-2">
                        @if($appointment->doctor && $appointment->doctor->user)
                            Dr. {{ $appointment->doctor->user->name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="py-2 px-2">{{ \Illuminate\Support\Str::limit($appointment->reason ?? 'N/A', 50) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Completed Appointments -->
    @if(isset($data['completed_appointments']) && count($data['completed_appointments']) > 0)
    <div class="bg-gray-700 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-white mb-4">Completed Appointments</h3>
        <table class="w-full text-gray-300">
            <thead class="border-b border-gray-600">
                <tr>
                    <th class="text-left py-2 px-2">Date</th>
                    <th class="text-left py-2 px-2">Time</th>
                    <th class="text-left py-2 px-2">Patient</th>
                    <th class="text-left py-2 px-2">Doctor</th>
                    <th class="text-left py-2 px-2">Reason</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['completed_appointments'] as $appointment)
                <tr class="border-b border-gray-600 hover:bg-gray-600">
                    <td class="py-2 px-2">{{ $appointment->date ? \Carbon\Carbon::parse($appointment->date)->format('M d, Y') : 'N/A' }}</td>
                    <td class="py-2 px-2">{{ $appointment->time ? \Carbon\Carbon::parse($appointment->time)->format('h:i A') : 'N/A' }}</td>
                    <td class="py-2 px-2">
                        @if($appointment->patient && $appointment->patient->user)
                            {{ $appointment->patient->user->name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="py-2 px-2">
                        @if($appointment->doctor && $appointment->doctor->user)
                            Dr. {{ $appointment->doctor->user->name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="py-2 px-2">{{ \Illuminate\Support\Str::limit($appointment->reason ?? 'N/A', 50) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
