/**
 * ACP Users Manager Component
 * 
 * Handles loading, displaying, searching and managing users in ACP
 */

import { request, handleApiError } from '../app/api.js';
import { DEBUG } from '../app/config.js';
import { escapeHtml, formatDateTime, formatRelativeTime, getUserInitials } from '../app/helpers/formatters.js';
import { isValidEmail } from '../app/helpers/validation.js';
import { set_control_error, clear_control_errors } from '../app/helpers/controlErrors.js';

// Constants
const ROLE_BADGE_CLASSES = {
    'u': 'neutral',
    'm': 'info',
    'a': 'warning',
    'su': 'danger'
};

const MESSAGE_AUTO_HIDE_DELAY = 5000;

// Alpine.js Users Manager Component
document.addEventListener('alpine:init', () => {
    window.Alpine.data('usersManager', () => ({
        // Reactive data
        users: [],
        pagination: {
            current: 1,
            last: 1,
            total: 0,
            per_page: 20
        },
        loading: false,
        listRequestId: 0,
        searchQuery: '',
        filters: {
            role: '',
            status: ''
        },
        get hasFilters() {
            return Boolean(this.searchQuery.trim() || this.filters.role || this.filters.status);
        },
        successMessage: '',
        errorMessage: '',
        editLoading: false,
        editSaving: false,
        editingUserId: null,
        editRequestId: 0,
        editingUser: {},
        availableRoles: [],
        editForm: {
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
        editPasswordForm: {
            new_password: '',
            confirm_password: ''
        },
        editSuccessMessage: '',
        editErrorMessage: '',

        // Initialization
        init() {
            Promise.all([
                this.loadAvailableRoles(),
                this.loadUsers()
            ]).catch(error => {
                if (DEBUG) window.log('error', 'Initialization error:', error);
                this.showRequestError(error, 'Error loading roles');
            });
        },

        // Unified API request handler
        async apiRequest(url, options = {}) {
            const response = await request(url, options);

            if (!response.ok) {
                let errorMessage = `Request failed: ${response.status}`;
                try {
                    const errorData = await response.json();
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    // Ignore JSON parse errors
                }
                const error = new Error(errorMessage);
                error.status = response.status;
                throw error;
            }

            const responseData = await response.json();
            return responseData.data || responseData;
        },

        // Display request errors at the operation level, in the relevant panel.
        showRequestError(error, prefix, edit = false) {
            const show = (message, type) => edit
                ? this.showEditMessage(message, type)
                : this.showMessage(message, type);

            if (handleApiError(error, show)) {
                show(`${prefix}: ${error.message}`, 'error');
            }
        },

        // Load users from API
        async loadUsers(page = 1) {
            const requestId = ++this.listRequestId;

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

                const data = await this.apiRequest(`/v1/users?${params.toString()}`);
                if (!data || requestId !== this.listRequestId) return;

                this.users = data.items || [];
                this.pagination = {
                    current: data.meta?.current_page || 1,
                    last: data.meta?.total_pages || 1,
                    total: data.meta?.total || 0,
                    per_page: data.meta?.per_page || 20
                };

                if (DEBUG) window.log('log', 'Users loaded:', this.users.length);
            } catch (error) {
                if (requestId !== this.listRequestId) return;
                if (DEBUG) window.log('error', 'Error loading users:', error);
                this.showRequestError(error, 'Error loading users');
            } finally {
                if (requestId === this.listRequestId) this.loading = false;
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
        hasAvatar(user) {
            return Boolean(user.avatar && user.avatar !== 'null');
        },

        getRoleLabel(role) {
            return this.availableRoles?.[role]?.name || role;
        },

        getRoleBadgeClass(role) {
            return ROLE_BADGE_CLASSES[role] || ROLE_BADGE_CLASSES['u'];
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

        // Helper: Check if user is current user
        isCurrentUser(userId) {
            const currentUser = window.Alpine.store('auth').user;
            return currentUser && currentUser.id && parseInt(userId) === parseInt(currentUser.id);
        },

        // User action methods
        toggleEditUser(user) {
            if (this.editSaving) return;

            if (DEBUG) window.log('log', 'Toggling edit for user:', user);

            // If editing this user already, close it
            if (this.editingUserId === user.id) {
                this.closeEditPanel();
                return;
            }

            // Otherwise, open edit for this user
            this.openEditPanel(user.id);
        },

        async openEditPanel(userId) {
            if (this.editSaving) return;

            const requestId = ++this.editRequestId;
            this.editingUserId = userId;
            this.editingUser = {};
            this.editLoading = true;
            this.clearEditMessages();
            this.clearEditFieldErrors();
            this.resetEditPasswordForm();

            try {
                if (!this.availableRoles || this.availableRoles.length === 0) {
                    await this.loadAvailableRoles();
                }

                if (requestId !== this.editRequestId) return;
                await this.loadUserDetails(userId, requestId);
            } catch (error) {
                if (requestId !== this.editRequestId) return;
                if (DEBUG) window.log('error', 'Error opening edit panel:', error);
                this.showRequestError(error, 'Failed to load user data', true);
            } finally {
                if (requestId === this.editRequestId) this.editLoading = false;
            }
        },

        closeEditPanel() {
            ++this.editRequestId;
            this.editingUserId = null;
            this.editingUser = {};
            this.editLoading = false;
            this.clearEditMessages();
            this.clearEditFieldErrors();
            this.resetEditPasswordForm();
        },

        async loadAvailableRoles() {
            this.availableRoles = await this.apiRequest('/v1/acp/users/roles');
            if (DEBUG) window.log('log', 'Available roles loaded:', this.availableRoles);
        },

        async loadUserDetails(userId, requestId = this.editRequestId) {
            try {
                const user = await this.apiRequest(`/v1/acp/users/${userId}`);
                if (requestId !== this.editRequestId || userId !== this.editingUserId) return;

                this.editingUser = user;
                if (DEBUG) window.log('log', 'Editing user loaded:', this.editingUser);
                this.populateEditForm();
            } catch (error) {
                if (requestId !== this.editRequestId || userId !== this.editingUserId) return;
                if (DEBUG) window.log('error', 'Error loading user details:', error);
                if (error.status === 404 || error.message.includes('404') || error.message.includes('not found')) {
                    this.editingUser = {};
                } else {
                    throw error;
                }
            }
        },

        populateEditForm() {
            if (!this.editingUser) return;

            const booleanFields = ['is_active', 'is_verified', 'is_banned', 'is_public'];
            const stringFields = ['email', 'role', 'nickname', 'first_name', 'last_name', 'gender', 'avatar', 'bio', 'birthday', 'website', 'ban_reason'];

            stringFields.forEach(field => {
                this.editForm[field] = this.editingUser[field] || (field === 'role' ? 'u' : '');
            });

            booleanFields.forEach(field => {
                this.editForm[field] = this.convertDbBoolean(this.editingUser[field]);
            });

            if (this.editingUser.ban_expired && this.editingUser.ban_expired > 0) {
                const date = new Date(this.editingUser.ban_expired * 1000);
                this.editForm.ban_expired_date = this.formatDateTimeLocal(date);
            } else {
                this.editForm.ban_expired_date = '';
            }
        },

        resetEditForm() {
            this.populateEditForm();
            this.clearEditMessages();
            this.clearEditFieldErrors();
            this.resetEditPasswordForm();
        },

        resetEditPasswordForm() {
            this.editPasswordForm.new_password = '';
            this.editPasswordForm.confirm_password = '';
        },

        clearEditFieldErrors() {
            clear_control_errors(this.$root);
        },

        validateEditForm() {
            this.clearEditFieldErrors();
            let hasErrors = false;

            if (!this.editForm.email || !isValidEmail(this.editForm.email)) {
                set_control_error(this.$root.querySelector(`#email_for_${this.editingUserId}`), 'Please enter a valid email address');
                hasErrors = true;
            }

            if (!this.editForm.role) {
                set_control_error(this.$root.querySelector(`#role_for_${this.editingUserId}`), 'Please select a role');
                hasErrors = true;
            }

            if (hasErrors) {
                this.$root.querySelector(`#email_for_${this.editingUserId}`).closest('details').open = true;
                this.showEditMessage('Please fix the errors below', 'error');
            }

            return !hasErrors;
        },

        prepareUserUpdateData() {
            const updateData = {
                email: this.editForm.email,
                role: this.editForm.role,
                is_active: this.editForm.is_active ? 1 : 0,
                is_verified: this.editForm.is_verified ? 1 : 0,
                is_banned: this.editForm.is_banned ? 1 : 0,
                ban_reason: this.editForm.ban_reason || '',
                nickname: this.editForm.nickname || null,
                first_name: this.editForm.first_name || null,
                last_name: this.editForm.last_name || null,
                gender: this.editForm.gender || null,
                bio: this.editForm.bio || null,
                birthday: this.editForm.birthday || null,
                website: this.editForm.website || null,
                is_public: this.editForm.is_public ? 1 : 0
            };

            if (this.editForm.is_banned && this.editForm.ban_expired_date) {
                const date = new Date(this.editForm.ban_expired_date);
                updateData.ban_expired = Math.floor(date.getTime() / 1000);
            } else {
                updateData.ban_expired = 0;
            }

            return updateData;
        },

        convertDbBoolean(value) {
            return value === true || value === 1 || value === '1';
        },

        async saveEditedUser(event) {
            if (event) event.preventDefault();
            if (this.editSaving || this.editLoading || !this.editingUser.id) return;
            if (!this.validateEditForm()) return;

            this.editSaving = true;
            this.clearEditMessages();

            try {
                const updateData = this.prepareUserUpdateData();
                await this.apiRequest(`/v1/acp/users/${this.editingUserId}`, {
                    method: 'PUT',
                    body: JSON.stringify(updateData)
                });

                await this.loadUserDetails(this.editingUserId);
                this.updateUserInList(this.editingUserId, {
                    email: this.editingUser.email,
                    role: this.editingUser.role,
                    is_active: this.convertDbBoolean(this.editingUser.is_active),
                    is_verified: this.convertDbBoolean(this.editingUser.is_verified),
                    is_banned: this.convertDbBoolean(this.editingUser.is_banned),
                    ban_reason: this.editingUser.ban_reason || '',
                    ban_expired: this.editingUser.ban_expired || 0,
                    nickname: this.editingUser.nickname || '',
                    last_activity: this.editingUser.last_activity
                });

                this.showEditMessage('User updated successfully', 'success');
            } catch (error) {
                if (DEBUG) window.log('error', 'Error saving user:', error);
                this.showRequestError(error, 'Error updating user', true);
            } finally {
                this.editSaving = false;
            }
        },

        validatePasswordForm() {
            if (!this.editPasswordForm.new_password) {
                this.showEditMessage('Please enter a new password', 'error');
                return false;
            }

            if (this.editPasswordForm.new_password !== this.editPasswordForm.confirm_password) {
                this.showEditMessage('Passwords do not match', 'error');
                return false;
            }

            return true;
        },

        async changeUserPassword() {
            if (this.editSaving || this.editLoading || !this.editingUser.id) return;
            if (!this.validatePasswordForm()) return;

            this.editSaving = true;
            this.clearEditMessages();

            try {
                await this.apiRequest(`/v1/acp/users/${this.editingUserId}/password`, {
                    method: 'PUT',
                    body: JSON.stringify({ password: this.editPasswordForm.new_password })
                });

                this.resetEditPasswordForm();
                this.showEditMessage('Password changed successfully', 'success');
            } catch (error) {
                if (DEBUG) window.log('error', 'Error changing password:', error);
                this.showRequestError(error, 'Error changing password', true);
            } finally {
                this.editSaving = false;
            }
        },

        async deleteUserAccount() {
            if (this.editSaving || this.editLoading || !this.editingUser.id) return;
            const requestId = this.editRequestId;
            const confirmed = await window.modal(
                'Delete User',
                `Are you sure you want to delete user <strong>${escapeHtml(this.editingUser && this.editingUser.login ? this.editingUser.login : 'Unknown')}</strong>? This action cannot be undone.`,
                'Delete',
                'Cancel',
                'danger'
            );

            if (!confirmed || this.editSaving || this.editLoading || requestId !== this.editRequestId) return;

            this.editSaving = true;
            this.clearEditMessages();

            try {
                await this.apiRequest(`/v1/acp/users/${this.editingUserId}`, {
                    method: 'DELETE'
                });

                this.users = this.users.filter(u => u.id !== this.editingUserId);
                this.showMessage('User deleted successfully', 'success');
                this.closeEditPanel();
            } catch (error) {
                if (DEBUG) window.log('error', 'Error deleting user:', error);
                this.showRequestError(error, 'Error deleting user', true);
            } finally {
                this.editSaving = false;
            }
        },

        async deleteUserAvatar() {
            if (this.editSaving || this.editLoading || !this.editingUser.id) return;
            const requestId = this.editRequestId;
            const confirmed = await window.modal(
                'Delete Avatar',
                `Are you sure you want to delete the avatar for user <strong>${escapeHtml(this.editingUser && this.editingUser.login ? this.editingUser.login : 'Unknown')}</strong>?`,
                'Delete Avatar',
                'Cancel',
                'warning'
            );

            if (!confirmed || this.editSaving || this.editLoading || requestId !== this.editRequestId) return;

            this.editSaving = true;
            this.clearEditMessages();

            try {
                await this.apiRequest(`/v1/acp/users/${this.editingUserId}/avatar`, {
                    method: 'DELETE'
                });

                // Update the editing user data
                if (this.editingUser) {
                    this.editingUser.avatar = null;
                }

                // Update the user in the list
                this.updateUserInList(this.editingUserId, { avatar: null });

                this.showEditMessage('Avatar deleted successfully', 'success');
            } catch (error) {
                if (DEBUG) window.log('error', 'Error deleting avatar:', error);
                this.showRequestError(error, 'Error deleting avatar', true);
            } finally {
                this.editSaving = false;
            }
        },

        formatDateTimeLocal(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        },

        formatDateTimeValue(timestamp) {
            return formatDateTime(timestamp);
        },

        formatRelativeTimeValue(timestamp) {
            return formatRelativeTime(timestamp);
        },

        showEditMessage(message, type = 'success') {
            this.clearEditMessages();
            if (type === 'success') {
                this.editSuccessMessage = message;
                setTimeout(() => {
                    if (this.editSuccessMessage === message) {
                        this.editSuccessMessage = '';
                    }
                }, MESSAGE_AUTO_HIDE_DELAY);
            } else {
                this.editErrorMessage = message;
                setTimeout(() => {
                    if (this.editErrorMessage === message) {
                        this.editErrorMessage = '';
                    }
                }, MESSAGE_AUTO_HIDE_DELAY);
            }
        },

        clearEditMessages() {
            this.editSuccessMessage = '';
            this.editErrorMessage = '';
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

                await this.apiRequest(`/v1/moderate/ban/${user.id}`, {
                    method: 'POST',
                    body: JSON.stringify(requestBody)
                });

                // Update only this user in the list
                this.updateUserInList(user.id, {
                    is_banned: true,
                    ban_reason: banData.reason || 'Banned by administrator',
                    ban_expired: banData.expires || 0
                });

                this.showMessage(`User "${user.login}" has been banned`, 'success');
            } catch (error) {
                if (DEBUG) window.log('error', 'Error banning user:', error);
                this.showRequestError(error, 'Error banning user');
            }
        },

        async showBanModal(user) {
            return new Promise((resolve) => {
                // Create modal HTML without extra attributes visible
                const modalHtml = `<div class="space-y-3 text-left"><p class="text-sm text-zinc-600 mb-2">Ban user: <strong>${escapeHtml(user.login)}</strong></p><div><label for="ban-reason" class="ui-label">Reason (optional)</label><input type="text" id="ban-reason" placeholder="Enter ban reason" class="ui-control" /></div><div><label for="ban-expires" class="ui-label">Expires (optional)</label><input type="datetime-local" id="ban-expires" class="ui-control" /><p class="ui-hint">Leave empty for permanent ban</p></div></div>`;

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
                `Are you sure you want to unban user <strong>${escapeHtml(user.login)}</strong>?`,
                'Unban',
                'Cancel',
                'info'
            );

            if (!confirmed) {
                return;
            }

            try {
                await this.apiRequest(`/v1/moderate/unban/${user.id}`);

                // Update only this user in the list
                this.updateUserInList(user.id, {
                    is_banned: false,
                    ban_reason: '',
                    ban_expired: 0
                });

                this.showMessage(`User "${user.login}" has been unbanned`, 'success');
            } catch (error) {
                if (DEBUG) window.log('error', 'Error unbanning user:', error);
                this.showRequestError(error, 'Error unbanning user');
            }
        },

        // Message handling
        showMessage(message, type = 'success') {
            this.clearMessages();
            if (type === 'success') {
                this.successMessage = message;
                setTimeout(() => this.successMessage = '', MESSAGE_AUTO_HIDE_DELAY);
            } else {
                this.errorMessage = message;
                setTimeout(() => this.errorMessage = '', MESSAGE_AUTO_HIDE_DELAY);
            }
        },

        clearMessages() {
            this.successMessage = '';
            this.errorMessage = '';
        }
    }));
});
