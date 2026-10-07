{{-- Patient Dashboard Partial for Shell --}}
@php
    $appointmentCount = isset($appointments) ? $appointments->count() : 0;
    $pendingCount = isset($appointments) ? $appointments->filter(fn ($item) => strtolower((string) $item->status) === 'pending')->count() : 0;
@endphp

<div class="space-y-6">
    <div class="clinic-page-heading">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-teal-700">Student health portal</p>
            <h1 class="clinic-page-title">Welcome, {{ Auth::user()->name }}</h1>
            <p class="clinic-page-subtitle">Schedule clinic visits, request medical certificates, and review your approved health information.</p>
        </div>
        <a href="{{ route('patients.schedule.appointment') }}" class="clinic-button-primary"><x-clinic-nav-icon name="calendar" class="h-5 w-5" />Set appointment</a>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-800">{{ session('info') }}</div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="clinic-stat-card"><div class="flex items-start justify-between"><div><p class="text-sm font-semibold text-slate-500">Appointments</p><p class="mt-3 text-4xl font-extrabold text-slate-800">{{ $appointmentCount }}</p><p class="mt-1 text-xs text-slate-400">Recent schedule</p></div><span class="clinic-stat-icon"><x-clinic-nav-icon name="calendar" class="h-6 w-6" /></span></div></div>
        <div class="clinic-stat-card"><div class="flex items-start justify-between"><div><p class="text-sm font-semibold text-slate-500">Pending</p><p class="mt-3 text-4xl font-extrabold text-slate-800">{{ $pendingCount }}</p><p class="mt-1 text-xs text-slate-400">Awaiting confirmation</p></div><span class="clinic-stat-icon"><x-clinic-nav-icon name="alert" class="h-6 w-6" /></span></div></div>
        <div class="clinic-stat-card"><div class="flex items-start justify-between"><div><p class="text-sm font-semibold text-slate-500">Health Profile</p><p class="mt-3 text-xl font-extrabold text-slate-800">{{ $patient ? 'Active' : 'Incomplete' }}</p><p class="mt-1 text-xs text-slate-400">Personal information</p></div><span class="clinic-stat-icon"><x-clinic-nav-icon name="records" class="h-6 w-6" /></span></div></div>
        <div class="clinic-stat-card"><div class="flex items-start justify-between"><div><p class="text-sm font-semibold text-slate-500">Privacy</p><p class="mt-3 text-xl font-extrabold text-slate-800">Protected</p><p class="mt-1 text-xs text-slate-400">Consent-controlled access</p></div><span class="clinic-stat-icon"><x-clinic-nav-icon name="privacy" class="h-6 w-6" /></span></div></div>
    </div>

    <section class="clinic-card">
        <div class="clinic-card-header"><div><h2 class="text-lg font-extrabold text-slate-800">Student Services</h2><p class="text-xs text-slate-500">Common health-service actions</p></div></div>
        <div class="clinic-card-body grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('patients.schedule.appointment') }}" class="group rounded-2xl border border-slate-200 p-5 transition hover:border-teal-300 hover:bg-teal-50">
                <span class="clinic-stat-icon"><x-clinic-nav-icon name="calendar" class="h-5 w-5" /></span><h3 class="mt-4 font-extrabold text-slate-800">Schedule Appointment</h3><p class="mt-1 text-xs leading-5 text-slate-500">Book a clinic consultation at an available time.</p><span class="mt-4 inline-flex text-xs font-bold text-teal-700">Schedule now →</span>
            </a>
            <a href="{{ route('patients.certificates.create') }}" class="group rounded-2xl border border-slate-200 p-5 transition hover:border-teal-300 hover:bg-teal-50">
                <span class="clinic-stat-icon"><x-clinic-nav-icon name="certificate" class="h-5 w-5" /></span><h3 class="mt-4 font-extrabold text-slate-800">Medical Certificate</h3><p class="mt-1 text-xs leading-5 text-slate-500">Submit and monitor a certification request.</p><span class="mt-4 inline-flex text-xs font-bold text-teal-700">Request certificate →</span>
            </a>
            <a href="#" onclick="switchTab('health-records'); return false;" class="group rounded-2xl border border-slate-200 p-5 transition hover:border-teal-300 hover:bg-teal-50">
                <span class="clinic-stat-icon"><x-clinic-nav-icon name="records" class="h-5 w-5" /></span><h3 class="mt-4 font-extrabold text-slate-800">Health Information</h3><p class="mt-1 text-xs leading-5 text-slate-500">Review approved history, immunization, and prescription details.</p><span class="mt-4 inline-flex text-xs font-bold text-teal-700">View information →</span>
            </a>
            <a href="{{ route('privacy-center.index') }}" class="group rounded-2xl border border-slate-200 p-5 transition hover:border-teal-300 hover:bg-teal-50">
                <span class="clinic-stat-icon"><x-clinic-nav-icon name="privacy" class="h-5 w-5" /></span><h3 class="mt-4 font-extrabold text-slate-800">Privacy & Consent</h3><p class="mt-1 text-xs leading-5 text-slate-500">Manage guardians and data authorizations.</p><span class="mt-4 inline-flex text-xs font-bold text-teal-700">Manage consent →</span>
            </a>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.45fr_1fr]">
        <section class="clinic-card overflow-hidden">
            <div class="clinic-card-header"><div><h2 class="text-lg font-extrabold text-slate-800">Recent Appointments</h2><p class="text-xs text-slate-500">Your latest clinic schedule</p></div><a href="{{ route('patients.appointments') }}" class="text-sm font-bold text-teal-700 hover:underline">View all</a></div>
            @if(isset($appointments) && $appointments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="clinic-table">
                        <thead><tr><th>Provider</th><th>Date</th><th>Time</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($appointments as $appointment)
                                @php($status = strtolower((string) $appointment->status))
                                <tr><td class="font-semibold text-slate-800">Dr. {{ $appointment->doctor->user->name ?? 'To be assigned' }}</td><td>{{ $appointment->date }}</td><td>{{ $appointment->time }}</td><td><span class="clinic-badge {{ in_array($status, ['confirmed', 'completed']) ? 'bg-emerald-100 text-emerald-800' : ($status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">{{ ucfirst($status ?: 'pending') }}</span></td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-10 text-center"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400"><x-clinic-nav-icon name="calendar" class="h-6 w-6" /></span><p class="mt-3 text-sm font-semibold text-slate-600">No appointments yet</p><a href="{{ route('patients.schedule.appointment') }}" class="mt-2 inline-flex text-xs font-bold text-teal-700 hover:underline">Schedule your first appointment</a></div>
            @endif
        </section>

        <section class="clinic-card">
            <div class="clinic-card-header"><h2 class="text-lg font-extrabold text-slate-800">Emergency Profile</h2></div>
            <div class="clinic-card-body">
                @if($patient)
                    <dl class="space-y-4 text-sm">
                        <div class="rounded-xl bg-slate-50 p-4"><dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Allergies</dt><dd class="mt-1 font-semibold text-slate-800">{{ $patient->allergies ?: 'None recorded' }}</dd></div>
                        <div class="rounded-xl bg-slate-50 p-4"><dt class="text-xs font-bold uppercase tracking-wider text-slate-400">Emergency contact</dt><dd class="mt-1 font-semibold text-slate-800">{{ $patient->emergency_contact_name ?: 'Not provided' }}</dd><dd class="text-xs text-slate-500">{{ $patient->emergency_relationship }} {{ $patient->emergency_contact_number ? '· '.$patient->emergency_contact_number : '' }}</dd></div>
                    </dl>
                    <a href="#" onclick="switchTab('profile'); return false;" class="clinic-button-secondary mt-4 w-full">Update profile</a>
                @else
                    <p class="text-sm leading-6 text-slate-500">Complete your health profile so the clinic can provide appropriate support.</p><a href="{{ route('patients.profile.create') }}" class="clinic-button-primary mt-4 w-full">Complete profile</a>
                @endif
            </div>
        </section>
    </div>
</div>
