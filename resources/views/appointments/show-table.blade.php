<div class="relative overflow-x-auto bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-4">Patient</th>
                <th scope="col" class="px-6 py-4">Date</th>
                <th scope="col" class="px-6 py-4">Time</th>
                <th scope="col" class="px-6 py-4">Doctor</th>
                <th scope="col" class="px-6 py-4">Status</th>
                <th scope="col" class="px-6 py-4">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointmentRows as $appointment)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $appointment->patient?->user?->name ?? 'Patient #'.$appointment->patient_id }}
                        <span class="block text-xs text-gray-500">ID: {{ $appointment->patient_id }}</span>
                    </th>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($appointment->date)->format('F j, Y') }}</td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}</td>
                    <td class="px-6 py-4">{{ $appointment->doctor?->user?->name ?? 'Doctor #'.$appointment->doc_id }}</td>
                    <td class="px-6 py-4">{{ $appointment->status }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('appointments.edit', $appointment) }}" class="font-medium text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" class="inline-block ms-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="font-medium text-red-600 hover:underline" onclick="return confirm('Are you sure?')">Remove</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No appointments found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(method_exists($appointmentRows, 'links'))
    <div class="mt-4">{{ $appointmentRows->links() }}</div>
@endif
