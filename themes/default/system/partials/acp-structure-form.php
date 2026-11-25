<?php declare(strict_types=1);
if (!defined('RooCMS')) {roocms_protect();}
?>
<div class="ui-toolbar mb-5">
    <div>
        <h3 class="text-base font-semibold text-zinc-900" x-text="editingPageId ? 'Page settings' : 'Create a page'"></h3>
        <p class="ui-hint">Set up the page, publication and search appearance.</p>
    </div>
    <button type="button" x-on:click="closeEditForm()" :disabled="modalLoading" class="btn secondary small" aria-label="Close page settings">
        <svg aria-hidden="true" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="m6 6 12 12M6 18 18 6"></path></svg>
        Close
    </button>
</div>

<div x-show="formLoading" class="ui-loading" role="status">
    <?php require __DIR__ . '/../../partials/ui-spinner.php'; ?> Loading page data…
</div>
<form x-show="formReady" x-on:submit.prevent="savePage()" class="space-y-4">
    <fieldset :disabled="modalLoading" class="grid min-w-0 grid-cols-1 items-start gap-4 lg:grid-cols-3">
        <legend class="sr-only">Page settings</legend>
        <div class="min-w-0 space-y-4 lg:col-span-2">
            <section class="ui-editor-section" aria-labelledby="page-basics-heading">
                <h4 id="page-basics-heading" class="ui-editor-heading">Page details</h4>
                <div class="space-y-4 p-4">
                    <div>
                        <label for="page-title" class="ui-label">Title <span class="text-rose-600" aria-hidden="true">*</span></label>
                        <input type="text" id="page-title" x-model="form.title" required class="ui-control" placeholder="e.g. About us">
                    </div>
                    <div>
                        <label for="page-slug" class="ui-label">URL slug <span class="text-rose-600" aria-hidden="true">*</span></label>
                        <input type="text" id="page-slug" x-model="form.slug" required class="ui-control font-mono" placeholder="about-us" aria-describedby="page-slug-hint">
                        <p id="page-slug-hint" class="ui-hint">The page identifier used in its URL.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="min-w-0">
                            <label for="page-parent" class="ui-label">Parent page</label>
                            <select id="page-parent" x-model="form.parent_id" :disabled="editingPageId === 1" class="select-custom ui-control">
                                <option value="1">Root level</option>
                                <template x-for="parent in availableParents" :key="parent.id">
                                    <option :value="parent.id" x-text="parent.title"></option>
                                </template>
                            </select>
                            <p x-show="editingPageId === 1" class="ui-hint">The home page stays at root level.</p>
                        </div>
                        <div class="min-w-0">
                            <label for="page-type" class="ui-label">Page type</label>
                            <select id="page-type" x-model="form.page_type" class="ui-control select-custom">
                                <option value="page">Page</option>
                                <option value="feed">Feed</option>
                            </select>
                        </div>
                    </div>
                </div>
            </section>

            <details class="ui-editor-section" open>
                <summary class="ui-editor-summary"><span>Search appearance <span class="ui-editor-caption">SEO settings</span></span></summary>
                <div class="ui-editor-body space-y-4">
                    <div>
                        <label for="page-meta-title" class="ui-label">Meta title <span class="font-normal text-zinc-400">· optional</span></label>
                        <input type="text" id="page-meta-title" x-model="form.meta_title" maxlength="70" class="ui-control" :placeholder="form.title">
                        <p class="ui-hint">A descriptive title for search results. Up to 70 characters.</p>
                    </div>
                    <div>
                        <label for="page-meta-description" class="ui-label">Meta description <span class="font-normal text-zinc-400">· optional</span></label>
                        <textarea id="page-meta-description" x-model="form.meta_description" rows="3" maxlength="160" class="ui-control" placeholder="A short description of this page…"></textarea>
                        <p class="ui-hint">Up to 160 characters.</p>
                    </div>
                    <label class="ui-setting-row border-t border-zinc-100 pt-4">
                        <span>Hide from search engines<span class="ui-hint">Ask search engines not to index this page.</span></span>
                        <input type="checkbox" id="page-noindex" x-model="form.noindex" role="switch" class="peer sr-only">
                        <span aria-hidden="true" class="ui-switch"></span>
                    </label>
                </div>
            </details>
        </div>

        <section class="ui-editor-section" aria-labelledby="page-publication-heading">
            <h4 id="page-publication-heading" class="ui-editor-heading">Publication</h4>
            <div class="space-y-5 p-4">
                <div>
                    <label for="page-status" class="ui-label">Status</label>
                    <select id="page-status" x-model="form.status" class="ui-control select-custom">
                        <option value="draft">Draft</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <p class="ui-hint">Only active pages are available to visitors.</p>
                </div>
                <label class="ui-setting-row border-t border-zinc-100 pt-4">
                    <span>Show in navigation<span class="ui-hint">Include this page in the site menu.</span></span>
                    <input type="checkbox" id="page-nav" x-model="form.nav" role="switch" class="peer sr-only">
                    <span aria-hidden="true" class="ui-switch success"></span>
                </label>
            </div>
        </section>
    </fieldset>
    <div class="ui-editor-footer ui-toolbar">
        <p class="text-xs text-zinc-500">Changes take effect after saving.</p>
        <div class="ui-actions">
            <button type="button" x-on:click="closeEditForm()" :disabled="modalLoading" class="btn secondary">Cancel</button>
            <button type="submit" :disabled="modalLoading" class="btn primary">
                <span x-show="!modalLoading" x-text="editingPageId ? 'Save changes' : 'Create page'"></span>
                <span x-show="modalLoading">Saving…</span>
            </button>
        </div>
    </div>
</form>
