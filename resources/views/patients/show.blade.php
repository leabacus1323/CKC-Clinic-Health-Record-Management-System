<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Patients') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Patient Records</h3>

                    @if(isset($patients) && $patients->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-medium text-gray-600">ID</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-600">School ID</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Patient Type</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Created</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($patients as $patient)
                                        <tr>
                                            <td class="px-4 py-2">{{ $patient->id }}</td>
                                            <td class="px-4 py-2">{{ $patient->school_id }}</td>
                                            <td class="px-4 py-2">{{ $patient->patient_type }}</td>
                                            <td class="px-4 py-2">{{ optional($patient->created_at)->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No patient records found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
