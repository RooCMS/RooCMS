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

ob_start();
?>

<div class="py-10">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-[260px_1fr] px-4 sm:px-6 lg:px-8 space-y-8">
        <?php require __DIR__ . '/../../partials/acp-nav.php'; ?>

        <section>
            <header class="mb-8">
                <nav class="mb-3 text-sm text-zinc-500" aria-label="Breadcrumbs">
                    <ol class="flex items-center gap-2">
                        <li><a href="/" class="hover:text-zinc-700">Home</a></li>
                        <li aria-hidden="true" class="text-zinc-400">/</li>
                        <li><a href="/!/acp" class="hover:text-zinc-700">ACP</a></li>
                        <li aria-hidden="true" class="text-zinc-400">/</li>
                        <li><span class="text-zinc-700">Structure</span></li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Site Structure</h1>
                <p class="mt-2 text-sm text-zinc-600">Manage pages, navigation and site hierarchy</p>
            </header>

            <div class="space-y-6" x-data="structureManager()">

                <!-- Create/Edit Form (Inline) -->
                <div x-show="editingPageId !== null || showCreateForm" x-transition class="rounded-xl border border-zinc-200/80 bg-white/80 p-6 shadow-sm backdrop-blur">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-zinc-900" x-text="editingPageId ? 'Edit Page' : 'Create New Page'"></h3>
                        <button type="button" x-on:click="closeEditForm()" class="cursor-pointer text-zinc-400 hover:text-zinc-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form x-on:submit.prevent="savePage()" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Slug -->
                            <div>
                                <label for="page-slug" class="block text-sm font-medium text-zinc-700 mb-1">Slug</label>
                                <input type="text" id="page-slug" x-model="form.slug" required
                                       class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-sky-500 focus:outline-none">
                                <p class="mt-1 text-xs text-zinc-500">URL-friendly identifier</p>
                            </div>

                            <!-- Title -->
                            <div>
                                <label for="page-title" class="block text-sm font-medium text-zinc-700 mb-1">Title</label>
                                <input type="text" id="page-title" x-model="form.title" required
                                       class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-sky-500 focus:outline-none">
                            </div>

                            <!-- Parent Page -->
                            <div>
                                <label for="page-parent" class="block text-sm font-medium text-zinc-700 mb-1">Parent Page</label>
                                <select id="page-parent" x-model="form.parent_id"
                                        :disabled="editingPageId === 1"
                                        :class="editingPageId === 1 ? 'select-custom block w-full rounded-lg border border-zinc-300 bg-zinc-100 px-3 py-2 text-sm text-zinc-500 cursor-not-allowed' : 'select-custom block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none'">
                                    <option value="1">Root Level (Home Page)</option>
                                    <template x-for="page in availableParents" :key="page.id">
                                        <option :value="page.id" x-text="page.title"></option>
                                    </template>
                                </select>
                                <p x-show="editingPageId === 1" class="mt-1 text-xs text-zinc-500">Home page (ID=1) must remain at root level</p>
                            </div>

                            <!-- Page Type -->
                            <div>
                                <label for="page-type" class="block text-sm font-medium text-zinc-700 mb-1">Page Type</label>
                                <select id="page-type" x-model="form.page_type"
                                        class="select-custom block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none">
                                    <option value="page">Page</option>
                                    <option value="feed">Feed</option>
                                </select>
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="page-status" class="block text-sm font-medium text-zinc-700 mb-1">Status</label>
                                <select id="page-status" x-model="form.status"
                                        class="select-custom block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none">
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
                                    <span class="relative inline-block h-6 w-11 rounded-full bg-zinc-300 transition peer-checked:bg-sky-900 after:absolute after:left-0.5 after:top-1/2 after:-translate-y-1/2 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Meta Title -->
                            <div>
                                <label for="page-meta-title" class="block text-sm font-medium text-zinc-700 mb-1">SEO: Meta Title (optional)</label>
                                <input type="text" id="page-meta-title" x-model="form.meta_title" maxlength="70"
                                       class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-sky-500 focus:outline-none">
                            </div>

                            <!-- Noindex -->
                            <div class="mt-8">
                                <label class="flex cursor-pointer items-center justify-between gap-4">
                                    <span class="text-sm text-zinc-800">Hide from search engines (noindex)</span>
                                    <input type="checkbox" id="page-noindex" x-model="form.noindex" class="peer sr-only">
                                    <span class="relative inline-block h-6 w-11 rounded-full bg-zinc-300 transition peer-checked:bg-sky-900 after:absolute after:left-0.5 after:top-1/2 after:-translate-y-1/2 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Meta Description -->
                        <div>
                            <label for="page-meta-description" class="block text-sm font-medium text-zinc-700 mb-1">SEO: Meta Description (optional)</label>
                            <textarea id="page-meta-description" x-model="form.meta_description" rows="3" maxlength="160"
                                      class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-sky-500 focus:outline-none"></textarea>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end gap-2 pt-4 border-t border-zinc-200">
                            <button type="button" x-on:click="closeEditForm()"
                                    class="cursor-pointer px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 rounded-lg hover:bg-zinc-50">
                                Cancel
                            </button>
                            <button type="submit" :disabled="modalLoading"
                                    class="cursor-pointer px-4 py-2 text-sm font-medium text-white bg-zinc-900 rounded-lg hover:bg-zinc-800 disabled:opacity-50">
                                <span x-show="!modalLoading" x-text="editingPageId ? 'Update Page' : 'Create Page'"></span>
                                <span x-show="modalLoading">Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Messages container -->
                <div class="messages-container">
                    <div class="form-success p-4 rounded-lg bg-green-50 border border-green-200 text-green-800" x-show="successMessage" x-text="successMessage" x-transition></div>
                    <div class="form-error p-4 rounded-lg bg-red-50 border border-red-200 text-red-800" x-show="errorMessage" x-text="errorMessage" x-transition></div>
                </div>

                <!-- Filters and Actions -->
                <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
                    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                        <!-- Filters -->
                        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
                            <div class="flex items-center gap-2">
                                <label for="status-filter" class="text-sm font-medium text-zinc-700">Status:</label>
                                <select id="status-filter" x-model="filters.status" x-on:change="loadPages()"
                                        class="select-custom rounded-lg border border-zinc-300 bg-white px-3 py-1.5 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none">
                                    <option value="">All</option>
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-2">
                                <label for="search-filter" class="text-sm font-medium text-zinc-700">Search:</label>
                                <input type="text" id="search-filter" x-model.debounce.300ms="filters.search" x-on:input="loadPages()"
                                       placeholder="Title or slug..."
                                       class="rounded-lg border border-zinc-300 bg-white px-3 py-1.5 text-sm text-zinc-900 placeholder-zinc-400 focus:border-sky-500 focus:outline-none">
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <button type="button" x-on:click="showCreateFormModal()"
                                    class="cursor-pointer inline-flex items-center justify-center rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Page
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Pages List -->
                <div class="rounded-xl border border-zinc-200/80 bg-white/80 shadow-sm backdrop-blur">
                    <!-- Table Header -->
                    <div class="border-b border-zinc-200/80 px-5 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-zinc-900">Pages</h3>
                            <div class="text-sm text-zinc-500" x-text="paginationInfo()"></div>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div x-show="loading" class="text-center py-12">
                        <div class="inline-flex items-center text-sm text-zinc-500">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading pages...
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div x-show="!loading && pages.length === 0" class="text-center py-12">
                        <div class="text-zinc-400">
                            <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-zinc-900">No pages found</h3>
                            <p class="mt-1 text-sm text-zinc-500">Get started by creating a new page.</p>
                        </div>
                    </div>

                    <!-- Pages List -->
                    <div x-show="!loading && pages.length > 0" class="overflow-hidden">
                        <!-- Desktop Header -->
                        <div class="hidden md:grid bg-zinc-50 grid-cols-12 gap-4">
                            <div class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider col-span-5">Page</div>
                            <div class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider col-span-2">Type</div>
                            <div class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider col-span-1">Children</div>
                            <div class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider col-span-4">Actions</div>
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
                                                        <svg x-show="page.status === 'active'" class="w-4 h-4" style="color: #10b981" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        <!-- Draft status -->
                                                        <svg x-show="page.status === 'draft'" class="w-4 h-4" style="color: #f59e0b" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        <!-- Inactive status -->
                                                        <svg x-show="page.status === 'inactive'" class="w-4 h-4" style="color: #ef4444" fill="currentColor" viewBox="0 0 20 20">
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
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium"
                                                  :class="page.page_type === 'page' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                                                  x-text="page.page_type === 'page' ? 'Page' : 'Feed'">
                                            </span>
                                        </div>
                                        <div class="col-span-1 px-6 py-4 text-sm text-zinc-900 align-middle inline-flex items-center" x-text="page.childs"></div>
                                        <div class="col-span-4 px-6 py-4 text-sm font-medium align-middle inline-flex items-center justify-end">
                                            <div class="flex items-center gap-2 flex-wrap justify-end">
                                            <button x-on:click="showEditModal(page.id)"
                                                    class="inline-flex items-center justify-center rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-sm font-medium text-zinc-900 hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 cursor-pointer w-[80px]">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </button>
                                            <button x-on:click="changeStatusWithConfirm(page.id, page.status)"
                                                    :class="getStatusButtonClass(page.status)"
                                                    class="inline-flex items-center justify-center rounded-lg border px-3 py-1.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 cursor-pointer w-[80px]">
                                                <span x-text="getStatusActionText(page.status)"></span>
                                            </button>
                                            <button x-on:click="deletePageWithConfirm(page.id, page.title)"
                                                    :disabled="page.childs > 0 || page.id === 1"
                                                    class="inline-flex items-center justify-center rounded-lg border border-rose-300 bg-white px-3 py-1.5 text-sm font-medium text-rose-700 hover:bg-rose-50 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 cursor-pointer w-[80px]">
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
                                                        <svg x-show="page.status === 'active'" class="w-4 h-4" style="color: #10b981" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        <!-- Draft status -->
                                                        <svg x-show="page.status === 'draft'" class="w-4 h-4" style="color: #f59e0b" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        <!-- Inactive status -->
                                                        <svg x-show="page.status === 'inactive'" class="w-4 h-4" style="color: #ef4444" fill="currentColor" viewBox="0 0 20 20">
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
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium"
                                                      :class="page.page_type === 'page' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                                                      x-text="page.page_type === 'page' ? 'Page' : 'Feed'">
                                                </span>
                                                <span class="text-sm text-zinc-600">
                                                    <span class="font-medium">Children:</span> <span x-text="page.childs"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 flex-wrap justify-end">
                                            <button x-on:click="showEditModal(page.id)"
                                                    class="inline-flex items-center justify-center rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-sm font-medium text-zinc-900 hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 cursor-pointer w-[80px]">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </button>
                                            <button x-on:click="changeStatusWithConfirm(page.id, page.status)"
                                                    :class="getStatusButtonClass(page.status)"
                                                    class="inline-flex items-center justify-center rounded-lg border px-3 py-1.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 cursor-pointer w-[80px]">
                                                <span x-text="getStatusActionText(page.status)"></span>
                                            </button>
                                            <button x-on:click="deletePageWithConfirm(page.id, page.title)"
                                                    :disabled="page.childs > 0 || page.id === 1"
                                                    class="inline-flex items-center justify-center rounded-lg border border-rose-300 bg-white px-3 py-1.5 text-sm font-medium text-rose-700 hover:bg-rose-50 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 cursor-pointer w-[80px]">
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
                        <div x-show="pagination.total > pagination.limit" class="bg-white px-5 py-3 border-t border-zinc-200 flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <button x-on:click="prevPage()" :disabled="pagination.current_page <= 1"
                                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-zinc-700 bg-white border border-zinc-300 hover:bg-zinc-50 disabled:opacity-50">
                                    Previous
                                </button>
                                <button x-on:click="nextPage()" :disabled="pagination.current_page >= pagination.pages"
                                        class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-zinc-700 bg-white border border-zinc-300 hover:bg-zinc-50 disabled:opacity-50">
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
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <button x-on:click="prevPage()" :disabled="pagination.current_page <= 1"
                                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-zinc-300 bg-white text-sm font-medium text-zinc-500 hover:bg-zinc-50 disabled:opacity-50">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                        </button>
                                        <template x-for="page in getPaginationPages()" :key="page">
                                            <button x-on:click="goToPage(page)"
                                                    :class="page === pagination.current_page ? 'z-10 bg-zinc-900 border-zinc-900 text-white' : 'bg-white border-zinc-300 text-zinc-500 hover:bg-zinc-50'"
                                                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                                <span x-text="page"></span>
                                            </button>
                                        </template>
                                        <button x-on:click="nextPage()" :disabled="pagination.current_page >= pagination.pages"
                                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-zinc-300 bg-white text-sm font-medium text-zinc-500 hover:bg-zinc-50 disabled:opacity-50">
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
