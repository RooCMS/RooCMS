// Global Alpine helpers and small UI utilities can be added here


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
                    alert: '<svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>',
                    warning: '<svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>',
                    notice: '<svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
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
        isAuthenticated: !!localStorage.getItem('access_token'),
        updateStatus() {
            this.isAuthenticated = !!localStorage.getItem('access_token');
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
        isAuth: !!localStorage.getItem('access_token'),

        async logout() {
            try {
                const token = localStorage.getItem('access_token');
                if (token) {
                    await fetch('/api/v1/auth/logout', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json'
                        }
                    });
                }

                // Clear token
                localStorage.removeItem('access_token');
                this.isAuth = false;

                // Redirect to home page
                window.location.href = '/';

            } catch (error) {
                console.error('Logout error:', error);
                // Clear token anyway and redirect
                localStorage.removeItem('access_token');
                this.isAuth = false;
                window.location.href = '/';
            }
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