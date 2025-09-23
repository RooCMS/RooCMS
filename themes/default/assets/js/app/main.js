// Global Alpine helpers and small UI utilities can be added here
import { logout } from './auth.js';
import { getAccessToken } from './api.js';


// Initialize when DOM is ready (CSP mode)
document.addEventListener('DOMContentLoaded', () => {
    if (window.Alpine) {
        window.Alpine.deferMutations();
    }

    Alpine = window.Alpine; // On any case ;)
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
        updateStatus() {
            this.isAuthenticated = !!getAccessToken();
        }
    });

    // Modal store component
    window.Alpine.data('modalStore', () => ({
        get $modal() { return window.Alpine.store('modal'); },
        get showCancelButton() { return this.$modal.cancel_text?.trim(); },
        confirm() { this.$modal.confirm(); },
        cancel() { this.$modal.cancel(); }
    }));

    // Auth buttons component
    window.Alpine.data('authButtons', () => ({
        isAuth: !!getAccessToken(),

        async logout() {
            await logout();
            this.isAuth = false;
        }
    }));
});


/**
 * Show modal dialog window
 * @param {string} title - Title of the modal window
 * @param {string} message - Message text
 * @param {string} confirm_text - Text of the confirm button (default "OK")
 * @param {string} cancel_text - Text of the cancel button (default "Cancel")
 * @param {string} type - Type of the modal: "alert", "notice", "warning" (affects the icon)
 * @returns {Promise<boolean>} - Returns true if the user clicked confirm
 */
export async function modal(title, message, confirm_text = "OK", cancel_text = "Cancel", type = "alert") {
    return await window.Alpine.store('modal').show(title, message, confirm_text, cancel_text, type);
}

// Global error handler
window.addEventListener('error', (e) => console.error('Global error:', e.error));