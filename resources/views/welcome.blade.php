<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'CKC Clinic Health Record Management System') }}</title>
    <link rel="shortcut icon" href="{{ asset('ckc.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <main class="clinic-login-shell">
        <section class="relative z-10 w-full max-w-5xl overflow-hidden rounded-3xl bg-white shadow-2xl shadow-slate-900/10 lg:grid lg:grid-cols-[1.1fr_0.9fr]">
            <div class="relative overflow-hidden p-8 text-white sm:p-12" style="background:linear-gradient(145deg,#11565b,#15949b)">
                <div class="relative z-10 flex items-center gap-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-lg"><x-application-logo class="h-14 w-14" /></div>
                    <div><p class="text-xs font-bold uppercase tracking-[0.2em] text-white/70">Christ the King College</p><h1 class="mt-1 text-xl font-extrabold">Health Services</h1></div>
                </div>
                <div class="relative z-10 mt-14 max-w-xl">
                    <h2 class="text-4xl font-extrabold leading-tight tracking-tight sm:text-5xl">Health care support for the entire CKC community.</h2>
                    <p class="mt-6 text-sm leading-7 text-white/80">Authorized clinic staff, school nurses, doctors, administrators, and students can sign in. Students can schedule appointments, request medical certificates, and access approved health information.</p>
                </div>
                <div class="pointer-events-none absolute -bottom-28 -right-20 h-80 w-80 rounded-full border-[52px] border-white/5"></div>
            </div>
            <div class="flex items-center p-8 sm:p-12">
                <div class="w-full">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-teal-700">CKC Clinic Health Record Management System</p>
                    <h3 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-800">Access your portal</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-500">Use your assigned account to continue to the appropriate dashboard.</p>
                    <a href="{{ route('login') }}" class="clinic-button-primary mt-8 w-full py-3">Proceed to secure sign in</a>
                    <p class="mt-5 text-center text-xs leading-5 text-slate-400">Access is role-based, monitored, and protected by an automatic inactivity lock.</p>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
