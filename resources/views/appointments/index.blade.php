<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Appointments') }}
        </h2>
    </x-slot>
    @if (session('success'))
    <div id="alert-border-3" class="flex items-center p-4 mb-4 text-green-800 border-t-4 border-green-300 bg-green-50 dark:text-green-400 dark:bg-gray-800 dark:border-green-800" role="alert">
        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
        </svg>
        <div class="ms-3 text-sm font-medium">
            {{ session('success') }}
        </div>
    </div>
    @endif
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
    {{--  --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Schedules") }}
                </div>
                <!-- Button to trigger modal -->
                <button id="openModalButton" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create New Appointment
                </button>
                {{-- View Table --}}
                @include('appointments.show')
            </div>
        </div>
    </div>
    {{-- Create Modal --}}
    @include('appointments.create')
</x-app-layout>
<!-- Include JavaScript -->
<script>
    const openModalButton = document.getElementById('openModalButton');
    const closeModalButton = document.getElementById('closeModalButton');
    const cancelModalButton = document.getElementById('cancelModalButton');
    const modalBackdrop = document.getElementById('modalBackdrop');
    const appointmentModal = document.getElementById('appointmentModal');

    // Open modal
    openModalButton.addEventListener('click', () => {
        modalBackdrop.classList.remove('hidden');
        appointmentModal.classList.remove('hidden');
    });

    // Close modal (by clicking the close button)
    closeModalButton.addEventListener('click', () => {
        modalBackdrop.classList.add('hidden');
        appointmentModal.classList.add('hidden');
    });

    // Close modal (by clicking the cancel button)
    cancelModalButton.addEventListener('click', () => {
        modalBackdrop.classList.add('hidden');
        appointmentModal.classList.add('hidden');
    });

    // Close modal when clicking on the backdrop
    modalBackdrop.addEventListener('click', () => {
        modalBackdrop.classList.add('hidden');
        appointmentModal.classList.add('hidden');
    });
</script>
