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


// Global error handler
window.addEventListener('error', (e) => {
    console.error('Global error:', e.error);
});