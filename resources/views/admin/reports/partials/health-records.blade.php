<div class="space-y-6">
    <h2 class="text-xl font-semibold text-gray-800">Health Records Summary</h2>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <div class="text-blue-600 text-sm">Total Records</div>
            <div class="text-3xl font-bold text-blue-700 mt-2">{{ $data['total_records'] ?? 0 }}</div>
        </div>
        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <div class="text-green-600 text-sm">Physical Exams</div>
            <div class="text-3xl font-bold text-green-700 mt-2">{{ $data['physical_exams'] ?? 0 }}</div>
        </div>
        <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
            <div class="text-purple-600 text-sm">Dental Exams</div>
            <div class="text-3xl font-bold text-purple-700 mt-2">{{ $data['dental_exams'] ?? 0 }}</div>
        </div>
        <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
            <div class="text-orange-600 text-sm">Immunizations</div>
            <div class="text-3xl font-bold text-orange-700 mt-2">{{ $data['immunizations'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Top Diagnoses -->
    @if(isset($data['top_diagnoses']) && count($data['top_diagnoses']) > 0)
    <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Diagnoses</h3>
        <table class="w-full text-gray-600">
            <thead class="border-b border-gray-200">
                <tr>
                    <th class="text-left py-2">Diagnosis</th>
                    <th class="text-right py-2">Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['top_diagnoses'] as $item)
                <tr class="border-b border-gray-100">
                    <td class="py-2">{{ $item->diagnosis ?? 'N/A' }}</td>
                    <td class="text-right py-2">{{ $item->count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Detailed Health Records -->
    @if(isset($data['recent_records']) && count($data['recent_records']) > 0)
    <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Health Records</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-200">
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
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-2 px-2">{{ $record->created_at->format('M d, Y') }}</td>
                        <td class="py-2 px-2">
                            @if($record->patient && $record->patient->user)
                                <div class="font-medium text-gray-800">{{ $record->patient->user->name }}</div>
                            @else
                                <span class="text-gray-400">N/A</span>
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
    <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Physical Examinations</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-200">
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
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-2 px-2">{{ $exam->created_at->format('M d, Y') }}</td>
                        <td class="py-2 px-2">
                            @if($exam->patient && $exam->patient->user)
                                <span class="font-medium text-gray-800">{{ $exam->patient->user->name }}</span>
                            @else
                                <span class="text-gray-400">N/A</span>
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
    <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Dental Examinations</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left py-2 px-2">Date</th>
                        <th class="text-left py-2 px-2">Patient</th>
                        <th class="text-left py-2 px-2">Doctor</th>
                        <th class="text-left py-2 px-2">Diagnosis</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['dental_examinations'] as $exam)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-2 px-2">{{ $exam->created_at->format('M d, Y') }}</td>
                        <td class="py-2 px-2">
                            @if($exam->patient && $exam->patient->user)
                                <span class="font-medium text-gray-800">{{ $exam->patient->user->name }}</span>
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="py-2 px-2">
                            @if($exam->doctor && $exam->doctor->user)
                                <span class="text-green-600">Dr. {{ $exam->doctor->user->name }}</span>
                            @else
                                <span class="text-gray-400">N/A</span>
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
    <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Immunization Records</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-200">
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
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-2 px-2">{{ $record->created_at->format('M d, Y') }}</td>
                        <td class="py-2 px-2">
                            @if($record->patient && $record->patient->user)
                                <span class="font-medium text-gray-800">{{ $record->patient->user->name }}</span>
                            @else
                                <span class="text-gray-400">N/A</span>
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
</div>