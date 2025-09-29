/**
 * RooCMS Service Worker
 * easy Service Worker without dependencies for caching and offline functionality
 * 
 * Возможности:
 * - Caching static resources with configurable TTL - can be disabled
 * - Caching API responses with configurable TTL - can be disabled
 * - Offline fallback for navigation
 * - Background sync for delayed requests
 * 
 * Настройка:
 * - API_CACHE_ENABLE/STATIC_CACHE_ENABLE: enabling/disabling caching
 * - API_CACHE_TTL/STATIC_CACHE_TTL: cache TTL (0 = forever)
 */

const CACHE_NAME = 'roocms-v2';
const API_CACHE_NAME = 'roocms-v2-api-v1';
const OFFLINE_PAGE = '/offline';

/**
 * Enabling/disabling API requests caching
 * - true: API requests are cached according to rules (GET requests to CACHEABLE_API_PATTERNS)
 * - false: API requests are proxied directly without caching
 * @type {boolean}
 */
const API_CACHE_ENABLE = false;

/**
 * Enabling/disabling static resources caching
 * - true: Static files (CSS, JS, images) are cached
 * - false: Static resources are proxied directly without caching
 * @type {boolean}
 */
const STATIC_CACHE_ENABLE = true;

/**
 * List of resources to cache when installing Service Worker
 * @type {string[]}
 */
const PRECACHE_URLS = [
    '/',
    '/themes/default/assets/css/roocms.min.css',
    '/themes/default/assets/js/alpine.csp.min.js',
    '/themes/default/assets/js/app/main.js',
    '/themes/default/assets/js/app/api.js',
    '/themes/default/assets/js/app/auth.js',
    '/themes/default/assets/js/app/config.js',
    OFFLINE_PAGE
];

/**
 * API endpoints for caching
 * @type {RegExp[]}
 */
const CACHEABLE_API_PATTERNS = [
    /^\/api\/v1\/users\/me$/,
    /^\/api\/v1\/settings/,
    /^\/api\/v1\/content/
];

/**
 * Pre-defined time values in milliseconds
 */
const TIME_CONSTANTS = {
    MINUTE: 60 * 1000,
    HOUR: 60 * 60 * 1000,
    DAY: 24 * 60 * 60 * 1000,
    WEEK: 7 * 24 * 60 * 60 * 1000,
    NEVER: 0  // Cache forever
};

/**
 * API cache TTL in milliseconds (1 minute)
 * Set 0 or TIME_CONSTANTS.NEVER to disable cache expiration (cache forever)
 * Примеры: TIME_CONSTANTS.MINUTE * 5, TIME_CONSTANTS.HOUR, TIME_CONSTANTS.DAY
 * @type {number}
 */
const API_CACHE_TTL = TIME_CONSTANTS.MINUTE;

/**
 * Static resources cache TTL in milliseconds (1 hour)
 * Set 0 or TIME_CONSTANTS.NEVER to disable cache expiration (cache forever)
 * Примеры: TIME_CONSTANTS.HOUR, TIME_CONSTANTS.DAY, TIME_CONSTANTS.WEEK
 * @type {number}
 */
const STATIC_CACHE_TTL = TIME_CONSTANTS.HOUR;




/**
 * Protocol function for logging
 * @param {string} type - Log type
 * @param {string} message - Log message
 */
function protocol(type, message) {
    const logMethods = {
        log: console.log,
        error: console.error,
        warn: console.warn
    };
    
    const logMethod = logMethods[type];
    if (logMethod) {
        logMethod('[SW] ' + message);
    }
}

/**
 * Universal function for loading and caching
 * @param {Request} request - Request
 * @param {string|null} cacheName - Cache name (or null to disable caching)
 * @returns {Promise<Response>} - Promise with response
 */
async function fetchAndCache(request, cacheName) {
    try {
        const response = await fetch(request);
        
        if (cacheName && response.ok) {
            const cache = await caches.open(cacheName);
            await cache.put(request, response.clone());
        }
        
        return response;
    } catch (error) {
        if (cacheName) {
            const cache = await caches.open(cacheName);
            const cached = await cache.match(request);
            if (cached) {
                return cached;
            }
        }
        throw error;
    }
}

/**
 * Handling static resources (if caching is enabled)
 * @param {Request} request - Request
 * @returns {Promise<Response>} - Promise with response
 */
async function handleStaticRequest(request) {
    // If static caching is disabled - simply proxy the request
    if (!STATIC_CACHE_ENABLE) {
        protocol('log', 'Static caching disabled, proxying request:', request.url);
        try {
            return await fetch(request);
        } catch (error) {
            protocol('error', 'Static request failed (no cache):', request.url, error);
            return new Response(
                'Resource not available offline',
                { status: 503 }
            );
        }
    }

    try {
        const cache = await caches.open(CACHE_NAME);
        const cached = await cache.match(request);

        if (cached) {
            // Check cache TTL (if TTL > 0)
            if (STATIC_CACHE_TTL > 0) {
                const cacheDate = cached.headers.get('sw-cache-date');
                if (cacheDate && (Date.now() - parseInt(cacheDate)) < STATIC_CACHE_TTL) {
                    protocol('log', 'Static cache hit (fresh):', request.url);
                    return cached;
                } else {
                    protocol('log', 'Static cache expired:', request.url);
                    // Cache expired, continue loading
                }
            } else {
                // TTL = 0, cache forever
                protocol('log', 'Static cache hit (permanent):', request.url);
                return cached;
            }
        }

        // No cache or cache expired - load and cache
        const response = await fetch(request);
        if (response.ok) {
            const responseClone = response.clone();
            // Add header with current time for TTL check
            responseClone.headers.set('sw-cache-date', Date.now().toString());
            await cache.put(request, responseClone);
            protocol('log', 'Static cached:', request.url);
        }

        return response;
    } catch (error) {
        protocol('log', 'Static offline:', request.url);
        
        // In offline mode - search in cache
        const cache = await caches.open(CACHE_NAME);
        return cache.match(request) || new Response(
            'Resource not available offline',
            { status: 503 }
        );
    }
}

/**
 * Installation of Service Worker - precaching (if enabled)
 */
self.addEventListener('install', (event) => {
    protocol('log', 'Installing...');
    
    if (STATIC_CACHE_ENABLE) {
        event.waitUntil(
            caches.open(CACHE_NAME)
                .then((cache) => {
                    protocol('log', 'Precaching app shell');
                    return cache.addAll(PRECACHE_URLS);
                })
                .then(() => {
                    protocol('log', 'Installed successfully');
                    return self.skipWaiting();
                })
                .catch((error) => {
                    protocol('error', 'Installation failed:', error);
                })
        );
    } else {
        protocol('log', 'Static caching disabled, skipping precaching');
        event.waitUntil(self.skipWaiting());
    }
});

/**
 * Activation of Service Worker - clearing old caches
 */
self.addEventListener('activate', (event) => {
    protocol('log', 'Activating...');
    
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames.map((cacheName) => {
                        if (cacheName !== CACHE_NAME && cacheName !== API_CACHE_NAME) {
                            protocol('log', 'Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                protocol('log', 'Activated successfully');
                return self.clients.claim();
            })
    );
});

/**
 * Handling fetch requests - main caching logic
 */
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip requests to other domains
    if (url.origin !== location.origin) {
        return;
    }

    // Handling API requests
    if (url.pathname.startsWith('/api/')) {
        event.respondWith(handleApiRequest(request));
        return;
    }

    // Handling navigation requests
    if (request.mode === 'navigate') {
        event.respondWith(handleNavigateRequest(request));
        return;
    }

    // Handling static resources
    event.respondWith(handleStaticRequest(request));
});

/**
 * Handling API requests with caching (if enabled)
 * @param {Request} request - Request
 * @returns {Promise<Response>} - Promise with response
 */
async function handleApiRequest(request) {
    const url = new URL(request.url);
    
    // If API caching is disabled - simply proxy the request
    if (!API_CACHE_ENABLE) {
        protocol('log', 'API caching disabled, proxying request:', url.pathname);
        try {
            return await fetch(request);
        } catch (error) {
            protocol('error', 'API request failed (no cache):', url.pathname, error);
            return new Response(
                JSON.stringify({ error: 'Network Error', message: 'API request failed and caching is disabled' }),
                { status: 503, headers: { 'Content-Type': 'application/json' } }
            );
        }
    }

    const isGETRequest = request.method === 'GET';
    const isCacheableAPI = CACHEABLE_API_PATTERNS.some(pattern => 
        pattern.test(url.pathname)
    );

    // Cache only GET requests to specific API endpoints
    if (!isGETRequest || !isCacheableAPI) {
        return fetchAndCache(request, null);
    }

    try {
        const cache = await caches.open(API_CACHE_NAME);
        const cached = await cache.match(request);

        if (cached) {
            // Check cache TTL (if TTL > 0)
            if (API_CACHE_TTL > 0) {
                const cacheDate = cached.headers.get('sw-cache-date');
                if (cacheDate && (Date.now() - parseInt(cacheDate)) < API_CACHE_TTL) {
                    protocol('log', 'API cache hit (fresh):', url.pathname);
                    return cached;
                } else {
                    protocol('log', 'API cache expired:', url.pathname);
                    // Cache expired, continue loading
                }
            } else {
                // TTL = 0, cache forever
                protocol('log', 'API cache hit (permanent):', url.pathname);
                return cached;
            }
        }

        // Cache expired or missing - make request
        const response = await fetch(request);
        if (response.ok) {
            const responseClone = response.clone();
            responseClone.headers.set('sw-cache-date', Date.now().toString());
            await cache.put(request, responseClone);
            protocol('log', 'API cached:', url.pathname);
        }

        return response;
    } catch (error) {
        protocol('log', 'API offline, serving cache:', url.pathname);
        const cache = await caches.open(API_CACHE_NAME);
        const cached = await cache.match(request);
        return cached || new Response(
            JSON.stringify({ error: 'Offline', message: 'No cached data available' }),
            { status: 503, headers: { 'Content-Type': 'application/json' } }
        );
    }
}

/**
 * Handling navigation requests
 * @param {Request} request - Request
 * @returns {Promise<Response>} - Promise with response
 */
async function handleNavigateRequest(request) {
    try {
        // First try to get fresh version
        const response = await fetch(request);
        return response;
    } catch (error) {
        protocol('log', 'Navigation offline, serving cache');
        
        // If offline - search in cache
        const cache = await caches.open(CACHE_NAME);
        const cached = await cache.match(request);
        
        if (cached) {
            return cached;
        }

        // If cache is missing - send offline page
        return cache.match(OFFLINE_PAGE) || new Response(
            '<h1>Offline</h1><p>You are currently offline</p>',
            { headers: { 'Content-Type': 'text/html' } }
        );
    }
}

/**
 * Performing background sync
 * @returns {Promise<void>}
 */
async function doBackgroundSync() {
    protocol('log', 'Performing background sync...');
    // Here you can add logic for sending delayed data
    // For example, sending forms that were not sent in offline mode
}

/**
 * Background Sync for delayed data sending
 */
self.addEventListener('sync', (event) => {
    protocol('log', 'Background sync:', event.tag);
    
    if (event.tag === 'background-sync') {
        event.waitUntil(doBackgroundSync());
    }
});

/**
 * Handling push notifications (if needed in the future)
 */
self.addEventListener('push', (event) => {
    protocol('log', 'Push received:', event);
    
    if (event.data) {
        const data = event.data.json();
        
        event.waitUntil(
            self.registration.showNotification(data.title, {
                body: data.body,
                icon: '/favicon.ico',
                badge: '/favicon.ico'
            })
        );
    }
});

/**
 * Handling clicks on notifications
 */
self.addEventListener('notificationclick', (event) => {
    protocol('log', 'Notification clicked:', event);
    
    event.notification.close();
    
    event.waitUntil(
        self.clients.openWindow('/')
    );
});

/**
 * Log the Service Worker script loaded
 */
protocol('log', 'Service Worker script loaded');