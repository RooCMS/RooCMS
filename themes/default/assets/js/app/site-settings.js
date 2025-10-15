import { request } from './api.js';

// Global settings object
const SiteSetting = {};

// Load settings from API
(async () => {
    try {
        const response = await request('/v1/settings');
        const data = await response.json();

        if (data.data) {
            // Flatten all settings to direct properties
            for (const category in data.data) {
                for (const key in data.data[category]) {
                    SiteSetting[key] = data.data[category][key];
                }
            }
        }
    } catch (error) {
        window.log('error', 'Failed to load site settings:', error);
    }
})();

export default SiteSetting;
