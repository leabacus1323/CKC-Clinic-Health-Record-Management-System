<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Immunization') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
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
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <button id="openModalButton" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Create New Immunization
                        </button>
                    </div>
                    {{--  --}}
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-4">Id</th>
                                    <th scope="col" class="px-6 py-4">Name</th>
                                    <th scope="col" class="px-6 py-4">Description</th>
                                    <th scope="col" class="px-6 py-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($immunization as $immunization)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $immunization->id }}</th>
                                    <td class="px-6 py-4">{{ $immunization->name }}</td>
                                    <td class="px-6 py-4">{{ $immunization->description }}</td>
                                    <td class="flex items-center px-6 py-4">
                                        <a href="{{ route('immunization.edit', $immunization->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                        <a class="font-medium text-red-600 dark:text-red-500 hover:underline ms-3">
                                            <form action="{{ route('immunization.destroy', $immunization->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Delete</button>
                                            </form>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--  --}}
    @include('immunization.create')
</x-app-layout>
<!-- Include JavaScript -->
<script>
    const openModalButton = document.getElementById('openModalButton');
    const closeModalButton = document.getElementById('closeModalButton');
    const cancelModalButton = document.getElementById('cancelModalButton');
    const modalBackdrop = document.getElementById('modalBackdrop');
    const immunizationModal = document.getElementById('immunizationModal');

    // Open modal
    openModalButton.addEventListener('click', () => {
        modalBackdrop.classList.remove('hidden');
        immunizationModal.classList.remove('hidden');
    });

    // Close modal (by clicking the close button)
    closeModalButton.addEventListener('click', () => {
        modalBackdrop.classList.add('hidden');
        immunizationModal.classList.add('hidden');
    });

    // Close modal (by clicking the cancel button)
    cancelModalButton.addEventListener('click', () => {
        modalBackdrop.classList.add('hidden');
        immunizationModal.classList.add('hidden');
    });

    // Close modal when clicking on the backdrop
    modalBackdrop.addEventListener('click', () => {
        modalBackdrop.classList.add('hidden');
        immunizationModal.classList.add('hidden');
    });
</script>
