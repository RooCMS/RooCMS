import { request, setAccessToken, setRefreshToken } from '../app/api.js';

/**
 * Calculates the profile completion percentage
 * @param {Object} user - User object
 * @returns {number} - Completion percentage (0-100)
 */
function calculateProfileCompletion(user) {
    if (!user) return 0;

    let complete = 0;
    const fields = ['first_name', 'last_name', 'nickname', 'gender', 'birthday'];
    const totalFields = fields.length;

    fields.forEach(field => {
        if (user[field]) complete += (100 / totalFields);
    });

    return Math.round(complete);
}

/**
 * Calculates the contact information completion percentage
 * @param {Object} user - User object
 * @returns {number} - Completion percentage (0-100)
 */
function calculateContactCompletion(user) {
    if (!user) return 0;

    const requiredFields = ['email', 'bio', 'website'];
    const optionalFields = ['phone', 'address', 'social_links'];

    let score = 0;

    // Required fields give more points
    requiredFields.forEach(field => {
        if (user[field]) score += 30; // 30 points for each required field
    });

    // Optional fields give less points
    optionalFields.forEach(field => {
        if (user[field]) score += 10; // 10 points for each optional field
    });

    return Math.min(score, 100); // Not more than 100%
}


/**
 * Handles the user profile
 */
document.addEventListener('alpine:init', () => {
    window.Alpine.data('userProfile', () => ({
        user: null,
        loading: true,
        error: '',
        emailVerificationMessage: '',
        emailVerificationType: '',
        togglingVisibility: false,
        sendingEmailVerification: false,

        async init() {
            await this.loadUserProfile();
        },

        formatDate(timestamp) {
            return window.FormatterUtils.formatDate(timestamp);
        },

        formatDateTime(timestamp) {
            return window.FormatterUtils.formatDateTime(timestamp);
        },

        get profileCompletionWidth() {
            return calculateProfileCompletion(this.user);
        },

        get contactCompletionWidth() {
            return calculateContactCompletion(this.user);
        },

        async loadUserProfile() {
            try {
                this.loading = true;
                this.error = '';

                const response = await request('/v1/users/me');
                if (!response.ok) {
                    if (response.status === 401) {
                        // Token expired or invalid, redirect to login
                        setAccessToken(null);
                        window.location.href = '/login';
                        return;
                    }
                    throw new Error(`Failed to load profile: ${response.status}`);
                }

                const data = await response.json();
                this.user = data.data || data;

            } catch (error) {
                console.error('Profile load error:', error);
                this.error = 'Failed to load profile information';

                // If unauthorized, redirect to login
                if (error.status === 401 || error.message?.includes('401') || error.message?.includes('Unauthorized')) {
                    setAccessToken(null);
                    window.location.href = '/login';
                }
            } finally {
                this.loading = false;
            }
        },

        async deleteAccount() {
            try {
                const confirmed = await window.modal(
                    'Delete account',
                    'Are you sure you want to delete your account? This action cannot be undone. All your data will be permanently deleted.',
                    'Delete account',
                    'Cancel'
                );

                if (!confirmed) {
                    return; // User canceled the action
                }

                // Call API to delete the account
                const response = await request('/v1/users/me', {
                    method: 'DELETE'
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || `Failed to delete account: ${response.status}`);
                }

                // Clear tokens
                setAccessToken(null);
                setRefreshToken(null);

                // Show success message
                await window.showMessage(
                    'Account deleted',
                    'Your account has been successfully deleted. You will be redirected to the home page.',
                    'success'
                );

                // Redirect to the home page
                window.location.href = '/';

            } catch (error) {
                console.error('Delete account error:', error);

                // Show error
                await window.showMessage(
                    'Error',
                    `Failed to delete account: ${error.message}`,
                    'alert'
                );
            }
        },

        async sendEmailVerification() {
            try {
                if (this.sendingEmailVerification) {
                    return;
                }

                // Clear previous message
                this.emailVerificationMessage = '';
                this.emailVerificationType = '';

                this.sendingEmailVerification = true;

                // Call API to send email verification
                const response = await request('/v1/users/me/verify-email', {
                    method: 'POST'
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || `Failed to send verification email: ${response.status}`);
                }

                const data = await response.json();

                // Show success message
                this.emailVerificationMessage = data.message || 'Verification email sent successfully!';
                this.emailVerificationType = 'success';

                // Hide message after 5 seconds
                setTimeout(() => {
                    this.emailVerificationMessage = '';
                    this.emailVerificationType = '';
                }, 5000);

            } catch (error) {
                console.error('Email verification error:', error);

                // Show error message
                this.emailVerificationMessage = error.message || 'Failed to send verification email. Please try again.';
                this.emailVerificationType = 'error';

                // Hide message after 5 seconds
                setTimeout(() => {
                    this.emailVerificationMessage = '';
                    this.emailVerificationType = '';
                }, 5000);
            } finally {
                this.sendingEmailVerification = false;
            }
        },

        async toggleProfileVisibility() {
            try {
                if (!this.user || this.togglingVisibility) {
                    return;
                }

                this.togglingVisibility = true;

                // Determine new visibility state
                const newVisibility = !this.user.is_public;

                // Call API to update profile visibility
                const response = await request('/v1/users/me', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        is_public: newVisibility
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || `Failed to update profile visibility: ${response.status}`);
                }

                const data = await response.json();

                // Update local user data
                this.user.is_public = newVisibility;

                // Show success message
                await window.showMessage(
                    'Profile Updated',
                    `Your profile is now ${newVisibility ? 'public' : 'private'}.`,
                    'success'
                );

            } catch (error) {
                console.error('Profile visibility update error:', error);

                // Show error message
                await window.showMessage(
                    'Error',
                    `Failed to update profile visibility: ${error.message}`,
                    'alert'
                );
            } finally {
                this.togglingVisibility = false;
            }
        },

    }));
});