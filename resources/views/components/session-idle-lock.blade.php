@auth
@if(!request()->routeIs('security.lock.*'))
<div id="idle-lock-warning" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/60 p-4" role="dialog" aria-modal="true" aria-labelledby="idle-lock-title">
    <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-2xl dark:bg-gray-800">
        <h2 id="idle-lock-title" class="text-xl font-semibold text-gray-900 dark:text-white">Screen locking soon</h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
            No activity was detected. This screen will lock in <strong><span id="idle-lock-countdown"></span> seconds</strong> to protect health records.
        </p>
        <div class="mt-5 flex gap-3">
            <button id="idle-lock-continue" type="button" class="flex-1 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Continue session</button>
            <button id="idle-lock-now" type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:text-gray-200">Lock now</button>
        </div>
    </div>
</div>
<script>
(() => {
    const timeoutMs = {{ max(120, (int) config('medical_security.idle_timeout_seconds', 180)) * 1000 }};
    const warningMs = {{ max(10, (int) config('medical_security.idle_warning_seconds', 30)) * 1000 }};
    const lockUrl = @json(route('security.lock.store'));
    const lockScreenUrl = @json(route('security.lock.show'));
    const activityUrl = @json(route('security.activity'));
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const modal = document.getElementById('idle-lock-warning');
    const countdown = document.getElementById('idle-lock-countdown');
    let deadline = Date.now() + timeoutMs;
    let timer;
    let locking = false;
    let lastActivityTouch = Date.now();

    const hideWarning = () => {
        modal?.classList.add('hidden');
        modal?.classList.remove('flex');
    };

    const touchServer = async () => {
        if (Date.now() - lastActivityTouch < 60000 || locking) return;
        lastActivityTouch = Date.now();

        try {
            const response = await fetch(activityUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (response.status === 423 || response.redirected) {
                locking = true;
                window.location.assign(lockScreenUrl);
            }
        } catch (_) {
            // Do not extend the local deadline when the server cannot be reached.
        }
    };

    const reset = () => {
        if (locking) return;
        deadline = Date.now() + timeoutMs;
        hideWarning();
        void touchServer();
    };

    const lock = async () => {
        if (locking) return;
        locking = true;
        try {
            await fetch(lockUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json, text/html'
                }
            });
        } finally {
            window.location.assign(lockScreenUrl);
        }
    };

    const tick = () => {
        const remaining = deadline - Date.now();
        if (remaining <= 0) {
            lock();
            return;
        }
        if (remaining <= warningMs) {
            modal?.classList.remove('hidden');
            modal?.classList.add('flex');
            if (countdown) countdown.textContent = String(Math.max(1, Math.ceil(remaining / 1000)));
        }
        timer = window.setTimeout(tick, 500);
    };

    ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll'].forEach(eventName => {
        window.addEventListener(eventName, reset, {passive: true});
    });
    document.getElementById('idle-lock-continue')?.addEventListener('click', reset);
    document.getElementById('idle-lock-now')?.addEventListener('click', lock);
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden && Date.now() >= deadline) lock();
    });
    tick();
})();
</script>
@endif
@endauth
