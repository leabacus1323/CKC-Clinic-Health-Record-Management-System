<x-guest-layout :wide="false">
    <div class="text-center">
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-amber-100 text-amber-700">
            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
        </div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Screen locked</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
            This session was locked to protect confidential health records. Enter your password to continue.
        </p>
    </div>

    <form method="POST" action="{{ route('security.lock.unlock') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required autofocus autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">Unlock securely</x-primary-button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button class="w-full text-sm font-medium text-gray-600 underline hover:text-gray-900 dark:text-gray-300">Sign out instead</button>
    </form>
</x-guest-layout>
