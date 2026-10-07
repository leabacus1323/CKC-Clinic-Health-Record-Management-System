<x-guest-layout>
    <div class="mb-8">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-teal-700">Secure account access</p>
        <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-800">Welcome back</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Sign in using your authorized CKC Clinic Health Record Management System account.</p>
    </div>

    <x-auth-session-status class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm font-medium text-emerald-800" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-2 block text-sm font-bold text-slate-700">Email address</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5h16v14H4V5Zm0 2 8 6 8-6"/></svg>
                </span>
                <input id="email" class="clinic-input pl-11" type="email" name="email" value="{{ old('email') }}" placeholder="name@ckc.edu.ph" required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="mb-2 flex items-center justify-between">
                <label for="password" class="block text-sm font-bold text-slate-700">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-bold text-teal-700 hover:text-teal-800 hover:underline" href="{{ route('password.request') }}">Forgot password?</a>
                @endif
            </div>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 10V8a6 6 0 1 1 12 0v2m-13 0h14v11H5V10Zm7 4v3"/></svg>
                </span>
                <input id="password" class="clinic-input pl-11 pr-12" type="password" name="password" placeholder="Enter your password" required autocomplete="current-password" />
                <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-teal-700" aria-label="Show password">
                    <svg id="eye-open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Zm9.5 2.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/></svg>
                    <svg id="eye-closed" class="hidden h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.1A10.7 10.7 0 0 1 12 5c6 0 9.5 7 9.5 7a15.8 15.8 0 0 1-3 3.8M6.2 6.2C3.8 8 2.5 12 2.5 12S6 19 12 19a9.8 9.8 0 0 0 3.2-.5"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-600">
            <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-teal-600 shadow-sm focus:ring-teal-500" name="remember">
            <span>Remember this account on this device</span>
        </label>

        <button type="submit" class="clinic-button-primary w-full py-3">Sign in</button>

        <div class="clinic-security-note">
            <div class="flex gap-3">
                <x-clinic-nav-icon name="privacy" class="mt-0.5 h-5 w-5 shrink-0" />
                <p>For your privacy, inactive sessions automatically lock after three minutes. Never share your account credentials.</p>
            </div>
        </div>
    </form>

    <script>
        document.getElementById('toggle-password')?.addEventListener('click', function () {
            const input = document.getElementById('password');
            const open = document.getElementById('eye-open');
            const closed = document.getElementById('eye-closed');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            open.classList.toggle('hidden', show);
            closed.classList.toggle('hidden', !show);
            this.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    </script>
</x-guest-layout>
