import { request, setAccessToken, setRefreshToken } from '../app/api.js';

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

        /**
         * Format date using global formatter
         */
        formatDate(timestamp) {
            return window.FormatterUtils.formatDate(timestamp);
        },

        /**
         * Format datetime using global formatter
         */
        formatDateTime(timestamp) {
            return window.FormatterUtils.formatDateTime(timestamp);
        },

        /**
         * Get avatar URL or null
         */
        get avatarUrl() {
            return this.user?.avatar ? `/up/${this.user.avatar}` : null;
        },

        /**
         * Calculate profile completion percentage
         */
        get profileCompletionWidth() {
            if (!this.user) return 0;

            const fields = ['first_name', 'last_name', 'nickname', 'gender', 'birthday'];
            const completed = fields.filter(field => this.user[field]).length;
            
            return Math.round((completed / fields.length) * 100);
        },

        /**
         * Calculate contact information completion percentage
         */
        get contactCompletionWidth() {
            if (!this.user) return 0;

            const requiredFields = ['email', 'bio', 'website'];
            const optionalFields = ['phone', 'address', 'social_links'];

            let score = 0;
            requiredFields.forEach(field => { if (this.user[field]) score += 30; });
            optionalFields.forEach(field => { if (this.user[field]) score += 10; });

            return Math.min(score, 100);
        },

        /**
         * Redirect to login page
         */
        redirectToLogin() {
            setAccessToken(null);
            window.location.href = '/!/login';
        },

        /**
         * Show temporary message with auto-hide
         */
        showTempMessage(message, type) {
            this.emailVerificationMessage = message;
            this.emailVerificationType = type;
            setTimeout(() => {
                this.emailVerificationMessage = '';
                this.emailVerificationType = '';
            }, 5000);
        },

        /**
         * Load user profile from API
         */
        async loadUserProfile() {
            try {
                this.loading = true;
                this.error = '';

                const response = await request('/v1/users/me');
                if (!response.ok) {
                    if (response.status === 401) {
                        this.redirectToLogin();
                        return;
                    }
                    throw new Error(`Failed to load profile: ${response.status}`);
                }

                const data = await response.json();
                this.user = data.data || data;

            } catch (error) {
                window.log('error', 'Profile load error:', error);
                this.error = 'Failed to load profile information';

                // If unauthorized, redirect to login
                if (error.status === 401 || error.message?.includes('401') || error.message?.includes('Unauthorized')) {
                    this.redirectToLogin();
                }
            } finally {
                this.loading = false;
            }
        },

        /**
         * Delete user account
         */
        async deleteAccount() {
            try {
                const confirmed = await window.modal(
                    'Delete account',
                    'Are you sure you want to delete your account? This action cannot be undone. All your data will be permanently deleted.',
                    'Delete account',
                    'Cancel'
                );

                if (!confirmed) return;

                const response = await request('/v1/users/me', { method: 'DELETE' });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || `Failed to delete account: ${response.status}`);
                }

                // Clear tokens and redirect
                setAccessToken(null);
                setRefreshToken(null);

                await window.showMessage(
                    'Account deleted',
                    'Your account has been successfully deleted. You will be redirected to the home page.',
                    'success'
                );

                window.location.href = '/';

            } catch (error) {
                window.log('error', 'Delete account error:', error);
                await window.showMessage('Error', `Failed to delete account: ${error.message}`, 'alert');
            }
        },

        /**
         * Send email verification
         */
        async sendEmailVerification() {
            if (this.sendingEmailVerification) return;

            try {
                this.sendingEmailVerification = true;
                this.emailVerificationMessage = '';
                this.emailVerificationType = '';

                const response = await request('/v1/users/me/verify-email', { method: 'POST' });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || `Failed to send verification email: ${response.status}`);
                }

                const data = await response.json();
                this.showTempMessage(data.message || 'Verification email sent successfully!', 'success');

            } catch (error) {
                window.log('error', 'Email verification error:', error);
                this.showTempMessage(error.message || 'Failed to send verification email. Please try again.', 'error');
            } finally {
                this.sendingEmailVerification = false;
            }
        },

        /**
         * Toggle profile visibility (public/private)
         */
        async toggleProfileVisibility() {
            if (!this.user || this.togglingVisibility) return;

            try {
                this.togglingVisibility = true;
                const newVisibility = !this.user.is_public;

                const response = await request('/v1/users/me', {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ is_public: newVisibility })
                });

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || `Failed to update profile visibility: ${response.status}`);
                }

                // Update local user data
                this.user.is_public = newVisibility;

                await window.showMessage(
                    'Profile Updated',
                    `Your profile is now ${newVisibility ? 'public' : 'private'}.`,
                    'success'
                );

            } catch (error) {
                window.log('error', 'Profile visibility update error:', error);
                await window.showMessage('Error', `Failed to update profile visibility: ${error.message}`, 'alert');
            } finally {
                this.togglingVisibility = false;
            }
        }

    }));
});