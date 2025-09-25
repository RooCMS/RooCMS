/**
 * Admin Panel Access Control
 *
 * Checks if user has admin privileges using global auth methods and redirects to 403 page if not authorized.
 * Should be included on all admin panel pages.
 */

import { isUserAdmin, isUserSuperAdmin } from './auth.js';

/**
 * Check admin access and redirect if not authorized
 */
function checkAdminAccess() {
    // Use global auth methods to check permissions
    if (!isUserAdmin() && !isUserSuperAdmin()) {
        redirectTo403();
        return false;
    }

    return true; // User is authorized
}

/**
 * Redirect to 403 page
 */
function redirectTo403() {
    window.location.href = '/403';
}

// Initialize admin access check when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    checkAdminAccess();
});

// Also check immediately if DOM is already loaded
if (document.readyState === 'loading') {
    // DOM is still loading, the event listener will handle it
} else {
    // DOM is already loaded, check immediately
    checkAdminAccess();
}
