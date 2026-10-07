<nav x-data="{ open: false }" class="border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
    @php($user = Auth::user())
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex min-w-0 items-center gap-4">
                <a href="{{ route('dashboard') }}" class="shrink-0"><x-application-logo class="h-9 w-9" /></a>
                <div class="hidden items-center gap-1 lg:flex">
                    @if(in_array($user->user_type, [\App\Models\User::TYPE_STUDENT, \App\Models\User::TYPE_FACULTY], true))
                        <a href="{{ route('patients.dashboard') }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">Dashboard</a>
                        <a href="{{ route('patients.health.records') }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">Health Records</a>
                        <a href="{{ route('patients.appointments') }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">Appointments</a>
                        <a href="{{ route('privacy-center.index') }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">Privacy & Consent</a>
                    @elseif($user->user_type === \App\Models\User::TYPE_STAFF)
                        <a href="{{ route('staff.dashboard') }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">Nurse Dashboard</a>
                        <a href="{{ route('staff.patients') }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">Patients</a>
                        <a href="{{ route('staff.nurse-logs.index') }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">Nurse Logs</a>
                        <a href="{{ route('staff.health-records') }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">Clinical Records</a>
                        <a href="{{ route('staff.prescriptions') }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">Prescriptions</a>
                        <a href="{{ route('clinical.life-safety.index') }}" class="rounded-md px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Life-Safety Alerts</a>
                    @elseif($user->user_type === \App\Models\User::TYPE_DOCTOR)
                        <a href="{{ route('doctor.dashboard') }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">Doctor Dashboard</a>
                        <a href="{{ route('doctor.patients') }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">Patients</a>
                        <a href="{{ route('doctor.health-records') }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">Clinical Records</a>
                        <a href="{{ route('doctor.prescriptions') }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">Prescriptions</a>
                        <a href="{{ route('clinical.life-safety.index') }}" class="rounded-md px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Life-Safety Alerts</a>
                    @elseif($user->user_type === \App\Models\User::TYPE_GUARDIAN)
                        <a href="{{ route('privacy-center.index') }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">Guardian Privacy Center</a>
                    @elseif($user->user_type === \App\Models\User::TYPE_PRINCIPAL)
                        <a href="{{ route('break-glass.index') }}" class="rounded-md bg-red-50 px-3 py-2 text-sm font-semibold text-red-800">Emergency Access</a>
                        <a href="{{ route('break-glass.audit') }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">Emergency Audit</a>
                    @elseif($user->user_type === \App\Models\User::TYPE_TEACHER)
                        <a href="{{ route('teacher.safety.index') }}" class="rounded-md bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-900">Student Life-Safety</a>
                    @elseif($user->user_type === \App\Models\User::TYPE_ADMIN)
                        <a href="{{ route('admin.dashboard') }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-gray-100">System Administration</a>
                    @endif
                </div>
            </div>

            <div class="hidden items-center gap-3 sm:flex">
                <div class="text-right"><div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</div><div class="text-xs text-gray-500">{{ $user->roleLabel() }}</div></div>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-md border px-3 py-2 text-sm font-medium hover:bg-gray-50 dark:text-white">Log out</button></form>
            </div>

            <button @click="open = !open" class="inline-flex items-center rounded-md p-2 text-gray-500 hover:bg-gray-100 sm:hidden" aria-label="Open navigation">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/><path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <div x-show="open" x-cloak class="border-t px-4 py-3 sm:hidden">
        <div class="mb-3"><div class="font-semibold">{{ $user->name }}</div><div class="text-xs text-gray-500">{{ $user->roleLabel() }}</div></div>
        <div class="space-y-1">
            @if(in_array($user->user_type, [\App\Models\User::TYPE_STUDENT, \App\Models\User::TYPE_FACULTY], true))
                <a href="{{ route('patients.dashboard') }}" class="block rounded px-3 py-2 hover:bg-gray-100">Dashboard</a>
                <a href="{{ route('patients.health.records') }}" class="block rounded px-3 py-2 hover:bg-gray-100">Health Records</a>
                <a href="{{ route('patients.appointments') }}" class="block rounded px-3 py-2 hover:bg-gray-100">Appointments</a>
                <a href="{{ route('privacy-center.index') }}" class="block rounded px-3 py-2 hover:bg-gray-100">Privacy & Consent</a>
            @elseif($user->user_type === \App\Models\User::TYPE_STAFF)
                <a href="{{ route('staff.dashboard') }}" class="block rounded px-3 py-2 hover:bg-gray-100">Nurse Dashboard</a>
                <a href="{{ route('staff.patients') }}" class="block rounded px-3 py-2 hover:bg-gray-100">Patients</a>
                <a href="{{ route('staff.nurse-logs.index') }}" class="block rounded px-3 py-2 hover:bg-gray-100">Nurse Logs</a>
                <a href="{{ route('staff.health-records') }}" class="block rounded px-3 py-2 hover:bg-gray-100">Clinical Records</a>
                <a href="{{ route('clinical.life-safety.index') }}" class="block rounded px-3 py-2 font-semibold text-red-700 hover:bg-red-50">Life-Safety Alerts</a>
            @elseif($user->user_type === \App\Models\User::TYPE_DOCTOR)
                <a href="{{ route('doctor.dashboard') }}" class="block rounded px-3 py-2 hover:bg-gray-100">Doctor Dashboard</a>
                <a href="{{ route('doctor.patients') }}" class="block rounded px-3 py-2 hover:bg-gray-100">Patients</a>
                <a href="{{ route('doctor.health-records') }}" class="block rounded px-3 py-2 hover:bg-gray-100">Clinical Records</a>
                <a href="{{ route('clinical.life-safety.index') }}" class="block rounded px-3 py-2 font-semibold text-red-700 hover:bg-red-50">Life-Safety Alerts</a>
            @elseif($user->user_type === \App\Models\User::TYPE_GUARDIAN)
                <a href="{{ route('privacy-center.index') }}" class="block rounded px-3 py-2 hover:bg-gray-100">Guardian Privacy Center</a>
            @elseif($user->user_type === \App\Models\User::TYPE_PRINCIPAL)
                <a href="{{ route('break-glass.index') }}" class="block rounded px-3 py-2 font-semibold text-red-700 hover:bg-red-50">Emergency Access</a>
            @elseif($user->user_type === \App\Models\User::TYPE_TEACHER)
                <a href="{{ route('teacher.safety.index') }}" class="block rounded px-3 py-2 font-semibold text-amber-900 hover:bg-amber-50">Student Life-Safety</a>
            @elseif($user->user_type === \App\Models\User::TYPE_ADMIN)
                <a href="{{ route('admin.dashboard') }}" class="block rounded px-3 py-2 hover:bg-gray-100">System Administration</a>
            @endif
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3 border-t pt-3">@csrf<button class="w-full rounded px-3 py-2 text-left hover:bg-gray-100">Log out</button></form>
    </div>
</nav>
