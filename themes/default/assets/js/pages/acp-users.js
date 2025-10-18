/**
 * Alpine.js Users Manager Component
 * Handles loading, displaying, searching and managing users in ACP
 */

import { request } from '../app/api.js';
import { DEBUG } from '../app/config.js';
import { formatDateTime, formatRelativeTime, getUserInitials } from '../app/helpers/formatters.js';

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
            window.log('log', 'Initializing Alpine users manager...');
            window.log('log', 'Access token present:', !!localStorage.getItem('access_token'));
            window.log('log', 'Refresh token present:', !!localStorage.getItem('refresh_token'));
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

            if (DEBUG) window.log('log', 'Loading users with params:', params.toString());

            const response = await request(`/v1/users?${params.toString()}`);

            if (DEBUG) window.log('log', 'API Response status:', response.status);

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

            if (DEBUG) window.log('log', 'Raw API response:', responseData);

            // API returns data wrapped in 'data' property
            const data = responseData.data || responseData;

            if (DEBUG) window.log('log', 'Extracted data:', data);

            this.users = data.items || [];
            this.pagination = {
                current: data.meta?.current_page || 1,
                last: data.meta?.total_pages || 1,
                total: data.meta?.total || 0,
                per_page: data.meta?.per_page || 20
            };

            if (DEBUG) {
                window.log('log', 'Users loaded successfully:', this.users.length, 'users');
                window.log('log', 'Pagination:', this.pagination);
                window.log('log', 'Pagination buttons should be:', {
                    prevDisabled: this.pagination.current <= 1,
                    nextDisabled: this.pagination.current >= this.pagination.last
                });
                window.log('log', 'First user sample:', this.users[0]);
            }
        } catch (error) {
            if (DEBUG) window.log('error', 'Error loading users:', error);
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
        if (DEBUG) window.log('log', 'goToPage called with:', page, 'current:', this.pagination.current, 'last:', this.pagination.last);
        if (page < 1 || page > this.pagination.last) {
            if (DEBUG) window.log('log', 'Page out of range, returning');
            return;
        }
        if (DEBUG) window.log('log', 'Loading page:', page);
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
        return getUserInitials(user);
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
        return formatRelativeTime(timestamp);
    },

    formatBanExpiry(timestamp) {
        if (!timestamp || timestamp === 0) return 'Permanent';

        const date = new Date(timestamp * 1000);
        const now = new Date();

        const formattedDate = formatDateTime(timestamp);

        // Check if already expired
        if (date < now) {
            return `Expired (${formattedDate})`;
        }

        return formattedDate;
    },

    // Helper: Update user in list
    updateUserInList(userId, updates) {
        const userIndex = this.users.findIndex(u => u.id === userId);
        if (userIndex !== -1) {
            Object.assign(this.users[userIndex], updates);
        }
    },

    // User action methods
    editUser(user) {
        if (DEBUG) window.log('log', 'Edit user:', user);
        // Redirect to user edit page with query parameter
        window.location.href = `/acp/user-edit?id=${user.id}`;
    },

    async banUser(user) {
        // Show ban modal with custom form
        const banData = await this.showBanModal(user);

        if (!banData) {
            return; // User cancelled
        }

        try {
            const requestBody = {};

            // Add reason if provided
            if (banData.reason && banData.reason.trim()) {
                requestBody.reason = banData.reason.trim();
            }

            // Add until timestamp if provided
            if (banData.expires) {
                requestBody.until = banData.expires;
            }

            const response = await request(`/v1/moderate/ban/${user.id}`, {
                method: 'POST',
                body: JSON.stringify(requestBody)
            });

            if (!response.ok) {
                throw new Error(`Failed to ban user: ${response.status}`);
            }

            // Update only this user in the list
            this.updateUserInList(user.id, {
                is_banned: true,
                ban_reason: banData.reason || 'Banned by administrator',
                ban_expired: banData.expires || 0
            });

            this.showMessage(`User "${user.login}" has been banned`, 'success');
        } catch (error) {
            if (DEBUG) window.log('error', 'Error banning user:', error);
            this.showMessage('Error banning user: ' + error.message, 'error');
        }
    },

    async showBanModal(user) {
        return new Promise((resolve) => {
            // Create modal HTML without extra attributes visible
            const modalHtml = `<div class="space-y-3 text-left"><p class="text-sm text-zinc-600 mb-2">Ban user: <strong>${user.login}</strong></p><div><label for="ban-reason" class="block text-sm font-medium text-zinc-700 mb-1">Reason (optional)</label><input type="text" id="ban-reason" placeholder="Enter ban reason" class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500" /></div><div><label for="ban-expires" class="block text-sm font-medium text-zinc-700 mb-1">Expires (optional)</label><input type="datetime-local" id="ban-expires" class="block w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500" /><p class="mt-1 text-xs text-zinc-500">Leave empty for permanent ban</p></div></div>`;

            // Use Alpine modal store
            const modal = window.Alpine.store('modal');
            modal.show('Ban User', modalHtml, 'Ban', 'Cancel', 'warning').then((confirmed) => {
                if (confirmed) {
                    const reason = document.getElementById('ban-reason')?.value || '';
                    const expiresInput = document.getElementById('ban-expires')?.value;
                    
                    let expires = null;
                    if (expiresInput) {
                        // Convert datetime-local to Unix timestamp
                        expires = Math.floor(new Date(expiresInput).getTime() / 1000);
                    }
                    
                    resolve({ reason, expires });
                } else {
                    resolve(null);
                }
            });
        });
    },

    async unbanUser(user) {
        // Use modal for confirmation
        const confirmed = await window.modal(
            'Unban User',
            `Are you sure you want to unban user <strong>${user.login}</strong>?`,
            'Unban',
            'Cancel',
            'notice'
        );

        if (!confirmed) {
            return;
        }

        try {
            const response = await request(`/v1/moderate/unban/${user.id}`);

            if (!response.ok) {
                throw new Error(`Failed to unban user: ${response.status}`);
            }

            // Update only this user in the list
            this.updateUserInList(user.id, {
                is_banned: false,
                ban_reason: '',
                ban_expired: 0
            });

            this.showMessage(`User "${user.login}" has been unbanned`, 'success');
        } catch (error) {
            if (DEBUG) window.log('error', 'Error unbanning user:', error);
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
