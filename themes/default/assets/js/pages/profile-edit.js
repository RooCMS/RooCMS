// Profile Edit JavaScript
// Handles profile editing functionality with Alpine.js

import { request, setAccessToken } from '../app/api.js';
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

        // Avatar data
        currentAvatar: null,
        avatarPreview: null,
        avatarUploading: false,
        avatarError: '',

        // Form state
        loading: false,
        errors: {},
        successMessage: '',
        errorMessage: '',


        // Initialize component
        async init() {
            await this.loadUserProfile();
            this.setupAvatarEventListeners();
        },

        // Handle 401 unauthorized responses
        handleUnauthorized() {
            setAccessToken(null);
            window.location.href = '/!/login';
        },

        // Generic error handler with 401 check
        handleError(error, context = 'Operation') {
            window.log('error', `${context} error:`, error);
            
            if (error.status === 401 || error.message?.includes('401') || error.message?.includes('Unauthorized')) {
                this.handleUnauthorized();
                return true; // Handled
            }
            return false; // Not handled
        },

        // Wrapper for avatar loading state
        async withAvatarLoading(asyncFn) {
            try {
                this.avatarUploading = true;
                this.showAvatarError('');
                this.updateAvatarUI();
                
                return await asyncFn();
            } finally {
                this.avatarUploading = false;
                this.updateAvatarUI();
            }
        },

        // Setup event listeners for avatar functionality
        setupAvatarEventListeners() {
            const avatarInput = document.querySelector('[data-avatar-input]');
            const deleteButton = document.querySelector('[data-delete-avatar]');
            
            if (avatarInput) {
                avatarInput.addEventListener('change', (e) => this.handleAvatarSelect(e));
            }
            
            if (deleteButton) {
                deleteButton.addEventListener('click', () => this.deleteAvatar());
            }
        },

        // Update avatar UI elements
        updateAvatarUI() {
            const placeholder = document.querySelector('[data-avatar-placeholder]');
            const currentImg = document.querySelector('[data-current-avatar]');
            const previewImg = document.querySelector('[data-avatar-preview]');
            const deleteContainer = document.querySelector('[data-delete-container]');
            const uploadLabel = document.querySelector('[data-upload-label]');
            const uploadText = document.querySelector('[data-upload-text]');
            
            // Hide all initially
            if (placeholder) placeholder.classList.add('hidden');
            if (currentImg) currentImg.classList.add('hidden');
            if (previewImg) previewImg.classList.add('hidden');
            if (deleteContainer) deleteContainer.classList.add('hidden');
            
            // Show appropriate elements
            if (this.avatarPreview) {
                if (previewImg) {
                    previewImg.src = this.avatarPreview;
                    previewImg.classList.remove('hidden');
                }
                if (deleteContainer) deleteContainer.classList.remove('hidden');
            } else if (this.currentAvatar) {
                if (currentImg) {
                    currentImg.src = this.currentAvatar;
                    currentImg.classList.remove('hidden');
                }
                if (deleteContainer) deleteContainer.classList.remove('hidden');
            } else {
                if (placeholder) placeholder.classList.remove('hidden');
            }
            
            // Update upload button
            if (uploadLabel && uploadText) {
                if (this.avatarUploading) {
                    uploadLabel.classList.add('opacity-50', 'cursor-not-allowed');
                    uploadText.textContent = 'Uploading...';
                } else {
                    uploadLabel.classList.remove('opacity-50', 'cursor-not-allowed');
                    uploadText.textContent = 'Choose Avatar';
                }
            }
        },

        // Show/hide avatar error
        showAvatarError(message) {
            const errorDiv = document.querySelector('[data-avatar-error]');
            const errorText = document.querySelector('[data-avatar-error-text]');
            
            if (errorDiv && errorText) {
                if (message) {
                    errorText.textContent = message;
                    errorDiv.classList.remove('hidden');
                    this.avatarError = message;
                } else {
                    errorDiv.classList.add('hidden');
                    this.avatarError = '';
                }
            }
        },

        // Load current user profile data
        async loadUserProfile() {
            try {
                this.loading = true;
                
                const response = await request('/v1/users/me');
                if (!response.ok) {
                    if (response.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }
                    throw new Error(`Failed to load profile: ${response.status}`);
                }

                const data = await response.json();
                const user = data.data || data;

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

                    // Set current avatar if exists
                    if (user.avatar) {
                        this.currentAvatar = `/up/${user.avatar}`;
                    }

                    this.updateAvatarUI();

                    // Ensure toggle reflects the loaded value
                    this.$nextTick(() => {
                        const toggleInput = document.querySelector('input[type="checkbox"][x-model="formData.is_public"]');
                        if (toggleInput) {
                            toggleInput.checked = Boolean(user.is_public);
                            toggleInput.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    });
                } else {
                    showErrorMessage('Failed to load profile data');
                }
            } catch (error) {
                if (!this.handleError(error, 'Profile load')) {
                    showErrorMessage('Error loading profile data');
                }
            } finally {
                this.loading = false;
            }
        },

        // Save profile changes
        async saveProfile() {
            try {
                this.loading = true;
                this.errors = {};

                clearFieldErrors(['first_name_error', 'last_name_error', 'nickname_error', 'gender_error', 'birthday_error', 'email_error', 'website_error', 'bio_error']);

                if (!this.validateForm()) {
                    return;
                }

                const response = await request('/v1/users/me', {
                    method: 'PATCH',
                    body: JSON.stringify(this.formData)
                });

                if (!response.ok) {
                    if (response.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }
                }

                const data = await response.json();

                if (response.ok && data.success) {
                    updateUserData(this.formData);
                    this.successMessage = 'Profile updated successfully!';
                    showSuccessMessage('Profile updated successfully!');
                    redirectAfterSuccess('/!/profile', 2000);
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
                if (!this.handleError(error, 'Profile save')) {
                    this.errorMessage = 'Error saving profile. Please try again.';
                    showErrorMessage('Error saving profile. Please try again.');
                }
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

        // Handle avatar file selection
        async handleAvatarSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Clear previous errors
            this.showAvatarError('');

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                this.showAvatarError('Please select a valid image file (JPEG, PNG, GIF, or WebP)');
                event.target.value = '';
                return;
            }

            // Validate file size (5MB)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                this.showAvatarError('Avatar file size must not exceed 5MB');
                event.target.value = '';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = (e) => {
                this.avatarPreview = e.target.result;
                this.updateAvatarUI();
            };
            reader.readAsDataURL(file);

            // Upload avatar
            await this.uploadAvatar(file);
            
            // Clear file input
            event.target.value = '';
        },

        // Upload avatar to server
        async uploadAvatar(file) {
            return await this.withAvatarLoading(async () => {
                const formData = new FormData();
                formData.append('avatar', file);

                const response = await request('/v1/users/me/avatar', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    if (response.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }
                    
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to upload avatar');
                }

                const data = await response.json();
                
                // Update current avatar and clear preview (add timestamp to force reload)
                this.currentAvatar = `/up/${data.data.avatar_path}?t=${Date.now()}`;
                this.avatarPreview = null;
                
                showSuccessMessage('Avatar uploaded successfully!');
            }).catch(error => {
                if (!this.handleError(error, 'Avatar upload')) {
                    this.showAvatarError(error.message || 'Failed to upload avatar. Please try again.');
                }
                this.avatarPreview = null;
            });
        },

        // Delete avatar
        async deleteAvatar() {
            return await this.withAvatarLoading(async () => {
                const response = await request('/v1/users/me/avatar', {
                    method: 'DELETE'
                });

                if (!response.ok) {
                    if (response.status === 401) {
                        this.handleUnauthorized();
                        return;
                    }
                    
                    if (response.status === 404) {
                        // No avatar to delete, just clear UI
                        this.currentAvatar = null;
                        this.avatarPreview = null;
                        return;
                    }
                    
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to delete avatar');
                }

                // Clear avatar from UI
                this.currentAvatar = null;
                this.avatarPreview = null;
                
                showSuccessMessage('Avatar deleted successfully!');
            }).catch(error => {
                if (!this.handleError(error, 'Avatar delete')) {
                    this.showAvatarError(error.message || 'Failed to delete avatar. Please try again.');
                }
            });
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
            if (this.avatarError) {
                setTimeout(() => {
                    this.avatarError = '';
                }, 5000);
            }
        }
    }));
});
