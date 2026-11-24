/**
 * ACP functionality
 * 
 * Handles the main functionality of the admin control panel.
 */

// Create Alpine component for ACP
import { init_custom_selects } from './helpers/customSelect.js?v=2';

let controls_initialized = false;
const init_controls = () => {
    const root = document.querySelector('.acp-shell');
    if (!root || controls_initialized) return;
    controls_initialized = true;
    init_custom_selects(root);
};
document.addEventListener('alpine:initialized', init_controls);
document.addEventListener('DOMContentLoaded', () => queueMicrotask(init_controls));

document.addEventListener('alpine:init', () => {
    window.Alpine.data('acp', () => ({
        init() {
            window.log('log', 'ACP initialized');
        }
    }));
}); 
