import { request } from '../app/api.js';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('userProfile', () => ({
        user: null,
        loading: true,
        error: '',

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
            if (this.user.first_name && this.user.last_name && this.user.nickname) return '75';
            if (this.user.first_name || this.user.last_name) return '40';
            return '20';
        },

        get profileCompletionPercent() {
            if (!this.user) return 'Loading...';
            if (this.user.first_name && this.user.last_name && this.user.nickname) return '75%';
            if (this.user.first_name || this.user.last_name) return '40%';
            return '20%';
        },

        get contactCompletionWidth() {
            if (!this.user) return '0';
            if (this.user.email && this.user.bio && this.user.website) return '100';
            if (this.user.email && this.user.bio) return '70';
            if (this.user.email) return '40';
            return '10';
        },

        get contactCompletionPercent() {
            if (!this.user) return 'Loading...';
            if (this.user.email && this.user.bio && this.user.website) return '100%';
            if (this.user.email && this.user.bio) return '70%';
            if (this.user.email) return '40%';
            return '10%';
        },

        async loadUserProfile() {
            try {
                this.loading = true;
                this.error = '';

                const response = await request('/v1/users/me');
                if (!response.ok) {
                    if (response.status === 401) {
                        // Token expired or invalid, redirect to login
                        localStorage.removeItem('access_token');
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
                    localStorage.removeItem('access_token');
                    window.location.href = '/login';
                }
            } finally {
                this.loading = false;
            }
        }
    }));
});
