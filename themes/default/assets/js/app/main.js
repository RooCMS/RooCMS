// Global Alpine helpers and small UI utilities can be added here


// Initialize when DOM is ready (CSP mode)
document.addEventListener('DOMContentLoaded', () => {
    if (window.Alpine) {
        window.Alpine.deferMutations();
    }

    Alpine = window.Alpine;
});


// Mark modules ready (used by potential starters)
window.__roocmsModulesReady = true;

// Global error handler
window.addEventListener('error', (e) => {
    console.error('Global error:', e.error);
});