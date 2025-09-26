import { API_BASE_URL, DEBUG } from './config.js';

let access_token = localStorage.getItem('access_token');
let refresh_token = localStorage.getItem('refresh_token');
let is_refreshing = false;
const refresh_waiters = [];

/**
 * Sets access token in localStorage and global variable
 * @param {string|null} token - JWT access token or null to remove
 * @returns {void}
 */
export function setAccessToken(token) {
    access_token = token;
    if (token) {
        localStorage.setItem('access_token', token);
    } else {
        localStorage.removeItem('access_token');
    }
}

/**
 * Sets refresh token in localStorage and global variable
 * @param {string|null} token - JWT refresh token or null to remove
 * @returns {void}
 */
export function setRefreshToken(token) {
    refresh_token = token;
    if (token) {
        localStorage.setItem('refresh_token', token);
    } else {
        localStorage.removeItem('refresh_token');
    }
}

/**
 * Gets current access token
 * @returns {string|null} - Current access token or null if not set
 */
export function getAccessToken() {
    return access_token;
}

/**
 * @typedef {Object} RequestOptions
 * @property {string} [method='GET'] - HTTP method
 * @property {Object.<string, string>} [headers] - HTTP headers
 * @property {string|FormData|null} [body] - Request body
 * @property {string} [credentials='include'] - Cookies parameters
 */

/**
 * Executes HTTP request to API with automatic token refresh
 * @param {string} path - Path to API endpoint (e.g., '/v1/users/me')
 * @param {RequestOptions} [options={}] - Request options
 * @returns {Promise<Response>} - Promise with Response object
 * @throws {Error} - In case of network or server error
 */
export async function request(path, options = {}) {
    const headers = Object.assign({'Content-Type': 'application/json'}, options.headers || {});
    if (access_token) headers['Authorization'] = `Bearer ${access_token}`;
    const req = Object.assign({}, options, { headers, credentials: 'include' });

    const res = await fetch(API_BASE_URL + path, req);

    // Don't try to refresh token for auth endpoints that legitimately return 401
    if (res.status === 401 && path.includes('/auth/')) {
        return res;
    }

    if (res.status !== 401) return res;

    if (!is_refreshing) {
        is_refreshing = true;
        do_refresh_token().finally(() => {
            is_refreshing = false;
            while (refresh_waiters.length) {
                const resume = refresh_waiters.shift();
                try {
                    // Check if token was refreshed successfully
                    if (access_token) {
                        resume();
                    } else {
                        // Token refresh failed, reject the waiting request
                        resume(new Error('Token refresh failed'));
                    }
                } catch(e) {}
            }
        });
    }

    try {
        await new Promise((resolve, reject) => {
            refresh_waiters.push((error) => {
                if (error) {
                    reject(error);
                } else {
                    resolve();
                }
            });
        });
    } catch (error) {
        // Token refresh failed, return a response-like object
        const fakeResponse = {
            ok: false,
            status: 401,
            statusText: 'Unauthorized',
            json: async () => ({
                error: true,
                message: 'Authentication failed - token refresh failed',
                status_code: 401
            }),
            text: async () => 'Authentication failed - token refresh failed',
            headers: new Headers({ 'Content-Type': 'application/json' })
        };
        return fakeResponse;
    }

    if (access_token) headers['Authorization'] = `Bearer ${access_token}`; else delete headers['Authorization'];
    return fetch(API_BASE_URL + path, Object.assign({}, options, { headers, credentials: 'include' }));
}

/**
 * Refreshes access token using refresh token
 * @returns {Promise<void>} - Promise without return value
 * @throws {Error} - If refresh token is missing or request fails
 */
export async function do_refresh_token() {
    if (!refresh_token) {
        throw new Error('No refresh token');
    }

    try {
        const res = await fetch(API_BASE_URL + '/v1/auth/refresh', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ refresh_token: refresh_token }),
            credentials: 'include'
        });
        if (!res.ok) {
            access_token = null;
            localStorage.removeItem('access_token');
            return;
        }
        const data = await res.json();
        const token = data?.data?.access_token ?? data?.access_token ?? null;
        const newRefreshToken = data?.data?.refresh_token ?? data?.refresh_token ?? null;

        if (DEBUG) console.debug('token refreshed');
        setAccessToken(token);

        // Save new refresh token if provided
        if (newRefreshToken) {
            setRefreshToken(newRefreshToken);
        }
    } catch (e) {
        access_token = null;
        localStorage.removeItem('access_token');
    }
}
