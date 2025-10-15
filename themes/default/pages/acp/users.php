<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

$page_title = 'Users — Admin Control Panel — RooCMS';
$page_description = 'Users for RooCMS ACP';

$theme_name = basename(dirname(dirname(__DIR__)));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [
	$theme_base.'/assets/js/app/acp.js',
    $theme_base.'/assets/js/app/acp-access.js',
    $theme_base.'/assets/js/pages/acp-users.js'
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
                        <li><span class="text-zinc-700">Users</span></li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Users</h1>
                <p class="mt-2 text-sm text-zinc-600">Manage users and their permissions</p>
            </header>

            <div class="space-y-8" x-data="usersManager()">

                <!-- Search and Filters -->
                <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-6 shadow-sm backdrop-blur">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <!-- Search -->
                        <div class="flex-1 max-w-md">
                            <label for="search" class="block text-sm font-medium text-zinc-800 mb-2">Search Users</label>
                            <div class="relative">
                                <input type="text"
                                       id="search"
                                       x-model="searchQuery"
                                       @input.debounce.300ms="searchUsers()"
                                       placeholder="Search by login, email, name..."
                                       class="block w-full rounded-lg border border-zinc-300 bg-white pl-10 pr-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-sky-500 focus:outline-none">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="flex flex-wrap gap-4">
                            <!-- Role Filter -->
                            <div class="min-w-[140px]">
                                <label for="role-filter" class="block text-sm font-medium text-zinc-800 mb-2">Role</label>
                                <select id="role-filter"
                                        x-model="filters.role"
                                        @change="applyFilters()"
                                        class="select-custom block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none">
                                    <option value="">All Roles</option>
                                    <option value="u">User</option>
                                    <option value="m">Moderator</option>
                                    <option value="a">Admin</option>
                                    <option value="su">Superuser</option>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="min-w-[140px]">
                                <label for="status-filter" class="block text-sm font-medium text-zinc-800 mb-2">Status</label>
                                <select id="status-filter"
                                        x-model="filters.status"
                                        @change="applyFilters()"
                                        class="select-custom block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="banned">Banned</option>
                                </select>
                            </div>

                            <!-- Clear Filters -->
                            <div class="flex items-end">
                                <button type="button"
                                        @click="clearFilters()"
                                        class="cursor-pointer inline-flex items-center px-4 py-2 text-sm font-medium text-zinc-700 bg-white border border-zinc-300 rounded-lg hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                                    Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users List -->
                <div class="rounded-xl border border-zinc-200/80 bg-white/80 shadow-sm backdrop-blur overflow-hidden">
                    <!-- Loading State -->
                    <div x-show="loading" class="flex items-center justify-center py-12">
                        <div class="flex items-center text-sm text-zinc-500">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading users...
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div x-show="!loading" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200">
                            <thead class="bg-zinc-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">User</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Role</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Last Activity</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-zinc-200">
                                <template x-for="user in users" :key="user.id">
                                    <tr class="hover:bg-zinc-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div x-show="!user.avatar" class="h-10 w-10 rounded-full bg-zinc-300 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-zinc-700" x-text="getInitials(user)"></span>
                                                    </div>
                                                    <img x-show="user.avatar && user.avatar !== 'null'" :src="user.avatar ? '/up/' + user.avatar : ''" :alt="user.login" class="h-10 w-10 rounded-full object-cover">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-zinc-900">
                                                        <span x-text="user.login"></span>
                                                        <span x-show="user.is_verified" class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            Verified
                                                        </span>
                                                    </div>
                                                    <div class="text-sm text-zinc-500" x-text="user.email"></div>
                                                    <div x-show="user.nickname" class="text-xs text-zinc-400" x-text="'@' + user.nickname"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium"
                                                  :class="getRoleBadgeClass(user.role)">
                                                <span x-text="getRoleLabel(user.role)"></span>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col gap-1.5">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium"
                                                      :class="user.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-800'">
                                                    <span x-text="user.is_active ? 'Active' : 'Inactive'"></span>
                                                </span>
                                                <div x-show="user.is_banned" class="flex flex-col gap-1 p-2 bg-rose-50 border border-rose-200 rounded-md">
                                                    <span class="text-xs font-semibold text-rose-700">🚫 Banned</span>
                                                    <div x-show="user.ban_reason" class="text-xs text-zinc-700">
                                                        <span class="font-medium">Reason:</span> <span x-text="user.ban_reason"></span>
                                                    </div>
                                                    <div class="text-xs text-zinc-700">
                                                        <span class="font-medium">Until:</span> 
                                                        <span x-text="formatBanExpiry(user.ban_expired)" :class="(!user.ban_expired || user.ban_expired === 0) ? 'text-rose-700 font-semibold' : ''"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500">
                                            <span x-text="formatLastActivity(user.last_activity)"></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <button @click="editUser(user)"
                                                        class="cursor-pointer inline-flex items-center justify-center rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-sm font-medium text-zinc-900 hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                                                    Edit
                                                </button>
                                                <button x-show="!user.is_banned" @click="banUser(user)"
                                                        class="cursor-pointer inline-flex items-center justify-center rounded-lg bg-rose-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500">
                                                    Ban
                                                </button>
                                                <button x-show="user.is_banned" @click="unbanUser(user)"
                                                        class="cursor-pointer inline-flex items-center justify-center rounded-lg bg-emerald-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                                    Unban
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div x-show="!loading && users.length === 0" class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-zinc-900">No users found</h3>
                        <p class="mt-1 text-sm text-zinc-500">Try adjusting your search or filters.</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div x-show="!loading && pagination.total > 0" class="flex items-center justify-between">
                    <div class="flex items-center text-sm text-zinc-700">
                        <p>
                            Showing <span class="font-medium" x-text="getShowingFrom()"></span>
                            to <span class="font-medium" x-text="getShowingTo()"></span>
                            of <span class="font-medium" x-text="pagination.total"></span> results
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button @click="goToPage(pagination.current - 1)"
                                :disabled="pagination.current <= 1"
                                class="cursor-pointer relative inline-flex items-center px-2 py-2 rounded-l-md border border-zinc-300 bg-white text-sm font-medium text-zinc-500 hover:bg-zinc-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>

                        <template x-for="page in getVisiblePages()" :key="page">
                            <template x-if="page !== '...'">
                                <button @click="goToPage(page)"
                                        :class="page === pagination.current ?
                                            'cursor-pointer z-10 bg-sky-50 border-sky-500 text-sky-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium' :
                                            'cursor-pointer bg-white border-zinc-300 text-zinc-500 hover:bg-zinc-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium'"
                                        x-text="page"></button>
                            </template>
                            <template x-if="page === '...'">
                                <span class="relative inline-flex items-center px-4 py-2 border border-zinc-300 bg-white text-sm font-medium text-zinc-700">...</span>
                            </template>
                        </template>

                        <button @click="goToPage(pagination.current + 1)"
                                :disabled="pagination.current >= pagination.last"
                                class="cursor-pointer relative inline-flex items-center px-2 py-2 rounded-r-md border border-zinc-300 bg-white text-sm font-medium text-zinc-500 hover:bg-zinc-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Messages -->
                <div class="messages-container">
                    <div class="form-success p-4 rounded-lg bg-green-50 border border-green-200 text-green-800" x-show="successMessage" x-text="successMessage" x-transition></div>
                    <div class="form-error p-4 rounded-lg bg-red-50 border border-red-200 text-red-800" x-show="errorMessage" x-text="errorMessage" x-transition></div>
                </div>
            </div>
        </section>
    </div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../../layouts/base.php';