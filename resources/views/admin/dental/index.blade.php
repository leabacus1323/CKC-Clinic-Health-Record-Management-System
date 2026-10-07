@extends('admin.layout.navigation')

@section('content')
<div class="relative overflow-x-auto">
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
    <div class="grid md:grid-cols-1 md:gap-6">
        <div class="relative z-0 w-full mb-3 group">
            <form class="mx-auto">
                <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="search" id="default-search" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search Patient..." required />
                    <button type="submit" class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Search</button>
                </div>
            </form>
        </div>
    </div>
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-4">Id</th>
                <th scope="col" class="px-6 py-4">Patient</th>
                <th scope="col" class="px-6 py-4">Doctor</th>
                <th scope="col" class="px-6 py-4">Teeth Status</th>
                <th scope="col" class="px-6 py-4">Diagnosis</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dentals as $dental)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $dental->id }}
                </th>
                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $dental->patient && $dental->patient->user ? $dental->patient->user->name : 'N/A' }}
                </td>
                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $dental->doctor && $dental->doctor->user ? $dental->doctor->user->name : 'N/A' }}
                </td>
                <td scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                    @if(is_array($dental->teeth_status))
                        @php
                            // Copy to local variable to avoid "indirect modification of overloaded property" warning
                            $teethStatusData = $dental->teeth_status;

                            // Flatten nested arrays (e.g. ['upper' => [...], 'lower' => [...]]) and filter to only strings/integers
                            $flatValues = [];
                            array_walk_recursive($teethStatusData, function($value) use (&$flatValues) {
                                if (is_string($value) || is_int($value)) {
                                    $flatValues[] = $value;
                                }
                            });

                            $statusCounts = !empty($flatValues) ? array_count_values($flatValues) : [];
                            $summary = [];
                            if(isset($statusCounts['healthy'])) $summary[] = $statusCounts['healthy'] . ' healthy';
                            if(isset($statusCounts['cavity'])) $summary[] = $statusCounts['cavity'] . ' cavities';
                            if(isset($statusCounts['filled'])) $summary[] = $statusCounts['filled'] . ' filled';
                            if(isset($statusCounts['missing'])) $summary[] = $statusCounts['missing'] . ' missing';
                            if(isset($statusCounts['other'])) $summary[] = $statusCounts['other'] . ' other';
                        @endphp
                        {{ !empty($summary) ? implode(', ', $summary) : 'No data' }}
                    @else
                        {{ $dental->teeth_status ?? 'N/A' }}
                    @endif
                </td>
                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $dental->diagnosis }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
