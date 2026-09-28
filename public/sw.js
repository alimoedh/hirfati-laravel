const CACHE_NAME = 'hirfati-v2';
const CACHE_URLS = [
    '/',
    '/manifest.json'
];

// التثبيت
self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(CACHE_URLS).catch(err => console.log('Cache error:', err)))
    );
});

// التنشيط
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(name => {
                    if (name !== CACHE_NAME) {
                        return caches.delete(name);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// اعتراض الطلبات — نستخدم الشبكة أولاً، ثم الكاش عند الفشل
self.addEventListener('fetch', event => {
    // تجاوز الطلبات غير GET
    if (event.request.method !== 'GET') return;

    // تجاوز طلبات API و admin
    const url = new URL(event.request.url);
    if (url.pathname.startsWith('/api') ||
        url.pathname.startsWith('/admin') ||
        url.pathname.startsWith('/login') ||
        url.pathname.startsWith('/auth')) {
        return;
    }

    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request);
        })
    );
});
