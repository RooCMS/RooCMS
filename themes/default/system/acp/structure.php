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
        <?php require __DIR__ . '/../partials/acp-nav.php'; ?>

        <section class="acp-content">
            <header class="mb-8">
                <?php require __DIR__ . '/../partials/acp-heading.php'; ?>
            </header>

            <div class="space-y-5" x-data="structureManager()">
                <div x-show="successMessage || errorMessage" x-cloak aria-live="polite">
                    <div class="form-success alert success" x-show="successMessage" x-text="successMessage"></div>
                    <div class="form-error alert danger" x-show="errorMessage" x-text="errorMessage" role="alert"></div>
                </div>

                <template x-if="showCreateForm">
                    <div class="ui-panel spacious">
                        <?php require __DIR__ . '/../partials/acp-structure-form.php'; ?>
                    </div>
                </template>

                <div class="ui-panel flush overflow-hidden">
                    <div class="ui-panel-header ui-toolbar">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-base font-semibold text-zinc-900">Pages</h2>
                                <span class="badge neutral compact" x-show="!loading" x-text="pagination.total"></span>
                            </div>
                            <p class="ui-hint">Page hierarchy, full structure paths and publication.</p>
                        </div>
                        <button type="button" x-on:click="showCreateFormModal()" :disabled="modalLoading" class="btn primary">
                            <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M12 5v14m-7-7h14"></path></svg>
                            Add page
                        </button>
                    </div>

                    <div class="grid grid-cols-1 items-end gap-4 border-b border-zinc-200/80 bg-zinc-50/50 p-5 sm:grid-cols-2 lg:grid-cols-[minmax(0,1fr)_10rem_auto]">
                        <div class="min-w-0">
                            <label for="search-filter" class="ui-label">Find a page</label>
                            <div class="relative">
                                <input type="search" id="search-filter" x-model.debounce.300ms="filters.search" placeholder="Search by title or URL slug…" class="ui-control search">
                                <svg aria-hidden="true" class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0"></path></svg>
                            </div>
                        </div>
                        <div class="min-w-0">
                            <label for="status-filter" class="ui-label">Status</label>
                            <select id="status-filter" x-model="filters.status" class="ui-control select-custom">
                                <option value="">All statuses</option>
                                <option value="draft">Draft</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <button type="button" x-on:click="reset_filters()" :disabled="!filters.search && !filters.status" class="btn secondary">Reset filters</button>
                    </div>

                    <div x-show="loading" class="ui-loading" role="status">
                        <?php require __DIR__ . '/../../partials/ui-spinner.php'; ?> Loading pages…
                    </div>

                    <div x-show="!loading && pages.length === 0" x-cloak class="ui-empty">
                        <svg aria-hidden="true" class="mx-auto mb-3 h-10 w-10 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l7 7v9a2 2 0 0 1-2 2ZM12 3v7h7"></path></svg>
                        <h3 class="ui-empty-title">No pages found</h3>
                        <p class="mt-1" x-text="filters.search || filters.status ? 'Try another search or reset the filters.' : 'Add your first page to start building the site.'"></p>
                    </div>

                    <div x-show="!loading && pages.length > 0">
                        <div class="ui-record-row structure ui-record-heading" aria-hidden="true">
                            <span>Page</span><span>Publication</span><span>Navigation</span><span class="text-right">Actions</span>
                        </div>
                        <div class="ui-record-list" role="list" aria-label="Site pages">
                            <template x-for="page in pages" :key="page.id">
                                <div role="listitem">
                                    <div class="ui-record-row structure" :class="{ 'is-open': editingPageId === page.id }">
                                        <div class="ui-record-cell ui-record-identity">
                                            <span x-show="page.level > 0" class="flex shrink-0 self-stretch" aria-hidden="true">
                                                <template x-for="level in indent_levels(page)" :key="level"><span class="ui-tree-indent"></span></template>
                                            </span>
                                            <span class="ui-record-icon" role="img" :aria-label="page.page_type === 'feed' ? 'Feed' : 'Page'" :title="page.page_type === 'feed' ? 'Feed' : 'Page'">
                                                <svg x-show="page.id === 1 && page.page_type !== 'feed'" aria-hidden="true" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m3 10 9-7 9 7M5 9v11h5v-6h4v6h5V9"></path></svg>
                                                <svg x-show="page.id !== 1 && page.page_type !== 'feed'" aria-hidden="true" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 3H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8Zm0 0v5h5M9 13h6m-6 4h4"></path></svg>
                                                <svg x-show="page.page_type === 'feed'" aria-hidden="true" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="1.5" d="M4 11a9 9 0 0 1 9 9M4 4a16 16 0 0 1 16 16"></path><circle cx="5" cy="19" r="1" fill="currentColor" stroke="none"></circle></svg>
                                            </span>
                                            <div class="min-w-0">
                                                <button type="button" x-on:click="showEditModal(page.id)" class="ui-record-title" :aria-expanded="editingPageId === page.id" :aria-controls="'page-editor-' + page.id" x-text="page.title"></button>
                                                <p class="mt-0.5 break-all font-mono text-xs text-zinc-500" title="Path in the site structure" x-text="page.structure_path"></p>
                                            </div>
                                        </div>
                                        <div class="ui-record-cell">
                                            <span class="ui-record-label">Publication</span>
                                            <button type="button" x-on:click="changeStatusWithConfirm(page.id, page.status)" class="badge compact outlined cursor-pointer" :class="getStatusBadgeClass(page.status)" :title="getStatusActionText(page.status)" :aria-label="getStatusText(page.status) + '. ' + getStatusActionText(page.status) + ' page ' + page.title">
                                                <span x-text="getStatusText(page.status)"></span>
                                                <svg aria-hidden="true" class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 16 16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 6a5 5 0 0 0-8.5-2L3 6m0 0V2m0 4h4m-4 4a5 5 0 0 0 8.5 2l1.5-2m0 0v4m0-4H9"></path></svg>
                                            </button>
                                        </div>
                                        <div class="ui-record-cell">
                                            <span class="ui-record-label">Navigation</span>
                                            <span class="inline-flex items-center gap-1.5 text-xs text-zinc-500">
                                                <svg aria-hidden="true" class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                                <span x-text="page.nav ? 'In menu' : 'Hidden'"></span>
                                            </span>
                                        </div>
                                        <div class="ui-record-actions">
                                            <button type="button" x-on:click="showEditModal(page.id)" :aria-expanded="editingPageId === page.id" :aria-controls="'page-editor-' + page.id" :aria-label="'Edit page ' + page.title" class="btn secondary small">
                                                <span x-text="editingPageId === page.id ? 'Close' : 'Edit'"></span>
                                            </button>
                                            <button type="button" x-on:click="deletePageWithConfirm(page.id, page.title)" :disabled="page.childs > 0 || page.id === 1" :aria-label="'Delete page ' + page.title" :title="page.id === 1 ? 'The home page cannot be deleted' : (page.childs > 0 ? 'Delete child pages first' : 'Delete page')" class="btn outline danger small">
                                                <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7h16M10 11v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <template x-if="editingPageId === page.id">
                                        <div class="ui-record-editor" :id="'page-editor-' + page.id">
                                            <?php require __DIR__ . '/../partials/acp-structure-form.php'; ?>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div x-show="!loading && pagination.total > 0" class="ui-pagination border-t border-zinc-200/80 px-5 py-4">
                        <p class="text-xs text-zinc-500" role="status" x-text="paginationInfo()"></p>
                        <nav x-show="pagination.pages > 1" class="ui-page-list" aria-label="Structure pages">
                            <button type="button" aria-label="Previous page" x-on:click="prevPage()" :disabled="pagination.current_page <= 1" class="btn secondary small">Previous</button>
                            <template x-for="page_number in getPaginationPages()" :key="page_number">
                                <button type="button" x-on:click="goToPage(page_number)" class="btn small" :class="page_number === pagination.current_page ? 'primary' : 'secondary'" :aria-current="page_number === pagination.current_page ? 'page' : null" x-text="page_number"></button>
                            </template>
                            <button type="button" aria-label="Next page" x-on:click="nextPage()" :disabled="pagination.current_page >= pagination.pages" class="btn secondary small">Next</button>
                        </nav>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../../layouts/base.php';
?>
