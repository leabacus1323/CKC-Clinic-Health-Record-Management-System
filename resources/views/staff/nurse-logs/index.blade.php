@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 rounded border-l-4 border-teal-500 bg-teal-50 p-4">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="font-semibold text-teal-900">Staff Duties Mode</p>
                <p class="text-sm text-teal-800">Record daily clinic visits, time in/out, complaints, and interventions.</p>
            </div>
            <a href="{{ route('staff.nurse-logs.create') }}" class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">Add Nurse Log</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">{{ session('success') }}</div>
    @endif

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-5">
        <div class="rounded-lg border bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Today's Visits</p><p class="text-3xl font-bold text-teal-700">{{ $summary['today'] }}</p></div>
        <div class="rounded-lg border bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Currently In Clinic</p><p class="text-3xl font-bold text-orange-600">{{ $summary['open'] }}</p></div>
        <div class="rounded-lg border bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Medicine Given Today</p><p class="text-3xl font-bold text-blue-700">{{ $summary['medicine'] }}</p></div>
        <div class="rounded-lg border bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">MGH Today</p><p class="text-3xl font-bold text-purple-700">{{ $summary['mgh'] }}</p></div>
        <div class="rounded-lg border bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Beds Available</p><p class="text-3xl font-bold text-green-700">{{ $bedSummary['available'] }}/{{ $bedSummary['total'] }}</p></div>
    </div>


    <div class="mb-6 rounded-lg border bg-white p-5 shadow-sm">
        <div class="mb-3 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Bed Availability</h2>
                <p class="text-sm text-gray-600">Beds are reserved automatically when the intervention is Rest and released after Record Time Out.</p>
            </div>
            <span class="rounded-full bg-green-50 px-3 py-1 text-sm font-semibold text-green-700">{{ $bedSummary['available'] }} available</span>
        </div>
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            @forelse($beds as $bed)
                @php($occupiedLog = $bed->activeRestLog)
                <div class="rounded-lg border p-4 {{ $occupiedLog ? 'border-orange-200 bg-orange-50' : 'border-green-200 bg-green-50' }}">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-gray-900">{{ $bed->name }}</p>
                        <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $occupiedLog ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700' }}">
                            {{ $occupiedLog ? 'Occupied' : 'Available' }}
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-gray-600">{{ $bed->location ?? 'Clinic Rest Area' }}</p>
                    @if($occupiedLog)
                        <p class="mt-2 text-xs text-gray-700">Patient: {{ $occupiedLog->patient?->user?->name ?? 'Patient #' . $occupiedLog->patient_id }}</p>
                        <p class="text-xs text-gray-500">Since {{ $occupiedLog->time_in?->format('h:i A') }}</p>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-500">No clinic beds configured yet. Run the NurseBedSeeder after migration.</p>
            @endforelse
        </div>
    </div>

    <div class="mb-6 rounded-lg border bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('staff.nurse-logs.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search patient or school ID" class="rounded-lg border-gray-300 text-sm focus:border-teal-500 focus:ring-teal-500">
            <select name="intervention_type" class="rounded-lg border-gray-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                <option value="">All interventions</option>
                <option value="prescribed_medicine" @selected(request('intervention_type') === 'prescribed_medicine')>Prescribed Medicine</option>
                <option value="mgh" @selected(request('intervention_type') === 'mgh')>MGH / May Go Home</option>
                <option value="rest" @selected(request('intervention_type') === 'rest')>Rest</option>
                <option value="first_aid" @selected(request('intervention_type') === 'first_aid')>First Aid</option>
                <option value="other" @selected(request('intervention_type') === 'other')>Other</option>
            </select>
            <input type="date" name="date" value="{{ request('date') }}" class="rounded-lg border-gray-300 text-sm focus:border-teal-500 focus:ring-teal-500">
            <div class="flex gap-2">
                <button type="submit" class="flex-1 rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">Filter</button>
                <a href="{{ route('staff.nurse-logs.index') }}" class="rounded-lg border px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Reset</a>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-lg border bg-white shadow-sm">
        <div class="border-b px-6 py-4">
            <h1 class="text-2xl font-semibold text-gray-800">Nurse Logs</h1>
            <p class="text-sm text-gray-600">Medicine interventions create prescription records and reduce medicine stock automatically.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Patient</th>
                        <th class="px-4 py-3 text-left">Time In</th>
                        <th class="px-4 py-3 text-left">Time Out</th>
                        <th class="px-4 py-3 text-left">Reason / Complaint</th>
                        <th class="px-4 py-3 text-left">Intervention</th>
                        <th class="px-4 py-3 text-left">Medicine / Prescription</th>
                        <th class="px-4 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($logs as $log)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">{{ $log->patient?->user?->name ?? 'Patient #' . $log->patient_id }}</div>
                                <div class="text-xs text-gray-500">{{ $log->patient?->school_id }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $log->time_in?->format('M d, Y h:i A') }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $log->time_out?->format('M d, Y h:i A') ?? 'Still in clinic' }}</td>
                            <td class="max-w-xs px-4 py-3 text-gray-700">{{ Str::limit($log->complaint, 90) }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-teal-50 px-2 py-1 text-xs font-semibold text-teal-700">{{ $log->intervention_label }}</span>
                                @if($log->interventions->where('intervention_type', 'rest')->first()?->rest_minutes ?? ($log->intervention_type === 'rest' && $log->rest_minutes))
                                    <div class="mt-1 text-xs text-gray-500">Rest: {{ $log->interventions->where('intervention_type', 'rest')->first()?->rest_minutes ?? $log->rest_minutes }} minutes</div>
                                @endif
                                @if(($log->interventions->where('intervention_type', 'rest')->first()?->bed) || ($log->intervention_type === 'rest' && $log->bed))
                                    <div class="mt-1 text-xs font-semibold text-orange-700">Bed: {{ $log->interventions->where('intervention_type', 'rest')->first()?->bed?->name ?? $log->bed->name }}</div>
                                @endif
                                @if(($log->interventions->where('intervention_type', 'mgh')->first()?->mgh_status) || ($log->intervention_type === 'mgh' && $log->mgh_status))
                                    <div class="mt-1 text-xs text-gray-500">{{ $log->interventions->where('intervention_type', 'mgh')->first()?->mgh_status ?? $log->mgh_status }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                @php($medicineInterventions = $log->interventions->where('intervention_type', 'prescribed_medicine'))
                                @if($medicineInterventions->isNotEmpty())
                                    @foreach($medicineInterventions as $intervention)
                                        <div>{{ $intervention->medicine?->name ?? 'Medicine' }} × {{ $intervention->medicine_quantity }}</div>
                                        @if($intervention->prescription_record_id)
                                            <div class="text-xs text-blue-600">Prescription #{{ $intervention->prescription_record_id }}</div>
                                        @endif
                                    @endforeach
                                @elseif($log->medicine)
                                    <div>{{ $log->medicine->name }} × {{ $log->medicine_quantity }}</div>
                                    <div class="text-xs text-blue-600">Prescription #{{ $log->prescription_record_id }}</div>
                                @else
                                    <span class="text-gray-400">Not applicable</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($log->time_out === null)
                                    <form method="POST" action="{{ route('staff.nurse-logs.checkout', $log) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="rounded bg-orange-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-orange-700">Record Time Out</button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">Closed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-gray-500">No nurse logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t px-6 py-4">{{ $logs->links() }}</div>
    </div>
</div>
@endsection
