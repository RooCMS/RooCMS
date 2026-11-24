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
        <?php require __DIR__ . '/../../partials/acp-nav.php'; ?>

        <section class="acp-content">
            <header class="mb-8">
                <?php require __DIR__ . '/../../partials/acp-heading.php'; ?>
            </header>

            <div class="space-y-10" x-data="settingsManager()">

                <!-- Settings Sections -->
                <div class="acp-settings-grid">
                <template x-for="(groupSettings, groupName) in settings" :key="groupName">
                    <section class="break-inside-avoid mb-8">
                        <h2 class="ui-section-title" x-text="getGroupTitle(groupName)"></h2>
                        <div class="ui-panel">
                            <form class="space-y-4" :data-group="groupName">
                                <template x-for="(value, key) in groupSettings" :key="key">
                                    <div class="field-container ui-field-group">
                                        <label class="ui-label" :for="getFieldId(groupName, key)">
                                            <div class="flex items-center gap-2">
                                                <span x-text="getFieldMeta(key, 'title') || key"></span>
                                                <span x-show="getFieldMeta(key, 'is_required')" class="badge danger compact">Required</span>
                                            </div>
                                        </label>

                                        <!-- Dynamic field rendering -->
                                        <div>
                                            <template x-if="getFieldType(key) === 'boolean'">
                                                <label class="flex cursor-pointer items-center justify-between gap-4">
                                                    <span class="text-sm text-zinc-800">Enabled</span>
                                                    <input type="checkbox"
                                                           :id="getFieldId(groupName, key)"
                                                           :name="key"
                                                           x-model="settings[groupName][key]"
                                                           class="peer sr-only">
                                                    <span class="ui-switch"></span>
                                                </label>
                                            </template>

                                            <template x-if="getFieldType(key) === 'select'">
                                                <select :id="getFieldId(groupName, key)"
                                                        :name="key"
                                                        x-model="settings[groupName][key]"
                                                        class="ui-control select-custom">
                                                    <template x-for="(label, optionValue) in getFieldOptions(key)" :key="optionValue">
                                                        <option :value="optionValue" x-text="label"></option>
                                                    </template>
                                                </select>
                                            </template>

                                            <template x-if="getFieldType(key) === 'text'">
                                                <textarea :id="getFieldId(groupName, key)"
                                                          :name="key"
                                                          :maxlength="getFieldMeta(key, 'max_length')"
                                                          rows="3"
                                                          x-model="settings[groupName][key]"
                                                          class="ui-control"></textarea>
                                            </template>

                                            <template x-if="getFieldType(key) === 'image' || getFieldType(key) === 'file'">
                                                <div class="space-y-2">
                                                    <input type="file"
                                                           :id="getFieldId(groupName, key)"
                                                           :name="key + '_file'"
                                                           :accept="getFieldType(key) === 'image' ? 'image/*' : '*/*'"
                                                           class="ui-file">
                                                    <input type="text"
                                                           :name="key"
                                                           x-model="settings[groupName][key]"
                                                           placeholder="Or enter file path/URL"
                                                           class="ui-control">
                                                </div>
                                            </template>

                                            <template x-if="getFieldType(key) !== 'boolean' && getFieldType(key) !== 'select' && getFieldType(key) !== 'text' && getFieldType(key) !== 'image' && getFieldType(key) !== 'file'">
                                                <input :type="getInputType(key)"
                                                       :id="getFieldId(groupName, key)"
                                                       :name="key"
                                                       :maxlength="getFieldType(key) === 'integer' ? null : getFieldMeta(key, 'max_length')"
                                                       :min="getFieldType(key) === 'integer' ? '0' : null"
                                                       :step="getFieldType(key) === 'integer' ? '1' : null"
                                                       x-model="settings[groupName][key]"
                                                       class="ui-control"
                                                       :class="{ 'color': getFieldType(key) === 'color' }">
                                            </template>
                                        </div>

                                        <!-- Field description -->
                                        <p x-show="getFieldMeta(key, 'description')" class="ui-hint" x-text="getFieldMeta(key, 'description')"></p>
                                    </div>
                                </template>
                            </form>
                        </div>
                    </section>
                </template>
                </div>


                <!-- Messages container -->
                <div class="messages-container">
                    <div class="form-success alert success" x-show="successMessage" x-text="successMessage" x-transition></div>
                    <div class="form-error alert danger" x-show="errorMessage" x-text="errorMessage" x-transition></div>
                </div>

                <!-- Loading state -->
                <div x-show="loading" class="ui-empty">
                    <div class="inline-flex items-center gap-3 text-sm text-zinc-500">
                        <?php require __DIR__ . '/../../partials/ui-spinner.php'; ?>
                        Loading settings...
                    </div>
                </div>


                <!-- Actions Section -->
                <section class="acp-content">
                    <div class="ui-panel">
                        <div class="ui-toolbar">
                            <p class="text-sm text-zinc-600">Save all settings or reset to default values</p>
                            <div class="ui-actions">
                                <button type="button"
                                        @click="resetSettings()"
                                        :disabled="loading"
                                        class="btn secondary">
                                    <span x-show="!loading">Reset</span>
                                    <span x-show="loading">Reset...</span>
                                </button>
                                <button type="button"
                                        @click="saveSettings()"
                                        :disabled="loading"
                                        class="btn primary">
                                    <svg x-show="!loading" class="w-4 h-4 mr-2 align-middle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span x-show="!loading">Save settings</span>
                                    <span x-show="loading">Saving...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../../layouts/base.php';
