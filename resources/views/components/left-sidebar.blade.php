<!-- Left Sidebar Navigation -->
<aside class="hidden md:flex flex-col w-64 bg-gradient-to-b from-slate-900 to-slate-800 text-white shadow-lg overflow-y-auto flex-shrink-0">
    <!-- Logo/Brand -->
    <div class="p-6 border-b border-slate-700">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-teal-500 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5-4a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <h1 class="text-lg font-bold">CKC Health</h1>
                <p class="text-xs text-slate-400">Portal</p>
            </div>
        </div>
    </div>

    <!-- User Info -->
    <div class="p-4 border-b border-slate-700">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-teal-500 text-sm font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-slate-400">{{ $userTypeName ?? 'User' }}</p>
            </div>
        </div>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 space-y-1 p-4">
        @php
            $currentUser = Auth::user();
            $isActive = fn($routes) => request()->routeIs(...(array)$routes);
        @endphp

        @if($currentUser->user_type == 0) <!-- ADMIN -->
            <!-- Admin Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('admin.dashboard') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4" />
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Users Management -->
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('admin.users.*') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm0 0h6v-2a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Users</span>
            </a>

            <!-- Medicines -->
            <a href="{{ route('staff.medications') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('staff.medications') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7.528-4.5a9 9 0 11-18.528 0m0 0a9 9 0 0118.528 0z" />
                </svg>
                <span>Medicines</span>
            </a>

        @elseif($currentUser->user_type == 1 || $currentUser->user_type == 4) <!-- STUDENT/FACULTY -->
            <!-- Patient Dashboard -->
            <a href="{{ route('patients.dashboard') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('patients.dashboard') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4" />
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Appointments -->
            <a href="{{ route('patients.appointments') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('patients.appointments*') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Appointments</span>
            </a>

            <!-- Health Records -->
            <a href="{{ route('patients.health.records') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('patients.health.*') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Health Records</span>
            </a>

            <!-- Medical Certificates -->
            <a href="{{ route('patients.certificates.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('patients.certificates*') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                <span>Certificates</span>
            </a>

        @elseif($currentUser->user_type == 2) <!-- STAFF -->
            <!-- Staff Dashboard -->
            <a href="{{ route('staff.dashboard') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('staff.dashboard', 'staff.index') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4" />
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Appointments -->
            <a href="{{ route('staff.appointments') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('staff.appointments') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Appointments</span>
            </a>

            <!-- Patients -->
            <a href="{{ route('staff.patients') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('staff.patients') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Patients</span>
            </a>

            <!-- Nurse Logs -->
            <a href="{{ route('staff.nurse-logs.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('staff.nurse-logs.*') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m-7 4h8a2 2 0 002-2V7.5L14.5 4H8a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Nurse Logs</span>
            </a>

            <!-- Health Records -->
            <a href="{{ route('staff.health-records') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('staff.health-records') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Health Records</span>
            </a>

            <!-- Medicines -->
            <a href="{{ route('staff.medications') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('staff.medications') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m7.528-4.5a9 9 0 11-18.528 0m0 0a9 9 0 0118.528 0z" />
                </svg>
                <span>Medicines</span>
            </a>

            <!-- Prescriptions -->
            <a href="{{ route('staff.prescriptions') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('staff.prescriptions') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z" />
                </svg>
                <span>Prescriptions</span>
            </a>

            <!-- Certificate Requests -->
            <a href="{{ route('staff.certificate-requests') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('staff.certificate-requests') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                <span>Certificates</span>
            </a>

        @elseif($currentUser->user_type == 3) <!-- DOCTOR -->
            <!-- Doctor Dashboard -->
            <a href="{{ route('doctor.dashboard') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('doctor.dashboard', 'doctor.index') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4" />
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Appointments -->
            <a href="{{ route('doctor.appointments') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('doctor.appointments') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Appointments</span>
            </a>

            <!-- Patients -->
            <a href="{{ route('doctor.patients') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('doctor.patients') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Patients</span>
            </a>

            <!-- Prescriptions -->
            <a href="{{ route('doctor.prescriptions') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('doctor.prescriptions') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747S17.5 6.253 12 6.253z" />
                </svg>
                <span>Prescriptions</span>
            </a>

        @elseif($currentUser->user_type == 5) <!-- GUARDIAN -->
            <!-- Privacy Center -->
            <a href="{{ route('privacy-center.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('privacy-center.*') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span>Privacy Center</span>
            </a>

        @elseif($currentUser->user_type == 6) <!-- PRINCIPAL -->
            <!-- Break Glass Access -->
            <a href="{{ route('break-glass.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('break-glass.*') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2.855m0 0v.345m0-.345a6.75 6.75 0 100-13.5 6.75 6.75 0 000 13.5zm0 1.655v3.5m0-3.5h3.5m-3.5 0h-3.5m7-6h3.5m-3.5 0h-3.5m0 6v3.5m0-3.5h3.5m-3.5 0h-3.5" />
                </svg>
                <span>Break Glass</span>
            </a>

        @elseif($currentUser->user_type == 7) <!-- TEACHER -->
            <!-- Teacher Safety -->
            <a href="{{ route('teacher.safety.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ $isActive('teacher.safety.*') ? 'bg-teal-600 text-white' : 'text-slate-300 hover:bg-slate-700' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Safety Alerts</span>
            </a>

        @endif
    </nav>

    <!-- Logout Section -->
    <div class="border-t border-slate-700 p-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Sign Out</span>
            </button>
        </form>
    </div>
</aside>

<!-- Mobile Menu Button (for small screens) -->
<div class="md:hidden fixed bottom-4 right-4 z-40">
    <button onclick="document.getElementById('mobile-sidebar').classList.toggle('hidden')"
            class="bg-teal-600 hover:bg-teal-700 text-white rounded-full p-4 shadow-lg">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
</div>

<!-- Mobile Sidebar -->
<div id="mobile-sidebar" class="hidden md:hidden fixed inset-0 z-30 bg-black/50">
    <div class="w-64 h-full bg-gradient-to-b from-slate-900 to-slate-800 text-white overflow-y-auto" onclick="event.stopPropagation()">
        <!-- Mobile sidebar content - same as desktop -->
        <div class="p-6 border-b border-slate-700">
            <button onclick="document.getElementById('mobile-sidebar').classList.add('hidden')"
                    class="absolute top-4 right-4 text-white hover:text-slate-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h1 class="text-lg font-bold">CKC Health</h1>
        </div>
        
        <!-- Copy of nav links for mobile -->
        <nav class="space-y-1 p-4">
            <!-- Navigation links repeat here for mobile -->
        </nav>
    </div>
</div>
