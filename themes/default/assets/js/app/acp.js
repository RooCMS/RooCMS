/**
 * ACP functionality
 * 
 * Handles the main functionality of the admin control panel.
 */

// Create Alpine component for ACP
document.addEventListener('alpine:init', () => {
    window.Alpine.data('acp', () => ({
        init() {
            window.log('log', 'ACP initialized');
        }
    }));
}); 