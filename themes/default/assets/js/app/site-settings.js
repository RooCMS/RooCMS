import { request } from './api.js';

// Global settings object
const SiteSetting = {};

// Load settings from API
export const siteSettingsReady = (async () => {
    try {
        const response = await request('/v1/settings');
        if (!response.ok) {
            throw new Error(`Failed to load site settings: ${response.status}`);
        }
        const data = await response.json();

        if (data.data) {
            // API returns already flattened settings, just copy them
            Object.assign(SiteSetting, data.data);
        }

    } catch (error) {
        console.error('Failed to load site settings:', error);
    }

    return SiteSetting;
})();

export default SiteSetting;
