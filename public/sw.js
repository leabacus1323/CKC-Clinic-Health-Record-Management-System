const CACHE_NAME = 'ctc-clinic-static-v4';
const OFFLINE_URL = '/offline.html';
const PRECACHE_URLS = [OFFLINE_URL, '/manifest.json', '/ckc.png'];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => cache.addAll(PRECACHE_URLS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // Never cache authenticated HTML or application data. Navigation remains network-first
    // and uses only the generic offline page when the network is unavailable.
    if (request.mode === 'navigate') {
        event.respondWith(fetch(request).catch(() => caches.match(OFFLINE_URL)));
        return;
    }

    const isStaticAsset = url.origin === self.location.origin && (
        url.pathname.startsWith('/build/assets/') ||
        /\.(?:css|js|png|jpe?g|webp|svg|gif|ico|woff2?)$/i.test(url.pathname)
    );

    if (!isStaticAsset) {
        return;
    }

    event.respondWith(
        caches.match(request).then((cached) => {
            if (cached) {
                return cached;
            }

            return fetch(request).then((response) => {
                if (response.ok) {
                    const copy = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
                }

                return response;
            });
        })
    );
});
