<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

$page_title = 'Settings — Admin Control Panel — RooCMS';
$page_description = 'System Settings for RooCMS';
$theme_name = basename(dirname(__DIR__, 2));
$theme_base = '/themes/'.$theme_name;
$page_scripts = [
    $theme_base.'/assets/js/app/acp.js',
    $theme_base.'/assets/js/app/acp-access.js',
    $theme_base.'/assets/js/pages/acp-settings.js'
];
$acp_heading = 'Settings';
$acp_intro = 'Manage the configuration of RooCMS';
$acp_breadcrumb = 'Settings';
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

            <form x-data="settingsManager()" x-on:submit.prevent="saveSettings()" novalidate class="space-y-5">
                <div x-show="loading && !active_group" class="ui-panel ui-empty" role="status">
                    <span class="inline-flex items-center gap-3 text-sm text-zinc-500">
                        <?php require __DIR__ . '/../../partials/ui-spinner.php'; ?> Loading settings…
                    </span>
                </div>

                <div x-show="active_group" x-cloak class="ui-settings-layout">
                    <nav class="ui-settings-nav" aria-label="Settings sections">
                        <p class="mb-3 px-3 text-xs font-medium uppercase tracking-wider text-zinc-400">Configuration</p>
                        <div class="grid grid-cols-2 gap-1 xl:grid-cols-1">
                            <template x-for="(groupSettings, groupName) in settings" :key="groupName">
                                <button type="button" class="ui-settings-link" :class="{ 'is-active': active_group === groupName }" :aria-pressed="active_group === groupName" :aria-controls="'settings-' + groupName" x-on:click="active_group = groupName">
                                    <svg aria-hidden="true" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="get_group_meta(groupName).icon"></path></svg>
                                    <span x-text="get_group_meta(groupName).title"></span>
                                </button>
                            </template>
                        </div>
                    </nav>

                    <div class="min-w-0">
                        <template x-for="(groupSettings, groupName) in settings" :key="groupName">
                            <section class="ui-panel flush" :id="'settings-' + groupName" :hidden="active_group !== groupName" :aria-labelledby="'settings-heading-' + groupName">
                                <header class="ui-panel-header">
                                    <h2 class="text-base font-semibold text-zinc-900" :id="'settings-heading-' + groupName" x-text="get_group_meta(groupName).title"></h2>
                                    <p class="ui-hint" x-text="get_group_meta(groupName).description"></p>
                                </header>
                                <fieldset :disabled="loading" class="min-w-0 divide-y divide-zinc-100">
                                    <legend class="sr-only" x-text="get_group_meta(groupName).title"></legend>
                                    <template x-for="(value, key) in groupSettings" :key="key">
                                        <div class="ui-settings-field">
                                            <div class="min-w-0">
                                                <label class="ui-label" :for="getFieldId(groupName, key)">
                                                    <span x-text="getFieldMeta(key, 'title') || key"></span>
                                                    <span x-show="getFieldMeta(key, 'is_required')" class="text-rose-500" aria-hidden="true">*</span>
                                                </label>
                                                <p x-show="getFieldMeta(key, 'description')" :id="getFieldId(groupName, key) + '-help'" class="ui-hint" x-text="getFieldMeta(key, 'description')"></p>
                                            </div>
                                            <div class="field-container min-w-0">
                                                <template x-if="getFieldType(key) === 'boolean'">
                                                    <label class="ui-setting-row justify-start">
                                                        <input type="checkbox" role="switch" :id="getFieldId(groupName, key)" :name="key" :aria-describedby="getFieldId(groupName, key) + '-help'" x-model="settings[groupName][key]" class="peer sr-only">
                                                        <span aria-hidden="true" class="ui-switch"></span>
                                                        <span class="text-sm text-zinc-500" x-text="settings[groupName][key] ? 'Enabled' : 'Disabled'"></span>
                                                    </label>
                                                </template>
                                                <template x-if="getFieldType(key) === 'select'">
                                                    <select :id="getFieldId(groupName, key)" :name="key" :aria-describedby="getFieldId(groupName, key) + '-help'" :aria-required="getFieldMeta(key, 'is_required')" x-model="settings[groupName][key]" class="ui-control select-custom">
                                                        <template x-for="(label, optionValue) in getFieldOptions(key)" :key="optionValue"><option :value="optionValue" x-text="label"></option></template>
                                                    </select>
                                                </template>
                                                <template x-if="getFieldType(key) === 'text'">
                                                    <textarea :id="getFieldId(groupName, key)" :name="key" :aria-describedby="getFieldId(groupName, key) + '-help'" :aria-required="getFieldMeta(key, 'is_required')" :maxlength="getFieldMeta(key, 'max_length')" rows="3" x-model="settings[groupName][key]" class="ui-control"></textarea>
                                                </template>
                                                <template x-if="getFieldType(key) === 'image' || getFieldType(key) === 'file'">
                                                    <div class="space-y-3">
                                                        <input type="file" :id="getFieldId(groupName, key)" :name="key + '_file'" :aria-describedby="getFieldId(groupName, key) + '-help'" :accept="getFieldType(key) === 'image' ? 'image/*' : '*/*'" class="ui-file">
                                                        <input type="text" :name="key" :aria-label="getFieldMeta(key, 'title') + ' path or URL'" x-model="settings[groupName][key]" placeholder="Or enter file path/URL" class="ui-control">
                                                    </div>
                                                </template>
                                                <template x-if="getFieldType(key) !== 'boolean' && getFieldType(key) !== 'select' && getFieldType(key) !== 'text' && getFieldType(key) !== 'image' && getFieldType(key) !== 'file'">
                                                    <input :type="getInputType(key)" :id="getFieldId(groupName, key)" :name="key" :aria-describedby="getFieldId(groupName, key) + '-help'" :aria-required="getFieldMeta(key, 'is_required')" :maxlength="getFieldType(key) === 'integer' ? null : getFieldMeta(key, 'max_length')" :min="getFieldType(key) === 'integer' ? '0' : null" :step="getFieldType(key) === 'integer' ? '1' : null" x-model="settings[groupName][key]" class="ui-control" :class="{ 'color': getFieldType(key) === 'color' }">
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </fieldset>
                            </section>
                        </template>
                    </div>
                </div>

                <div class="ui-settings-actions sticky bottom-4" x-show="active_group" x-cloak>
                    <div class="ui-toolbar">
                        <p class="text-xs text-zinc-500">Changes in all sections are saved together.</p>
                        <div class="ui-actions">
                            <button type="button" x-on:click="resetSettings()" :disabled="loading || !active_group" class="btn secondary">Reset to defaults</button>
                            <button type="submit" :disabled="loading || !active_group" :aria-busy="loading" class="btn primary">
                                <span x-show="loading" aria-hidden="true"><?php require __DIR__ . '/../../partials/ui-spinner.php'; ?></span>
                                Save settings
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
</div>
<?php $page_content = ob_get_clean();
require __DIR__ . '/../../layouts/base.php';
