import { request } from '../app/api.js';
import { formatDate, formatDateTime } from '../app/helpers/formatters.js';

/**
 * Get user by id
 * @param {string} user_id - User id
 * @returns {Promise<object>} - User data
 */
async function getUserById(user_id) {
    const res = await request(`/v1/users/${user_id}`, { method: 'GET' });
    const json = await res.json();

    if (!res.ok) {
        const error = new Error(json.message || 'Request failed');
        error.status = res.status;
        throw error;
    }

    return json.data;
}

// Error messages map for better UX
const ERROR_MESSAGES = {
    401: { message: 'To view this user\'s profile, you need to sign in.', showLogin: true },
    403: { message: 'Access denied to user profile', showLogin: false },
    404: { message: 'User not found', showLogin: false }
};

// Alpine.js User Profile Component
document.addEventListener('alpine:init', () => {
    window.Alpine.data('userProfile', () => ({
        // Component state
        loading: true,
        error: null,
        user: null,
        showLoginLink: false,

        // Initialize component
        async init() {
            const userId = window.autoUserId;

            if (!userId) {
                this.error = 'No user ID specified in URL';
                this.loading = false;
                return;
            }

            await this.loadUserProfile(userId);
        },

        // Load user profile data
        async loadUserProfile(userId) {
            this.loading = true;
            this.error = null;

            try {
                this.user = await getUserById(userId);
                this.updatePageMeta();
            } catch (error) {
                // Don't log expected errors to console
                if (![401, 403, 404].includes(error.status)) {
                    window.log('error', 'Error loading user profile:', error);
                }
                this.handleError(error);
            } finally {
                this.loading = false;
            }
        },

        // Update page title and subtitle
        updatePageMeta() {
            const displayName = this.user.nickname || `id: ${this.user.id}`;
            document.title = `User Profile - ${displayName} — RooCMS`;
            
            const subtitleElement = document.getElementById('user-subtitle');
            if (subtitleElement) {
                subtitleElement.textContent = `Profile of ${this.user.nickname || `user id: ${this.user.id}`}`;
            }
        },

        // Handle and display errors
        handleError(error) {
            const errorConfig = ERROR_MESSAGES[error.status];
            
            if (errorConfig) {
                this.error = errorConfig.message;
                this.showLoginLink = errorConfig.showLogin;
            } else {
                this.error = error.message || 'Failed to load user profile';
                this.showLoginLink = false;
            }
        },

        // Get user display name
        get displayName() {
            if (!this.user) return 'Loading...';
            const fullName = `${this.user.first_name || ''} ${this.user.last_name || ''}`.trim();
            return this.user.nickname || fullName || this.user.login;
        },

        // Get status badges
        get statusBadges() {
            if (!this.user) return [];

            const badges = [];
            
            badges.push({
                text: this.user.is_active == 1 ? 'Active' : 'Inactive',
                classes: this.user.is_active == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
            });

            if (this.user.is_banned == 1) {
                badges.push({
                    text: 'Banned',
                    classes: 'bg-red-100 text-red-800'
                });
            }

            return badges;
        },

        // Get formatted birthday
        get formattedBirthday() {
            return this.user?.birthday ? formatDate(this.user.birthday) : '-';
        },

        // Get last activity
        get lastActivity() {
            return this.user?.last_activity ? formatDateTime(this.user.last_activity) : '-';
        },

        // Get avatar URL
        get avatarUrl() {
            return this.user?.avatar ? `/up/${this.user.avatar}` : null;
        },

        // Get website link
        get websiteLink() {
            return this.user?.website ? { url: this.user.website, text: this.user.website } : null;
        }
    }));
});