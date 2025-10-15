import { request } from './api.js';

// Global settings object
const SiteSetting = {};

// Load settings from API
(async () => {
    try {
        const response = await request('/v1/settings');
        const data = await response.json();

        if (data.data) {
            // API returns already flattened settings, just copy them
            Object.assign(SiteSetting, data.data);
        }

    } catch (error) {
        window.log('error', 'Failed to load site settings:', error);
    }
})();

export default SiteSetting;
