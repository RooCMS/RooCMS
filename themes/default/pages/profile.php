<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

$page_title = 'My Profile — RooCMS';
$page_description = 'Manage your account settings and profile information.';

$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [$theme_base.'/assets/js/pages/profile.js'];

ob_start();
?>

<div class="min-h-full py-8 sm:py-16 lg:py-8">
    <div class="mx-auto max-w-8xl px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Profile Header -->
        <div class="text-center relative">
            <div class="absolute inset-0 bg-gradient-to-r from-amber-100/80 to-sky-50/80 rounded-3xl blur-3xl -z-10"></div>
            <div class="relative">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                    My Profile Dashboard
                </h1>
                <p class="mt-3 text-lg text-gray-600 max-w-md mx-auto">
                    Manage your account settings and personal information
                </p>
                <div class="mt-4 flex justify-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                        Account Active
                    </span>
                </div>
            </div>
        </div>

        <!-- Profile Card -->
        <div class="bg-white/50 backdrop-blur-sm py-8 px-6 shadow-sm rounded-2xl border border-gray-200/50" x-data="userProfile">
            <!-- Profile Info -->
            <div class="space-y-8">
                <!-- Account Information -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600/5 to-indigo-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-indigo-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                Account Security
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 rounded-full text-sm font-medium border border-green-200">
                                <span x-text="user ? (user.is_verified ? 'Protected' : 'Needs Attention') : 'Loading...'"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="group bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-medium text-gray-500">User ID</div>
                                    <div class="h-2 w-2 bg-blue-400 rounded-full"></div>
                                </div>
                                <div class="text-xl font-bold text-gray-900" x-text="(user || {}).id || 'Loading...'"></div>
                                <div class="mt-2 text-xs text-gray-400">Unique identifier</div>
                            </div>

                            <div class="group bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-medium text-gray-500">Role</div>
                                    <div class="h-2 w-2 bg-purple-400 rounded-full"></div>
                                </div>
                                <div class="text-xl font-bold text-gray-900" x-text="(user || {}).role || 'Loading...'"></div>
                                <div class="mt-2 text-xs text-gray-400">Account level</div>
                            </div>

                            <div class="group bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-medium text-gray-500">Username</div>
                                    <div class="h-2 w-2 bg-indigo-400 rounded-full"></div>
                                </div>
                                <div class="text-xl font-bold text-gray-900" x-text="(user || {}).login || 'Loading...'"></div>
                                <div class="mt-2 text-xs text-gray-400">Login credentials</div>
                            </div>

                            <div class="group bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-medium text-gray-500">Verification</div>
                                    <div class="h-2 w-2 rounded-full" x-bind:class="user ? (user.is_verified ? 'bg-green-400' : 'bg-red-400') : 'bg-gray-300'"></div>
                                </div>
                                <div class="text-xl font-bold" x-bind:class="user ? (user.is_verified ? 'text-green-600' : 'text-red-600') : 'text-gray-500'">
                                    <span x-text="user ? (user.is_verified ? 'Verified ✓' : 'Not Verified ✗') : 'Loading...'"></span>
                                </div>
                                <div class="mt-2 text-xs text-gray-400">Email status</div>
                            </div>
                        </div>

                        <!-- Security Progress -->
                        <div class="mt-6 bg-white/60 rounded-xl p-4 border border-gray-200/50 shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Security Score</span>
                                <span class="text-sm font-bold text-gray-900" x-text="user ? (user.is_verified ? '85%' : '45%') : 'Loading...'"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-500" x-bind:class="user ? (user.is_verified ? 'bg-gradient-to-r from-green-400 to-green-600 w-[85%]' : 'bg-gradient-to-r from-red-400 to-red-600 w-[45%]') : 'bg-gray-300 w-0'"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600/5 to-pink-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                Personal Details
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 rounded-full text-sm font-medium border border-purple-200">
                                <span x-text="user ? (user.first_name && user.last_name && user.nickname && user.gender && user.birthday ? 'Complete' : 'Incomplete') : 'Loading...'"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="group bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-medium text-gray-500">First Name</div>
                                    <div class="h-2 w-2 bg-purple-400 rounded-full"></div>
                                </div>
                                <div class="text-xl font-bold text-gray-900" x-text="(user || {}).first_name || 'Not set'" x-bind:class="user ? (user.first_name ? 'text-gray-900' : 'text-red-600') : 'text-gray-500'"></div>
                                <div class="mt-2 text-xs text-gray-400">Given name</div>
                            </div>

                            <div class="group bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-medium text-gray-500">Last Name</div>
                                    <div class="h-2 w-2 bg-pink-400 rounded-full"></div>
                                </div>
                                <div class="text-xl font-bold text-gray-900" x-text="(user || {}).last_name || 'Not set'" x-bind:class="user ? (user.last_name ? 'text-gray-900' : 'text-red-600') : 'text-gray-500'"></div>
                                <div class="mt-2 text-xs text-gray-400">Family name</div>
                            </div>

                            <div class="group bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-medium text-gray-500">Nickname</div>
                                    <div class="h-2 w-2 bg-indigo-400 rounded-full"></div>
                                </div>
                                <div class="text-xl font-bold text-gray-900" x-text="(user || {}).nickname || 'Not set'" x-bind:class="user ? (user.nickname ? 'text-gray-900' : 'text-red-600') : 'text-gray-500'"></div>
                                <div class="mt-2 text-xs text-gray-400">Display name</div>
                            </div>

                            <div class="group bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-medium text-gray-500">Gender</div>
                                    <div class="h-2 w-2 bg-rose-400 rounded-full"></div>
                                </div>
                                <div class="text-xl font-bold text-gray-900" x-text="(user || {}).gender || 'Not set'" x-bind:class="user ? (user.gender ? 'text-gray-900' : 'text-red-600') : 'text-gray-500'"></div>
                                <div class="mt-2 text-xs text-gray-400">Identity</div>
                            </div>

                            <div class="group bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300 lg:col-span-2">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-medium text-gray-500">Birthday</div>
                                    <div class="h-2 w-2 bg-amber-400 rounded-full"></div>
                                </div>
                                <div class="text-xl font-bold text-gray-900" x-text="formatDate((user || {}).birthday) || 'Not set'" x-bind:class="user ? (user.birthday ? 'text-gray-900' : 'text-red-600') : 'text-gray-500'"></div>
                                <div class="mt-2 text-xs text-gray-400">Date of birth</div>
                            </div>
                        </div>

                        <!-- Profile Completion -->
                        <div class="mt-6 bg-white/60 rounded-xl p-4 border border-gray-200/50 shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Profile Completion</span>
                                <span class="text-sm font-bold text-gray-900" x-text="profileCompletionWidth + '%'"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-500 bg-gradient-to-r from-purple-400 to-pink-600"
                                     x-bind:style="{ width: profileCompletionWidth + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-600/5 to-teal-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-6 border border-emerald-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-emerald-500 to-teal-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                Contact & Bio
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-800 rounded-full text-sm font-medium border border-emerald-200">
                                <span x-text="user ? (user.email && user.bio && user.website ? 'Complete' : 'Needs Setup') : 'Loading...'"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="group bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-medium text-gray-500">Email Address</div>
                                    <div class="h-2 w-2 bg-emerald-400 rounded-full"></div>
                                </div>
                                <div class="text-xl font-bold text-gray-900 break-all" x-text="(user || {}).email || 'Loading...'"></div>
                                <div class="mt-2 text-xs text-gray-400">Primary contact</div>
                                <div class="mt-3">
                                    <button class="text-xs bg-gradient-to-r from-blue-500 to-indigo-500 text-white px-3 py-1 rounded-lg hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 flex items-center gap-1">
                                        <svg class="w-3 h-3 flex-shrink-0 py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>Send Email</span>
                                    </button>
                                </div>
                            </div>

                            <div class="group bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-medium text-gray-500">Website</div>
                                    <div class="h-2 w-2 bg-teal-400 rounded-full"></div>
                                </div>
                                <div class="text-xl font-bold">
                                    <span x-show="(user || {}).website" class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">
                                        <a x-bind:href="(user || {}).website" target="_blank" class="hover:underline flex items-center" x-text="(user || {}).website">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    </span>
                                    <span x-show="!(user || {}).website" class="text-gray-500">Not set</span>
                                </div>
                                <div class="mt-2 text-xs text-gray-400">Personal site</div>
                                <div class="mt-3" x-show="(user || {}).website">
                                    <button class="text-xs bg-gradient-to-r from-green-500 to-emerald-500 text-white px-3 py-1 rounded-lg hover:from-green-600 hover:to-emerald-600 transition-all duration-200 flex items-center gap-1">
                                        <svg class="w-3 h-3 flex-shrink-0 py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                        <span>Visit Site</span>
                                    </button>
                                </div>
                            </div>

                            <div class="group bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300 lg:col-span-2">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-medium text-gray-500">About Me</div>
                                    <div class="h-2 w-2 bg-cyan-400 rounded-full"></div>
                                </div>
                                <div class="text-lg font-medium text-gray-900 whitespace-pre-line min-h-[60px]" x-text="(user || {}).bio || 'No biography set yet. Tell us about yourself!'"></div>
                                <div class="mt-2 text-xs text-gray-400">Personal description</div>
                                <div class="mt-3" x-show="(user || {}).bio">
                                    <button class="text-xs bg-gradient-to-r from-purple-500 to-pink-500 text-white px-3 py-1 rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all duration-200 flex items-center gap-1">
                                        <svg class="w-3 h-3 flex-shrink-0 py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span>Edit Bio</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Score -->
                        <div class="mt-6 bg-white/60 rounded-xl p-4 border border-gray-200/50 shadow-sm">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Contact Score</span>
                                <span class="text-sm font-bold text-gray-900" x-text="contactCompletionWidth + '%'"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-500 bg-gradient-to-r from-emerald-400 to-teal-600"
                                     x-bind:style="{ width: contactCompletionWidth + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Information -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-amber-600/5 to-orange-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                Activity & Status
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-amber-100 to-orange-100 text-amber-800 rounded-full text-sm font-medium border border-amber-200">
                                <span x-text="user ? (user.is_active ? 'Active' : 'Inactive') : 'Loading...'"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                            <div class="group bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-medium text-gray-500">Profile Visibility</div>
                                    <div class="h-2 w-2 rounded-full" x-bind:class="user ? (user.is_public ? 'bg-green-400' : 'bg-gray-400') : 'bg-gray-300'"></div>
                                </div>
                                <div class="text-xl font-bold" x-bind:class="user ? (user.is_public ? 'text-green-600' : 'text-gray-600') : 'text-gray-500'">
                                    <span x-text="user ? (user.is_public ? 'Public' : 'Private') : 'Loading...'"></span>
                                </div>
                                <div class="mt-2 text-xs text-gray-400">Profile access</div>
                                <div class="mt-3">
                                    <button class="text-xs bg-gradient-to-r from-gray-500 to-slate-500 text-white px-3 py-1 rounded-lg hover:from-gray-600 hover:to-slate-600 transition-all duration-200 flex items-center gap-1"
                                            x-bind:class="(user || {}).is_public ? 'bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600' : ''">
                                        <svg class="w-3 h-3 flex-shrink-0 py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <span x-text="user ? (user.is_public ? 'Make Private' : 'Make Public') : 'Toggle'"></span>
                                    </button>
                                </div>
                            </div>

                            <div class="group bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-medium text-gray-500">Account Status</div>
                                    <div class="h-2 w-2 rounded-full" x-bind:class="user ? (user.is_active ? 'bg-green-400' : 'bg-red-400') : 'bg-gray-300'"></div>
                                </div>
                                <div class="text-xl font-bold" x-bind:class="user ? (user.is_active ? 'text-green-600' : 'text-red-600') : 'text-gray-500'">
                                    <span x-text="user ? (user.is_active ? 'Active' : 'Inactive') : 'Loading...'"></span>
                                </div>
                                <div class="mt-2 text-xs text-gray-400">Account state</div>
                                <div class="mt-3">
                                    <span class="text-xs px-2 py-1 rounded-full border flex items-center gap-1" x-bind:class="user ? (user.is_active ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200') : 'bg-gray-100 text-gray-800 border-gray-200'">
                                        <svg class="w-3 h-3 flex-shrink-0 py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span x-text="user ? (user.is_active ? 'Enabled' : 'Disabled') : 'Status'"></span>
                                    </span>
                                </div>
                            </div>

                            <div class="group bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-medium text-gray-500">Account Created</div>
                                    <div class="h-2 w-2 bg-amber-400 rounded-full"></div>
                                </div>
                                <div class="text-xl font-bold text-gray-900" x-text="formatDate((user || {}).created_at) || 'Loading...'"></div>
                                <div class="mt-2 text-xs text-gray-400">Member since</div>
                                <div class="mt-3">
                                    <span class="text-xs bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 px-2 py-1 rounded-full flex items-center gap-1">
                                        <svg class="w-3 h-3 flex-shrink-0 py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>Joined</span>
                                    </span>
                                </div>
                            </div>

                            <div class="group bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-medium text-gray-500">Last Activity</div>
                                    <div class="h-2 w-2 bg-orange-400 rounded-full"></div>
                                </div>
                                <div class="text-xl font-bold text-gray-900" x-text="formatDateTime((user || {}).last_activity) || 'Loading...'"></div>
                                <div class="mt-2 text-xs text-gray-400">Recent activity</div>
                                <div class="mt-3">
                                    <span class="text-xs bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 px-2 py-1 rounded-full flex items-center gap-1">
                                        <svg class="w-3 h-3 flex-shrink-0 py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>Online</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Timeline -->
                        <div class="mt-6 bg-white/60 rounded-xl p-4 border border-gray-200/50 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-700">Account Timeline</span>
                                <span class="text-xs text-gray-500">Since creation</span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center text-sm gap-3">
                                    <div class="w-3 h-3 bg-amber-400 rounded-full flex-shrink-0"></div>
                                    <span class="text-gray-600">Account created</span>
                                    <span class="ml-auto text-gray-500" x-text="formatDate((user || {}).created_at) || 'Loading...'"></span>
                                </div>
                                <div class="flex items-center text-sm gap-3">
                                    <div class="w-3 h-3 bg-orange-400 rounded-full flex-shrink-0"></div>
                                    <span class="text-gray-600">Last activity</span>
                                    <span class="ml-auto text-gray-500" x-text="formatDateTime((user || {}).last_activity) || 'Loading...'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8 space-y-4">
                <div class="bg-gradient-to-r from-gray-50 to-slate-50 rounded-2xl p-6 border border-gray-200/50 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-gray-500 to-slate-500 mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        Quick Actions
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <button class="group flex items-center justify-center p-4 bg-white/80 backdrop-blur-sm rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                            <div class="text-center">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-indigo-500 mx-auto mb-2 shadow-sm group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-6 h-6 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </div>
                                <div class="text-sm font-medium text-gray-900">Edit Profile</div>
                                <div class="text-xs text-gray-500 mt-1">Update info</div>
                            </div>
                        </button>

                        <button class="group flex items-center justify-center p-4 bg-white/80 backdrop-blur-sm rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                            <div class="text-center">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-emerald-500 mx-auto mb-2 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-6 h-6 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <div class="text-sm font-medium text-gray-900">Security</div>
                                <div class="text-xs text-gray-500 mt-1">Manage access</div>
                            </div>
                        </button>

                        <button class="group flex items-center justify-center p-4 bg-white/80 backdrop-blur-sm rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                            <div class="text-center">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-purple-500 to-pink-500 mx-auto mb-2 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-6 h-6 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="text-sm font-medium text-gray-900">Help & Support</div>
                                <div class="text-xs text-gray-500 mt-1">Get assistance</div>
                            </div>
                        </button>

                        <button @click="deleteAccount()" class="cursor-pointer group flex items-center justify-center p-4 bg-white/80 backdrop-blur-sm rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                            <div class="text-center">
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-red-500 to-rose-500 mx-auto mb-2 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-6 h-6 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </div>
                                <div class="text-sm font-medium text-gray-900">Delete Account</div>
                                <div class="text-xs text-gray-500 mt-1">Remove profile</div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="flex justify-center">
                    <a href="/" class="group inline-flex items-center px-6 py-3 mt-4 bg-gradient-to-r from-gray-600 to-slate-600 text-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1 gap-3">
                        <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform duration-300 flex-shrink-0 py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span class="font-medium">Back to Home</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php'; ?>
