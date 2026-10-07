@php($isStandalone = $standalone ?? false)
@if($isStandalone)
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">New Immunization</h2>
    </x-slot>
    <div class="py-12"><div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
@endif
<div id="immunizationModal" class="{{ $isStandalone ? '' : 'fixed inset-0 flex items-center justify-center hidden z-50' }}">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">New Immunization</h2>
            @unless($isStandalone)
                <button id="closeModalButton" type="button" class="text-gray-600 hover:text-gray-900 text-xl" aria-label="Close">&times;</button>
            @endunless
        </div>
        <form action="{{ route('immunization.store') }}" method="POST" class="mt-4">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="mt-1 p-2 w-full border border-gray-300 rounded-lg" />
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea id="description" name="description" rows="3" required class="mt-1 p-2 w-full border border-gray-300 rounded-lg">{{ old('description') }}</textarea>
                @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-end space-x-2">
                @if($isStandalone)
                    <a href="{{ route('immunization.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Cancel</a>
                @else
                    <button type="button" id="cancelModalButton" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Cancel</button>
                @endif
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Save Immunization</button>
            </div>
        </form>
    </div>
</div>
@if(!$isStandalone)
<div id="modalBackdrop" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden z-40"></div>
@endif
@if($isStandalone)
    </div></div>
</x-app-layout>
@endif
