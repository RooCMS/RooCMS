// Profile Edit JavaScript
// Handles profile editing functionality with Alpine.js

import { request } from '../app/api.js';
import { getCurrentUser, updateUserData } from '../app/auth.js';
import { isValidEmail, isNotEmpty } from '../app/helpers/validation.js';
import { showFieldError, clearFieldErrors, showSuccessMessage, showErrorMessage, redirectAfterSuccess } from '../app/helpers/formHelpers.js';

document.addEventListener('alpine:init', () => {
    Alpine.data('profileEdit', () => ({
        // Form data
        formData: {
            first_name: '',
            last_name: '',
            nickname: '',
            gender: '',
            birthday: '',
            email: '',
            website: '',
            bio: '',
            is_public: false
        },

        // Form state
        loading: false,
        errors: {},
        successMessage: '',
        errorMessage: '',


        // Initialize component
        async init() {
            await this.loadUserProfile();
        },

        // Load current user profile data
        async loadUserProfile() {
            try {
                this.loading = true;
                const user = await getCurrentUser();

                if (user) {
                    this.formData = {
                        first_name: user.first_name || '',
                        last_name: user.last_name || '',
                        nickname: user.nickname || '',
                        gender: user.gender || '',
                        birthday: user.birthday || '',
                        email: user.email || '',
                        website: user.website || '',
                        bio: user.bio || '',
                        is_public: Boolean(user.is_public)
                    };

                    // Ensure toggle reflects the loaded value
                    this.$nextTick(() => {
                        const toggleInput = document.querySelector('input[type="checkbox"][x-model="formData.is_public"]');
                        if (toggleInput) {
                            toggleInput.checked = Boolean(user.is_public);
                            // Trigger Alpine.js reactivity
                            toggleInput.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    });

                } else {
                    showErrorMessage('Failed to load profile data');
                }
            } catch (error) {
                console.error('Error loading profile:', error);
                showErrorMessage('Error loading profile data');
            } finally {
                this.loading = false;
            }
        },

        // Save profile changes
        async saveProfile() {
            try {
                this.loading = true;
                this.errors = {};

                // Clear previous errors
                clearFieldErrors(['first_name_error', 'last_name_error', 'nickname_error', 'gender_error', 'birthday_error', 'email_error', 'website_error', 'bio_error']);

                // Validate form
                if (!this.validateForm()) {
                    return;
                }

                const response = await request('/v1/users/me', {
                    method: 'PATCH',
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Update local user data
                    updateUserData(this.formData);

                    this.successMessage = 'Profile updated successfully!';
                    showSuccessMessage('Profile updated successfully!');
                    redirectAfterSuccess('/profile', 2000);
                } else {
                    if (data.errors) {
                        this.handleValidationErrors(data.errors);
                        this.errorMessage = 'Please fix the errors and try again.';
                    } else {
                        this.errorMessage = data.message || 'Failed to update profile';
                        showErrorMessage(data.message || 'Failed to update profile');
                    }
                }
            } catch (error) {
                console.error('Error saving profile:', error);
                this.errorMessage = 'Error saving profile. Please try again.';
                showErrorMessage('Error saving profile. Please try again.');
            } finally {
                this.loading = false;
            }
        },

        // Validate form data
        validateForm() {
            let isValid = true;

            // Required fields validation
            if (!isNotEmpty(this.formData.nickname)) {
                this.errors.nickname = 'Nickname is required';
                showFieldError('nickname_error', this.errors.nickname);
                isValid = false;
            }

            if (!isNotEmpty(this.formData.email)) {
                this.errors.email = 'Email is required';
                showFieldError('email_error', this.errors.email);
                isValid = false;
            } else if (!isValidEmail(this.formData.email)) {
                this.errors.email = 'Please enter a valid email address';
                showFieldError('email_error', this.errors.email);
                isValid = false;
            }

            // Optional fields validation
            if (this.formData.website && !this.isValidUrl(this.formData.website)) {
                this.errors.website = 'Please enter a valid URL';
                showFieldError('website_error', this.errors.website);
                isValid = false;
            }

            if (this.formData.bio && this.formData.bio.length > 500) {
                this.errors.bio = 'Bio cannot exceed 500 characters';
                showFieldError('bio_error', this.errors.bio);
                isValid = false;
            }

            return isValid;
        },

        // Handle validation errors from server
        handleValidationErrors(serverErrors) {
            Object.keys(serverErrors).forEach(field => {
                const errorId = `${field}_error`;
                const errorMessage = Array.isArray(serverErrors[field])
                    ? serverErrors[field][0]
                    : serverErrors[field];
                showFieldError(errorId, errorMessage);
            });
        },

        // Validate URL format
        isValidUrl(url) {
            try {
                new URL(url);
                return true;
            } catch {
                return false;
            }
        },

        // Auto-hide messages after 5 seconds
        $nextTick() {
            if (this.successMessage) {
                setTimeout(() => {
                    this.successMessage = '';
                }, 5000);
            }
            if (this.errorMessage) {
                setTimeout(() => {
                    this.errorMessage = '';
                }, 5000);
            }
        }
    }));
});
