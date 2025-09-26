/**
 * Alpine.js Settings Manager Component
 * Handles loading, displaying and updating system settings
 */

import { request } from '../app/api.js';
import { DEBUG } from '../app/config.js';

// Alpine.js Settings Manager Component
window.settingsManager = () => ({
    // Reactive data
    settings: {},
    meta: {},
    loading: false,
    successMessage: '',
    errorMessage: '',

    // Initialization
    init() {
        if (DEBUG) console.log('Initializing Alpine settings manager...');
        this.loadSettings();
    },

    // Load settings from API
    async loadSettings() {
        if (this.loading) return;

        this.loading = true;
        this.clearMessages();

    try {
        const response = await request('/v1/admin/settings');

        if (!response.ok) {
            throw new Error(`Failed to load settings: ${response.status}`);
        }

            const data = await response.json();
            this.settings = data.data || {};
            await this.loadMetaData();
            this.settings = this.processSettingsData(this.settings);

            if (DEBUG) console.log('Settings loaded successfully:', this.settings);
    } catch (error) {
            if (DEBUG) console.error('Error loading settings:', error);
            this.showMessage('Error loading settings: ' + error.message, 'error');
    } finally {
            this.loading = false;
        }
    },

    // Load metadata for all settings
    async loadMetaData() {
        const allKeys = Object.values(this.settings)
            .filter(group => group && typeof group === 'object')
            .flatMap(group => Object.keys(group));

        const metaPromises = allKeys.map(async (key) => {
            try {
                const response = await request(`/v1/admin/settings/key-${key}`);
                if (response.ok) {
                    const data = await response.json();
                    if (data.data?.meta) {
                        this.meta[key] = data.data.meta;
                    }
                }
            } catch (error) {
                if (DEBUG) console.warn(`Failed to load meta for ${key}:`, error);
            }
        });

        await Promise.all(metaPromises);
        if (DEBUG) console.log('Meta loaded:', this.meta);
    },

    // Save settings
    async saveSettings() {
        if (this.loading) return;

        this.loading = true;
        this.clearMessages();
        this.clearValidationErrors();

        try {
            const formData = this.collectFormData();

        const response = await request('/v1/admin/settings', {
            method: 'PATCH',
            body: JSON.stringify(formData)
        });

        if (!response.ok) {
                if (response.status === 422) {
                    const errorData = await response.json();
                    if (errorData.details && errorData.details.validation_errors) {
                        this.showValidationErrors(errorData.details.validation_errors);
                        return;
                    }
                }
            throw new Error(`Failed to save settings: ${response.status}`);
        }

        const result = await response.json();
            this.showMessage('Settings saved successfully');
            await this.loadSettings();

    } catch (error) {
            if (DEBUG) console.error('Error saving settings:', error);
            this.showMessage('Error saving settings: ' + error.message, 'error');
    } finally {
            this.loading = false;
        }
    },

    // Reset settings
    async resetSettings() {
        if (this.loading) return;

    if (!confirm('Are you sure you want to reset all settings to default values?')) {
        return;
    }

        this.loading = true;
        this.clearMessages();

    try {
        const response = await request('/v1/admin/settings/reset/all', {
            method: 'GET'
        });

        if (!response.ok) {
            throw new Error(`Failed to reset settings: ${response.status}`);
        }

        const result = await response.json();
            this.showMessage('Settings reset to default values');
            await this.loadSettings();

    } catch (error) {
            if (DEBUG) console.error('Error resetting settings:', error);
            this.showMessage('Error resetting settings: ' + error.message, 'error');
    } finally {
            this.loading = false;
        }
    },

    // Helper methods
    getGroupTitle(groupName) {
        const titles = {
            'site': 'Site settings',
            'general': 'General settings',
            'mailer': 'Email settings',
            'security': 'Security',
            'system': 'System'
        };
        return titles[groupName] || groupName.charAt(0).toUpperCase() + groupName.slice(1);
    },

    // Generate unique field ID
    getFieldId(groupName, key) {
        return `${groupName}_${key}`;
    },

    // Process settings data to convert types correctly
    processSettingsData(data) {
        const processed = {};

        for (const [groupName, groupSettings] of Object.entries(data)) {
            if (groupSettings && typeof groupSettings === 'object') {
                processed[groupName] = {};

                for (const [key, value] of Object.entries(groupSettings)) {
                    processed[groupName][key] = this.getFieldType(key) === 'boolean'
                        ? this.toBoolean(value)
                        : value;
                }
            }
        }

        return processed;
    },

    // Convert value to boolean properly
    toBoolean(value) {
        return !!(value === true || value === 1 || value === '1' || value === 'true');
    },

    getFieldType(key) {
        return this.meta[key]?.type || 'string';
    },

    getFieldMeta(key, prop) {
        return this.meta[key]?.[prop];
    },

    getFieldOptions(key) {
        const options = this.meta[key]?.options || {};
        if (typeof options === 'object') {
            return options;
        }
        return {};
    },

    collectFormData() {
        const data = {};

        // Flatten settings object to key-value pairs
        Object.entries(this.settings).forEach(([groupName, groupSettings]) => {
            if (groupSettings && typeof groupSettings === 'object') {
                Object.entries(groupSettings).forEach(([key, value]) => {
                    // Convert string numbers to numbers for consistency
                    if (typeof value === 'string' && value !== '' && !isNaN(value)) {
                        data[key] = Number(value);
                    } else {
                        data[key] = value;
                    }
                });
            }
        });

        return data;
    },

    clearValidationErrors() {
        document.querySelectorAll('.validation-error').forEach(el => {
            el.classList.remove('validation-error', 'border-red-500', 'focus:border-red-500');
            el.classList.add('border-zinc-300', 'focus:border-zinc-500');
        });
        document.querySelectorAll('.field-error-message').forEach(el => el.remove());
    },

    showValidationErrors(validationErrors) {
        this.showMessage('Please correct the validation errors below.', 'error');

        Object.entries(validationErrors).forEach(([fieldName, errorMessage]) => {
            // Find the field element by searching through all groups
            let fieldElement = null;

            // Search through each group to find the field
            for (const [groupName, groupSettings] of Object.entries(this.settings)) {
                if (groupSettings.hasOwnProperty(fieldName)) {
                    // Found the field in this group, now find the DOM element
                    const groupElement = document.querySelector(`[data-group="${groupName}"]`);
                    if (groupElement) {
                        fieldElement = groupElement.querySelector(`[name="${fieldName}"]`);
                        if (fieldElement) break;
                    }
                }
            }

            if (fieldElement) {
                this.highlightFieldError(fieldElement, errorMessage);

                // Scroll to first error
                if (Object.keys(validationErrors)[0] === fieldName) {
                    fieldElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    fieldElement.focus();
                }
            }
        });
    },

    highlightFieldError(fieldElement, errorMessage) {
        fieldElement.classList.add('validation-error', 'border-red-500', 'focus:border-red-500');
        fieldElement.classList.remove('border-zinc-300', 'focus:border-zinc-500');

        const fieldContainer = fieldElement.closest('.field-container');
        if (fieldContainer) {
            const errorElement = document.createElement('p');
            errorElement.className = 'field-error-message mt-1 text-xs text-red-600';
            errorElement.textContent = errorMessage;

            const existingDesc = fieldContainer.querySelector('p:not(.field-error-message)');
            if (existingDesc) {
                existingDesc.insertAdjacentElement('afterend', errorElement);
            } else {
                fieldContainer.appendChild(errorElement);
            }
        }
    },

    showMessage(message, type = 'success') {
        if (type === 'success') {
            this.successMessage = message;
            this.errorMessage = '';
            // Use global helper for backward compatibility
            if (window.FormHelperUtils?.showSuccessMessage) {
                window.FormHelperUtils.showSuccessMessage(message, '.form-success');
            }
        } else {
            this.errorMessage = message;
            this.successMessage = '';
            // Use global helper for backward compatibility
            if (window.FormHelperUtils?.showErrorMessage) {
                window.FormHelperUtils.showErrorMessage(message, '.form-error');
            }
        }
    },

    clearMessages() {
        this.successMessage = '';
        this.errorMessage = '';
        // Use global helper
        if (window.FormHelperUtils?.clearFormMessages) {
            window.FormHelperUtils.clearFormMessages('.form-success', '.form-error');
        }
    }
});