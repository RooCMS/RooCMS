/**
 * Alpine.js Users Manager Component
 * Handles loading, displaying, searching and managing users in ACP
 */

import { request } from '../app/api.js';
import { DEBUG } from '../app/config.js';

// Alpine.js Users Manager Component
window.usersManager = () => ({
    // Reactive data
    users: [],
    pagination: {
        current: 1,
        last: 1,
        total: 0,
        per_page: 20
    },
    loading: false,
    searchQuery: '',
    filters: {
        role: '',
        status: ''
    },
    successMessage: '',
    errorMessage: '',

    // Initialization
    init() {
        if (DEBUG) {
            log('log', 'Initializing Alpine users manager...');
            log('log', 'Access token present:', !!localStorage.getItem('access_token'));
            log('log', 'Refresh token present:', !!localStorage.getItem('refresh_token'));
        }
        this.loadUsers();
    },

    // Load users from API
    async loadUsers(page = 1) {
        if (this.loading) return;

        this.loading = true;
        this.clearMessages();

        try {
            const params = new URLSearchParams({
                page: page.toString(),
                limit: this.pagination.per_page.toString()
            });

            // Add search query if present
            if (this.searchQuery.trim()) {
                params.append('search', this.searchQuery.trim());
            }

            // Add filters
            if (this.filters.role) {
                params.append('role', this.filters.role);
            }

            // Handle status filter
            if (this.filters.status) {
                switch (this.filters.status) {
                    case 'active':
                        params.append('is_active', '1');
                        break;
                    case 'inactive':
                        params.append('is_active', '0');
                        break;
                    case 'banned':
                        params.append('is_banned', '1');
                        break;
                }
            }

            if (DEBUG) log('log', 'Loading users with params:', params.toString());

            const response = await request(`/v1/users?${params.toString()}`);

            if (DEBUG) log('log', 'API Response status:', response.status);

            if (!response.ok) {
                // Handle specific error cases
                if (response.status === 401) {
                    this.showMessage('Authentication required. Please log in.', 'error');
                    // Redirect to login after a short delay
                    setTimeout(() => window.location.href = '/login', 2000);
                    return;
                } else if (response.status === 403) {
                    this.showMessage('Access denied. Admin privileges required.', 'error');
                    return;
                }

                let errorMessage = `Failed to load users: ${response.status}`;
                try {
                    const errorData = await response.json();
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    // Ignore JSON parse errors
                }
                throw new Error(errorMessage);
            }

            const responseData = await response.json();

            if (DEBUG) log('log', 'Raw API response:', responseData);

            // API returns data wrapped in 'data' property
            const data = responseData.data || responseData;

            if (DEBUG) log('log', 'Extracted data:', data);

            this.users = data.items || [];
            this.pagination = {
                current: data.meta?.current_page || 1,
                last: data.meta?.total_pages || 1,
                total: data.meta?.total || 0,
                per_page: data.meta?.per_page || 20
            };

            if (DEBUG) {
                log('log', 'Users loaded successfully:', this.users.length, 'users');
                log('log', 'Pagination:', this.pagination);
                log('log', 'Pagination buttons should be:', {
                    prevDisabled: this.pagination.current <= 1,
                    nextDisabled: this.pagination.current >= this.pagination.last
                });
                log('log', 'First user sample:', this.users[0]);
            }
        } catch (error) {
            if (DEBUG) log('error', 'Error loading users:', error);
            this.showMessage('Error loading users: ' + error.message, 'error');
        } finally {
            this.loading = false;
        }
    },

    // Search users with debounce
    searchUsers() {
        this.pagination.current = 1; // Reset to first page when searching
        this.loadUsers();
    },

    // Apply filters
    applyFilters() {
        this.pagination.current = 1; // Reset to first page when filtering
        this.loadUsers();
    },

    // Clear all filters and search
    clearFilters() {
        this.searchQuery = '';
        this.filters.role = '';
        this.filters.status = '';
        this.pagination.current = 1;
        this.loadUsers();
    },

    // Pagination methods
    goToPage(page) {
        if (DEBUG) log('log', 'goToPage called with:', page, 'current:', this.pagination.current, 'last:', this.pagination.last);
        if (page < 1 || page > this.pagination.last) {
            if (DEBUG) log('log', 'Page out of range, returning');
            return;
        }
        if (DEBUG) log('log', 'Loading page:', page);
        this.loadUsers(page);
    },

    getVisiblePages() {
        const current = this.pagination.current;
        const last = this.pagination.last;
        const pages = [];

        // Always show first page
        if (last > 1) pages.push(1);

        // Calculate range around current page
        let start = Math.max(2, current - 1);
        let end = Math.min(last - 1, current + 1);

        // Add ellipsis before current range if needed
        if (start > 2) pages.push('...');

        // Add pages in current range
        for (let i = start; i <= end; i++) {
            pages.push(i);
        }

        // Add ellipsis after current range if needed
        if (end < last - 1) pages.push('...');

        // Always show last page
        if (last > 1) pages.push(last);

        return pages;
    },

    getShowingFrom() {
        if (this.pagination.total === 0) return 0;
        return ((this.pagination.current - 1) * this.pagination.per_page) + 1;
    },

    getShowingTo() {
        const from = this.getShowingFrom();
        const to = from + this.users.length - 1;
        return Math.min(to, this.pagination.total);
    },

    // User utility methods
    getInitials(user) {
        const firstName = user.first_name || '';
        const lastName = user.last_name || '';
        const login = user.login || '';

        if (firstName && lastName) {
            return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
        } else if (firstName) {
            return firstName.substring(0, 2).toUpperCase();
        } else if (lastName) {
            return lastName.substring(0, 2).toUpperCase();
        } else {
            return login.substring(0, 2).toUpperCase();
        }
    },

    getRoleLabel(role) {
        const roleLabels = {
            'u': 'User',
            'm': 'Moderator',
            'a': 'Admin',
            'su': 'Superuser'
        };
        return roleLabels[role] || role;
    },

    getRoleBadgeClass(role) {
        const roleClasses = {
            'u': 'bg-zinc-100 text-zinc-700',
            'm': 'bg-sky-100 text-sky-700',
            'a': 'bg-amber-100 text-amber-700',
            'su': 'bg-rose-100 text-rose-700'
        };
        return roleClasses[role] || 'bg-zinc-100 text-zinc-700';
    },

    formatLastActivity(timestamp) {
        if (!timestamp) return 'Never';

        // Convert Unix timestamp (seconds) to milliseconds for JavaScript Date
        const date = new Date(timestamp * 1000);
        const now = new Date();
        const diffMs = now - date;
        const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
        const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
        const diffMinutes = Math.floor(diffMs / (1000 * 60));

        if (diffDays > 0) {
            return diffDays === 1 ? '1 day ago' : `${diffDays} days ago`;
        } else if (diffHours > 0) {
            return diffHours === 1 ? '1 hour ago' : `${diffHours} hours ago`;
        } else if (diffMinutes > 0) {
            return diffMinutes === 1 ? '1 minute ago' : `${diffMinutes} minutes ago`;
        } else {
            return 'Just now';
        }
    },

    // User action methods
    editUser(user) {
        // TODO: Implement user editing modal/form
        if (DEBUG) log('log', 'Edit user:', user);
        this.showMessage('User editing not implemented yet', 'error');
    },

    async banUser(user) {
        if (!confirm(`Are you sure you want to ban user "${user.login}"?`)) {
            return;
        }

        try {
            const response = await request(`/v1/users/${user.id}`, {
                method: 'PUT',
                body: JSON.stringify({
                    is_banned: true,
                    ban_reason: 'Banned by administrator'
                })
            });

            if (!response.ok) {
                throw new Error(`Failed to ban user: ${response.status}`);
            }

            // Reload users list
            this.loadUsers(this.pagination.current);
            this.showMessage(`User "${user.login}" has been banned`, 'success');
        } catch (error) {
            if (DEBUG) log('error', 'Error banning user:', error);
            this.showMessage('Error banning user: ' + error.message, 'error');
        }
    },

    async unbanUser(user) {
        if (!confirm(`Are you sure you want to unban user "${user.login}"?`)) {
            return;
        }

        try {
            const response = await request(`/v1/users/${user.id}`, {
                method: 'PUT',
                body: JSON.stringify({
                    is_banned: false,
                    ban_reason: null,
                    ban_expired: null
                })
            });

            if (!response.ok) {
                throw new Error(`Failed to unban user: ${response.status}`);
            }

            // Reload users list
            this.loadUsers(this.pagination.current);
            this.showMessage(`User "${user.login}" has been unbanned`, 'success');
        } catch (error) {
            if (DEBUG) log('error', 'Error unbanning user:', error);
            this.showMessage('Error unbanning user: ' + error.message, 'error');
        }
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
    }
});
