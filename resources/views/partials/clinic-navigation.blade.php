@php
    $user = Auth::user();
    $items = [];
    $sectionTitle = 'Navigation';
    $portalTitle = 'Health Services';

    if ($user) {
        switch ($user->user_type) {
            case \App\Models\User::TYPE_STUDENT:
            case \App\Models\User::TYPE_FACULTY:
                $portalTitle = $user->user_type === \App\Models\User::TYPE_STUDENT ? 'Student Health Portal' : 'Faculty Health Portal';
                $items = [
                    ['label' => 'Dashboard', 'route' => 'patients.dashboard', 'patterns' => ['patients.dashboard', 'patients.ajax.dashboard'], 'icon' => 'dashboard'],
                    ['label' => 'Appointments', 'route' => 'patients.appointments', 'patterns' => ['patients.appointments', 'patients.schedule.*', 'patients.ajax.appointments'], 'icon' => 'calendar'],
                    ['label' => 'Health Records', 'route' => 'patients.health.records', 'patterns' => ['patients.health.*', 'patients.prescription.*', 'patients.examination.*', 'patients.immunization.*', 'patients.ajax.health-records'], 'icon' => 'records'],
                    ['label' => 'Medical Certificates', 'route' => 'patients.certificates.index', 'patterns' => ['patients.certificates.*', 'patients.ajax.certificates'], 'icon' => 'certificate'],
                    ['label' => 'Profile', 'route' => 'patients.profile', 'patterns' => ['patients.profile*', 'patients.ajax.profile'], 'icon' => 'profile'],
                    ['label' => 'Privacy & Consent', 'route' => 'privacy-center.index', 'patterns' => ['privacy-center.*'], 'icon' => 'privacy'],
                ];
                break;

            case \App\Models\User::TYPE_STAFF:
                $portalTitle = 'School Nurse Portal';
                $items = [
                    ['label' => 'Dashboard', 'route' => 'staff.dashboard', 'patterns' => ['staff.dashboard', 'staff.index', 'staff.ajax.dashboard'], 'icon' => 'dashboard'],
                    ['label' => 'Appointments', 'route' => 'staff.appointments', 'patterns' => ['staff.appointments*', 'staff.ajax.appointments'], 'icon' => 'calendar'],
                    ['label' => 'Nurse Logs', 'route' => 'staff.nurse-logs.index', 'patterns' => ['staff.nurse-logs.*', 'staff.ajax.nurse-logs'], 'icon' => 'records'],
                    ['label' => 'Patients', 'route' => 'staff.patients', 'patterns' => ['staff.patients*', 'staff.patient-details', 'staff.ajax.patients'], 'icon' => 'users'],
                    ['label' => 'Clinical Records', 'route' => 'staff.health-records', 'patterns' => ['staff.health-records*', 'staff.ajax.health-records', 'staff.dental-exams.*', 'staff.physical-exams.*'], 'icon' => 'records'],
                    ['label' => 'Prescriptions', 'route' => 'staff.prescriptions', 'patterns' => ['staff.prescription*', 'staff.prescriptions*', 'staff.ajax.prescriptions'], 'icon' => 'medicine'],
                    ['label' => 'Medicines', 'route' => 'staff.medications', 'patterns' => ['staff.medications', 'staff.medicines.*', 'staff.ajax.medicines'], 'icon' => 'medicine'],
                    ['label' => 'Immunization Review', 'route' => 'immunizations.review.index', 'patterns' => ['immunizations.review.*'], 'icon' => 'shield'],
                    ['label' => 'Safety Alerts', 'route' => 'clinical.life-safety.index', 'patterns' => ['clinical.life-safety.*'], 'icon' => 'alert'],
                    ['label' => 'Certificate Requests', 'route' => 'staff.certificate-requests', 'patterns' => ['staff.certificate-requests*'], 'icon' => 'certificate'],
                    ['label' => 'Reports', 'route' => 'staff.reports', 'patterns' => ['staff.reports*', 'staff.statistics.*', 'staff.ajax.reports', 'staff.ajax.statistics'], 'icon' => 'chart'],
                    ['label' => 'Infrastructure Logs', 'route' => 'staff.infrastructure-logs.index', 'patterns' => ['staff.infrastructure-logs.*'], 'icon' => 'settings'],
                    ['label' => 'Announcements', 'route' => 'staff.announcements', 'patterns' => ['staff.announcements*'], 'icon' => 'alert'],
                ];
                break;

            case \App\Models\User::TYPE_DOCTOR:
                $portalTitle = 'Doctor Portal';
                $items = [
                    ['label' => 'Dashboard', 'route' => 'doctor.dashboard', 'patterns' => ['doctor.dashboard', 'doctor.index', 'doctor.ajax.dashboard'], 'icon' => 'dashboard'],
                    ['label' => 'Appointments', 'route' => 'doctor.appointments', 'patterns' => ['doctor.appointments*', 'doctor.ajax.appointments'], 'icon' => 'calendar'],
                    ['label' => 'Patients', 'route' => 'doctor.patients', 'patterns' => ['doctor.patients*', 'doctor.patient-details', 'doctor.ajax.patients'], 'icon' => 'users'],
                    ['label' => 'Clinical Records', 'route' => 'doctor.health-records', 'patterns' => ['doctor.health-records*', 'doctor.ajax.health-records', 'doctor.dental-exams.*', 'doctor.physical-exams.*'], 'icon' => 'records'],
                    ['label' => 'Prescriptions', 'route' => 'doctor.prescriptions', 'patterns' => ['doctor.prescription*', 'doctor.prescriptions*', 'doctor.ajax.prescriptions'], 'icon' => 'medicine'],
                    ['label' => 'Medicines', 'route' => 'doctor.medications', 'patterns' => ['doctor.medications', 'doctor.medicines.*', 'doctor.ajax.medications'], 'icon' => 'medicine'],
                    ['label' => 'Immunization Review', 'route' => 'immunizations.review.index', 'patterns' => ['immunizations.review.*'], 'icon' => 'shield'],
                    ['label' => 'Safety Alerts', 'route' => 'clinical.life-safety.index', 'patterns' => ['clinical.life-safety.*'], 'icon' => 'alert'],
                    ['label' => 'Certificate Requests', 'route' => 'doctor.certificate-requests', 'patterns' => ['doctor.certificate-requests*'], 'icon' => 'certificate'],
                    ['label' => 'Reports', 'route' => 'doctor.reports', 'patterns' => ['doctor.reports*', 'doctor.ajax.reports'], 'icon' => 'chart'],
                    ['label' => 'Profile', 'route' => 'doctor.profile.show', 'patterns' => ['doctor.profile.*'], 'icon' => 'profile'],
                ];
                break;

            case \App\Models\User::TYPE_ADMIN:
                $portalTitle = 'System Administration';
                $items = [
                    ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'patterns' => ['admin.dashboard'], 'icon' => 'dashboard'],
                    ['label' => 'Health Analytics', 'route' => 'admin.analytics.index', 'patterns' => ['admin.analytics.*'], 'icon' => 'chart'],
                    ['label' => 'User Accounts', 'route' => 'admin.users.index', 'patterns' => ['admin.users.*'], 'icon' => 'users'],
                    ['label' => 'Provider Accounts', 'route' => 'admin.doctors.index', 'patterns' => ['admin.doctors.*'], 'icon' => 'users'],
                    ['label' => 'Guardian Verification', 'route' => 'guardian-approvals.index', 'patterns' => ['guardian-approvals.*'], 'icon' => 'shield'],
                    ['label' => 'Vaccine Requirements', 'route' => 'immunization-requirements.index', 'patterns' => ['immunization-requirements.*'], 'icon' => 'shield'],
                    ['label' => 'Import Accounts', 'route' => 'admin.import.users', 'patterns' => ['admin.import.*'], 'icon' => 'upload'],
                    ['label' => 'Infrastructure Logs', 'route' => 'admin.system.index', 'patterns' => ['admin.system.*'], 'icon' => 'settings'],
                ];
                break;

            case \App\Models\User::TYPE_GUARDIAN:
                $portalTitle = 'Guardian Privacy Center';
                $items = [
                    ['label' => 'Privacy Center', 'route' => 'privacy-center.index', 'patterns' => ['privacy-center.*'], 'icon' => 'privacy'],
                ];
                break;

            case \App\Models\User::TYPE_PRINCIPAL:
                $portalTitle = 'Emergency Administration';
                $items = [
                    ['label' => 'Emergency Access', 'route' => 'break-glass.index', 'patterns' => ['break-glass.index', 'break-glass.create', 'break-glass.show'], 'icon' => 'alert', 'danger' => true],
                    ['label' => 'Emergency Audit', 'route' => 'break-glass.audit', 'patterns' => ['break-glass.audit'], 'icon' => 'records'],
                ];
                break;

            case \App\Models\User::TYPE_TEACHER:
                $portalTitle = 'Teacher Safety Portal';
                $items = [
                    ['label' => 'Student Life-Safety', 'route' => 'teacher.safety.index', 'patterns' => ['teacher.safety.*'], 'icon' => 'alert'],
                ];
                break;
        }
    }

    $activeItem = collect($items)->first(fn ($item) => request()->routeIs(...$item['patterns']));
    $pageLabel = $activeItem['label'] ?? $portalTitle;
@endphp

<header class="clinic-topbar">
    <button type="button" @click="sidebarOpen = true" class="mr-3 rounded-lg p-2 text-white/90 hover:bg-white/10 md:hidden" aria-label="Open navigation">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>

    <div class="min-w-0 flex-1">
        <p class="truncate text-[11px] font-semibold uppercase tracking-[0.18em] text-white/70">Christ the King College</p>
        <h1 class="truncate text-base font-bold sm:text-lg">{{ $pageLabel }}</h1>
    </div>

    <button id="pwa-install-btn" type="button" onclick="installPWA()" style="display:none" class="mr-2 hidden rounded-xl bg-white/10 px-3 py-2 text-xs font-semibold hover:bg-white/20 sm:inline-flex">Install App</button>

    <!-- Notification Bell -->
    <x-notification-bell />

    <div class="relative" @click.outside="profileOpen = false">
        <button type="button" @click="profileOpen = !profileOpen" class="flex items-center gap-2 rounded-xl px-2 py-1.5 hover:bg-white/10" aria-label="Open account menu">
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-white/20 text-sm font-bold ring-1 ring-white/30">
                {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
            </span>
            <span class="hidden text-left sm:block">
                <span class="block max-w-40 truncate text-sm font-semibold">{{ $user?->name }}</span>
                <span class="block text-[11px] text-white/70">{{ $user?->roleLabel() }}</span>
            </span>
            <svg class="hidden h-4 w-4 sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
        </button>

        <div x-cloak x-show="profileOpen" x-transition class="absolute right-0 mt-2 w-60 overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-xl">
            <div class="border-b border-slate-100 px-4 py-3">
                <p class="truncate text-sm font-bold text-slate-800">{{ $user?->name }}</p>
                <p class="truncate text-xs text-slate-500">{{ $user?->email }}</p>
            </div>
            @if($user && in_array($user->user_type, [\App\Models\User::TYPE_STUDENT, \App\Models\User::TYPE_FACULTY], true))
                <a href="{{ route('patients.profile') }}" class="flex items-center gap-2 px-4 py-3 text-sm font-semibold hover:bg-teal-50 hover:text-teal-800"><x-clinic-nav-icon name="profile" class="h-4 w-4" />Profile</a>
            @elseif($user?->user_type === \App\Models\User::TYPE_DOCTOR)
                <a href="{{ route('doctor.profile.show') }}" class="flex items-center gap-2 px-4 py-3 text-sm font-semibold hover:bg-teal-50 hover:text-teal-800"><x-clinic-nav-icon name="profile" class="h-4 w-4" />Profile</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
                @csrf
                <button class="flex w-full items-center gap-2 px-4 py-3 text-left text-sm font-semibold text-rose-700 hover:bg-rose-50"><x-clinic-nav-icon name="logout" class="h-4 w-4" />Log out</button>
            </form>
        </div>
    </div>
</header>

<div x-cloak x-show="sidebarOpen" x-transition.opacity class="clinic-overlay" @click="sidebarOpen = false"></div>

<aside class="clinic-sidebar" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    <div class="clinic-brand">
        <a href="{{ route('dashboard') }}" class="clinic-brand-mark">
            <x-application-logo class="h-10 w-10 object-contain" />
        </a>
        <a href="{{ route('dashboard') }}" class="min-w-0">
            <p class="truncate text-sm font-extrabold leading-tight text-slate-800">CKC Clinic Health Record Management System</p>
            <p class="truncate text-[11px] font-medium text-teal-700">{{ $portalTitle }}</p>
        </a>
        <button type="button" @click="sidebarOpen = false" class="ml-auto rounded-lg p-2 text-slate-400 hover:bg-slate-100 md:hidden" aria-label="Close navigation">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <div class="clinic-nav-section flex-1">
        <p class="clinic-nav-label">{{ $sectionTitle }}</p>
        @foreach($items as $item)
            @if(Route::has($item['route']))
                <x-clinic-nav-link
                    :href="route($item['route'])"
                    :active="request()->routeIs(...$item['patterns'])"
                    :icon="$item['icon']"
                    :danger="$item['danger'] ?? false">
                    {{ $item['label'] }}
                </x-clinic-nav-link>
            @endif
        @endforeach
    </div>

    <div class="border-t border-slate-200 p-3">
        @if($user?->user_type === \App\Models\User::TYPE_ADMIN)
            <div class="mb-3 rounded-xl border border-teal-100 bg-teal-50 p-3 text-xs leading-5 text-teal-900">
                Administrator access excludes patient medical text and treatment notes.
            </div>
        @endif
        <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-teal-100 font-bold text-teal-800">{{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}</span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-xs font-bold text-slate-800">{{ $user?->name }}</p>
                <p class="truncate text-[11px] text-slate-500">{{ $user?->roleLabel() }}</p>
            </div>
        </div>
    </div>
</aside>
