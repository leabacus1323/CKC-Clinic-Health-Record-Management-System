@php($isStandalone = $standalone ?? false)

@if($isStandalone)
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">New Appointment</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
@endif
<div id="appointmentModal" class="{{ $isStandalone ? '' : 'fixed inset-0 flex items-center justify-center hidden z-50' }}">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800">New Appointment</h2>
            @unless($isStandalone)
                <button id="closeModalButton" type="button" class="text-gray-600 hover:text-gray-900 text-xl" aria-label="Close">&times;</button>
            @endunless
        </div>
        <form action="{{ route('appointments.store') }}" method="POST" class="mt-4">
            @csrf
            <div class="mb-4">
                <label for="patient" class="block text-sm font-medium text-gray-700">Patient</label>
                <select name="patient_id" id="patient" required class="mt-1 p-2 w-full border border-gray-300 rounded-lg">
                    <option value="">Select patient</option>
                    @foreach(($patients ?? collect()) as $patient)
                        <option value="{{ $patient->id }}" @selected(old('patient_id') == $patient->id)>
                            {{ $patient->user?->name ?? 'Patient #'.$patient->id }} (ID {{ $patient->id }})
                        </option>
                    @endforeach
                </select>
                @error('patient_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="doctor" class="block text-sm font-medium text-gray-700">Doctor</label>
                <select name="doc_id" id="doctor" required class="mt-1 p-2 w-full border border-gray-300 rounded-lg">
                    <option value="">Select doctor</option>
                    @foreach(($doctors ?? collect()) as $doctor)
                        <option value="{{ $doctor->id }}" @selected(old('doc_id') == $doctor->id)>
                            {{ $doctor->user?->name ?? 'Doctor #'.$doctor->id }}
                        </option>
                    @endforeach
                </select>
                @error('doc_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="date" class="block text-sm font-medium text-gray-700">Appointment Date</label>
                <input type="date" name="date" id="date" value="{{ old('date') }}" required class="mt-1 p-2 w-full border border-gray-300 rounded-lg">
                @error('date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="time" class="block text-sm font-medium text-gray-700">Time</label>
                <input type="time" name="time" id="time" value="{{ old('time') }}" required class="mt-1 p-2 w-full border border-gray-300 rounded-lg">
                @error('time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label for="reason" class="block text-sm font-medium text-gray-700">Reason <span class="text-gray-500">(optional)</span></label>
                <textarea name="reason" id="reason" rows="3" class="mt-1 p-2 w-full border border-gray-300 rounded-lg">{{ old('reason') }}</textarea>
                @error('reason')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end space-x-2">
                @if($isStandalone)
                    <a href="{{ route('appointments.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Cancel</a>
                @else
                    <button type="button" id="cancelModalButton" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Cancel</button>
                @endif
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Save Appointment</button>
            </div>
        </form>
    </div>
</div>
@if(!$isStandalone)
<div id="modalBackdrop" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden z-40"></div>
@endif
@if($isStandalone)
        </div>
    </div>
</x-app-layout>
@endif
