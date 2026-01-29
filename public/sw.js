self.addEventListener('install', function (event) {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    console.log('Service worker activated.');
});

self.addEventListener('fetch', function (event) {
    // Basic fetch handler (can be expanded for caching later if needed)
});
