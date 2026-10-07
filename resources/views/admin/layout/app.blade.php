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
    <body class="clinic-body">
        <div x-data="{ sidebarOpen: false, profileOpen: false }" class="clinic-shell">
            @include('partials.clinic-navigation')

            <div class="clinic-page">
                <main id="main-content" class="clinic-main">
                    <div class="clinic-content">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>

        @yield('scripts')
        @stack('scripts')
        @stack('modals')
        <x-session-idle-lock />
    </body>
</html>
