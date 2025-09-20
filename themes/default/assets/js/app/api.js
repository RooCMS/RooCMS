import { API_BASE_URL, DEBUG } from './config.js';

let access_token = null;
let is_refreshing = false;
const refresh_waiters = [];

export function setAccessToken(token) {
    access_token = token;
}

export function getAccessToken() {
    return access_token;
}

export async function request(path, options = {}) {
    const headers = Object.assign({'Content-Type': 'application/json'}, options.headers || {});
    if (access_token) headers['Authorization'] = `Bearer ${access_token}`;
    const req = Object.assign({}, options, { headers, credentials: 'include' });

    const res = await fetch(API_BASE_URL + path, req);
    if (res.status !== 401) return res;

    if (!is_refreshing) {
        is_refreshing = true;
        refresh_token().finally(() => {
            is_refreshing = false;
            while (refresh_waiters.length) {
                const resume = refresh_waiters.shift();
                try { resume(); } catch(e) {}
            }
        });
    }
    await new Promise(resolve => refresh_waiters.push(resolve));

    if (access_token) headers['Authorization'] = `Bearer ${access_token}`; else delete headers['Authorization'];
    return fetch(API_BASE_URL + path, Object.assign({}, options, { headers, credentials: 'include' }));
}

export async function refresh_token() {
    try {
        const res = await fetch(API_BASE_URL + '/v1/auth/refresh', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: '{}',
            credentials: 'include'
        });
        if (!res.ok) { getAccessToken() = null; return; }
        const data = await res.json();
        getAccessToken() = data?.data?.access_token ?? data?.access_token ?? null;
        const token = data?.data?.access_token ?? data?.access_token ?? null;
        if (DEBUG) console.debug('token refreshed');
        setAccessToken(token);
    } catch (e) {
        getAccessToken() = null;
    }
}


