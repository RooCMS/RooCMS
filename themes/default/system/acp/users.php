<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

$page_title = 'Users — Admin Control Panel — RooCMS';
$page_description = 'Users for RooCMS ACP';

$theme_name = basename(dirname(__DIR__, 2));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [
	$theme_base.'/assets/js/app/acp.js',
    $theme_base.'/assets/js/app/acp-access.js',
    $theme_base.'/assets/js/pages/acp-users.js'
];

$acp_heading = 'Users';
$acp_intro = 'Manage users and their permissions';
$acp_breadcrumb = 'Users';
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

            <div class="space-y-5" x-data="usersManager()" x-cloak>

                <div role="status" class="alert success" x-show="successMessage" x-text="successMessage"></div>
                <div role="alert" class="alert danger" x-show="errorMessage" x-text="errorMessage"></div>

                <!-- Search and filters -->
                <div class="ui-panel">
                    <div class="ui-filter-grid">
                        <div class="ui-filter-search">
                            <label for="search" class="ui-label">Search users</label>
                            <div class="relative">
                                <input type="search" id="search" x-model="searchQuery"
                                       @input.debounce.300ms="searchUsers()" placeholder="Login, email or name"
                                       class="ui-control search">
                                <svg aria-hidden="true" class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="min-w-0">
                            <label for="role-filter" class="ui-label">Role</label>
                            <select id="role-filter" x-model="filters.role" @change="applyFilters()" class="ui-control select-custom">
                                <option value="">All roles</option>
                                <template x-for="role in availableRoles" :key="role.key"><option :value="role.key" x-text="role.name"></option></template>
                            </select>
                        </div>
                        <div class="min-w-0">
                            <label for="status-filter" class="ui-label">Status</label>
                            <select id="status-filter" x-model="filters.status" @change="applyFilters()" class="ui-control select-custom">
                                <option value="">All statuses</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="banned">Banned</option>
                            </select>
                        </div>
                        <button type="button" @click="clearFilters()" :disabled="!hasFilters" class="btn secondary">Reset filters</button>
                    </div>
                </div>

                <!-- Users List -->
                <div class="ui-panel flush overflow-hidden">
                    <div class="ui-panel-header ui-toolbar">
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-semibold text-zinc-900">Users</h2>
                            <span class="badge neutral" x-show="!loading" x-text="pagination.total"></span>
                        </div>
                        <p class="text-sm text-zinc-500" role="status" aria-live="polite">
                            <span x-show="loading">Loading users…</span>
                            <span x-show="!loading && pagination.total > 0">Showing <span x-text="getShowingFrom()"></span>–<span x-text="getShowingTo()"></span> of <span x-text="pagination.total"></span></span>
                        </p>
                    </div>
                    <div x-show="loading" class="ui-loading" role="status">
                        <?php require __DIR__ . '/../../partials/ui-spinner.php'; ?> Loading users…
                    </div>

                    <div x-show="!loading && users.length > 0">
                        <div class="ui-record-row ui-record-heading" aria-hidden="true">
                            <span>User</span><span>Role</span><span>Status</span><span>Last activity</span><span class="text-right">Actions</span>
                        </div>
                        <div class="ui-record-list" role="list" aria-label="Users">
                            <template x-for="user in users" :key="user.id">
                                <div role="listitem">
                                    <div class="ui-record-row" :class="{ 'is-open': editingUserId === user.id }">
                                        <div class="ui-record-cell ui-record-identity">
                                            <div class="shrink-0">
                                                <span x-show="!hasAvatar(user)" class="avatar small initials" x-text="getInitials(user)" aria-hidden="true"></span>
                                                <template x-if="hasAvatar(user)"><img :src="'/up/' + user.avatar" alt="" class="avatar small object-cover"></template>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-1.5">
                                                    <span class="font-semibold text-zinc-900" x-text="user.login"></span>
                                                    <span x-show="user.is_verified" class="text-emerald-600" title="Verified" aria-label="Verified">
                                                        <svg aria-hidden="true" class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                    </span>
                                                    <span x-show="isCurrentUser(user.id)" class="badge violet compact">You</span>
                                                </div>
                                                <p class="text-zinc-500" x-text="user.email"></p>
                                                <p class="ui-hint"><span x-text="'ID ' + user.id"></span><span x-show="user.nickname" x-text="' · @' + user.nickname"></span></p>
                                            </div>
                                        </div>
                                        <div class="ui-record-cell">
                                            <span class="ui-record-label">Role</span>
                                            <span class="badge" :class="getRoleBadgeClass(user.role)" x-text="getRoleLabel(user.role)"></span>
                                        </div>
                                        <div class="ui-record-cell">
                                            <span class="ui-record-label">Status</span>
                                            <span class="badge" :class="user.is_banned ? 'danger' : (user.is_active ? 'success' : 'neutral')" x-text="user.is_banned ? 'Banned' : (user.is_active ? 'Active' : 'Inactive')"></span>
                                            <div x-show="user.is_banned" class="mt-1 space-y-1 text-xs text-zinc-500">
                                                <p x-show="user.ban_reason" class="line-clamp-2" :title="user.ban_reason" x-text="user.ban_reason"></p>
                                                <p x-text="formatBanExpiry(user.ban_expired)"></p>
                                            </div>
                                        </div>
                                        <div class="ui-record-cell text-zinc-500">
                                            <span class="ui-record-label">Last activity</span>
                                            <span :title="formatDateTimeValue(user.last_activity)" x-text="formatLastActivity(user.last_activity)"></span>
                                        </div>
                                        <div class="ui-record-actions">
                                            <button type="button" @click="toggleEditUser(user)" :aria-expanded="editingUserId === user.id" :aria-controls="'user-editor-' + user.id" :aria-label="'Edit user ' + user.login" class="btn secondary small">
                                                <span x-text="editingUserId === user.id ? 'Close' : 'Edit'"></span>
                                            </button>
                                            <button type="button" x-show="!user.is_banned" @click="banUser(user)" :aria-label="'Ban user ' + user.login" class="btn outline danger small">Ban</button>
                                            <button type="button" x-show="user.is_banned" @click="unbanUser(user)" :aria-label="'Unban user ' + user.login" class="btn outline success small">Unban</button>
                                        </div>
                                    </div>

                                    <!-- Inline Edit Row -->
                                    <div x-show="editingUserId === user.id" x-transition x-cloak :id="'user-editor-' + user.id">
                                        <div class="ui-record-editor space-y-6">

                                            <!-- Loading State -->
                                            <div x-show="editLoading" class="flex items-center justify-center py-8">
                                                <div class="inline-flex items-center gap-3 text-sm text-zinc-500">
                                                    <?php require __DIR__ . '/../../partials/ui-spinner.php'; ?>
                                                    Loading user data...
                                                </div>
                                            </div>

                                            <!-- User Not Found -->
                                            <div x-show="!editLoading && (!editingUser || !editingUser.login)" class="ui-empty">
                                                <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                </svg>
                                                <h3 class="ui-empty-title">User not found</h3>
                                                <p class="mt-1 text-sm text-zinc-500">The requested user does not exist or has been deleted.</p>
                                            </div>

                                            <!-- Edit Form -->
                                            <div x-show="!editLoading && editingUser && editingUser.login">
                                                <form x-on:submit.prevent="saveEditedUser($event)" novalidate class="space-y-6">
                                                    <!-- Account Information -->
                                                    <div class="ui-panel spacious">
                                                        <h3 class="text-lg font-semibold text-zinc-900 mb-4">Account Information</h3>

                                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                                            <!-- Login (read-only) -->
                                                            <div>
                                                                <label :for="'login_for_' + user.id" class="ui-label">Login</label>
                                                                <input type="text" :id="'login_for_' + user.id" x-bind:value="editingUser && editingUser.login ? editingUser.login : ''" readonly disabled autocomplete="username"
                                                                        class="ui-control">
                                                                <p class="ui-hint">Login cannot be changed</p>
                                                            </div>

                                                            <!-- Email -->
                                                            <div>
                                                                <label :for="'email_for_' + user.id" class="ui-label">Email <span class="badge danger">Required</span></label>
                                                                <input type="email" :id="'email_for_' + user.id" x-model="editForm.email" required
                                                                        class="ui-control">
                                                            </div>

                                                            <!-- Role -->
                                                            <div>
                                                                <label :for="'role_for_' + user.id" class="ui-label">Role <span class="badge danger">Required</span></label>
                                                                <select :id="'role_for_' + user.id" x-model="editForm.role" required
                                                                        class="ui-control select-custom">
                                                                    <template x-for="role in availableRoles" :key="role.key">
                                                                        <option :value="role.key" x-text="role.name + ' - ' + role.description"></option>
                                                                    </template>
                                                                </select>
                                                            </div>

                                                            <!-- Status Toggles -->
                                                            <div>
                                                                <label class="flex cursor-pointer items-center justify-between gap-4">
                                                                    <span class="text-sm text-zinc-800">Account Active</span>
                                                                    <input type="checkbox" :id="'is_active_for_' + user.id" x-model="editForm.is_active" class="peer sr-only">
                                                                    <span class="ui-switch success"></span>
                                                                </label>
                                                            </div>
                                                            <div>
                                                                <label class="flex cursor-pointer items-center justify-between gap-4">
                                                                    <span class="text-sm text-zinc-800">Email Verified</span>
                                                                    <input type="checkbox" :id="'is_verified_for_' + user.id" x-model="editForm.is_verified" class="peer sr-only">
                                                                    <span class="ui-switch success"></span>
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <!-- Created/Updated Info -->
                                                        <div x-show="editingUser && editingUser.created_at" class="mt-6 pt-6 border-t border-zinc-200">
                                                            <dl class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                                                <div>
                                                                    <dt class="font-medium text-zinc-500">Created</dt>
                                                                    <dd class="mt-1 text-zinc-900" x-text="editingUser && editingUser.created_at ? formatDateTimeValue(editingUser.created_at) : ''"></dd>
                                                                </div>
                                                                <div>
                                                                    <dt class="font-medium text-zinc-500">Updated</dt>
                                                                    <dd class="mt-1 text-zinc-900" x-text="editingUser && editingUser.updated_at ? formatDateTimeValue(editingUser.updated_at) : ''"></dd>
                                                                </div>
                                                                <div>
                                                                    <dt class="font-medium text-zinc-500">Last Activity</dt>
                                                                    <dd class="mt-1 text-zinc-900" x-text="editingUser && editingUser.last_activity ? formatRelativeTimeValue(editingUser.last_activity) : ''"></dd>
                                                                </div>
                                                            </dl>
                                                        </div>
                                                    </div>

                                                    <!-- Avatar Management -->
                                                    <div class="ui-panel spacious" x-show="editingUser.avatar && editingUser.avatar !== 'null'">
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                            <div>
                                                                <h3 class="text-lg font-semibold text-zinc-900 mb-4">Avatar Management</h3>
                                                            </div>
                                                            <div class="flex items-center gap-3">
                                                                <div class="flex-shrink-0 h-16 w-16">
                                                                    <div x-show="!editingUser.avatar" class="h-16 w-16 rounded-full bg-zinc-300 flex items-center justify-center">
                                                                        <span class="text-lg font-medium text-zinc-700" x-text="editingUser && editingUser.login ? getInitials(editingUser) : '?'"></span>
                                                                    </div>
                                                                    <img x-show="editingUser.avatar && editingUser.avatar !== 'null'" :src="editingUser && editingUser.avatar ? '/up/' + editingUser.avatar : ''" :alt="editingUser && editingUser.login ? editingUser.login : 'User'" class="h-16 w-16 rounded-full object-cover">
                                                                </div>
                                                                <div class="flex flex-col gap-2">
                                                                    <button x-show="editingUser && editingUser.avatar && editingUser.avatar !== 'null'"
                                                                            @click="deleteUserAvatar()"
                                                                            x-bind:disabled="editSaving"
                                                                            type="button"
                                                                            class="btn outline danger small">
                                                                        <svg x-show="!editSaving" class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                        </svg>
                                                                        <svg x-show="editSaving" class="ui-spinner small mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                                        </svg>
                                                                        <span x-text="editSaving ? 'Deleting...' : 'Delete Avatar'"></span>
                                                                    </button>
                                                                    <p x-show="!editingUser.avatar || editingUser.avatar === 'null'" class="text-xs text-zinc-500">No avatar uploaded</p>
                                                                    <p x-show="editingUser && editingUser.avatar && editingUser.avatar !== 'null'" class="text-xs text-zinc-500">Remove user's avatar</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Profile Information -->
                                                    <div class="ui-panel spacious">
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                            <div>
                                                                <h3 class="text-lg font-semibold text-zinc-900 mb-4">Profile Information</h3>
                                                            </div>

                                                            <div>
                                                                <label class="flex cursor-pointer items-center justify-between gap-4">
                                                                    <span class="text-sm text-zinc-800">Profile is Public</span>
                                                                    <input type="checkbox" :id="'is_public_for_' + user.id" x-model="editForm.is_public" class="peer sr-only">
                                                                    <span class="ui-switch"></span>
                                                                </label>
                                                            </div>

                                                            <div>
                                                                <label :for="'nickname_for_' + user.id" class="ui-label">Nickname</label>
                                                                <input type="text" :id="'nickname_for_' + user.id" x-model="editForm.nickname"
                                                                        class="ui-control">
                                                            </div>

                                                            <div>
                                                                <label :for="'gender_for_' + user.id" class="ui-label">Gender</label>
                                                                <select :id="'gender_for_' + user.id" x-model="editForm.gender"
                                                                        class="ui-control select-custom">
                                                                    <option value="">Not specified</option>
                                                                    <option value="male">Male</option>
                                                                    <option value="female">Female</option>
                                                                    <option value="other">Other</option>
                                                                </select>
                                                            </div>

                                                            <div>
                                                                <label :for="'first_name_for_' + user.id" class="ui-label">First Name</label>
                                                                <input type="text" :id="'first_name_for_' + user.id" x-model="editForm.first_name"
                                                                        class="ui-control">
                                                            </div>

                                                            <div>
                                                                <label :for="'last_name_for_' + user.id" class="ui-label">Last Name</label>
                                                                <input type="text" :id="'last_name_for_' + user.id" x-model="editForm.last_name"
                                                                        class="ui-control">
                                                            </div>

                                                            <div>
                                                                <label :for="'birthday_for_' + user.id" class="ui-label">Birthday</label>
                                                                <input type="date" :id="'birthday_for_' + user.id" x-model="editForm.birthday"
                                                                        class="ui-control">
                                                            </div>

                                                            <div>
                                                                <label :for="'website_for_' + user.id" class="ui-label">Website</label>
                                                                <input type="url" :id="'website_for_' + user.id" x-model="editForm.website" placeholder="https://example.com"
                                                                        class="ui-control">
                                                            </div>

                                                            <div class="md:col-span-2">
                                                                <label :for="'bio_for_' + user.id" class="ui-label">Bio</label>
                                                                <textarea :id="'bio_for_' + user.id" x-model="editForm.bio" rows="3"
                                                                            class="ui-control"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Ban Management -->
                                                    <div class="ui-panel spacious">
                                                        <h3 class="text-lg font-semibold text-zinc-900 mb-4">Ban Management</h3>

                                                        <div class="space-y-4">
                                                            <label class="flex cursor-pointer items-center justify-between gap-4">
                                                                <span class="text-sm font-medium text-zinc-900">User is Banned</span>
                                                                <input type="checkbox" :id="'is_banned_for_' + user.id" x-model="editForm.is_banned" class="peer sr-only">
                                                                <span class="ui-switch"></span>
                                                            </label>

                                                            <div x-show="editForm.is_banned" class="space-y-4 pl-6 border-l-2 border-rose-300" x-cloak>
                                                                <div>
                                                                    <label :for="'ban_reason_for_' + user.id" class="ui-label">Ban Reason</label>
                                                                    <textarea :id="'ban_reason_for_' + user.id" x-model="editForm.ban_reason" rows="2"
                                                                                class="ui-control"></textarea>
                                                                </div>

                                                                <div>
                                                                    <label :for="'ban_expired_for_' + user.id" class="ui-label">Ban Expires</label>
                                                                    <input type="datetime-local" :id="'ban_expired_for_' + user.id" x-model="editForm.ban_expired_date"
                                                                            class="ui-control">
                                                                    <p class="ui-hint">Leave empty for permanent ban</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Password Change -->
                                                    <div class="ui-panel spacious">
                                                        <h3 class="text-lg font-semibold text-zinc-900 mb-4">Change Password</h3>

                                                        <div class="space-y-4">
                                                            <div>
                                                                <label :for="'new_password_for_' + user.id" class="ui-label">New Password</label>
                                                                <input type="password" :id="'new_password_for_' + user.id" x-model="editPasswordForm.new_password" autocomplete="new-password"
                                                                        class="ui-control">
                                                                <p class="ui-hint">Leave empty to keep current password</p>
                                                            </div>

                                                            <div>
                                                                <label :for="'confirm_password_for_' + user.id" class="ui-label">Confirm Password</label>
                                                                <input type="password" :id="'confirm_password_for_' + user.id" x-model="editPasswordForm.confirm_password" autocomplete="new-password"
                                                                        class="ui-control">
                                                            </div>

                                                            <button x-on:click="changeUserPassword()" type="button" x-bind:disabled="editSaving || !editPasswordForm.new_password"
                                                                    class="btn secondary">
                                                                <svg x-show="editSaving" class="ui-spinner small mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                                Change Password
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Actions -->
                                                    <div class="p-6 rounded-xl border border-zinc-200/80 bg-zinc-50/50 backdrop-blur">
                                                        <div class="ui-toolbar">
                                                            <div class="flex items-center gap-4">
                                                                <div x-show="editSaving" x-cloak class="flex items-center gap-2 text-sky-600">
                                                                    <?php require __DIR__ . '/../../partials/ui-spinner.php'; ?>
                                                                    <span class="text-sm font-medium">Saving...</span>
                                                                </div>
                                                                <div class="text-green-800" x-show="editSuccessMessage" x-text="editSuccessMessage" x-transition></div>
                                                                <div class="text-red-800" x-show="editErrorMessage" x-text="editErrorMessage" x-transition></div>
                                                            </div>

                                                            <div class="ui-actions">
                                                                <button x-on:click="deleteUserAccount()" type="button" x-bind:disabled="editSaving"
                                                                        class="btn outline danger">
                                                                    Delete User
                                                                </button>

                                                                <button x-on:click="resetEditForm()" type="button" x-bind:disabled="editSaving"
                                                                        class="btn secondary">
                                                                    Reset
                                                                </button>

                                                                <button type="submit" x-bind:disabled="editSaving"
                                                                        class="btn primary">
                                                                    Save Changes
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>


                    <!-- Empty State -->
                    <div x-show="!loading && !errorMessage && users.length === 0" class="ui-empty">
                        <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="ui-empty-title">No users found</h3>
                        <p class="mt-1 text-sm text-zinc-500" x-text="hasFilters ? 'Try a different search or reset the filters.' : 'Registered users will appear here.'"></p>
                        <button type="button" x-show="hasFilters" @click="clearFilters()" class="btn secondary small mt-4">Reset filters</button>
                    </div>
                </div>

                <!-- Pagination -->
                <div x-show="!loading && pagination.total > 0" class="ui-pagination">
                    <div class="flex items-center text-sm text-zinc-700">
                        <p>
                            Showing <span class="font-medium" x-text="getShowingFrom()"></span>
                            to <span class="font-medium" x-text="getShowingTo()"></span>
                            of <span class="font-medium" x-text="pagination.total"></span> results
                        </p>
                    </div>
                    <div class="ui-page-list" role="navigation" aria-label="User pages">
                        <button type="button" aria-label="Previous page" @click="goToPage(pagination.current - 1)"
                                :disabled="pagination.current <= 1"
                                class="btn secondary small">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>

                        <template x-for="(page, index) in getVisiblePages()" :key="index">
                            <span>
                            <template x-if="page !== '...'">
                                <button @click="goToPage(page)"
                                        class="btn small" :class="page === pagination.current ? 'primary' : 'secondary'" :aria-current="page === pagination.current ? 'page' : null" x-text="page"></button>
                            </template>
                            <template x-if="page === '...'">
                                <span class="px-2 text-sm text-zinc-500">...</span>
                            </template>
                            </span>
                        </template>

                        <button type="button" aria-label="Next page" @click="goToPage(pagination.current + 1)"
                                :disabled="pagination.current >= pagination.last"
                                class="btn secondary small">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>


            </div>
        </section>
    </div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../../layouts/base.php';
