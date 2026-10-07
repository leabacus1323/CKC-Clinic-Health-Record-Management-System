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
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="CKC Clinic HRMS">
        <link rel="manifest" href="{{ asset('manifest.json') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @if(request()->routeIs('doctor.index', 'doctor.dashboard', 'doctor.ajax.dashboard', 'staff.index', 'staff.dashboard', 'staff.ajax.dashboard'))
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        @endif

        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').catch(() => {});
                });
            }

            let deferredPrompt;
            window.addEventListener('beforeinstallprompt', (event) => {
                event.preventDefault();
                deferredPrompt = event;
                const button = document.getElementById('pwa-install-btn');
                if (button) button.style.display = 'inline-flex';
            });

            window.addEventListener('appinstalled', () => {
                const button = document.getElementById('pwa-install-btn');
                if (button) button.style.display = 'none';
                deferredPrompt = null;
            });

            function installPWA() {
                if (!deferredPrompt) return;
                deferredPrompt.prompt();
                deferredPrompt.userChoice.finally(() => deferredPrompt = null);
            }
        </script>
    </head>
    <body class="clinic-body">
        <div x-data="{ sidebarOpen: false, profileOpen: false }" class="clinic-shell flex">
            <div class="flex-1 flex flex-col">
                @include('partials.clinic-navigation')

                <div class="clinic-page">
                    @isset($header)
                        <header class="border-b border-slate-200 bg-white">
                            <div class="clinic-content px-4 py-5 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <main id="main-content" class="clinic-main">
                        <div class="clinic-content">
                            @yield('content')
                            {{ $slot ?? '' }}
                        </div>
                    </main>
                </div>
            </div>
        </div>

        @yield('scripts')
        @stack('scripts')
        @stack('modals')
        <x-session-idle-lock />
    </body>
</html>
