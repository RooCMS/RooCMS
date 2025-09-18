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

function registerHeader(){
    if (!window.Alpine || typeof window.Alpine.data !== 'function') return false;

    window.Alpine.data('headerData', () => ({
        mobileMenuOpen: false,
        searchQuery: '',
        isLoggedIn: false,
        userName: '',
        currentPath: '',

        init() {
            this.currentPath = window.location.pathname;
            this.checkAuthStatus();
        },

        checkAuthStatus() {
            const token = localStorage.getItem('access_token') || this.getCookie('access_token');
            this.isLoggedIn = !!token;
            if (this.isLoggedIn) {
                this.userName = 'Пользователь';
            }
        },

        getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        },

        performSearch() {
            if (this.searchQuery.trim()) {
                if (window.notify) {
                    window.notify().show(`Поиск: "${this.searchQuery}"`, 'info');
                }
                this.searchQuery = '';
            }
        }
    }));

    return true;
}

// Component registration function
function registerComponentsAndInitHeader() {
    if (!window.Alpine || typeof window.Alpine.data !== 'function') {
        return false;
    }

    try {
        registerNotify();
        registerHeader();

        // Initialize header after components are registered
        setTimeout(() => {
            const header = document.querySelector('.modern-header');
            if (header) {
                header.removeAttribute('x-ignore');
                // Force Alpine to process the header
                if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                    window.Alpine.initTree(header);
                }
            }
        }, 10);

        return true;
    } catch (error) {
        console.error('Error registering components:', error);
        return false;
    }
}

// Try immediate registration if Alpine is already loaded
if (window.Alpine && typeof window.Alpine.data === 'function') {
    registerComponentsAndInitHeader();
}

// Also register on alpine:init event
document.addEventListener('alpine:init', registerComponentsAndInitHeader);

// Mark modules ready (used by potential starters)
window.__roocmsModulesReady = true;

// Global error handler
window.addEventListener('error', (e) => {
    console.error('Global error:', e.error);
    if (DEBUG) showToast('Произошла ошибка', 'error');
});