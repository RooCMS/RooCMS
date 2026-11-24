<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

$page_title = 'Structure — Admin Control Panel — RooCMS';
$page_description = 'Manage site structure and pages';

$theme_name = basename(dirname(__DIR__, 2));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [
	$theme_base.'/assets/js/app/acp.js',
    $theme_base.'/assets/js/app/acp-access.js',
    $theme_base.'/assets/js/pages/acp-structure.js'
];

$acp_heading = 'Site Structure';
$acp_intro = 'Manage pages, navigation and site hierarchy';
$acp_breadcrumb = 'Structure';
$acp_home_label = 'Home';

ob_start();
?>

<div class="acp-shell">
    <div class="acp-layout">
        <?php require __DIR__ . '/../../partials/acp-nav.php'; ?>

        <section class="acp-content">
            <header class="mb-8">
                <?php require __DIR__ . '/../../partials/acp-heading.php'; ?>
            </header>

            <div class="space-y-6" x-data="structureManager()">

                <!-- Create/Edit Form (Inline) -->
                <div x-show="editingPageId !== null || showCreateForm" x-transition class="ui-panel spacious">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-zinc-900" x-text="editingPageId ? 'Edit Page' : 'Create New Page'"></h3>
                        <button type="button" x-on:click="closeEditForm()" class="cursor-pointer text-zinc-400 hover:text-zinc-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <p x-show="formLoading" class="text-sm text-zinc-500">Loading page data...</p>
                    <form x-show="formReady" x-on:submit.prevent="savePage()" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Slug -->
                            <div>
                                <label for="page-slug" class="ui-label">Slug</label>
                                <input type="text" id="page-slug" x-model="form.slug" required
                                       class="ui-control">
                                <p class="ui-hint">URL-friendly identifier</p>
                            </div>

                            <!-- Title -->
                            <div>
                                <label for="page-title" class="ui-label">Title</label>
                                <input type="text" id="page-title" x-model="form.title" required
                                       class="ui-control">
                            </div>

                            <!-- Parent Page -->
                            <div>
                                <label for="page-parent" class="ui-label">Parent Page</label>
                                <select id="page-parent" x-model="form.parent_id"
                                        :disabled="editingPageId === 1"
                                        class="select-custom ui-control">
                                    <option value="1">Root Level (Home Page)</option>
                                    <template x-for="page in availableParents" :key="page.id">
                                        <option :value="page.id" x-text="page.title"></option>
                                    </template>
                                </select>
                                <p x-show="editingPageId === 1" class="ui-hint">Home page (ID=1) must remain at root level</p>
                            </div>

                            <!-- Page Type -->
                            <div>
                                <label for="page-type" class="ui-label">Page Type</label>
                                <select id="page-type" x-model="form.page_type"
                                        class="ui-control select-custom">
                                    <option value="page">Page</option>
                                    <option value="feed">Feed</option>
                                </select>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="page-status" class="ui-label">Status</label>
                                <select id="page-status" x-model="form.status"
                                        class="ui-control select-custom">
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <!-- Navigation -->
                            <div class="mt-8">
                                <label class="flex cursor-pointer items-center justify-between gap-4">
                                    <span class="text-sm text-zinc-800">Show in navigation</span>
                                    <input type="checkbox" id="page-nav" x-model="form.nav" class="peer sr-only">
                                    <span class="ui-switch"></span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Meta Title -->
                            <div>
                                <label for="page-meta-title" class="ui-label">SEO: Meta Title (optional)</label>
                                <input type="text" id="page-meta-title" x-model="form.meta_title" maxlength="70"
                                       class="ui-control">
                            </div>

                            <!-- Noindex -->
                            <div class="mt-8">
                                <label class="flex cursor-pointer items-center justify-between gap-4">
                                    <span class="text-sm text-zinc-800">Hide from search engines (noindex)</span>
                                    <input type="checkbox" id="page-noindex" x-model="form.noindex" class="peer sr-only">
                                    <span class="ui-switch"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Meta Description -->
                        <div>
                            <label for="page-meta-description" class="ui-label">SEO: Meta Description (optional)</label>
                            <textarea id="page-meta-description" x-model="form.meta_description" rows="3" maxlength="160"
                                      class="ui-control"></textarea>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end gap-2 pt-4 border-t border-zinc-200">
                            <button type="button" x-on:click="closeEditForm()"
                                    class="btn secondary">
                                Cancel
                            </button>
                            <button type="submit" :disabled="modalLoading"
                                    class="btn primary">
                                <span x-show="!modalLoading" x-text="editingPageId ? 'Update Page' : 'Create Page'"></span>
                                <span x-show="modalLoading">Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Messages container -->
                <div class="messages-container">
                    <div class="form-success alert success" x-show="successMessage" x-text="successMessage" x-transition></div>
                    <div class="form-error alert danger" x-show="errorMessage" x-text="errorMessage" x-transition></div>
                </div>

                <!-- Filters and Actions -->
                <div class="ui-panel">
                    <div class="ui-toolbar">
                        <!-- Filters -->
                        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                            <div class="flex items-center gap-2">
                                <label for="status-filter" class="text-sm font-medium text-zinc-700">Status:</label>
                                <select id="status-filter" x-model="filters.status"
                                        class="ui-control compact select-custom">
                                    <option value="">All</option>
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <label for="search-filter" class="text-sm font-medium text-zinc-700">Search:</label>
                                <input type="text" id="search-filter" x-model.debounce.300ms="filters.search"
                                       placeholder="Title or slug..."
                                       class="ui-control compact">
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="ui-actions">
                            <button type="button" x-on:click="showCreateFormModal()"
                                    class="btn primary">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Page
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pages List -->
                <div class="ui-panel flush">
                    <!-- Table Header -->
                    <div class="ui-panel-header">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-zinc-900">Pages</h3>
                            <div class="text-sm text-zinc-500" x-text="paginationInfo()"></div>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div x-show="loading" class="ui-empty">
                        <div class="inline-flex items-center gap-3 text-sm text-zinc-500">
                            <?php require __DIR__ . '/../../partials/ui-spinner.php'; ?>
                            Loading pages...
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div x-show="!loading && pages.length === 0" class="ui-empty">
                        <div class="text-zinc-400">
                            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="ui-empty-title">No pages found</h3>
                            <p class="mt-1 text-sm text-zinc-500">Get started by creating a new page.</p>
                        </div>
                    </div>

                    <!-- Pages List -->
                    <div x-show="!loading && pages.length > 0" class="overflow-hidden">
                        <!-- Desktop Header -->
                        <div class="hidden md:grid bg-zinc-50 grid-cols-12 gap-4">
                            <div class="ui-table-heading col-span-5">Page</div>
                            <div class="ui-table-heading col-span-2">Type</div>
                            <div class="ui-table-heading col-span-1">Children</div>
                            <div class="ui-table-heading col-span-4">Actions</div>
                        </div>
                        <!-- Pages Grid -->
                        <div class="bg-white divide-y divide-zinc-200">
                            <template x-for="page in pages" :key="page.id">
                                <div>
                                    <!-- Desktop View -->
                                    <div class="hidden md:grid hover:bg-zinc-50 grid-cols-12 gap-4">
                                        <div class="col-span-5 px-6 py-4">
                                            <div class="flex items-center">
                                                <!-- Status & Navigation Icons -->
                                                <div class="flex-shrink-0 mr-3 flex items-center gap-1">
                                                    <!-- Status Icons -->
                                                    <div>
                                                        <!-- Active status -->
                                                        <svg x-show="page.status === 'active'" class="ui-status-icon success" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        <!-- Draft status -->
                                                        <svg x-show="page.status === 'draft'" class="ui-status-icon warning" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        <!-- Inactive status -->
                                                        <svg x-show="page.status === 'inactive'" class="ui-status-icon danger" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                    <!-- Navigation Icon -->
                                                    <div>
                                                        <svg x-show="page.nav" class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        <svg x-show="!page.nav" class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-sm font-medium text-zinc-900" x-text="page.title"></div>
                                                    <div class="text-sm text-zinc-500">/<span x-text="page.slug"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-span-2 px-6 py-4 align-middle inline-flex items-center">
                                            <span class="badge"
                                                  :class="page.page_type === 'page' ? 'info' : 'success'"
                                                  x-text="page.page_type === 'page' ? 'Page' : 'Feed'">
                                            </span>
                                        </div>
                                        <div class="col-span-1 px-6 py-4 text-sm text-zinc-900 align-middle inline-flex items-center" x-text="page.childs"></div>
                                        <div class="col-span-4 px-6 py-4 text-sm font-medium align-middle inline-flex items-center justify-end">
                                            <div class="ui-actions justify-end">
                                            <button x-on:click="showEditModal(page.id)"
                                                    class="btn secondary small min-w-20">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </button>
                                            <button x-on:click="changeStatusWithConfirm(page.id, page.status)"
                                                    :class="getStatusButtonClass(page.status)"
                                                    class="btn small min-w-20">
                                                <span x-text="getStatusActionText(page.status)"></span>
                                            </button>
                                            <button x-on:click="deletePageWithConfirm(page.id, page.title)"
                                                    :disabled="page.childs > 0 || page.id === 1"
                                                    class="btn outline danger small min-w-20">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Delete
                                            </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Mobile View -->
                                    <div class="md:hidden p-4 hover:bg-zinc-50 border-b border-zinc-200">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-center flex-1 min-w-0">
                                                <!-- Status & Navigation Icons -->
                                                <div class="flex-shrink-0 mr-3 flex items-center gap-1">
                                                    <!-- Status Icons -->
                                                    <div>
                                                        <!-- Active status -->
                                                        <svg x-show="page.status === 'active'" class="ui-status-icon success" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        <!-- Draft status -->
                                                        <svg x-show="page.status === 'draft'" class="ui-status-icon warning" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        <!-- Inactive status -->
                                                        <svg x-show="page.status === 'inactive'" class="ui-status-icon danger" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                    <!-- Navigation Icon -->
                                                    <div>
                                                        <svg x-show="page.nav" class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        <svg x-show="!page.nav" class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-sm font-medium text-zinc-900" x-text="page.title"></div>
                                                    <div class="text-sm text-zinc-500">/<span x-text="page.slug"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-3">
                                                <span class="badge"
                                                      :class="page.page_type === 'page' ? 'info' : 'success'"
                                                      x-text="page.page_type === 'page' ? 'Page' : 'Feed'">
                                                </span>
                                                <span class="text-sm text-zinc-600">
                                                    <span class="font-medium">Children:</span> <span x-text="page.childs"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ui-actions justify-end">
                                            <button x-on:click="showEditModal(page.id)"
                                                    class="btn secondary small min-w-20">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </button>
                                            <button x-on:click="changeStatusWithConfirm(page.id, page.status)"
                                                    :class="getStatusButtonClass(page.status)"
                                                    class="btn small min-w-20">
                                                <span x-text="getStatusActionText(page.status)"></span>
                                            </button>
                                            <button x-on:click="deletePageWithConfirm(page.id, page.title)"
                                                    :disabled="page.childs > 0 || page.id === 1"
                                                    class="btn outline danger small min-w-20">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Pagination -->
                        <div x-show="pagination.total > pagination.limit" class="ui-pagination border-t border-zinc-200 bg-white px-5 py-3">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <button aria-label="Previous page" x-on:click="prevPage()" :disabled="pagination.current_page <= 1"
                                        class="btn secondary">
                                    Previous
                                </button>
                                <button aria-label="Next page" x-on:click="nextPage()" :disabled="pagination.current_page >= pagination.pages"
                                        class="btn secondary ml-3">
                                    Next
                                </button>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-zinc-700">
                                        Showing <span class="font-medium" x-text="getShowingFrom()"></span> to <span class="font-medium" x-text="getShowingTo()"></span> of <span class="font-medium" x-text="pagination.total"></span> results
                                    </p>
                                </div>
                                <div>
                                    <nav class="ui-page-list" aria-label="Structure pages">
                                        <button aria-label="Previous page" x-on:click="prevPage()" :disabled="pagination.current_page <= 1"
                                                class="btn secondary small">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                        </button>
                                        <template x-for="page in getPaginationPages()" :key="page">
                                            <button x-on:click="goToPage(page)"
                                                    :class="page === pagination.current_page ? 'primary' : 'secondary'" :aria-current="page === pagination.current_page ? 'page' : null"
                                                    class="btn small">
                                                <span x-text="page"></span>
                                            </button>
                                        </template>
                                        <button aria-label="Next page" x-on:click="nextPage()" :disabled="pagination.current_page >= pagination.pages"
                                                class="btn secondary small">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../../layouts/base.php';
?>
