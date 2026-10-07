<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Appointments') }}
        </h2>
    </x-slot>
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div id="alert-border-4" class="flex items-center p-4 mb-4 text-yellow-800 border-t-4 border-yellow-300 bg-yellow-50 dark:text-yellow-300 dark:bg-gray-800 dark:border-yellow-800" role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <div class="ms-3 text-sm font-medium">
                    {{ $error }}
                </div>
            </div>
        @endforeach
    @endif
    <div id="appointmentModal" class="inset-0 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Edit Appointment</h2>
            </div>
            <form action="{{ route('appointments.update', $appointment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="patient_id" class="block text-sm font-medium text-gray-700">Patient ID</label>
                    <input type="text" name="patient_id" class="mt-1 p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"value="{{ $appointment->patient_id }}" required>
                </div>
                <div class="mb-4">
                    <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" name="date" class="mt-1 p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $appointment->date }}" required>
                </div>

                <div class="mb-4">
                    <label for="time" class="block text-sm font-medium text-gray-700">Time</label>
                    <input type="time" name="time" step="3600" pattern="[0-2][0-9]:[0-5][0-9]" class="mt-1 p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}" required>
                </div>

                <div class="mb-4">
                    <label for="doc_id" class="block text-sm font-medium text-gray-700">Doctor ID</label>
                    <input type="text" name="doc_id" class="mt-1 p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $appointment->doc_id }}" required>
                </div>

                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="Pending" {{ $appointment->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Confirmed" {{ $appointment->status == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="Cancelled" {{ $appointment->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('appointments.index')  }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Cancel</a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Update Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Automatically open the modal
        const modalBackdrop = document.getElementById('appointmentModal');
        modalBackdrop.classList.remove('hidden');
    });
</script>
