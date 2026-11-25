import { show_notice } from '../app/helpers/notices.js';
/**
 * ACP Settings Manager Component
 * 
 * Handles loading, displaying and updating system settings
 */

import { request, handleApiError } from '../app/api.js';
import { DEBUG } from '../app/config.js';
import { set_control_error, clear_control_errors } from '../app/helpers/controlErrors.js';

// Constants
const INPUT_TYPE_MAPPING = {
    'boolean': 'checkbox',
    'integer': 'number',
    'number': 'number',
    'string': 'text',
    'color': 'color',
    'text': 'textarea',
    'html': 'text',
    'date': 'date',
    'email': 'email',
    'select': 'select',
    'image': 'file',
    'file': 'file'
};

const SETTINGS_GROUPS = {
    site: { title: 'Site settings', description: 'Your site identity, domain and contact details.', icon: 'M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM3 12h18M12 3a18 18 0 0 1 0 18 18 18 0 0 1 0-18Z' },
    mailer: { title: 'Email settings', description: 'Sending mail, sender details and SMTP connection.', icon: 'M4 5h16v14H4zM4 6l8 6 8-6' },
    security: { title: 'Security', description: 'Passwords, authentication tokens and session lifetime.', icon: 'm12 3 8 4v5c0 5-8 9-8 9s-8-4-8-9V7l8-4Zm-4 9 3 3 5-6' },
    gd: { title: 'Images', description: 'Image dimensions, thumbnail quality and watermarks.', icon: 'M4 4h16v16H4zM4 16l5-5 4 4 3-3 4 4M14 8h.01' }
};

// Alpine.js Settings Manager Component
document.addEventListener('alpine:init', () => {
    window.Alpine.data('settingsManager', () => ({
        // Reactive data
        settings: {},
        meta: {},
        active_group: '',
        loading: false,

        // Initialization
        async init() {
            if (DEBUG) window.log('log', 'Initializing Alpine settings manager...');
            await this.loadSettings();
        },

        // Load settings from API
        async loadSettings() {
            if (this.loading) return;

            this.loading = true;

            try {
                await this.fetchSettings();
            } catch (error) {
                if (DEBUG) window.log('error', 'Error loading settings:', error);
                this.showMessage(error.message || 'Error loading settings', 'error');
            } finally {
                this.loading = false;
            }
        },

        // Refresh data within an operation that already manages the loading state.
        async fetchSettings() {
            const response = await this.apiRequest('/v1/acp/settings/meta');
            const data = response.data || response;
            this.meta = data.meta || {};
            this.settings = this.processSettingsData(data.settings || {});
            if (!this.settings[this.active_group]) this.active_group = Object.keys(this.settings)[0] || '';

            if (DEBUG) window.log('log', 'Settings and metadata loaded successfully:', { settings: this.settings, meta: this.meta });
        },

        // Save settings
        async saveSettings() {
            if (this.loading) return;

            this.loading = true;
            this.clearValidationErrors();

            try {
                const formData = this.collectFormData();
                await this.apiRequest('/v1/acp/settings', {
                    method: 'PATCH',
                    body: JSON.stringify(formData)
                });

                await this.fetchSettings();
                this.showMessage('Settings saved successfully');
            } catch (error) {
                if (DEBUG) window.log('error', 'Error saving settings:', error);
                
                // Check if it's a validation error
                if (error.validationErrors) {
                    this.showValidationErrors(error.validationErrors);
                } else {
                    this.showMessage(error.message || 'Error saving settings', 'error');
                }
            } finally {
                this.loading = false;
            }
        },

        // Reset settings
        async resetSettings() {
            if (this.loading) return;

            const confirmed = await this.confirmReset();
            if (!confirmed || this.loading) return;

            this.loading = true;

            try {
                await this.apiRequest('/v1/acp/settings/reset/all');
                await this.fetchSettings();
                this.showMessage('Settings reset to default values');
            } catch (error) {
                if (DEBUG) window.log('error', 'Error resetting settings:', error);
                this.showMessage(error.message || 'Error resetting settings', 'error');
            } finally {
                this.loading = false;
            }
        },

        // Unified API request handler
        async apiRequest(url, options = {}) {
            const response = await request(url, options);

            if (!response.ok) {
                // Handle authentication and authorization errors
                if (!handleApiError(response, (message, type) => this.showMessage(message, type))) {
                    throw new Error('Authentication failed');
                }

                const errorData = await response.json();
                // Handle validation errors (422)
                if (response.status === 422) {
                    if (errorData.details?.validation_errors) {
                        const error = new Error('Validation failed');
                        error.validationErrors = errorData.details.validation_errors;
                        error.status = response.status;
                        throw error;
                    }
                }

                const error = new Error(errorData.message || `Request failed: ${response.status}`);
                error.status = response.status;
                throw error;
            }

            return await response.json();
        },

        // Confirm reset with modal
        async confirmReset() {
            return await window.modal(
                'Reset Settings',
                'Are you sure you want to reset all settings to default values?\nThis action cannot be undone.',
                'Yes, Reset',
                'Cancel',
                'warning'
            );
        },

        // Helper methods
        get_group_meta(group_name) {
            return SETTINGS_GROUPS[group_name] || {
                title: group_name.charAt(0).toUpperCase() + group_name.slice(1),
                description: '',
                icon: 'M4 7h16M4 17h16M8 4v6m8 4v6'
            };
        },

        // Generate unique field ID
        getFieldId(groupName, key) {
            return `${groupName}_${key}`;
        },

        // Process settings data to convert types correctly
        processSettingsData(data) {
            const processed = {};

            for (const [groupName, groupSettings] of Object.entries(data).reverse()) {
                if (!groupSettings || typeof groupSettings !== 'object') continue;

                processed[groupName] = {};
                for (const [key, value] of Object.entries(groupSettings)) {
                    processed[groupName][key] = this.convertValueByType(key, value);
                }
            }

            return processed;
        },

        // Convert value based on metadata type
        convertValueByType(key, value) {
            const fieldType = this.getFieldType(key);

            switch (fieldType) {
                case 'boolean':
                    return !!(value === true || value === 1 || value === '1' || value === 'true');
                case 'integer':
                    return typeof value === 'string' ? parseInt(value, 10) : value;
                case 'number':
                    return typeof value === 'string' ? parseFloat(value) : value;
                default:
                    return value;
            }
        },

        getFieldType(key) {
            return this.meta[key]?.type || 'string';
        },

        getInputType(key) {
            const fieldType = this.getFieldType(key);
            return INPUT_TYPE_MAPPING[fieldType] || 'text';
        },

        getFieldMeta(key, prop) {
            return this.meta[key]?.[prop];
        },

        getFieldOptions(key) {
            return this.meta[key]?.options || {};
        },

        collectFormData() {
            const data = {};

            // Flatten settings object to key-value pairs (already converted)
            Object.values(this.settings).forEach((groupSettings) => {
                if (!groupSettings || typeof groupSettings !== 'object') return;

                Object.entries(groupSettings).forEach(([key, value]) => {
                    data[key] = value;
                });
            });

            return data;
        },

        clearValidationErrors() {
            clear_control_errors(this.$root);
        },

        showValidationErrors(validationErrors) {
            this.showMessage('Please correct the validation errors below.', 'error');

            let first_field = null;
            Object.entries(validationErrors).forEach(([fieldName, errorMessage]) => {
                const fieldElement = this.findFieldElement(fieldName);

                if (fieldElement) {
                    set_control_error(fieldElement, errorMessage);
                    first_field ??= fieldElement;
                }
            });
            if (first_field) {
                this.active_group = this.meta[first_field.name].category;
                this.$nextTick(() => {
                    first_field.focus({ preventScroll: true });
                    first_field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
            }
        },

        // Find field element by name
        findFieldElement(fieldName) {
            return this.$root.querySelector(`[name="${CSS.escape(fieldName)}"]`);
        },

        showMessage: show_notice
    }));
});
