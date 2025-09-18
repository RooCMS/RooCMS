// Global Alpine helpers and small UI utilities can be added here

// Try immediate registration if Alpine is already loaded
if (window.Alpine && typeof window.Alpine.data === 'function') {
    console.log('Alpine is already loaded');
}

// Mark modules ready (used by potential starters)
window.__roocmsModulesReady = true;

// Global error handler
window.addEventListener('error', (e) => {
    console.error('Global error:', e.error);
});