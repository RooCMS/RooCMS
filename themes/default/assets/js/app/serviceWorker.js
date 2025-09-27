/**
 * Service Worker Registration Utility
 * Easy Service Worker registration without dependencies
 */

/**
 * @typedef {Object} ServiceWorkerStatus
 * @property {boolean} supported - Is Service Worker supported in the browser
 * @property {boolean} registered - Is Service Worker registered
 * @property {ServiceWorkerRegistration|null} registration - Registration object
 * @property {Error|null} error - Registration error, if any
 */

/**
 * Path to Service Worker file
 * @type {string}
 */
const SW_PATH = '/themes/default/sw.min.js';

/**
 * Service Worker registration options
 * @type {Object}
 */
const SW_OPTIONS = {
    scope: '/themes/default/'
};


/**
 * Log function
 * @param {string} message - Log message
 */
function log(message) {
    console.log('[SW Registration] ' + message);
}

/**
 * Checks if Service Worker is supported in the browser
 * @returns {boolean} - true if Service Worker is supported
 */
export function isServiceWorkerSupported() {
    return 'serviceWorker' in navigator;
}

/**
 * Registers Service Worker
 * @returns {Promise<ServiceWorkerStatus>} - Promise with registration status
 */
export async function registerServiceWorker() {
    const status = {
        supported: isServiceWorkerSupported(),
        registered: false,
        registration: null,
        error: null
    };

    if (!status.supported) {
        log('Service Worker not supported');
        return status;
    }

    try {
        log('Registering Service Worker...');
        
        const registration = await navigator.serviceWorker.register(SW_PATH, SW_OPTIONS);
        
        status.registered = true;
        status.registration = registration;
        
        log('Service Worker registered successfully:', registration);

        // Event handlers for Service Worker lifecycle
        registration.addEventListener('updatefound', () => {
            log('New Service Worker version found');
            
            const newWorker = registration.installing;
            if (newWorker) {
                newWorker.addEventListener('statechange', () => {
                    log('Service Worker state:', newWorker.state);
                    
                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                        // New version installed, can show notification to user
                        notifyUserAboutUpdate();
                    }
                });
            }
        });

        // Check for updates periodically
        setInterval(() => {
            registration.update();
        }, 60000); // Check every minute

    } catch (error) {
        log('Failed to register Service Worker:', error);
        status.error = error;
    }

    return status;
}

/**
 * Unregisters Service Worker
 * @returns {Promise<boolean>} - Promise with result of unregistration
 */
export async function unregisterServiceWorker() {
    if (!isServiceWorkerSupported()) {
        return false;
    }

    try {
        const registration = await navigator.serviceWorker.getRegistration();
        if (registration) {
            const result = await registration.unregister();
            log('Service Worker unregistered:', result);
            return result;
        }
        return true;
    } catch (error) {
        log('Failed to unregister Service Worker:', error);
        return false;
    }
}

/**
 * Gets current Service Worker registration
 * @returns {Promise<ServiceWorkerRegistration|null>} - Promise with registration or null
 */
export async function getServiceWorkerRegistration() {
    if (!isServiceWorkerSupported()) {
        return null;
    }

    try {
        return await navigator.serviceWorker.getRegistration();
    } catch (error) {
        log('Failed to get Service Worker registration:', error);
        return null;
    }
}

/**
 * Checks if Service Worker is active
 * @returns {boolean} - true if Service Worker is active
 */
export function isServiceWorkerActive() {
    return !!(navigator.serviceWorker && navigator.serviceWorker.controller);
}

/**
 * Notifies user about available update
 * @private
 */
function notifyUserAboutUpdate() {
    log('New version available');
    
    // Can show notification to user about available update
    if (window.modal) {
        window.modal(
            'Update available',
            'A new version of the application is available. Update now?',
            'Update',
            'Later',
            'notice'
        ).then((confirmed) => {
            if (confirmed) {
                window.location.reload();
            }
        });
    } else {
        // Fallback if modal window is not available
        if (confirm('A new version of the application is available. Update now?')) {
            window.location.reload();
        }
    }
}

/**
 * Sends message to active Service Worker
 * @param {any} message - Message to send
 * @returns {Promise<any>} - Promise with response from Service Worker
 */
export async function sendMessageToServiceWorker(message) {
    if (!isServiceWorkerActive()) {
        throw new Error('Service Worker is not active');
    }

    return new Promise((resolve, reject) => {
        const messageChannel = new MessageChannel();
        
        messageChannel.port1.onmessage = (event) => {
            if (event.data.error) {
                reject(new Error(event.data.error));
            } else {
                resolve(event.data);
            }
        };

        navigator.serviceWorker.controller.postMessage(message, [messageChannel.port2]);
    });
}

/**
 * Initializes Service Worker when page is loaded
 * @returns {Promise<void>}
 */
export async function initServiceWorker() {
    if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost') {
        log('Service Worker requires HTTPS');
        return;
    }

    try {
        const status = await registerServiceWorker();
        
        if (status.registered) {
            log('Service Worker initialized successfully');
        } else if (status.error) {
            log('Service Worker initialization failed:', status.error);
        } else {
            log('Service Worker not supported');
        }
    } catch (error) {
        log('Service Worker initialization error:', error);
    }
}

// Automatic initialization of Service Worker when page is loaded
if (typeof window !== 'undefined') {
    window.addEventListener('load', initServiceWorker);
}

/**
 * Example usage:
 * 
 * import { registerServiceWorker, isServiceWorkerSupported } from './serviceWorker.js';
 * 
 * // Check support
 * if (isServiceWorkerSupported()) {
 *     log('Service Worker supported');
 * }
 * 
 * // Manual registration
 * registerServiceWorker().then(status => {
 *     if (status.registered) {
 *         log('SW registered');
 *     }
 * });
 * 
 * // Sending message to Service Worker
 * sendMessageToServiceWorker({ action: 'clearCache' })
 *     .then(response => log('SW response:', response));
 */
