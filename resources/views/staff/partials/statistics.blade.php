<!-- Statistics Partial -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Patient Statistics</h2>
    <p class="text-gray-600">View patients by course and educational level</p>
</div>

<!-- Statistics by Course and Educational Level -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- By Course -->
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Patients by Course</h3>
        @if($byCourse->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-3">Course Name</th>
                            <th class="px-4 py-3 text-center">Total</th>
                            <th class="px-4 py-3 text-center">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byCourse as $course)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('staff.statistics.course', $course->id) }}" class="font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $course->course_name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-center">{{ $course->total_patients }}</td>
                            <td class="px-4 py-3 text-center">{{ $totalPatients > 0 ? round(($course->total_patients / $totalPatients) * 100, 1) : 0 }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No course data available.</p>
        @endif
    </div>

    <!-- By Educational Level -->
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Patients by Educational Level</h3>
        @if($byEducationalLevel->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-3">Level Name</th>
                            <th class="px-4 py-3 text-center">Total</th>
                            <th class="px-4 py-3 text-center">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byEducationalLevel as $level)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('staff.statistics.level', $level->id) }}" class="font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $level->level_name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-center">{{ $level->total_patients }}</td>
                            <td class="px-4 py-3 text-center">{{ $totalPatients > 0 ? round(($level->total_patients / $totalPatients) * 100, 1) : 0 }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-4">No educational level data available.</p>
        @endif
    </div>
</div>
