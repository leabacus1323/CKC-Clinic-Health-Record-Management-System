<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Immunization Details</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $immunization->name }}</h3>
                    <a href="{{ route('immunization.index') }}" class="text-blue-600 hover:underline">Back</a>
                </div>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $immunization->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 whitespace-pre-line text-gray-900 dark:text-gray-100">{{ $immunization->description }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
