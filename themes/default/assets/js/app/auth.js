import { request, setAccessToken, setRefreshToken } from './api.js';

export async function login(login, password) {
    const res = await request('/v1/auth/login', {
        method: 'POST',
        body: JSON.stringify({ login, password })
    });
    const j = await res.json();

    if (!res.ok) {
        const error = new Error(j?.message || 'Login failed');
        error.status = res.status;
        error.details = j?.details?.validation_errors || j?.errors || null;
        throw error;
    }

    const token = j?.data?.access_token;
    const refreshToken = j?.data?.refresh_token;

    if (token) {
        setAccessToken(token);
        if (refreshToken) {
            setRefreshToken(refreshToken);
        }
        return j.data;
    } else {
        throw new Error('Access token not found in response');
    }
}

export async function register(login, email, password, password_confirmation) {
    const res = await request('/v1/auth/register', {
        method: 'POST',
        body: JSON.stringify({ login, email, password, password_confirmation })
    });
    const j = await res.json();

    if (!res.ok) {
        const error = new Error(j?.message || 'Registration failed');
        error.status = res.status;
        error.details = j?.details?.validation_errors || j?.errors || null;
        throw error;
    }

    return j.data;
}

export async function forgotPassword(email) {
    const res = await request('/v1/auth/password/recovery', {
        method: 'POST',
        body: JSON.stringify({ email })
    });
    const j = await res.json();

    if (!res.ok) {
        const error = new Error(j?.message || 'Password recovery failed');
        error.status = res.status;
        error.details = j?.details?.validation_errors || j?.errors || null;
        throw error;
    }

    return j.data;
}

export async function resetPassword(token, password, password_confirmation) {
    const res = await request('/v1/auth/password/reset', {
        method: 'POST',
        body: JSON.stringify({ token, password, password_confirmation })
    });
    const j = await res.json();

    if (!res.ok) {
        const error = new Error(j?.message || 'Password reset failed');
        error.status = res.status;
        error.details = j?.details?.validation_errors || j?.errors || null;
        throw error;
    }

    return j.data;
}

export async function logout() {
    try { await request('/v1/auth/logout', { method: 'POST' }); } catch(e) {}
    setAccessToken(null);
    setRefreshToken(null);
    // Redirect to home page after logout
    window.location.href = '/';
}