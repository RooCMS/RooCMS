/**
 * Alpine.js User Edit Manager Component
 * Handles user editing operations in ACP
 */

import { request } from '../app/api.js';
import { DEBUG } from '../app/config.js';
import { formatDateTime, formatRelativeTime } from '../app/helpers/formatters.js';
import { isValidEmail, isNotEmpty, hasMinLength, valuesMatch } from '../app/helpers/validation.js';
import { showFieldError, clearFieldErrors } from '../app/helpers/formHelpers.js';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('userEditManager', () => ({
        user: {},
        availableRoles: [],
        form: {
            email: '',
            role: 'u',
            is_active: true,
            is_verified: false,
            is_banned: false,
            ban_reason: '',
            ban_expired_date: '',
            nickname: '',
            first_name: '',
            last_name: '',
            gender: '',
            avatar: '',
            bio: '',
            birthday: '',
            website: '',
            is_public: false
        },
        passwordForm: {
            new_password: '',
            confirm_password: ''
        },
        loading: true,
        saving: false,
        successMessage: '',
        errorMessage: '',
        userId: null,
        async init() {
            if (DEBUG) window.log('log', 'Initializing User Edit Manager...');
            
            // Get user ID from URL
            this.userId = this.getUserIdFromUrl();
            
            if (!this.userId) {
                this.errorMessage = 'Invalid user ID';
                this.loading = false;
                return;
            }

            // Load available roles and user data
            await Promise.all([
                this.loadAvailableRoles(),
                this.loadUserData()
            ]);
        },

        // Get user ID from URL query parameter
        getUserIdFromUrl() {
            // Try to get from query parameter: /acp/user_edit?id=123
            const urlParams = new URLSearchParams(window.location.search);
            const userId = urlParams.get('id');
            
            if (userId) {
                const parsedId = parseInt(userId, 10);
                if (!isNaN(parsedId) && parsedId > 0) {
                    return parsedId;
                }
            }
            
            return null;
        },

        // Load available roles
        async loadAvailableRoles() {
            try {
                const response = await request('/v1/acp/users/roles');
                
                if (!response.ok) {
                    throw new Error('Failed to load roles');
                }

                const responseData = await response.json();
                this.availableRoles = responseData.data || [];
                
                if (DEBUG) window.log('log', 'Available roles loaded:', this.availableRoles);
            } catch (error) {
                if (DEBUG) window.log('error', 'Error loading roles:', error);
                // Set default roles if API fails
                this.availableRoles = [
                    { value: 'u', label: 'User', description: 'Regular user' },
                    { value: 'm', label: 'Moderator', description: 'Can moderate content' },
                    { value: 'a', label: 'Admin', description: 'Can manage site settings' },
                    { value: 'su', label: 'Superuser', description: 'Full system access' }
                ];
            }
        },

        // Generic API call handler with error handling
        async handleApiCall(apiCall, successMessage) {
            this.saving = true;
            this.clearMessages();
            
            try {
                const result = await apiCall();
                if (successMessage) {
                    this.showMessage(successMessage, 'success');
                }
                return result;
            } catch (error) {
                if (DEBUG) window.log('error', 'API call failed:', error);
                this.showMessage('Error: ' + error.message, 'error');
                throw error;
            } finally {
                this.saving = false;
            }
        },

        // Load user data (unified method)
        async loadUserData(showLoading = true) {
            if (showLoading) {
                this.loading = true;
                this.clearMessages();
            }

            try {
                if (DEBUG) window.log('log', 'Loading user:', this.userId);

                const response = await request(`/v1/acp/users/${this.userId}`);

                if (!response.ok) {
                    if (response.status === 404) {
                        this.user = {};
                        return;
                    }
                    throw new Error(`Failed to load user: ${response.status}`);
                }

                const responseData = await response.json();
                this.user = responseData.data || responseData;

                if (DEBUG) window.log('log', 'User loaded:', this.user);

                // Populate form only on initial load
                if (showLoading) {
                    this.populateForm();
                }
            } catch (error) {
                if (DEBUG) window.log('error', 'Error loading user:', error);
                if (showLoading) {
                    this.showMessage('Failed to load user: ' + error.message, 'error');
                }
            } finally {
                if (showLoading) {
                    this.loading = false;
                }
            }
        },

        // Convert database boolean values to JS boolean
        convertDbBoolean(value) {
            return value == '1' || value === true;
        },

        // Populate form from user data
        populateForm() {
            if (!this.user) return;

            // Boolean fields that need conversion
            const booleanFields = ['is_active', 'is_verified', 'is_banned', 'is_public'];
            
            // String fields with defaults
            const stringFields = ['email', 'role', 'nickname', 'first_name', 'last_name', 
                                'gender', 'avatar', 'bio', 'birthday', 'website', 'ban_reason'];

            // Map string fields
            stringFields.forEach(field => {
                this.form[field] = this.user[field] || (field === 'role' ? 'u' : '');
            });

            // Map boolean fields
            booleanFields.forEach(field => {
                this.form[field] = this.convertDbBoolean(this.user[field]);
            });

            // Handle special ban_expired field
            if (this.user.ban_expired && this.user.ban_expired > 0) {
                const date = new Date(this.user.ban_expired * 1000);
                this.form.ban_expired_date = this.formatDateTimeLocal(date);
            } else {
                this.form.ban_expired_date = '';
            }

            if (DEBUG) window.log('log', 'Form populated:', this.form);
        },

        // Reset form to original values
        resetForm() {
            this.populateForm();
            this.passwordForm.new_password = '';
            this.passwordForm.confirm_password = '';
            this.clearMessages();
            this.clearFieldErrors();
        },

        // Clear field errors
        clearFieldErrors() {
            clearFieldErrors(['email_error', 'role_error']);
        },

        // Validate form data
        validateUserForm() {
            this.clearFieldErrors();
            let hasErrors = false;

            if (!this.form.email || !isValidEmail(this.form.email)) {
                showFieldError('email_error', 'Please enter a valid email address');
                hasErrors = true;
            }

            if (!this.form.role) {
                showFieldError('role_error', 'Please select a role');
                hasErrors = true;
            }

            if (hasErrors) {
                this.showMessage('Please fix the errors below', 'error');
            }

            return !hasErrors;
        },

        // Prepare user data for API request
        prepareUserUpdateData() {
            const updateData = {
                email: this.form.email,
                role: this.form.role,
                is_active: this.form.is_active ? 1 : 0,
                is_verified: this.form.is_verified ? 1 : 0,
                is_banned: this.form.is_banned ? 1 : 0,
                ban_reason: this.form.ban_reason || '',
                nickname: this.form.nickname || null,
                first_name: this.form.first_name || null,
                last_name: this.form.last_name || null,
                gender: this.form.gender || null,
                bio: this.form.bio || null,
                birthday: this.form.birthday || null,
                website: this.form.website || null,
                is_public: this.form.is_public ? 1 : 0
            };

            // Handle ban expiration
            if (this.form.is_banned && this.form.ban_expired_date) {
                const date = new Date(this.form.ban_expired_date);
                updateData.ban_expired = Math.floor(date.getTime() / 1000);
            } else {
                updateData.ban_expired = 0;
            }

            return updateData;
        },

        // Send user update request to API
        async sendUserUpdateRequest(updateData) {
            if (DEBUG) window.log('log', 'Saving user with data:', updateData);

            const response = await request(`/v1/acp/users/${this.userId}`, {
                method: 'PUT',
                body: JSON.stringify(updateData)
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Failed to update user');
            }

            return response;
        },

        // Save user changes
        async saveUser(event) {
            if (event) event.preventDefault();
            if (this.saving) return;

            if (!this.validateUserForm()) return;

            await this.handleApiCall(async () => {
                const updateData = this.prepareUserUpdateData();
                await this.sendUserUpdateRequest(updateData);
                await this.loadUserData(false); // Refresh without loading state
            }, 'User updated successfully');
        },

        // Validate password form
        validatePasswordForm() {
            if (!this.passwordForm.new_password) {
                this.showMessage('Please enter a new password', 'error');
                return false;
            }

            if (this.passwordForm.new_password !== this.passwordForm.confirm_password) {
                this.showMessage('Passwords do not match', 'error');
                return false;
            }

            return true;
        },

        // Change password
        async changePassword() {
            if (this.saving) return;

            if (!this.validatePasswordForm()) return;

            await this.handleApiCall(async () => {
                if (DEBUG) window.log('log', 'Changing password for user:', this.userId);

                const response = await request(`/v1/acp/users/${this.userId}/password`, {
                    method: 'PUT',
                    body: JSON.stringify({
                        password: this.passwordForm.new_password
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to change password');
                }

                // Clear password fields on success
                this.passwordForm.new_password = '';
                this.passwordForm.confirm_password = '';
            }, 'Password changed successfully');
        },

        // Delete user
        async deleteUser() {
            const confirmed = await window.modal(
                'Delete User',
                `Are you sure you want to delete user <strong>${this.user && this.user.login ? this.user.login : 'Unknown'}</strong>? This action cannot be undone.`,
                'Delete',
                'Cancel',
                'danger'
            );

            if (!confirmed) return;

            await this.handleApiCall(async () => {
                if (DEBUG) window.log('log', 'Deleting user:', this.userId);

                const response = await request(`/v1/acp/users/${this.userId}`, {
                    method: 'DELETE'
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to delete user');
                }

                // Redirect to users list after short delay
                setTimeout(() => {
                    window.location.href = '/acp/users';
                }, 1500);
            }, 'User deleted successfully. Redirecting...');
        },

        // Format datetime for datetime-local input
        formatDateTimeLocal(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        },

        // Format timestamp to readable date
        formatDateTime(timestamp) {
            return formatDateTime(timestamp);
        },

        // Format timestamp to relative time
        formatRelativeTime(timestamp) {
            return formatRelativeTime(timestamp);
        },

        // Message handling
        showMessage(message, type = 'success') {
            this.clearMessages();
            if (type === 'success') {
                this.successMessage = message;
                setTimeout(() => this.successMessage = '', 5000);
            } else {
                this.errorMessage = message;
                setTimeout(() => this.errorMessage = '', 5000);
            }
        },

        clearMessages() {
            this.successMessage = '';
            this.errorMessage = '';
        },

    }));
});
