// Global Alpine helpers and small UI utilities can be added here
import { getAccessToken } from './api.js';
import {
    logout,
    getCurrentUser,
    getUserData,
    getUserId,
    getUserLogin,
    getUserEmail,
    getUserNickname,
    getUserRole,
    getUserAvatar,
    getUserFullName,
    isUserAdmin,
    isUserSuperAdmin,
    isUserModerator,
    updateUserData
} from './auth.js';

// Import utilities and make them globally available
import * as ValidationUtils from './helpers/validation.js';
import * as FormatterUtils from './helpers/formatters.js';
import * as FormHelperUtils from './helpers/formHelpers.js';

// Make utilities globally available
window.ValidationUtils = ValidationUtils;
window.FormatterUtils = FormatterUtils;
window.FormHelperUtils = FormHelperUtils;


// Initialize when DOM is ready (CSP mode)
document.addEventListener('DOMContentLoaded', async () => {
    if (window.Alpine) {
        window.Alpine.deferMutations();
    }

    Alpine = window.Alpine; // On any case ;)

    // Load user data if authenticated
    if (getAccessToken()) {
        const userData = getUserData();
        if (!userData) {
            // If we have a token but no user data, fetch it
            await getCurrentUser();
        }
    }
});

// Global Alpine data
document.addEventListener('alpine:init', () => {

    // Modal store
    window.Alpine.store('modal', {
        isOpen: false,
        title: '', message: '', confirm_text: '', cancel_text: '', type: 'alert',
        resolve: null,

        show(title, message, confirm_text = "OK", cancel_text = "Cancel", type = "alert") {
            return new Promise((resolve) => {
                this.title = title;
                this.message = message.replace(/\n/g, '<br>');
                this.confirm_text = confirm_text;
                this.cancel_text = cancel_text;
                this.type = type;
                this.resolve = resolve;
                this.isOpen = true;
                this.setIcon(type);
            });
        },

        setIcon(type) {
            window.Alpine.nextTick(() => {
                const modalIcon = document.querySelector('#modal-icon');
                if (!modalIcon) return;

                const icons = {
                    alert: '<svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>',
                    warning: '<svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>',
                    notice: '<svg class="w-10 h-10 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    success: '<svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                    default: ''
                };
                modalIcon.innerHTML = icons[type] || icons.default;
            });
        },

        confirm() { if (this.resolve) { this.resolve(true); this.resolve = null; this.isOpen = false; } },
        cancel() { if (this.resolve) { this.resolve(false); this.resolve = null; this.isOpen = false; } }
    });

    // Mobile menu store
    window.Alpine.store('mobileMenu', {
        open: false,
        toggle() {
            this.open = !this.open;
        }
    });

    // Auth store
    window.Alpine.store('auth', {
        isAuthenticated: !!getAccessToken(),
        user: getUserData(),

        // Update authentication status and user data
        updateStatus() {
            this.isAuthenticated = !!getAccessToken();
            this.user = getUserData();
        },

        // Role and permission checks
        isAdmin() {
            return isUserAdmin();
        },

        isSuperAdmin() {
            return isUserSuperAdmin();
        },

        isModerator() {
            return isUserModerator();
        },

        hasPermission(permission) {
            return hasUserPermission(permission);
        },

        // User data getters
        getId() {
            return getUserId();
        },

        getLogin() {
            return getUserLogin();
        },

        getEmail() {
            return getUserEmail();
        },

        getNickname() {
            return getUserNickname();
        },

        getRole() {
            return getUserRole();
        },

        getAvatar() {
            return getUserAvatar();
        },

        getFullName() {
            return getUserFullName();
        },

        // Update user data
        updateUserData(updates) {
            const updatedUser = updateUserData(updates);
            if (updatedUser) {
                this.user = updatedUser;
                return true;
            }
            return false;
        },

        // Refresh user data from server
        async refreshUserData() {
            if (this.isAuthenticated) {
                const userData = await getCurrentUser();
                if (userData) {
                    this.user = userData;
                    return userData;
                }
            }
            return null;
        }
    });

    // Modal store component
    window.Alpine.data('modalStore', () => ({
        get $modal() { return window.Alpine.store('modal'); },
        get showCancelButton() { return this.$modal.cancel_text?.trim(); },
        confirm() { this.$modal.confirm(); },
        cancel() { this.$modal.cancel(); }
    }));

    // Auth buttons component - handles authentication UI state
    window.Alpine.data('authButtons', () => ({
        // Get authentication status from global auth system
        isAuth: !!getAccessToken(),

        // Logout function with global state update
        async logout() {
            await logout(); // Use global logout function
            this.isAuth = false;
            window.Alpine.store('auth').updateStatus(); // Update global auth store
        }
    }));
});


/**
 * Show modal dialog window
 * @param {string} title - Title of the modal window
 * @param {string} message - Message text
 * @param {string} confirm_text - Text of the confirm button (default "OK")
 * @param {string} cancel_text - Text of the cancel button (default "Cancel")
 * @param {string} type - Type of the modal: "alert", "notice", "warning", "success" (affects the icon)
 * @returns {Promise<boolean>} - Returns true if the user clicked confirm
 */
export async function modal(title, message, confirm_text = "OK", cancel_text = "Cancel", type = "alert") {
    // Check if modal store is available
    if (window.Alpine && window.Alpine.store && window.Alpine.store('modal')) {
        return await window.Alpine.store('modal').show(title, message, confirm_text, cancel_text, type);
    }
    return false;
}

/**
 * Show a message modal (no cancel button)
 * @param {string} title - Title of the modal window
 * @param {string} message - Message text
 * @param {string} type - Type of the modal: "notice", "warning", "success" (affects the icon)
 * @returns {Promise<boolean>} - Always returns true when dismissed
 */
export async function showMessage(title, message, type = "notice") {
    return await modal(title, message, "OK", "", type);
}

// Make modal functions globally available
window.modal = modal;
window.showMessage = showMessage;

// Global error handler
window.addEventListener('error', (e) => console.error('Global error:', e.error));