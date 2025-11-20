/**
 * ACP Structure Manager Component
 * 
 * Handles loading, displaying and managing site structure/pages
 */

import { request, handleApiError } from '../app/api.js';
import { DEBUG } from '../app/config.js';

// Alpine.js Structure Manager Component
document.addEventListener('alpine:init', () => {
    window.Alpine.data('structureManager', () => ({
        // Reactive data
        pages: [],
        availableParents: [],
        pagination: {
            total: 0,
            limit: 50,
            offset: 0,
            pages: 0,
            current_page: 1
        },
        filters: {
            status: '',
            search: ''
        },
        loading: false,
        successMessage: '',
        errorMessage: '',

        // Form state
        showCreateForm: false,
        editingPageId: null,
        modalLoading: false,
        form: {
            slug: '',
            title: '',
            parent_id: 1,
            page_type: 'page',
            status: 'draft',
            nav: false,
            meta_title: '',
            meta_description: '',
            noindex: false
        },

        // Initialization
        init() {
            Promise.all([
                this.loadPages()
            ]).catch(error => {
                if (DEBUG) window.log('error', 'Initialization error:', error);
            });
        },

        // Load pages from API
        async loadPages() {
            if (this.loading) return;

            this.loading = true;
            this.clearMessages();

            try {
                const params = new URLSearchParams({
                    limit: this.pagination.limit,
                    offset: this.pagination.offset
                });

                if (this.filters.status) params.append('status', this.filters.status);
                if (this.filters.search) params.append('search', this.filters.search);

                const response = await this.apiRequest(`/v1/acp/structure?${params.toString()}`);

                this.pages = response.pages || [];
                this.pagination = response.pagination || this.pagination;

            } catch (error) {
                this.showMessage(error.message || 'Error loading pages', 'error');
            } finally {
                this.loading = false;
            }
        },

        // Load available parent pages for dropdown
        async loadAvailableParents() {
            try {
                const response = await this.apiRequest('/v1/acp/structure?limit=1000');
                this.availableParents = (response.pages || []).filter(page => page.id !== this.editingPageId);
            } catch (error) {
                this.availableParents = [];
            }
        },

        // Show create form
        async showCreateFormModal() {
            this.editingPageId = null;
            this.showCreateForm = true;
            this.resetForm();
            await this.loadAvailableParents();
        },

        // Show edit modal
        async showEditModal(pageId) {
            this.editingPageId = pageId;
            this.showCreateForm = true;

            try {
                const response = await this.apiRequest(`/v1/acp/structure/${pageId}`);
                this.populateForm(response);
                await this.loadAvailableParents();
            } catch (error) {
                this.showMessage('Error loading page data', 'error');
            }
        },

        // Close edit form
        closeEditForm() {
            this.showCreateForm = false;
            this.editingPageId = null;
            this.resetForm();
        },

        // Reset form to defaults
        resetForm() {
            this.form = {
                slug: '',
                title: '',
                parent_id: 1,
                page_type: 'page',
                status: 'draft',
                nav: false,
                meta_title: '',
                meta_description: '',
                noindex: false
            };
        },

        // Populate form with page data
        populateForm(pageData) {
            this.form = {
                slug: pageData.slug || '',
                title: pageData.title || '',
                parent_id: pageData.parent_id || 1,
                page_type: pageData.page_type || 'page',
                status: pageData.status || 'draft',
                nav: pageData.nav || false,
                meta_title: pageData.meta_title || '',
                meta_description: pageData.meta_description || '',
                noindex: pageData.noindex || false
            };
        },

        // Save page (create or update)
        async savePage() {
            if (this.modalLoading) return;

            this.modalLoading = true;
            this.clearMessages();

            try {
                const url = this.editingPageId
                    ? `/v1/acp/structure/${this.editingPageId}`
                    : '/v1/acp/structure';

                const method = this.editingPageId ? 'PUT' : 'POST';

                await this.apiRequest(url, {
                    method: method,
                    body: JSON.stringify(this.form)
                });

                this.showMessage(`Page ${this.editingPageId ? 'updated' : 'created'} successfully`);
                this.closeEditForm();
                await this.loadPages();

            } catch (error) {
                if (error.validationErrors) {
                    this.showValidationErrors(error.validationErrors);
                } else {
                    this.showMessage(error.message || 'Error saving page', 'error');
                }
            } finally {
                this.modalLoading = false;
            }
        },

        // Change page status with confirmation
        async changeStatusWithConfirm(pageId, currentStatus) {
            const page = this.pages.find(p => p.id === pageId);
            if (!page) return;

            const statusMap = {
                'draft': 'active',
                'active': 'inactive',
                'inactive': 'draft'
            };

            const newStatus = statusMap[currentStatus] || 'active';
            const statusText = {
                'draft': 'Draft',
                'active': 'Active',
                'inactive': 'Inactive'
            };

            const confirmed = await window.modal(
                'Change Page Status',
                `Are you sure you want to change status of "<strong>${page.title}</strong>" from ${statusText[currentStatus]} to ${statusText[newStatus]}?`,
                'Change Status',
                'Cancel',
                'notice'
            );

            if (confirmed) {
                await this.changeStatus(pageId, newStatus);
            }
        },

        // Change page status
        async changeStatus(pageId, newStatus) {
            try {
                await this.apiRequest(`/v1/acp/structure/${pageId}/status`, {
                    method: 'PATCH',
                    body: JSON.stringify({ status: newStatus })
                });

                this.showMessage('Page status updated successfully');
                await this.loadPages();

            } catch (error) {
                this.showMessage(error.message || 'Error changing page status', 'error');
            }
        },

        // Delete page with confirmation
        async deletePageWithConfirm(pageId, title) {
            const page = this.pages.find(p => p.id === pageId);
            if (!page) return;

            if (page.childs > 0) {
                await window.modal(
                    'Cannot Delete Page',
                    `Page "<strong>${title}</strong>" has ${page.childs} child page(s). Please delete child pages first.`,
                    'OK',
                    '',
                    'warning'
                );
                return;
            }

            if (pageId === 1) {
                await window.modal(
                    'Cannot Delete Page',
                    'The home page cannot be deleted.',
                    'OK',
                    '',
                    'warning'
                );
                return;
            }

            const confirmed = await window.modal(
                'Delete Page',
                `Are you sure you want to delete page "<strong>${title}</strong>"? This action cannot be undone.`,
                'Delete',
                'Cancel',
                'alert'
            );

            if (confirmed) {
                await this.deletePage(pageId);
            }
        },

        // Delete page
        async deletePage(pageId) {
            try {
                await this.apiRequest(`/v1/acp/structure/${pageId}`, {
                    method: 'DELETE'
                });

                this.showMessage('Page deleted successfully');
                await this.loadPages();

            } catch (error) {
                this.showMessage(error.message || 'Error deleting page', 'error');
            }
        },

        // Unified API request handler
        async apiRequest(url, options = {}) {
            const response = await request(url, options);

            if (!response.ok) {
                if (!handleApiError(response, (message, type) => this.showMessage(message, type))) {
                    throw new Error('Authentication failed');
                }

                if (response.status === 422) {
                    const errorData = await response.json();
                    if (errorData.details?.validation_errors) {
                        const error = new Error('Validation failed');
                        error.validationErrors = errorData.details.validation_errors;
                        throw error;
                    }
                }

                const errorData = await response.json();
                throw new Error(errorData.message || `Request failed: ${response.status}`);
            }

            return await response.json();
        },

        // Pagination helpers
        prevPage() {
            if (this.pagination.current_page > 1) {
                this.goToPage(this.pagination.current_page - 1);
            }
        },

        nextPage() {
            if (this.pagination.current_page < this.pagination.pages) {
                this.goToPage(this.pagination.current_page + 1);
            }
        },

        goToPage(page) {
            this.pagination.offset = (page - 1) * this.pagination.limit;
            this.pagination.current_page = page;
            this.loadPages();
        },

        getPaginationPages() {
            const pages = [];
            const current = this.pagination.current_page;
            const total = this.pagination.pages;

            if (total <= 7) {
                for (let i = 1; i <= total; i++) {
                    pages.push(i);
                }
            } else {
                pages.push(1);
                if (current > 3) pages.push('...');
                const start = Math.max(2, current - 1);
                const end = Math.min(total - 1, current + 1);
                for (let i = start; i <= end; i++) {
                    pages.push(i);
                }
                if (current < total - 2) pages.push('...');
                pages.push(total);
            }

            return pages.filter(p => p !== '...');
        },

        getShowingFrom() {
            return this.pagination.offset + 1;
        },

        getShowingTo() {
            return Math.min(this.pagination.offset + this.pagination.limit, this.pagination.total);
        },

        paginationInfo() {
            const from = this.getShowingFrom();
            const to = this.getShowingTo();
            const total = this.pagination.total;
            return `${from}-${to} of ${total} pages`;
        },

        // Status helpers
        getStatusBadgeClass(status) {
            const classes = {
                'draft': 'bg-yellow-100 text-yellow-800',
                'active': 'bg-green-100 text-green-800',
                'inactive': 'bg-red-100 text-red-800'
            };
            return classes[status] || 'bg-zinc-100 text-zinc-800';
        },

        getStatusText(status) {
            const texts = {
                'draft': 'Draft',
                'active': 'Active',
                'inactive': 'Inactive'
            };
            return texts[status] || status;
        },

        getStatusActionText(status) {
            const actions = {
                'draft': 'Activate',
                'active': 'Deactivate',
                'inactive': 'Draft'
            };
            return actions[status] || 'Change';
        },

        getStatusButtonClass(status) {
            const classes = {
                'draft': 'text-green-600 hover:text-green-900',
                'active': 'text-red-600 hover:text-red-900',
                'inactive': 'text-yellow-600 hover:text-yellow-900'
            };
            return classes[status] || 'text-zinc-600 hover:text-zinc-900';
        },

        // Validation errors
        showValidationErrors(validationErrors) {
            this.showMessage('Please correct the validation errors below.', 'error');
            console.error('Validation errors:', validationErrors);
        },

        // Messages
        showMessage(message, type = 'success') {
            if (type === 'success') {
                this.successMessage = message;
                this.errorMessage = '';
            } else {
                this.errorMessage = message;
                this.successMessage = '';
            }

            setTimeout(() => {
                if (type === 'success') {
                    this.successMessage = '';
                } else {
                    this.errorMessage = '';
                }
            }, 5000);
        },

        clearMessages() {
            this.successMessage = '';
            this.errorMessage = '';
        }
    }));
});
