@extends('admin.layout.navigation')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-white mb-2">Appointments Management</h1>
            <p class="text-gray-300">View and manage all patient appointments</p>
        </div>
        <a href="{{ route('admin.appointments.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            New Appointment
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                    @if(request('search'))
                        {{ $appointments->count() }} / {{ $stats['total'] }}
                    @else
                        {{ $stats['total'] }}
                    @endif
                </p>
                @if(request('search'))
                    <p class="text-xs text-gray-500 dark:text-gray-400">Filtered / All</p>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['pending'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Confirmed</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['confirmed'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
                <svg class="w-6 h-6 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Cancelled</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['cancelled'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 9v2m-6.938 4h13.856c.946 0 1.337-1.213.574-1.756L9.756 15a1 1 0 00-.512-.244v0a1 1 0 00-.512.244L2.006 18.244C1.243 18.787 1.634 20 2.58 20z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Today</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['today'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Search Bar -->
<div class="mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form method="GET" action="{{ route('admin.appointments.index') }}" class="flex items-center space-x-4">
            <div class="flex-1">
                <label for="search" class="sr-only">Search appointments</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search by patient name, doctor name, specialization, status, date, or reason..."
                           title="Press Ctrl+F to focus search, Escape to clear, Enter to search"
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white dark:bg-gray-700 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('admin.appointments.index') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    Clear
                </a>
            @endif
        </form>

        @if(request('search'))
            <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900 rounded-md">
                <p class="text-sm text-blue-800 dark:text-blue-200">
                    <span class="font-medium">Search Results:</span>
                    Showing {{ $appointments->count() }} appointment(s) for "{{ request('search') }}"
                    @if($appointments->count() === 0)
                        <span class="block mt-1 text-blue-600 dark:text-blue-300">No appointments found matching your search criteria.</span>
                    @endif
                </p>
            </div>
        @endif

        <!-- Quick Filter Buttons -->
        <div class="mt-4 flex flex-wrap gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400 mr-2">Quick filters:</span>
            <a href="{{ route('admin.appointments.index', ['search' => 'Pending']) }}"
               class="px-3 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full hover:bg-yellow-200 transition-colors {{ request('search') === 'Pending' ? 'ring-2 ring-yellow-400' : '' }}">
                Pending
            </a>
            <a href="{{ route('admin.appointments.index', ['search' => 'Confirmed']) }}"
               class="px-3 py-1 text-xs bg-green-100 text-green-800 rounded-full hover:bg-green-200 transition-colors {{ request('search') === 'Confirmed' ? 'ring-2 ring-green-400' : '' }}">
                Confirmed
            </a>
            <a href="{{ route('admin.appointments.index', ['search' => 'Cancelled']) }}"
               class="px-3 py-1 text-xs bg-red-100 text-red-800 rounded-full hover:bg-red-200 transition-colors {{ request('search') === 'Cancelled' ? 'ring-2 ring-red-400' : '' }}">
                Cancelled
            </a>
            <a href="{{ route('admin.appointments.index', ['search' => now()->format('Y-m-d')]) }}"
               class="px-3 py-1 text-xs bg-indigo-100 text-indigo-800 rounded-full hover:bg-indigo-200 transition-colors {{ request('search') === now()->format('Y-m-d') ? 'ring-2 ring-indigo-400' : '' }}">
                Today
            </a>
            <a href="{{ route('admin.appointments.index', ['search' => 'Student']) }}"
               class="px-3 py-1 text-xs bg-purple-100 text-purple-800 rounded-full hover:bg-purple-200 transition-colors {{ request('search') === 'Student' ? 'ring-2 ring-purple-400' : '' }}">
                Students
            </a>
            <a href="{{ route('admin.appointments.index', ['search' => 'Staff']) }}"
               class="px-3 py-1 text-xs bg-cyan-100 text-cyan-800 rounded-full hover:bg-cyan-200 transition-colors {{ request('search') === 'Staff' ? 'ring-2 ring-cyan-400' : '' }}">
                Staff
            </a>
        </div>
    </div>
</div>

<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
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
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-4">
                    Patient Name
                </th>
                <th scope="col" class="px-6 py-4">
                    Date
                </th>
                <th scope="col" class="px-6 py-4">
                    Time
                </th>
                <th scope="col" class="px-6 py-4">
                    Doctor Name
                </th>
                <th scope="col" class="px-6 py-4">
                    Status
                </th>
                <th scope="col" class="px-6 py-4">
                    Action
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $appointment)
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $appointment->patient->user->name ?? 'Patient #' . $appointment->patient_id }}
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        ID: {{ $appointment->patient_id }} • {{ $appointment->patient->patient_type ?? 'Unknown Type' }}
                    </div>
                </th>
                <td class="px-6 py-4">
                    {{ \Carbon\Carbon::parse($appointment->date)->format('F j, Y') }}
                </td>
                <td class="px-6 py-4">
                    {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}
                </td>
                <td class="px-6 py-4">
                    {{ $appointment->doctor->user->name ?? 'Doctor #' . $appointment->doc_id }}
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        ID: {{ $appointment->doc_id }} • {{ $appointment->doctor->specialization ?? 'General Practice' }}
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full
                        {{ $appointment->status === 'Confirmed' ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200' :
                           ($appointment->status === 'Pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200' :
                           'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200') }}">
                        {{ $appointment->status }}
                    </span>
                </td>
                <td class="flex items-center px-6 py-4">
                    <a href="{{ route('admin.appointments.edit', $appointment->id) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                    <a class="font-medium text-red-600 dark:text-red-500 hover:underline ms-3">
                        <form action="{{ route('admin.appointments.destroy', $appointment->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline ms-3r" onclick="return confirm('Are you sure?')">Remove</button>
                        </form>
                    </a>
                </td>
            </tr>
            @empty
            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                <td colspan="6" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center justify-center">
                        @if(request('search'))
                            <svg class="w-12 h-12 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No matching appointments found</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">No appointments match your search for "{{ request('search') }}"</p>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                <p class="mb-2">Search tips:</p>
                                <ul class="text-left list-disc list-inside space-y-1">
                                    <li>Try searching for part of a name (e.g., "john" instead of "John Doe")</li>
                                    <li>Search by appointment status (Pending, Confirmed, Cancelled)</li>
                                    <li>Search by date (e.g., "2024-01-15" or "January")</li>
                                    <li>Search by doctor specialization (e.g., "Cardiology")</li>
                                    <li>Search by patient type (Student, Staff)</li>
                                </ul>
                            </div>
                            <a href="{{ route('admin.appointments.index') }}"
                               class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg">
                                Clear Search
                            </a>
                        @else
                            <svg class="w-12 h-12 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3a4 4 0 118 0v4m-4 9v2m-6.938 4h13.856c.946 0 1.337-1.213.574-1.756L9.756 15a1 1 0 00-.512-.244v0a1 1 0 00-.512.244L2.006 18.244C1.243 18.787 1.634 20 2.58 20z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No appointments found</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Get started by creating a new appointment.</p>
                            <a href="{{ route('admin.appointments.create') }}"
                               class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                                Create First Appointment
                            </a>
                        @endif
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
// Auto-submit search form on Enter key
document.getElementById('search').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        this.closest('form').submit();
    }
});

// Clear search field on Escape key
document.getElementById('search').addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        this.value = '';
        this.closest('form').submit();
    }
});

// Focus search field on Ctrl+F or Cmd+F
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        document.getElementById('search').focus();
    }
});

// Add loading state to search button
document.querySelector('form').addEventListener('submit', function() {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Searching...';
    submitBtn.disabled = true;

    // Re-enable after a short delay in case of quick return
    setTimeout(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }, 2000);
});
</script>
@endsection
