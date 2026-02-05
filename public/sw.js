const CACHE_NAME = 'great10-v1';
const ASSETS = [
    '/',
    '/assets/style.css',
    '/assets/favicon.ico',
    '/offline.html'
];

// Install Event
self.addEventListener('install', (e) => {
    e.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(ASSETS);
        })
    );
});

// Fetch Event
self.addEventListener('fetch', (e) => {
    e.respondWith(
        caches.match(e.request).then((response) => {
            return response || fetch(e.request).catch(() => {
                // Fallback for navigation requests
                if (e.request.mode === 'navigate') {
                    return caches.match('/offline.html');
                }
            });
        })
    );
});

// Activate Event (Cleanup)
self.addEventListener('activate', (e) => {
    e.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            );
        })
    );
});
