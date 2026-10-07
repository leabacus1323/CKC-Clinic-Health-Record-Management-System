<div class="space-y-6">
    <h2 class="text-xl font-semibold text-gray-800">Health Records Summary</h2>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <div class="text-blue-600 text-sm">Total Records</div>
            <div class="text-3xl font-bold text-blue-700 mt-2">{{ $data['total_records'] ?? 0 }}</div>
        </div>
        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <div class="text-green-600 text-sm">With Diagnosis</div>
            <div class="text-3xl font-bold text-green-700 mt-2">{{ $data['records_with_diagnosis'] ?? 0 }}</div>
        </div>
        <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
            <div class="text-purple-600 text-sm">Top Condition</div>
            <div class="text-xl font-bold text-purple-700 mt-2">
                {{ $data['top_conditions'][0]->diagnosis ?? 'None' }}
                @if(isset($data['top_conditions'][0]))
                    <span class="text-sm text-purple-500">({{ $data['top_conditions'][0]->count }} cases)</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Top Conditions -->
    @if(isset($data['top_conditions']) && count($data['top_conditions']) > 0)
    <div class="bg-gray-50 rounded-lg p-4 border">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Conditions</h3>
        <table class="w-full text-gray-600">
            <thead class="border-b border-gray-300">
                <tr>
                    <th class="text-left py-2">Condition</th>
                    <th class="text-right py-2">Cases</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['top_conditions'] as $condition)
                <tr class="border-b border-gray-200">
                    <td class="py-2">{{ $condition->diagnosis }}</td>
                    <td class="text-right py-2">{{ $condition->count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Detailed Health Records -->
    @if(isset($data['recent_records']) && count($data['recent_records']) > 0)
    <div class="bg-gray-50 rounded-lg p-4 border">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Health Records</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-600">
                <thead class="border-b border-gray-300">
                    <tr>
                        <th class="text-left py-2 px-2">Date</th>
                        <th class="text-left py-2 px-2">Patient</th>
                        <th class="text-left py-2 px-2">Diagnosis</th>
                        <th class="text-left py-2 px-2">Symptoms</th>
                        <th class="text-left py-2 px-2">Treatment</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['recent_records'] as $record)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-2 px-2">{{ $record->created_at->format('M d, Y') }}</td>
                        <td class="py-2 px-2">
                            @if($record->patient && $record->patient->user)
                                <div class="font-medium">{{ $record->patient->user->name }}</div>
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="py-2 px-2">{{ $record->diagnosis ?? 'N/A' }}</td>
                        <td class="py-2 px-2">{{ \Illuminate\Support\Str::limit($record->symptoms ?? 'N/A', 50) }}</td>
                        <td class="py-2 px-2">{{ \Illuminate\Support\Str::limit($record->treatment ?? 'N/A', 50) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Physical Examinations Detail -->
    @if(isset($data['physical_examinations']) && count($data['physical_examinations']) > 0)
    <div class="bg-gray-50 rounded-lg p-4 border">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Physical Examinations</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-600">
                <thead class="border-b border-gray-300">
                    <tr>
                        <th class="text-left py-2 px-2">Date</th>
                        <th class="text-left py-2 px-2">Patient</th>
                        <th class="text-left py-2 px-2">Height</th>
                        <th class="text-left py-2 px-2">Weight</th>
                        <th class="text-left py-2 px-2">BP</th>
                        <th class="text-left py-2 px-2">Heart</th>
                        <th class="text-left py-2 px-2">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['physical_examinations'] as $exam)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-2 px-2">{{ $exam->created_at->format('M d, Y') }}</td>
                        <td class="py-2 px-2">
                            @if($exam->patient && $exam->patient->user)
                                {{ $exam->patient->user->name }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="py-2 px-2">{{ $exam->height ?? 'N/A' }}</td>
                        <td class="py-2 px-2">{{ $exam->weight ?? 'N/A' }}</td>
                        <td class="py-2 px-2">{{ $exam->bp ?? 'N/A' }}</td>
                        <td class="py-2 px-2">{{ $exam->heart ?? 'N/A' }}</td>
                        <td class="py-2 px-2">{{ \Illuminate\Support\Str::limit($exam->remarks ?? 'N/A', 40) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Dental Examinations Detail -->
    @if(isset($data['dental_examinations']) && count($data['dental_examinations']) > 0)
    <div class="bg-gray-50 rounded-lg p-4 border">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Dental Examinations</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-600">
                <thead class="border-b border-gray-300">
                    <tr>
                        <th class="text-left py-2 px-2">Date</th>
                        <th class="text-left py-2 px-2">Patient</th>
                        <th class="text-left py-2 px-2">Doctor</th>
                        <th class="text-left py-2 px-2">Diagnosis</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['dental_examinations'] as $exam)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-2 px-2">{{ $exam->created_at->format('M d, Y') }}</td>
                        <td class="py-2 px-2">
                            @if($exam->patient && $exam->patient->user)
                                {{ $exam->patient->user->name }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="py-2 px-2">
                            @if($exam->doctor && $exam->doctor->user)
                                Dr. {{ $exam->doctor->user->name }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="py-2 px-2">{{ \Illuminate\Support\Str::limit($exam->diagnosis ?? 'N/A', 50) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Immunization Records Detail -->
    @if(isset($data['immunization_records']) && count($data['immunization_records']) > 0)
    <div class="bg-gray-50 rounded-lg p-4 border">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Immunization Records</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-600">
                <thead class="border-b border-gray-300">
                    <tr>
                        <th class="text-left py-2 px-2">Date</th>
                        <th class="text-left py-2 px-2">Patient</th>
                        <th class="text-left py-2 px-2">Vaccine</th>
                        <th class="text-left py-2 px-2">Dosage</th>
                        <th class="text-left py-2 px-2">Administered By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['immunization_records'] as $record)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-2 px-2">{{ $record->created_at->format('M d, Y') }}</td>
                        <td class="py-2 px-2">
                            @if($record->patient && $record->patient->user)
                                {{ $record->patient->user->name }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="py-2 px-2">{{ $record->vaccine_name ?? 'N/A' }}</td>
                        <td class="py-2 px-2">{{ $record->dosage ?? 'N/A' }}</td>
                        <td class="py-2 px-2">{{ $record->administered_by ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Prescriptions Detail -->
    @if(isset($data['prescriptions_detail']) && count($data['prescriptions_detail']) > 0)
    <div class="bg-gray-50 rounded-lg p-4 border">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Prescriptions</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-600">
                <thead class="border-b border-gray-300">
                    <tr>
                        <th class="text-left py-2 px-2">Date</th>
                        <th class="text-left py-2 px-2">Patient</th>
                        <th class="text-left py-2 px-2">Medicine</th>
                        <th class="text-left py-2 px-2">Dosage</th>
                        <th class="text-left py-2 px-2">Frequency</th>
                        <th class="text-left py-2 px-2">Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['prescriptions_detail'] as $prescription)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-2 px-2">{{ $prescription->created_at->format('M d, Y') }}</td>
                        <td class="py-2 px-2">
                            @if($prescription->patient && $prescription->patient->user)
                                {{ $prescription->patient->user->name }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="py-2 px-2">{{ $prescription->medicine->name ?? 'N/A' }}</td>
                        <td class="py-2 px-2">{{ $prescription->dosage ?? 'N/A' }}</td>
                        <td class="py-2 px-2">{{ $prescription->frequency ?? 'N/A' }}</td>
                        <td class="py-2 px-2">{{ $prescription->duration ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
