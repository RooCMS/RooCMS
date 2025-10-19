<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

// Get user ID from query parameter
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


if(!$user_id || $user_id <= 0) {
    getout(404, 'Invalid user ID');
}

$page_title = 'Edit User — Admin Control Panel — RooCMS';
$page_description = 'Edit user for RooCMS ACP';

$theme_name = basename(dirname(dirname(__DIR__)));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [
	$theme_base.'/assets/js/app/acp.js',
    $theme_base.'/assets/js/app/acp-access.js',
    $theme_base.'/assets/js/pages/acp-user-edit.js'
];

ob_start();
?>

<div class="py-10">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-[260px_1fr] px-4 sm:px-6 lg:px-8 space-y-8">
        <?php require __DIR__ . '/../../layouts/acp-nav.php'; ?>

        <section x-data="userEditManager">
            <header class="mb-8">
                <nav class="mb-3 text-sm text-zinc-500" aria-label="Breadcrumbs">
                    <ol class="flex items-center gap-2">
                        <li><a href="/" class="hover:text-zinc-700">Home</a></li>
                        <li aria-hidden="true" class="text-zinc-400">/</li>
                        <li><a href="/acp" class="hover:text-zinc-700">ACP</a></li>
                        <li aria-hidden="true" class="text-zinc-400">/</li>
                        <li><a href="/acp/users" class="hover:text-zinc-700">Users</a></li>
                        <li aria-hidden="true" class="text-zinc-400">/</li>
                        <li><span class="text-zinc-700">Edit User #<?php render_html((string)$user_id); ?></span></li>
                    </ol>
                </nav>
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">
                            <span x-show="!loading && user && user.login" x-text="'Edit User: ' + user.login"></span>
                            <span x-show="loading">Loading...</span>
                        </h1>
                        <p class="mt-2 text-sm text-zinc-600">Manage user account and profile settings</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="/acp/users" class="inline-flex items-center px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 rounded-lg hover:bg-zinc-50">
                            Back to Users
                        </a>
                    </div>
                </div>
            </header>

            <!-- Loading State -->
            <div x-show="loading" class="flex items-center justify-center py-12">
                <div class="flex items-center text-sm text-zinc-500">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Loading user data...
                </div>
            </div>

            <!-- User Not Found -->
            <div x-show="!loading && (!user || !user.login)" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-zinc-900">User not found</h3>
                <p class="mt-1 text-sm text-zinc-500">The requested user does not exist or has been deleted.</p>
                <div class="mt-6">
                    <a href="/acp/users" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-sky-600 hover:bg-sky-700">
                        Back to Users
                    </a>
                </div>
            </div>

            <!-- Edit Form -->
            <div x-show="!loading && user && user.login">
                <form x-on:submit.prevent="saveUser($event)" novalidate class="space-y-8">
                    <!-- Account Information -->
                    <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-6 shadow-sm backdrop-blur">
                    <h2 class="text-lg font-semibold text-zinc-900 mb-4">Account Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Login (read-only) -->
                        <div>
                            <label for="login" class="block text-sm font-medium text-zinc-700 mb-2">Login</label>
                            <input type="text" id="login" x-bind:value="user && user.login ? user.login : ''" readonly disabled autocomplete="username"
                                   class="block w-full rounded-lg border border-zinc-300 bg-zinc-50 px-3 py-2 text-sm text-zinc-500 cursor-not-allowed">
                            <p class="mt-1 text-xs text-zinc-500">Login cannot be changed</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-zinc-700 mb-2">Email <span class="badge danger">Required</span></label>
                            <input type="email" id="email" x-model="form.email" required
                                   class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                            <div id="email_error" class="mt-1 text-sm text-red-600 hidden"></div>
                        </div>

                        <!-- Role -->
                        <div>
                            <label for="role" class="block text-sm font-medium text-zinc-700 mb-2">Role <span class="badge danger">Required</span></label>
                            <select id="role" x-model="form.role" required
                                    class="select-custom block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                                <template x-for="role in availableRoles" x-bind:key="role.value">
                                    <option x-bind:value="role.value" x-text="role.label + ' - ' + role.description"></option>
                                </template>
                            </select>
                            <div id="role_error" class="mt-1 text-sm text-red-600 hidden"></div>
                        </div>

                        <!-- Status Toggles -->
                        <div class="space-y-4">
                            <label class="flex cursor-pointer items-center justify-between gap-4">
                                <span class="text-sm text-zinc-800">Account Active</span>
                                <input type="checkbox" id="is_active" x-model="form.is_active" class="peer sr-only">
                                <span class="relative inline-block h-6 w-11 rounded-full bg-zinc-300 transition peer-checked:bg-emerald-600 after:absolute after:left-0.5 after:top-1/2 after:-translate-y-1/2 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
                            </label>
                        </div>
                        <div class="space-y-4">
                            <label class="flex cursor-pointer items-center justify-between gap-4">
                                <span class="text-sm text-zinc-800">Email Verified</span>
                                <input type="checkbox" id="is_verified" x-model="form.is_verified" class="peer sr-only">
                                <span class="relative inline-block h-6 w-11 rounded-full bg-zinc-300 transition peer-checked:bg-emerald-600 after:absolute after:left-0.5 after:top-1/2 after:-translate-y-1/2 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Created/Updated Info -->
                    <div x-show="user && user.created_at" class="mt-6 pt-6 border-t border-zinc-200">
                        <dl class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <dt class="font-medium text-zinc-500">Created</dt>
                                <dd class="mt-1 text-zinc-900" x-text="user && user.created_at ? formatDateTime(user.created_at) : ''"></dd>
                            </div>
                            <div>
                                <dt class="font-medium text-zinc-500">Updated</dt>
                                <dd class="mt-1 text-zinc-900" x-text="user && user.updated_at ? formatDateTime(user.updated_at) : ''"></dd>
                            </div>
                            <div>
                                <dt class="font-medium text-zinc-500">Last Activity</dt>
                                <dd class="mt-1 text-zinc-900" x-text="user && user.last_activity ? formatRelativeTime(user.last_activity) : ''"></dd>
                            </div>
                        </dl>
                    </div>
                </div>

                    <!-- Profile Information -->
                    <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-6 shadow-sm backdrop-blur">
                    <h2 class="text-lg font-semibold text-zinc-900 mb-4">Profile Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nickname -->
                        <div>
                            <label for="nickname" class="block text-sm font-medium text-zinc-700 mb-2">Nickname</label>
                            <input type="text" id="nickname" x-model="form.nickname"
                                   class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                        </div>

                        <!-- Gender -->
                        <div>
                            <label for="gender" class="block text-sm font-medium text-zinc-700 mb-2">Gender</label>
                            <select id="gender" x-model="form.gender"
                                    class="select-custom block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                                <option value="">Not specified</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-zinc-700 mb-2">First Name</label>
                            <input type="text" id="first_name" x-model="form.first_name"
                                   class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-zinc-700 mb-2">Last Name</label>
                            <input type="text" id="last_name" x-model="form.last_name"
                                   class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                        </div>

                        <!-- Birthday -->
                        <div>
                            <label for="birthday" class="block text-sm font-medium text-zinc-700 mb-2">Birthday</label>
                            <input type="date" id="birthday" x-model="form.birthday"
                                   class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                        </div>

                        <!-- Website -->
                        <div>
                            <label for="website" class="block text-sm font-medium text-zinc-700 mb-2">Website</label>
                            <input type="url" id="website" x-model="form.website" placeholder="https://example.com"
                                   class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                        </div>

                        <!-- Bio -->
                        <div class="md:col-span-2">
                            <label for="bio" class="block text-sm font-medium text-zinc-700 mb-2">Bio</label>
                            <textarea id="bio" x-model="form.bio" rows="3"
                                      class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"></textarea>
                        </div>

                        <!-- Profile Public -->
                        <div class="md:col-span-2">
                            <label class="flex cursor-pointer items-center justify-between gap-4">
                                <span class="text-sm text-zinc-800">Profile is Public</span>
                                <input type="checkbox" id="is_public" x-model="form.is_public" class="peer sr-only">
                                <span class="relative inline-block h-6 w-11 rounded-full bg-zinc-300 transition peer-checked:bg-purple-600 after:absolute after:left-0.5 after:top-1/2 after:-translate-y-1/2 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
                            </label>
                        </div>
                    </div>
                </div>

                    <!-- Ban Management -->
                    <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-6 shadow-sm backdrop-blur">
                    <h2 class="text-lg font-semibold text-zinc-900 mb-4">Ban Management</h2>
                    
                    <div class="space-y-4">
                        <label class="flex cursor-pointer items-center justify-between gap-4">
                            <span class="text-sm font-medium text-zinc-900">User is Banned</span>
                            <input type="checkbox" id="is_banned" x-model="form.is_banned" class="peer sr-only">
                            <span class="relative inline-block h-6 w-11 rounded-full bg-zinc-300 transition peer-checked:bg-red-600 after:absolute after:left-0.5 after:top-1/2 after:-translate-y-1/2 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
                        </label>

                        <div x-show="form.is_banned" class="space-y-4 pl-6 border-l-2 border-rose-300">
                            <!-- Ban Reason -->
                            <div>
                                <label for="ban_reason" class="block text-sm font-medium text-zinc-700 mb-2">Ban Reason</label>
                                <textarea id="ban_reason" x-model="form.ban_reason" rows="2"
                                          class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"></textarea>
                            </div>

                            <!-- Ban Expiration -->
                            <div>
                                <label for="ban_expired" class="block text-sm font-medium text-zinc-700 mb-2">Ban Expires</label>
                                <input type="datetime-local" id="ban_expired" x-model="form.ban_expired_date"
                                       class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                                <p class="mt-1 text-xs text-zinc-500">Leave empty for permanent ban</p>
                            </div>
                        </div>
                    </div>
                </div>

                    <!-- Password Change -->
                    <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-6 shadow-sm backdrop-blur">
                        <h2 class="text-lg font-semibold text-zinc-900 mb-4">Change Password</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="new_password" class="block text-sm font-medium text-zinc-700 mb-2">New Password</label>
                                <input type="password" id="new_password" x-model="passwordForm.new_password" autocomplete="new-password"
                                       class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                                <p class="mt-1 text-xs text-zinc-500">Leave empty to keep current password</p>
                            </div>

                            <div>
                                <label for="confirm_password" class="block text-sm font-medium text-zinc-700 mb-2">Confirm Password</label>
                                <input type="password" id="confirm_password" x-model="passwordForm.confirm_password" autocomplete="new-password"
                                       class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500">
                            </div>

                            <button x-on:click="changePassword()" type="button" x-bind:disabled="saving || !passwordForm.new_password"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-amber-600 hover:bg-amber-700 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                <svg x-show="saving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Change Password
                            </button>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="p-6 rounded-xl border border-zinc-200/80 bg-zinc-50/50 backdrop-blur">
                        <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
                            <div class="flex items-center gap-4">
                                <!-- Loading Spinner -->
                                <div x-show="saving" x-cloak class="flex items-center gap-2 text-sky-600">
                                    <svg class="animate-spin h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Saving...</span>
                                </div>

                                <!-- Success Message -->
                                <div x-show="successMessage" x-cloak class="flex items-center gap-2 text-green-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-sm font-medium" x-text="successMessage"></span>
                                </div>

                                <!-- Error Message -->
                                <div x-show="errorMessage" x-cloak class="flex items-center gap-2 text-red-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span class="text-sm font-medium" x-text="errorMessage"></span>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <button x-on:click="deleteUser()" type="button" x-bind:disabled="saving"
                                        class="inline-flex items-center px-4 py-2 border border-rose-300 text-sm font-medium rounded-lg text-rose-700 bg-white hover:bg-rose-50 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 cursor-pointer">
                                    Delete User
                                </button>

                                <button x-on:click="resetForm()" type="button" x-bind:disabled="saving"
                                        class="inline-flex items-center px-4 py-2 border border-zinc-300 text-sm font-medium rounded-lg text-zinc-700 bg-white hover:bg-zinc-50 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                                    Reset
                                </button>

                                <button type="submit" x-bind:disabled="saving"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-sky-600 hover:bg-sky-700 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 cursor-pointer">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../../layouts/base.php';

