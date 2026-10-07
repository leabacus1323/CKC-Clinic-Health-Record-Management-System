<div class="space-y-6">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Nurse Logs</h2>
            <p class="mt-1 text-sm text-gray-600">Quick view of recent patient clinic visits and interventions.</p>
        </div>
        <a href="{{ route('staff.nurse-logs.create') }}" class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">Add Nurse Log</a>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
        <div class="rounded-lg border bg-white p-4 shadow-sm"><p class="text-sm text-gray-500">Today's Visits</p><p class="text-2xl font-bold text-teal-700">{{ $summary['today'] }}</p></div>
        <div class="rounded-lg border bg-white p-4 shadow-sm"><p class="text-sm text-gray-500">Currently In Clinic</p><p class="text-2xl font-bold text-orange-600">{{ $summary['open'] }}</p></div>
        <div class="rounded-lg border bg-white p-4 shadow-sm"><p class="text-sm text-gray-500">Medicine Given</p><p class="text-2xl font-bold text-blue-700">{{ $summary['medicine'] }}</p></div>
        <div class="rounded-lg border bg-white p-4 shadow-sm"><p class="text-sm text-gray-500">MGH Today</p><p class="text-2xl font-bold text-purple-700">{{ $summary['mgh'] }}</p></div>
        <div class="rounded-lg border bg-white p-4 shadow-sm"><p class="text-sm text-gray-500">Beds Available</p><p class="text-2xl font-bold text-green-700">{{ $bedSummary['available'] }}/{{ $bedSummary['total'] }}</p></div>
    </div>

    <div class="overflow-hidden rounded-lg border bg-white shadow-sm">
        <div class="flex items-center justify-between border-b px-5 py-3">
            <h3 class="font-semibold text-gray-800">Recent Nurse Logs</h3>
            <a href="{{ route('staff.nurse-logs.index') }}" class="text-sm font-semibold text-teal-700 hover:text-teal-900">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Patient</th>
                        <th class="px-4 py-3 text-left">Time In</th>
                        <th class="px-4 py-3 text-left">Intervention</th>
                        <th class="px-4 py-3 text-left">Medicine / Bed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $log->patient?->user?->name ?? 'Patient #' . $log->patient_id }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $log->time_in?->format('M d, h:i A') }}</td>
                            <td class="px-4 py-3"><span class="rounded-full bg-teal-50 px-2 py-1 text-xs font-semibold text-teal-700">{{ $log->intervention_label }}</span></td>
                            <td class="px-4 py-3 text-gray-700">
                                @php($medicineInterventions = $log->interventions->where('intervention_type', 'prescribed_medicine'))
                                @php($restIntervention = $log->interventions->where('intervention_type', 'rest')->first())
                                @if($medicineInterventions->isNotEmpty())
                                    @foreach($medicineInterventions as $intervention)
                                        <div>{{ $intervention->medicine?->name ?? 'Medicine' }} × {{ $intervention->medicine_quantity }}</div>
                                    @endforeach
                                @elseif($log->medicine)
                                    {{ $log->medicine->name }}
                                @elseif($restIntervention?->bed)
                                    {{ $restIntervention->bed->name }}
                                @elseif($log->bed)
                                    {{ $log->bed->name }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No nurse logs recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
