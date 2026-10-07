{{-- Dashboard Content Partial --}}
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Staff Dashboard</h1>
    <p class="text-gray-600">Welcome back, {{ Auth::user()->name }}</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
        <div class="flex items-center">
            <div class="p-3 bg-blue-500 rounded-full">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-800">Today's Appointments</h3>
                <p class="text-2xl font-bold text-blue-600">{{ $todayAppointments }}</p>
            </div>
        </div>
    </div>

    <div class="bg-green-50 p-6 rounded-lg border border-green-200">
        <div class="flex items-center">
            <div class="p-3 bg-green-500 rounded-full">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-800">This Week</h3>
                <p class="text-2xl font-bold text-green-600">{{ $weeklyAppointments }}</p>
            </div>
        </div>
    </div>

    <div class="bg-purple-50 p-6 rounded-lg border border-purple-200">
        <div class="flex items-center">
            <div class="p-3 bg-purple-500 rounded-full">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-800">Total Patients</h3>
                <p class="text-2xl font-bold text-purple-600">{{ $totalPatients }}</p>
            </div>
        </div>
    </div>

    <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-500 rounded-full">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-800">Pending</h3>
                <p class="text-2xl font-bold text-yellow-600">{{ $pendingAppointments }}</p>
            </div>
        </div>
    </div>
</div>
