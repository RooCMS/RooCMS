<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

$page_title = 'My Profile — RooCMS';
$page_description = 'Manage your account settings and profile information.';

$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [$theme_base.'/assets/js/pages/profile.js'];

ob_start();
?>

<div class="min-h-full py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl w-full mx-auto space-y-8">
        <!-- Profile Header -->
        <div class="text-center">
            <div class="flex justify-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-r from-blue-600 to-purple-600">
                    <span class="text-2xl font-bold text-white">👤</span>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                My Profile
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Manage your account settings
            </p>
        </div>

        <!-- Profile Card -->
        <div class="bg-white py-8 px-6 shadow-lg rounded-lg border border-gray-200" x-data="userProfile">
            <!-- Profile Info -->
            <div class="space-y-8">
                <!-- Account Information -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Account Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-white p-4 rounded-md border">
                            <div class="text-sm text-gray-500">User ID</div>
                            <div class="text-lg font-medium text-gray-900" x-text="(user || {}).id || 'Loading...'"></div>
                        </div>

                        <div class="bg-white p-4 rounded-md border">
                            <div class="text-sm text-gray-500">Role</div>
                            <div class="text-lg font-medium text-gray-900" x-text="(user || {}).role || 'Loading...'"></div>
                        </div>

                        <div class="bg-white p-4 rounded-md border">
                            <div class="text-sm text-gray-500">Login</div>
                            <div class="text-lg font-medium text-gray-900" x-text="(user || {}).login || 'Loading...'"></div>
                        </div>

                        <div class="bg-white p-4 rounded-md border">
                            <div class="text-sm text-gray-500">Email Verified</div>
                            <div class="text-lg font-medium" x-bind:class="user ? (user.is_verified ? 'text-green-600' : 'text-red-600') : 'text-gray-500'">
                                <span x-text="user ? (user.is_verified ? 'Verified ✓' : 'Not Verified ✗') : 'Loading...'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Personal Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-white p-4 rounded-md border">
                            <div class="text-sm text-gray-500">First Name</div>
                            <div class="text-lg font-medium text-gray-900" x-text="(user || {}).first_name || 'Not set'"></div>
                        </div>

                        <div class="bg-white p-4 rounded-md border">
                            <div class="text-sm text-gray-500">Last Name</div>
                            <div class="text-lg font-medium text-gray-900" x-text="(user || {}).last_name || 'Not set'"></div>
                        </div>

                        <div class="bg-white p-4 rounded-md border">
                            <div class="text-sm text-gray-500">Nickname</div>
                            <div class="text-lg font-medium text-gray-900" x-text="(user || {}).nickname || 'Not set'"></div>
                        </div>

                        <div class="bg-white p-4 rounded-md border">
                            <div class="text-sm text-gray-500">Gender</div>
                            <div class="text-lg font-medium text-gray-900" x-text="(user || {}).gender || 'Not set'"></div>
                        </div>

                        <div class="bg-white p-4 rounded-md border lg:col-span-2">
                            <div class="text-sm text-gray-500">Birthday</div>
                            <div class="text-lg font-medium text-gray-900" x-text="formatDate((user || {}).birthday)"></div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-green-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Contact Information
                    </h3>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded-md border">
                            <div class="text-sm text-gray-500">Email</div>
                            <div class="text-lg font-medium text-gray-900" x-text="(user || {}).email || 'Loading...'"></div>
                        </div>

                        <div class="bg-white p-4 rounded-md border">
                            <div class="text-sm text-gray-500">Website</div>
                            <div class="text-lg font-medium">
                                <span x-show="(user || {}).website" class="text-blue-600">
                                    <a x-bind:href="(user || {}).website" target="_blank" class="hover:underline" x-text="(user || {}).website"></a>
                                </span>
                                <span x-show="!(user || {}).website" class="text-gray-500">Not set</span>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-md border lg:col-span-2">
                            <div class="text-sm text-gray-500">Bio</div>
                            <div class="text-lg font-medium text-gray-900 whitespace-pre-line" x-text="(user || {}).bio || 'Not set'"></div>
                        </div>
                    </div>
                </div>

                <!-- Activity Information -->
                <div class="bg-purple-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Activity Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-white p-4 rounded-md border">
                            <div class="text-sm text-gray-500">Account Created</div>
                            <div class="text-lg font-medium text-gray-900" x-text="formatDate((user || {}).created_at) || 'Loading...'"></div>
                        </div>

                        <div class="bg-white p-4 rounded-md border">
                            <div class="text-sm text-gray-500">Last Activity</div>
                            <div class="text-lg font-medium text-gray-900" x-text="formatDateTime((user || {}).last_activity) || 'Loading...'"></div>
                        </div>

                        <div class="bg-white p-4 rounded-md border">
                            <div class="text-sm text-gray-500">Profile Visibility</div>
                            <div class="text-lg font-medium" x-bind:class="user ? (user.is_public ? 'text-green-600' : 'text-gray-600') : 'text-gray-500'">
                                <span x-text="user ? (user.is_public ? 'Public' : 'Private') : 'Loading...'"></span>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-md border">
                            <div class="text-sm text-gray-500">Account Status</div>
                            <div class="text-lg font-medium" x-bind:class="user ? (user.is_active ? 'text-green-600' : 'text-red-600') : 'text-gray-500'">
                                <span x-text="user ? (user.is_active ? 'Active' : 'Inactive') : 'Loading...'"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 space-y-4">
                <a href="/" class="w-full flex justify-center items-center py-2 px-4 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</div>


<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php'; ?>
