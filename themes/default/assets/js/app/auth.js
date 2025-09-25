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
    const userData = j?.data?.user;

    if (token) {
        setAccessToken(token);
        if (refreshToken) {
            setRefreshToken(refreshToken);
        }
        if (userData) {
            setUserData(userData);
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
    clearUserData();
    // Redirect to home page after logout
    window.location.href = '/';
}

export async function getCurrentUser() {
    try {
        const res = await request('/v1/users/me');
        if (res.ok) {
            const userData = await res.json();
            setUserData(userData.data || userData);
            return userData.data || userData;
        }
    } catch (e) {
        console.error('Failed to get current user:', e);
    }
    return null;
}

export function getUserData() {
    try {
        const data = localStorage.getItem('user_data');
        return data ? JSON.parse(data) : null;
    } catch (e) {
        return null;
    }
}

export function setUserData(userData) {
    if (userData) {
        localStorage.setItem('user_data', JSON.stringify(userData));
    } else {
        clearUserData();
    }
}

export function clearUserData() {
    localStorage.removeItem('user_data');
}