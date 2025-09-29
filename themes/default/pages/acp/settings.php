<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

$page_title = 'Settings — RooCMS';
$page_description = 'System Settings for RooCMS';

$theme_name = basename(dirname(dirname(__DIR__)));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [
	$theme_base.'/assets/js/app/acp.js',
    $theme_base.'/assets/js/app/acp-access.js',
    $theme_base.'/assets/js/pages/acp-settings.js'
];

ob_start();
?>

<div class="py-10">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-[260px_1fr] px-4 sm:px-6 lg:px-8 space-y-8">
        <?php require __DIR__ . '/../../layouts/acp-nav.php'; ?>

        <section>
            <header class="mb-8">
                <nav class="mb-3 text-sm text-zinc-500" aria-label="Breadcrumbs">
                    <ol class="flex items-center gap-2">
                        <li><a href="/" class="hover:text-zinc-700">Home</a></li>
                        <li aria-hidden="true" class="text-zinc-400">/</li>
                        <li><a href="/acp" class="hover:text-zinc-700">ACP</a></li>
                        <li aria-hidden="true" class="text-zinc-400">/</li>
                        <li><span class="text-zinc-700">Settings</span></li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Settings</h1>
                <p class="mt-2 text-sm text-zinc-600">Manage the configuration of RooCMS</p>
            </header>

            <div class="space-y-10" x-data="settingsManager()">

                <!-- Settings Sections -->
                <template x-for="(groupSettings, groupName) in settings" :key="groupName">
                    <section>
                        <h2 class="mb-4 text-base font-semibold text-zinc-900" x-text="getGroupTitle(groupName)"></h2>
                        <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
                            <form class="space-y-4" :data-group="groupName">
                                <template x-for="(value, key) in groupSettings" :key="key">
                                    <div class="field-container">
                                        <label class="mb-1 block text-sm font-medium text-zinc-800" :for="getFieldId(groupName, key)">
                                            <div class="flex items-center gap-2">
                                                <span x-text="getFieldMeta(key, 'title') || key"></span>
                                                <span x-show="getFieldMeta(key, 'is_required')" class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-red-100 text-red-800 border border-red-200">Required</span>
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
                                                    <span class="relative inline-block h-6 w-11 rounded-full bg-zinc-300 transition peer-checked:bg-sky-900 after:absolute after:left-0.5 after:top-1/2 after:-translate-y-1/2 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
                                                </label>
                                            </template>

                                            <template x-if="getFieldType(key) === 'select'">
                                                <select :id="getFieldId(groupName, key)"
                                                        :name="key"
                                                        x-model="settings[groupName][key]"
                                                        class="select-custom block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none">
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
                                                          class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-sky-500 focus:outline-none"></textarea>
                                            </template>

                                            <template x-if="getFieldType(key) === 'image' || getFieldType(key) === 'file'">
                                                <div class="space-y-2">
                                                    <input type="file"
                                                           :id="getFieldId(groupName, key)"
                                                           :name="key + '_file'"
                                                           :accept="getFieldType(key) === 'image' ? 'image/*' : '*/*'"
                                                           class="block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-zinc-50 file:text-zinc-700 hover:file:bg-zinc-100">
                                                    <input type="text"
                                                           :name="key"
                                                           x-model="settings[groupName][key]"
                                                           placeholder="Or enter file path/URL"
                                                           class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-sky-500 focus:outline-none">
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
                                                       :class="getFieldType(key) === 'color' ? 
                                                           'block w-20 h-10 rounded-lg border border-zinc-300 bg-white cursor-pointer focus:border-sky-500 focus:outline-none' :
                                                           'block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-sky-500 focus:outline-none'">
                                            </template>
                                        </div>

                                        <!-- Field description -->
                                        <p x-show="getFieldMeta(key, 'description')" class="mt-1 text-xs text-zinc-500" x-text="getFieldMeta(key, 'description')"></p>
                                    </div>
                                </template>
                            </form>
                        </div>
                    </section>
                </template>


                <!-- Messages container -->
                <div class="messages-container">
                    <div class="form-success p-4 rounded-lg bg-green-50 border border-green-200 text-green-800" x-show="successMessage" x-text="successMessage" x-transition></div>
                    <div class="form-error p-4 rounded-lg bg-red-50 border border-red-200 text-red-800" x-show="errorMessage" x-text="errorMessage" x-transition></div>
                </div>

                <!-- Loading state -->
                <div x-show="loading" class="text-center py-8">
                    <div class="inline-flex items-center text-sm text-zinc-500">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading settings...
                    </div>
                </div>


                <!-- Actions Section -->
                <section>
                    <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-zinc-600">Save all settings or reset to default values</p>
                            <div class="flex gap-2">
                                <button type="button"
                                        @click="resetSettings()"
                                        :disabled="loading"
                                        class="cursor-pointer inline-flex items-center justify-center rounded-lg border border-zinc-300 bg-white px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 disabled:opacity-50 transition-colors">
                                    <span x-show="!loading">Reset</span>
                                    <span x-show="loading">Reset...</span>
                                </button>
                                <button type="button"
                                        @click="saveSettings()"
                                        :disabled="loading"
                                        class="cursor-pointer inline-flex items-center justify-center rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 disabled:opacity-50 transition-colors">
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
