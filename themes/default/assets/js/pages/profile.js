import { request, setAccessToken, setRefreshToken } from '../app/api.js';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('userProfile', () => ({
        user: null,
        loading: true,
        error: '',
        emailVerificationMessage: '',
        emailVerificationType: '',

        async init() {
            await this.loadUserProfile();
        },

        formatDate(timestamp) {
            if (!timestamp) return 'Not set';
            const date = new Date(timestamp * 1000);
            return date.toLocaleDateString();
        },

        formatDateTime(timestamp) {
            if (!timestamp) return 'Not set';
            const date = new Date(timestamp * 1000);
            return date.toLocaleString();
        },

        get profileCompletionWidth() {
            if (!this.user) return '0';
            let complete = 0;
            if (this.user.first_name) complete += 20;
            if (this.user.last_name) complete += 20;
            if (this.user.nickname) complete += 20;
            if (this.user.gender) complete += 20;
            if (this.user.birthday) complete += 20;
            return complete;
        },

        get contactCompletionWidth() {
            if (!this.user) return '0';
            if (this.user.email && this.user.bio && this.user.website) return '100';
            if (this.user.email && this.user.bio) return '70';
            if (this.user.email) return '40';
            return '10';
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
                // Show modal window through Alpine store
                const modalStore = window.Alpine.store('modal');
                const confirmed = await modalStore.show(
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
                await modalStore.show(
                    'Account deleted',
                    'Your account has been successfully deleted. You will be redirected to the home page.',
                    'OK',
                    ''
                );

                // Redirect to the home page
                window.location.href = '/';

            } catch (error) {
                console.error('Delete account error:', error);

                // Show error
                await modalStore.show(
                    'Error',
                    `Failed to delete account: ${error.message}`,
                    'OK',
                    ''
                );
            }
        },

        async sendEmailVerification() {
            try {
                // Clear previous message
                this.emailVerificationMessage = '';
                this.emailVerificationType = '';

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
            }
        }
    }));
});
