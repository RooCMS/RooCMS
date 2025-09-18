// Simple header utilities
window.headerUtils = {
    isLoggedIn: false,
    userName: 'Пользователь',

    init() {
        this.checkAuthStatus();
        this.setupKeyboardShortcuts();
    },

    checkAuthStatus() {
        const token = localStorage.getItem('access_token') || this.getCookie('access_token');
        this.isLoggedIn = !!token;
    },

    getCookie(name) {
        const value = '; ' + document.cookie;
        const parts = value.split('; ' + name + '=');
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    },

    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl+K for search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInputs = document.querySelectorAll('.search-input');
                if (searchInputs.length > 0) {
                    searchInputs[0].focus();
                }
            }
        });
    }
};

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => window.headerUtils.init());
} else {
    window.headerUtils.init();
}
