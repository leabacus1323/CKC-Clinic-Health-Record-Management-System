<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CKC Clinic Health Record Management System') }}</title>
        <link rel="shortcut icon" href="{{ asset('ckc.png') }}" type="image/png">
        <link rel="apple-touch-icon" href="{{ asset('ckc.png') }}">
        <meta name="theme-color" content="#0f7f86">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-800 antialiased">
        <main class="clinic-login-shell">
            <div class="clinic-login-card {{ $wide ? 'max-w-6xl' : 'max-w-5xl' }}">
                <section class="clinic-login-brand-panel">
                    <div class="relative z-10">
                        <div class="mb-10 flex items-center gap-4">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-lg ring-4 ring-white/15">
                                <x-application-logo class="h-14 w-14 object-contain" />
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-white/70">Christ the King College</p>
                                <h1 class="mt-1 text-xl font-extrabold">CKC Clinic Health Record Management System</h1>
                            </div>
                        </div>

                        <h2 class="max-w-md text-4xl font-extrabold leading-tight tracking-tight">Secure, organized, and responsive campus health services.</h2>
                        <p class="mt-5 max-w-md text-sm leading-7 text-white/80">
                            Authorized clinic staff, school nurses, doctors, administrators, and students can sign in. Students can schedule appointments, request medical certificates, and access approved health information.
                        </p>
                    </div>

                    <div class="relative z-10 grid gap-3 text-sm text-white/85">
                        <div class="flex items-center gap-3 rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/15"><x-clinic-nav-icon name="shield" class="h-5 w-5" /></span>
                            <div><p class="font-bold text-white">Privacy-first access</p><p class="text-xs text-white/70">Role-based records and permanent audit tracking.</p></div>
                        </div>
                        <div class="flex items-center gap-3 rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/15"><x-clinic-nav-icon name="calendar" class="h-5 w-5" /></span>
                            <div><p class="font-bold text-white">Student self-service</p><p class="text-xs text-white/70">Appointments, certificates, and health information in one portal.</p></div>
                        </div>
                    </div>

                    <div class="pointer-events-none absolute -bottom-24 -right-20 h-72 w-72 rounded-full border-[42px] border-white/5"></div>
                    <div class="pointer-events-none absolute -left-16 top-48 h-40 w-40 rounded-full bg-white/5"></div>
                </section>

                <section class="clinic-login-form-panel">
                    <div class="w-full">
                        <div class="clinic-mobile-brand">
                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white shadow ring-2 ring-teal-100">
                                <x-application-logo class="h-12 w-12 object-contain" />
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.16em] text-teal-700">Christ the King College</p>
                                <p class="text-sm font-extrabold text-slate-800">Health Services</p>
                            </div>
                        </div>

                        {{ $slot }}
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
