/**
 * Alpine.js Settings Manager Component
 * Handles loading, displaying and updating system settings
 */

import { request, handleApiError } from '../app/api.js';
import { modal } from '../app/main.js';
import { DEBUG } from '../app/config.js';

// Alpine.js Settings Manager Component
document.addEventListener('alpine:init', () => {
    window.Alpine.data('settingsManager', () => ({
        // Reactive data
        settings: {},
        meta: {},
        loading: false,
        successMessage: '',
        errorMessage: '',

        // Initialization
        async init() {
            if (DEBUG) window.log('log', 'Initializing Alpine settings manager...');
            await this.loadSettings();
        },

        // Load settings from API
        async loadSettings() {
            if (this.loading) return;

            this.loading = true;
            this.clearMessages();

            try {
                const data = await this.apiRequest('/v1/acp/settings/meta');
                
                // API now returns { settings: {...}, meta: {...} }
                this.settings = data.data?.settings || data.settings || {};
                this.meta = data.data?.meta || data.meta || {};
                
                this.settings = this.processSettingsData(this.settings);

                if (DEBUG) window.log('log', 'Settings and metadata loaded successfully:', { settings: this.settings, meta: this.meta });
            } catch (error) {
                if (DEBUG) window.log('error', 'Error loading settings:', error);
                this.showMessage(error.message || 'Error loading settings', 'error');
            } finally {
                this.loading = false;
            }
        },

        // Save settings
        async saveSettings() {
            if (this.loading) return;

            this.loading = true;
            this.clearMessages();
            this.clearValidationErrors();

            try {
                const formData = this.collectFormData();
                const response = await request('/v1/acp/settings', {
                    method: 'PATCH',
                    body: JSON.stringify(formData)
                });

                if (!response.ok) {
                    // Handle authentication and authorization errors
                    if (!handleApiError(response, (message, type) => this.showMessage(message, type))) {
                        return;
                    }

                    // Handle validation errors
                    if (response.status === 422) {
                        const errorData = await response.json();
                        if (errorData.details?.validation_errors) {
                            this.showValidationErrors(errorData.details.validation_errors);
                            return;
                        }
                    }

                    throw new Error(`Failed to save settings: ${response.status}`);
                }

                await response.json();
                this.showMessage('Settings saved successfully');
                await this.loadSettings();
            } catch (error) {
                if (DEBUG) window.log('error', 'Error saving settings:', error);
                this.showMessage(error.message || 'Error saving settings', 'error');
            } finally {
                this.loading = false;
            }
        },

        // Reset settings
        async resetSettings() {
            if (this.loading) return;

            const confirmed = await this.confirmReset();
            if (!confirmed) return;

            this.loading = true;
            this.clearMessages();

            try {
                await this.apiRequest('/v1/acp/settings/reset/all');
                this.showMessage('Settings reset to default values');
                await this.loadSettings();
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
                throw new Error(errorData.message || `Request failed: ${response.status}`);
            }

            return await response.json();
        },

        // Confirm reset with modal
        async confirmReset() {
            return await modal(
                'Reset Settings',
                'Are you sure you want to reset all settings to default values?\nThis action cannot be undone.',
                'Yes, Reset',
                'Cancel',
                'warning'
            );
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
            
            // Map field types to HTML input types
            const typeMapping = {
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
            
            return typeMapping[fieldType] || 'text';
        },

        getFieldMeta(key, prop) {
            return this.meta[key]?.[prop];
        },

        getFieldOptions(key) {
            return this.meta[key]?.options || {};
        },

        collectFormData() {
            const data = {};

            // Flatten settings object to key-value pairs with proper type conversion
            Object.values(this.settings).forEach((groupSettings) => {
                if (!groupSettings || typeof groupSettings !== 'object') return;

                Object.entries(groupSettings).forEach(([key, value]) => {
                    data[key] = this.convertValueByType(key, value);
                });
            });

            return data;
        },

        clearValidationErrors() {
            document.querySelectorAll('.validation-error').forEach(el => {
                el.classList.remove('validation-error', 'border-red-500', 'focus:border-red-500');
                el.classList.add('border-zinc-300', 'focus:border-sky-500');
            });
            document.querySelectorAll('.field-error-message').forEach(el => el.remove());
        },

        showValidationErrors(validationErrors) {
            this.showMessage('Please correct the validation errors below.', 'error');

            const entries = Object.entries(validationErrors);
            entries.forEach(([fieldName, errorMessage], index) => {
                const fieldElement = this.findFieldElement(fieldName);

                if (fieldElement) {
                    this.highlightFieldError(fieldElement, errorMessage);

                    // Scroll to first error
                    if (index === 0) {
                        fieldElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        setTimeout(() => fieldElement.focus(), 100);
                    }
                }
            });
        },

        // Find field element by name
        findFieldElement(fieldName) {
            // Try direct name attribute first
            let field = document.querySelector(`[name="${fieldName}"]`);
            if (field) return field;

            // Try to find by ID in groups
            for (const [groupName, groupSettings] of Object.entries(this.settings)) {
                if (groupSettings?.[fieldName] !== undefined) {
                    field = document.getElementById(this.getFieldId(groupName, fieldName));
                    if (field) return field;
                }
            }

            // Fallback: search by ID ending with field name
            return document.querySelector(`[id$="_${fieldName}"]`);
        },

        highlightFieldError(fieldElement, errorMessage) {
            // Add error classes to the field
            fieldElement.classList.add('validation-error', 'border-red-500', 'focus:border-red-500');
            fieldElement.classList.remove('border-zinc-300', 'focus:border-sky-500');

            // Find the field container
            const fieldContainer = fieldElement.closest('.field-container');
            if (fieldContainer) {
                // Remove any existing error messages for this field
                const existingError = fieldContainer.querySelector('.field-error-message');
                if (existingError) {
                    existingError.remove();
                }

                // Create new error message element
                const errorElement = document.createElement('p');
                errorElement.className = 'field-error-message mt-1 text-xs text-red-600';
                errorElement.textContent = errorMessage;

                // Insert error message after the field or after existing description
                const fieldDiv = fieldElement.closest('div');
                if (fieldDiv && fieldDiv.parentNode === fieldContainer) {
                    fieldDiv.insertAdjacentElement('afterend', errorElement);
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
    }));
});
