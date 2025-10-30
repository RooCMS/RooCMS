<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

$page_title = 'User — RooCMS';
$page_description = 'User profile RooCMS';

$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [$theme_base.'/assets/js/app/api.js', $theme_base.'/assets/js/app/auth.js', $theme_base.'/assets/js/pages/user.js'];

ob_start();
?>

<script nonce="<?php render_html($csp_nonce); ?>">
// Get user id from URL parameters
function getUserIdFromUrl() {
    const url = new URL(window.location);

    // First try to get named parameter
    let code = url.searchParams.get('id');
    if (code) {
        return code;
    }

    // If no named parameter, check if there's a single unnamed parameter
    const search = window.location.search;
    if (search && search.length > 1) {
        // Remove the leading '?' and split by '&'
        const params = search.substring(1).split('&');
        // Look for a parameter without '=' (unnamed parameter)
        for (const param of params) {
            if (!param.includes('=')) {
                return param;
            }
        }
    }

    return null;
}

const userId = getUserIdFromUrl();

// If user id is in URL, auto-submit user id
if (userId) {
    // Store the user id for the JavaScript to use
    window.autoUserId = userId;
}
</script>

<div class="min-h-full py-8 sm:py-16 lg:py-8" x-data="userProfile">
    <div class="mx-auto max-w-8xl px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- User Profile Header -->
        <div class="text-center relative">
            <div class="absolute inset-0 bg-gradient-to-r from-amber-100/80 to-sky-50/80 rounded-3xl blur-3xl -z-10"></div>
            <div class="relative">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                    User Profile
                </h1>
                <p class="mt-3 text-lg text-gray-600 max-w-md mx-auto" id="user-subtitle">
                    Loading user information...
                </p>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="bg-white/50 backdrop-blur-sm py-12 px-6 shadow-sm rounded-2xl border border-gray-200/50 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900 mx-auto mb-4"></div>
            <p class="text-gray-600">Loading user profile...</p>
        </div>

        <!-- Error State -->
        <div x-show="error && !loading" class="bg-red-50/50 backdrop-blur-sm py-12 px-6 shadow-sm rounded-2xl border border-red-200/50 text-center">
            <div x-show="!showLoginLink" class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div x-show="!showLoginLink">
                <h3 class="text-lg font-semibold text-red-900 mb-2">Error Loading Profile</h3>
            </div>
            <div x-show="showLoginLink">
                <svg class="w-8 h-8 text-blue-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-blue-900 mb-2">Authentication Required</h3>
            </div>
            <p class="text-gray-700" x-text="error"></p>
            <a x-show="showLoginLink" href="/!/login" class="inline-flex items-center px-4 py-2 mt-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                Sign In to View Profile
            </a>
        </div>

        <!-- User Profile Card -->
        <div class="bg-white/50 backdrop-blur-sm py-8 px-6 shadow-sm rounded-2xl border border-gray-200/50" x-show="!loading && !error">
            <!-- Profile Avatar and Basic Info -->
            <div class="text-center mb-8">
                <div class="w-48 h-48 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center shadow-sm mx-auto mb-4">
                    <template x-if="avatarUrl">
                        <img :src="avatarUrl" alt="Profile Avatar" class="w-48 h-48 rounded-full object-cover border-4 border-white shadow-sm">
                    </template>
                    <template x-if="!avatarUrl">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </template>
                </div>
                <h2 class="text-2xl font-bold text-gray-900" x-text="displayName"></h2>
                
                <div class="mt-2 flex justify-center gap-2">
                    <template x-for="badge in statusBadges" :key="badge.text">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="badge.classes" x-text="badge.text"></span>
                    </template>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Information -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200/50">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-indigo-500 mr-3 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        Personal Information
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-200/50">
                            <span class="text-sm font-medium text-gray-600">First Name:</span>
                            <span class="text-sm text-gray-900" x-text="(user || {}).first_name || '-'"></span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200/50">
                            <span class="text-sm font-medium text-gray-600">Last Name:</span>
                            <span class="text-sm text-gray-900" x-text="(user || {}).last_name || '-'"></span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200/50">
                            <span class="text-sm font-medium text-gray-600">Gender:</span>
                            <span class="text-sm text-gray-900" x-text="(user || {}).gender || '-'"></span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b-0 border-gray-200/50">
                            <span class="text-sm font-medium text-gray-600">Birthday:</span>
                            <span class="text-sm text-gray-900" x-text="formattedBirthday"></span>
                        </div>
                    </div>
                </div>

                <!-- Contact & Activity -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-200/50">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-green-500 to-emerald-500 mr-3 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        Contact & Activity
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-200/50">
                            <span class="text-sm font-medium text-gray-600">Email:</span>
                            <span class="text-sm text-gray-900" x-text="(user || {}).email || 'private'"></span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200/50">
                            <span class="text-sm font-medium text-gray-600">Website:</span>
                            <template x-if="websiteLink">
                                <a :href="websiteLink.url" target="_blank" rel="nofollow noopener noreferrer" class="text-sm text-blue-600 hover:text-blue-800" x-text="websiteLink.text"></a>
                            </template>
                            <template x-if="!websiteLink">
                                <span class="text-sm text-gray-900">-</span>
                            </template>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200/50">
                            <span class="text-sm font-medium text-gray-600">Role:</span>
                            <span class="text-sm text-gray-900" x-text="(user || {}).role_name || (user || {}).role || '-'"></span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b-0 border-gray-200/50">
                            <span class="text-sm font-medium text-gray-600">Last Activity:</span>
                            <span class="text-sm text-gray-900" x-text="lastActivity"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bio Section -->
            <div class="mt-6 bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-200/50">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 mr-3 shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    About
                </h3>
                <div class="bg-white/80 backdrop-blur-sm rounded-xl p-4 border border-gray-200/50">
                    <p class="text-gray-700 whitespace-pre-wrap" x-text="(user || {}).bio || 'No bio available.'"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';