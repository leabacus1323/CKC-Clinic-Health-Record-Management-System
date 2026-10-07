@php
    $appointmentRows = $appointments ?? collect(isset($appointment) ? [$appointment] : []);
@endphp

@if($standalone ?? false)
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Appointment Details</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('appointments.index') }}" class="text-blue-600 hover:underline">&larr; Back to appointments</a>
            </div>
            @include('appointments.show-table', ['appointmentRows' => $appointmentRows])
        </div>
    </div>
</x-app-layout>
@else
    @include('appointments.show-table', ['appointmentRows' => $appointmentRows])
@endif
