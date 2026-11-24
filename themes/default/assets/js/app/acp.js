/**
 * ACP functionality
 * 
 * Handles the main functionality of the admin control panel.
 */

// Create Alpine component for ACP
import { init_custom_selects } from './helpers/customSelect.js';

const init_controls = () => {
    const root = document.querySelector('.acp-shell');
    if (root) init_custom_selects(root);
};
document.addEventListener('alpine:initialized', init_controls, { once: true });

document.addEventListener('alpine:init', () => {
    window.Alpine.data('acp', () => ({
        init() {
            window.log('log', 'ACP initialized');
        }
    }));
}); 
