// Global Alpine helpers and small UI utilities can be added here


// Initialize when DOM is ready (CSP mode)
document.addEventListener('DOMContentLoaded', () => {
    if (window.Alpine) {
        window.Alpine.deferMutations();
    }

    Alpine = window.Alpine;
});

// Global Alpine data
document.addEventListener('alpine:init', () => {
    // Mobile menu store
    Alpine.store('mobileMenu', {
        open: false,
        toggle() {
            this.open = !this.open;
        }
    });

    // Mobile menu
    Alpine.data('MobileMenu', () => ({
        mobileMenuOpen: false,
    }));
});


// Global error handler
window.addEventListener('error', (e) => {
    console.error('Global error:', e.error);
});