/**
 * Authentication and User Management Module
 *
 * This module provides global access to user data and authentication functions.
 * All user-related data is automatically synchronized with Alpine.js store.
 *
 * @global
 */
import { request, setAccessToken, setRefreshToken } from './api.js';


/**
 * Clears user data from localStorage
 * @returns {void}
 */
export function clearUserData() {
    localStorage.removeItem('user_data');
    // Update Alpine store if available
    if (window.Alpine && window.Alpine.store && window.Alpine.store('auth')) {
        window.Alpine.store('auth').user = null;
        window.Alpine.store('auth').isAuthenticated = false;
    }
}

/**
 * Sets user data in localStorage and Alpine store
 * @param {Object} userData - User data
 * @returns {void}
 */
export function setUserData(userData) {
    if (userData) {
        localStorage.setItem('user_data', JSON.stringify(userData));
        // Update Alpine store if available
        if (window.Alpine && window.Alpine.store && window.Alpine.store('auth')) {
            window.Alpine.store('auth').user = userData;
            window.Alpine.store('auth').isAuthenticated = true;
        }
    } else {
        clearUserData();
    }
}

/**
 * Logs in user
 * @param {string} login - User login
 * @param {string} password - User password
 * @returns {Promise<Object>} - User data
 */
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

/**
 * Registers user
 * @param {string} login - User login
 * @param {string} email - User email
 * @param {string} password - User password
 * @param {string} password_confirmation - User password confirmation
 * @returns {Promise<Object>} - User data
 */
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

/**
 * Logs out user
 * @returns {Promise<void>}
 */
export async function logout() {
    try { await request('/v1/auth/logout', { method: 'POST' }); } catch(e) {}
    setAccessToken(null);
    setRefreshToken(null);
    clearUserData();
    // Redirect to home page after logout
    window.location.href = '/';
}

/**
 * Gets current user
 * @returns {Promise<Object>} - User data
 */
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

/**
 * Gets user data from localStorage
 * @returns {Object|null} - User data
 */
export function getUserData() {
    try {
        const data = localStorage.getItem('user_data');
        return data ? JSON.parse(data) : null;
    } catch (e) {
        return null;
    }
}

/**
 * Gets user ID
 * Additional user data helper methods
 * @returns {number|null} - User ID
 */
export function getUserId() {
    const user = getUserData();
    return user?.id || null;
}

/**
 * Gets user login
 * @returns {string|null} - User login
 */
export function getUserLogin() {
    const user = getUserData();
    return user?.login || user?.username || null;
}

/**
 * Gets user email
 * @returns {string|null} - User email
 */
export function getUserEmail() {
    const user = getUserData();
    return user?.email || null;
}

/**
 * Gets user nickname
 * @returns {string|null} - User nickname
 */
export function getUserNickname() {
    const user = getUserData();
    return user?.nickname || user?.login || user?.username || null;
}

/**
 * Gets user role
 * @returns {string|null} - User role
 */
export function getUserRole() {
    const user = getUserData();
    return user?.role || null;
}

/**
 * Gets user avatar
 * @returns {string|null} - User avatar
 */
export function getUserAvatar() {
    const user = getUserData();
    return user?.avatar || user?.avatar_url || null;
}

export function getUserFullName() {
    const user = getUserData();
    return user?.full_name || user?.first_name + ' ' + user?.last_name || null;
}

export function isUserAdmin() {
    const role = getUserRole();
    return role === 'a' || role === 'su';
}

export function isUserSuperAdmin() {
    const role = getUserRole();
    return role === 'su';
}

export function isUserModerator() {
    const role = getUserRole();
    return role === 'm' || role === 'a' || role === 'su';
}

export function updateUserData(updates) {
    const currentUser = getUserData();
    if (currentUser) {
        const updatedUser = { ...currentUser, ...updates };
        setUserData(updatedUser);
        return updatedUser;
    }
    return null;
}


/**
 * Global User Data API Documentation
 * ==================================
 *
 * All user data functions automatically sync with Alpine.js store ($store.auth)
 *
 * Usage Examples:
 *
 * // Get user data
 * import { getUserId, getUserNickname, getUserRole } from './auth.js';
 *
 * const userId = getUserId();
 * const nickname = getUserNickname();
 * const role = getUserRole();
 *
 * // Check permissions
 * import { isUserAdmin, hasUserPermission } from './auth.js';
 *
 * if (isUserAdmin()) {
 *     // Show admin panel
 * }
 *
 * if (hasUserPermission('manage_content')) {
 *     // Allow content management
 * }
 *
 * // In Alpine.js templates:
 * <div x-show="$store.auth.isAdmin()">Admin Panel</div>
 * <span x-text="$store.auth.getNickname()"></span>
 * <img x-bind:src="$store.auth.getAvatar()" alt="Avatar">
 *
 * // Update user data
 * import { updateUserData } from './auth.js';
 *
 * updateUserData({ nickname: 'New Nickname' });
 *
 * // Available methods:
 * - getUserId() - Get user ID
 * - getUserLogin() - Get user login/username
 * - getUserEmail() - Get user email
 * - getUserNickname() - Get user nickname (fallback to login)
 * - getUserRole() - Get user role
 * - getUserAvatar() - Get user avatar URL
 * - getUserFullName() - Get user's full name
 * - isUserAdmin() - Check if user is admin
 * - isUserSuperAdmin() - Check if user is super admin
 * - isUserModerator() - Check if user is moderator or higher
 * - updateUserData(updates) - Update user data locally
 *
 * Alpine Store Methods ($store.auth):
 * - isAuthenticated - Boolean authentication status
 * - user - Full user object
 * - isAdmin() - Check admin status
 * - isSuperAdmin() - Check super admin status
 * - isModerator() - Check moderator status
 * - getId() - Get user ID
 * - getLogin() - Get user login
 * - getEmail() - Get user email
 * - getNickname() - Get user nickname
 * - getRole() - Get user role
 * - getAvatar() - Get user avatar
 * - getFullName() - Get user's full name
 * - updateUserData(updates) - Update user data
 * - refreshUserData() - Refresh data from server
 * - updateStatus() - Update authentication status
 */