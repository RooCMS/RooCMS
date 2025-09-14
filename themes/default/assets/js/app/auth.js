import { request, setAccessToken } from './api.js';

export async function login(login, password) {
    const res = await request('/v1/auth/login', {
        method: 'POST',
        body: JSON.stringify({ login, password })
    });
    const j = await res.json();
    if (!res.ok) throw new Error(j?.message || 'Login failed');
    const token = j?.data?.access_token ?? j?.access_token;
    setAccessToken(token || null);
    return j?.data ?? j;
}

export async function logout() {
    try { await request('/v1/auth/logout', { method: 'POST' }); } catch(e) {}
    setAccessToken(null);
}


