/**
 * Alpine.js User Edit Manager Component
 * Handles user editing operations in ACP
 */

import { request } from '../app/api.js';
import { DEBUG } from '../app/config.js';
import { formatDateTime, formatRelativeTime } from '../app/helpers/formatters.js';
import { isValidEmail, isNotEmpty, hasMinLength, valuesMatch } from '../app/helpers/validation.js';
import { showFieldError, clearFieldErrors } from '../app/helpers/formHelpers.js';

// Alpine.js User Edit Manager Component
document.addEventListener('alpine:init', () => {
    Alpine.data('userEditManager', () => ({
        // Reactive data
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

        // Initialization
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
                this.loadUser()
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

        // Load user data
        async loadUser() {
            this.loading = true;
            this.clearMessages();

            try {
                if (DEBUG) window.log('log', 'Loading user:', this.userId);

                const response = await request(`/v1/acp/users/${this.userId}`);

                if (!response.ok) {
                    if (response.status === 404) {
                        this.user = {};
                        this.loading = false;
                        return;
                    }
                    throw new Error(`Failed to load user: ${response.status}`);
                }

                const responseData = await response.json();
                this.user = responseData.data || responseData;

                if (DEBUG) window.log('log', 'User loaded:', this.user);

                // Populate form
                this.populateForm();
            } catch (error) {
                if (DEBUG) window.log('error', 'Error loading user:', error);
                this.showMessage('Failed to load user: ' + error.message, 'error');
            } finally {
                this.loading = false;
            }
        },

        // Refresh user data without loading state (for after save)
        async refreshUserData() {
            try {
                if (DEBUG) window.log('log', 'Refreshing user data:', this.userId);

                const response = await request(`/v1/acp/users/${this.userId}`);

                if (!response.ok) {
                    if (response.status === 404) {
                        this.user = {};
                        return;
                    }
                    throw new Error(`Failed to refresh user: ${response.status}`);
                }

                const responseData = await response.json();
                const freshUserData = responseData.data || responseData;

                // Update only the metadata fields that might have changed, keep other data intact
                if (freshUserData.updated_at) {
                    this.user.updated_at = freshUserData.updated_at;
                }
                if (freshUserData.last_activity) {
                    this.user.last_activity = freshUserData.last_activity;
                }
                // Keep created_at as is, it shouldn't change

                if (DEBUG) window.log('log', 'User metadata refreshed:', {
                    updated_at: this.user.updated_at,
                    last_activity: this.user.last_activity
                });

            } catch (error) {
                if (DEBUG) window.log('error', 'Error refreshing user data:', error);
                // Don't show error message for refresh failures, just log
            }
        },

        // Populate form from user data
        populateForm() {
            if (!this.user) return;

            // Account fields
            this.form.email = this.user.email || '';
            this.form.role = this.user.role || 'u';
            this.form.is_active = this.user.is_active == '1' || this.user.is_active === true;
            this.form.is_verified = this.user.is_verified == '1' || this.user.is_verified === true;
            
            // Ban fields
            this.form.is_banned = this.user.is_banned == '1' || this.user.is_banned === true;
            this.form.ban_reason = this.user.ban_reason || '';
            
            // Convert ban_expired timestamp to datetime-local format
            if (this.user.ban_expired && this.user.ban_expired > 0) {
                const date = new Date(this.user.ban_expired * 1000);
                this.form.ban_expired_date = this.formatDateTimeLocal(date);
            } else {
                this.form.ban_expired_date = '';
            }

            // Profile fields
            this.form.nickname = this.user.nickname || '';
            this.form.first_name = this.user.first_name || '';
            this.form.last_name = this.user.last_name || '';
            this.form.gender = this.user.gender || '';
            this.form.avatar = this.user.avatar || '';
            this.form.bio = this.user.bio || '';
            this.form.birthday = this.user.birthday || '';
            this.form.website = this.user.website || '';
            this.form.is_public = this.user.is_public == '1' || this.user.is_public === true;

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

        // Save user changes
        async saveUser(event) {
            // Always prevent default form submission
            if (event) event.preventDefault();

            if (this.saving) return;

            // Clear previous field errors
            this.clearFieldErrors();

            // Client-side validation
            let hasErrors = false;

            if (!this.form.email || !this.isValidEmail(this.form.email)) {
                showFieldError('email_error', 'Please enter a valid email address');
                hasErrors = true;
            }

            if (!this.form.role) {
                showFieldError('role_error', 'Please select a role');
                hasErrors = true;
            }

            if (hasErrors) {
                // Also show global error message
                this.showMessage('Please fix the errors below', 'error');
                return;
            }

            this.saving = true;
            this.clearMessages();
            this.clearFieldErrors();

            try {
                // Prepare update data
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
                    updateData.ban_expired = 0; // Permanent ban or no ban
                }

                if (DEBUG) window.log('log', 'Saving user with data:', updateData);

                const response = await request(`/v1/acp/users/${this.userId}`, {
                    method: 'PUT',
                    body: JSON.stringify(updateData)
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to update user');
                }

                this.showMessage('User updated successfully', 'success');

                // Refresh user data without loading state
                await this.refreshUserData();
            } catch (error) {
                if (DEBUG) window.log('error', 'Error saving user:', error);
                this.showMessage('Error saving user: ' + error.message, 'error');
            } finally {
                this.saving = false;
            }
        },

        // Change password
        async changePassword() {
            if (this.saving) return;

            // Validate passwords
            if (!this.passwordForm.new_password) {
                this.showMessage('Please enter a new password', 'error');
                return;
            }

            if (this.passwordForm.new_password !== this.passwordForm.confirm_password) {
                this.showMessage('Passwords do not match', 'error');
                return;
            }

            this.saving = true;
            this.clearMessages();

            try {
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

                this.showMessage('Password changed successfully', 'success');
                
                // Clear password fields
                this.passwordForm.new_password = '';
                this.passwordForm.confirm_password = '';
            } catch (error) {
                if (DEBUG) window.log('error', 'Error changing password:', error);
                this.showMessage('Error changing password: ' + error.message, 'error');
            } finally {
                this.saving = false;
            }
        },

        // Delete user
        async deleteUser() {
            // Confirm deletion
            const confirmed = await window.modal(
                'Delete User',
                `Are you sure you want to delete user <strong>${this.user && this.user.login ? this.user.login : 'Unknown'}</strong>? This action cannot be undone.`,
                'Delete',
                'Cancel',
                'danger'
            );

            if (!confirmed) return;

            this.saving = true;
            this.clearMessages();

            try {
                if (DEBUG) window.log('log', 'Deleting user:', this.userId);

                const response = await request(`/v1/acp/users/${this.userId}`, {
                    method: 'DELETE'
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Failed to delete user');
                }

                this.showMessage('User deleted successfully. Redirecting...', 'success');
                
                // Redirect to users list after short delay
                setTimeout(() => {
                    window.location.href = '/acp/users';
                }, 1500);
            } catch (error) {
                if (DEBUG) window.log('error', 'Error deleting user:', error);
                this.showMessage('Error deleting user: ' + error.message, 'error');
                this.saving = false;
            }
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

        // Validation helpers for template
        isValidEmail(email) {
            return isValidEmail(email);
        },

        isNotEmpty(value) {
            return isNotEmpty(value);
        },

        hasMinLength(value, minLength) {
            return hasMinLength(value, minLength);
        }
    }));
});
