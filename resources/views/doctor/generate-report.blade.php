@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Generate Reports</h1>
    </div>

    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <!-- Report Generation Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8 border">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Generate New Report</h2>

        <form action="{{ route('doctor.reports.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Report Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Report Title *</label>
                    <input type="text"
                           id="title"
                           name="title"
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="e.g., Monthly Patient Statistics Report"
                           required>
                </div>

                <!-- Report Type -->
                <div>
                    <label for="report_type" class="block text-sm font-medium text-gray-700 mb-2">Report Type *</label>
                    <select id="report_type"
                            name="report_type"
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                        <option value="">Select Report Type</option>
                        <option value="patient_statistics">Patient Statistics</option>
                        <option value="appointment_statistics">Appointment Statistics</option>
                        <option value="health_records">Health Records Summary</option>
                        <option value="prescriptions">Prescription Report</option>
                        <option value="medicine_inventory">Medicine Inventory</option>
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date"
                           id="date_from"
                           name="date_from"
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           value="{{ now()->subYear()->format('Y-m-d') }}">
                </div>

                <!-- Date To -->
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date"
                           id="date_to"
                           name="date_to"
                           class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           value="{{ now()->format('Y-m-d') }}">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Add any notes or description for this report"></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-500 focus:ring-opacity-50">
                    Generate Report
                </button>
            </div>
        </form>
    </div>

    <!-- Saved Reports List -->
    <div class="bg-white rounded-lg shadow-md p-6 border">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Previously Generated Reports</h2>

        @if($savedReports->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left text-gray-600">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Date Range</th>
                        <th class="px-6 py-3">Generated On</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($savedReports as $report)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $report->title }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">
                                {{ ucwords(str_replace('_', ' ', $report->report_type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            {{ $report->parameters['date_from'] ?? 'N/A' }} to {{ $report->parameters['date_to'] ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">{{ $report->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('doctor.reports.view', $report->id) }}"
                                   class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                    View
                                </a>
                                <a href="{{ route('doctor.reports.export', ['id' => $report->id, 'format' => 'csv']) }}"
                                   class="px-3 py-1 bg-purple-600 text-white text-sm rounded hover:bg-purple-700">
                                    Export CSV
                                </a>
                                <form action="{{ route('doctor.reports.delete', $report->id) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this report?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 text-center py-8">No reports generated yet. Create your first report above!</p>
        @endif
    </div>
</div>

<script>
    // Update date_to minimum value when date_from changes
    document.getElementById('date_from').addEventListener('change', function() {
        document.getElementById('date_to').setAttribute('min', this.value);
    });
</script>
@endsection
