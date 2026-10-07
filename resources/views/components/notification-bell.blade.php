<div x-data="notificationBell()" class="relative" @keydown.escape.window="open = false">
    <button type="button" @click="open = !open" class="relative rounded-xl p-2 text-white/90 transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/40" aria-label="Open notifications" :aria-expanded="open.toString()">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 0 0-4-5.7V5a2 2 0 1 0-4 0v.3A6 6 0 0 0 6 11v3.2a2 2 0 0 1-.6 1.4L4 17h11Zm0 0v1a3 3 0 1 1-6 0v-1h6Z"/></svg>
        <span x-cloak x-show="notificationCount > 0" class="absolute right-0 top-0 flex h-5 items-center justify-center rounded-full px-1 text-[10px] font-extrabold text-white" style="min-width:1.25rem;background:#e11d48;box-shadow:0 0 0 2px #0f7f86" x-text="notificationCount > 99 ? '99+' : notificationCount"></span>
    </button>

    <div x-cloak x-show="open" x-transition.origin.top.right @click.outside="open = false" class="fixed left-4 right-4 top-16 z-50 mt-2 overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-700 shadow-2xl sm:absolute sm:left-auto sm:right-0 sm:top-full sm:w-96" style="max-height:calc(100vh - 5rem)">
        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-4 py-3">
            <div><h3 class="text-sm font-extrabold text-slate-800">Notifications</h3><p class="text-[11px] text-slate-500">Appointments, requests, and announcements</p></div>
            <button type="button" @click="fetchNotifications()" class="rounded-lg p-2 text-slate-500 hover:bg-white hover:text-teal-700" aria-label="Refresh notifications">
                <svg class="h-4 w-4" :class="loading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 11a8 8 0 1 0 2 5m0 0v-5m0 5h-5"/></svg>
            </button>
        </div>

        <div class="overflow-y-auto sm:max-h-96" style="max-height:calc(100vh - 10rem)">
            <template x-if="loading && notificationCount === 0"><div class="p-8 text-center text-sm text-slate-500">Loading notifications…</div></template>
            <template x-if="!loading && notificationCount === 0"><div class="p-8 text-center"><svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 0 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h11Zm0 0v1a3 3 0 1 1-6 0v-1h6Z"/></svg><p class="mt-3 text-sm font-semibold text-slate-600">No active notifications</p></div></template>

            <template x-for="section in sections" :key="section.key">
                <div x-show="notifications[section.key] && notifications[section.key].length" class="border-b border-slate-100 last:border-0">
                    <div class="sticky top-0 z-10 flex items-center justify-between px-4 py-2" :class="section.headerClass"><h4 class="text-[11px] font-extrabold uppercase tracking-wider" x-text="section.label"></h4><span class="rounded-full bg-white/70 px-2 py-0.5 text-[10px] font-bold" x-text="sectionTotal(section.key)"></span></div>
                    <template x-for="item in notifications[section.key]" :key="item.id">
                        <a :href="item.url || '#notification'" @click="if (!item.url) $event.preventDefault()" class="block border-t border-slate-100 px-4 py-3 transition hover:bg-slate-50">
                            <div class="flex items-start justify-between gap-3"><p class="text-sm font-bold text-slate-800" x-text="item.title"></p><span x-show="item.count > 1" class="shrink-0 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-extrabold text-slate-600" x-text="item.count"></span></div>
                            <p class="mt-1 text-xs leading-5 text-slate-500" x-text="item.message || formatDate(item.created_at)"></p>
                            <span x-show="item.status" class="mt-2 inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold capitalize text-amber-800" x-text="item.status"></span>
                        </a>
                    </template>
                </div>
            </template>
        </div>

        <div x-show="notificationCount > 0" class="border-t border-slate-100 bg-slate-50 px-4 py-2.5 text-center text-[11px] font-semibold text-slate-500"><span x-text="notificationCount"></span> active notification<span x-show="notificationCount !== 1">s</span></div>
    </div>
</div>

<script>
    function notificationBell() {
        return {
            open: false,
            loading: false,
            notifications: { appointments: [], requests: [], announcements: [] },
            notificationCount: 0,
            endpoint: @js(route('notifications.get')),
            sections: [
                { key: 'appointments', label: 'Appointments', headerClass: 'bg-sky-50 text-sky-700' },
                { key: 'requests', label: 'Requests', headerClass: 'bg-emerald-50 text-emerald-800' },
                { key: 'announcements', label: 'Announcements', headerClass: 'bg-teal-50 text-teal-700' },
            ],
            init() {
                this.fetchNotifications();
                this.timer = setInterval(() => this.fetchNotifications(), 60000);
            },
            sectionTotal(key) {
                return (this.notifications[key] || []).reduce((total, item) => total + Math.max(1, Number(item.count || 1)), 0);
            },
            formatDate(value) {
                if (!value) return '';
                return new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(new Date(value));
            },
            async fetchNotifications() {
                this.loading = true;
                try {
                    const response = await fetch(this.endpoint, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin', cache: 'no-store' });
                    if (response.status === 401 || response.status === 419) { window.location.reload(); return; }
                    if (!response.ok) throw new Error(`Notification request failed (${response.status})`);
                    const data = await response.json();
                    this.notifications = {
                        appointments: Array.isArray(data.appointments) ? data.appointments : [],
                        requests: Array.isArray(data.requests) ? data.requests : [],
                        announcements: Array.isArray(data.announcements) ? data.announcements : [],
                    };
                    this.notificationCount = Number(data.total || 0);
                } catch (error) {
                    console.error('Unable to load notifications:', error);
                } finally {
                    this.loading = false;
                }
            },
        };
    }
</script>
