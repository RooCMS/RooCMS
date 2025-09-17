// Global Alpine helpers and small UI utilities can be added here
function registerNotify(){
    if (!window.Alpine || typeof window.Alpine.data !== 'function') return false;
    window.Alpine.data('notify', () => ({
        visible: false,
        text: '',
        type: 'info',
        show(message, type = 'info', timeout = 3000) {
            this.text = message; this.type = type; this.visible = true;
            if (timeout > 0) setTimeout(() => { this.visible = false; }, timeout);
        }
    }));
    // Expose as named component usable as x-data="notify"
    if (typeof window.notify === 'undefined') {
        window.notify = () => ({
            visible: false,
            text: '',
            type: 'info',
            show(message, type = 'info', timeout = 3000) {
                this.text = message; this.type = type; this.visible = true;
                if (timeout > 0) setTimeout(() => { this.visible = false; }, timeout);
            }
        });
    }
    return true;
}

// Try immediate registration if Alpine is already present
registerNotify();

// Also register on alpine:init to catch the startup moment
document.addEventListener('alpine:init', registerNotify);

// Mark modules ready (used by potential starters)
window.__roocmsModulesReady = true;

// Global error handler
window.addEventListener('error', (e) => {
    console.error('Global error:', e.error);
    if (DEBUG) showToast('Произошла ошибка', 'error');
});


