<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

$page_title = 'Edit Profile — RooCMS';
$page_description = 'Update your personal information and account settings.';

$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [$theme_base.'/assets/js/pages/profile-edit.js'];

ob_start();
?>

<div class="min-h-full py-8 sm:py-16 lg:py-8">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Profile Edit Header -->
        <div class="text-center relative">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-100/80 to-purple-50/80 rounded-3xl blur-3xl -z-10"></div>
            <div class="relative">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                    Edit Profile
                </h1>
                <p class="mt-3 text-lg text-gray-600 max-w-md mx-auto">
                    Update your personal information and account preferences
                </p>
                <div class="mt-4 flex justify-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 border border-blue-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editing Profile
                    </span>
                </div>
            </div>
        </div>

        <!-- Profile Edit Form -->
        <div class="bg-white/50 backdrop-blur-sm py-8 px-6 shadow-sm rounded-2xl border border-gray-200/50" x-data="profileEdit">
            <form @submit.prevent="saveProfile()" class="space-y-8">
                <!-- Personal Information Section -->
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
                                <span x-text="formData.nickname && formData.email ? 'Complete' : 'Incomplete'"></span>
                            </div>
                        </div>

                        <!-- Avatar Section -->
                        <div class="mb-8 p-4 bg-white/60 rounded-xl border border-purple-200/30">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Profile Avatar
                            </h4>
                            
                            <div class="flex items-start gap-6">
                                <!-- Current Avatar -->
                                <div class="flex-shrink-0">
                                    <div data-avatar-placeholder class="w-24 h-24 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <img data-current-avatar alt="Current Avatar" class="w-24 h-24 rounded-full object-cover border-2 border-white shadow-sm hidden">
                                    <img data-avatar-preview alt="Avatar Preview" class="w-24 h-24 rounded-full object-cover border-2 border-purple-300 shadow-sm hidden">
                                </div>
                                
                                <!-- Avatar Controls -->
                                <div class="flex-1">
                                    <div class="space-y-4">
                                        <!-- Upload Button -->
                                        <div>
                                            <input 
                                                type="file" 
                                                id="avatar-upload" 
                                                accept="image/jpeg,image/png,image/gif,image/webp"
                                                data-avatar-input
                                                class="hidden"
                                            >
                                            <label 
                                                for="avatar-upload" 
                                                data-upload-label
                                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all duration-200 cursor-pointer shadow-sm"
                                            >
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                </svg>
                                                <span data-upload-text>Choose Avatar</span>
                                            </label>
                                        </div>
                                        
                                        <!-- Delete Button -->
                                        <div data-delete-container class="hidden">
                                            <button 
                                                type="button"
                                                data-delete-avatar
                                                class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200 shadow-sm"
                                            >
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Remove Avatar
                                            </button>
                                        </div>
                                        
                                        <!-- Avatar Guidelines -->
                                        <div class="text-sm text-gray-600">
                                            <p class="mb-1"><strong>Guidelines:</strong></p>
                                            <ul class="list-disc list-inside space-y-1 text-xs">
                                                <li>Maximum file size: 5MB</li>
                                                <li>Supported formats: JPEG, PNG, GIF, WebP</li>
                                                <li>Recommended size: 400x400 pixels or larger</li>
                                                <li>Square images work best</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Avatar Error Message -->
                            <div data-avatar-error class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg hidden">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm text-red-700" data-avatar-error-text></span>
                                </div>
                            </div>
                        </div>

                        <!-- Personal Details Form -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="group">
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    First Name
                                </label>
                                <input
                                    type="text"
                                    id="first_name"
                                    x-model="formData.first_name"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 bg-white/80 backdrop-blur-sm"
                                    placeholder="Enter your first name"
                                >
                                <div id="first_name_error" class="mt-1 text-sm text-red-600 hidden"></div>
                            </div>

                            <div class="group">
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Last Name
                                </label>
                                <input
                                    type="text"
                                    id="last_name"
                                    x-model="formData.last_name"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 bg-white/80 backdrop-blur-sm"
                                    placeholder="Enter your last name"
                                >
                                <div id="last_name_error" class="mt-1 text-sm text-red-600 hidden"></div>
                            </div>

                            <div class="group">
                                <label for="nickname" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nickname *
                                </label>
                                <input
                                    type="text"
                                    id="nickname"
                                    x-model="formData.nickname"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 bg-white/80 backdrop-blur-sm"
                                    placeholder="Enter your nickname"
                                    required
                                >
                                <div id="nickname_error" class="mt-1 text-sm text-red-600 hidden"></div>
                            </div>

                            <div class="group">
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                                    Gender
                                </label>
                                <select
                                    id="gender"
                                    x-model="formData.gender"
                                    class="select-custom w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 bg-white/80 backdrop-blur-sm"
                                >
                                    <option value="" disabled>Select gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                                <div id="gender_error" class="mt-1 text-sm text-red-600 hidden"></div>
                            </div>

                            <div class="group md:col-span-2">
                                <label for="birthday" class="block text-sm font-medium text-gray-700 mb-2">
                                    Birthday
                                </label>
                                <input
                                    type="date"
                                    id="birthday"
                                    x-model="formData.birthday"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-200 bg-white/80 backdrop-blur-sm"
                                >
                                <div id="birthday_error" class="mt-1 text-sm text-red-600 hidden"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
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
                                <span x-text="formData.email ? 'Complete' : 'Needs Setup'"></span>
                            </div>
                        </div>

                        <!-- Contact Form -->
                        <div class="space-y-6">
                            <div class="group">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address *
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    x-model="formData.email"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200 bg-white/80 backdrop-blur-sm"
                                    placeholder="Enter your email address"
                                    required
                                >
                                <div id="email_error" class="mt-1 text-sm text-red-600 hidden"></div>
                                <div class="mt-2 text-xs text-zinc-500">
                                    Email cannot be changed here. Contact support if you need to update your email.
                                </div>
                            </div>

                            <div class="group">
                                <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                                    Website
                                </label>
                                <input
                                    type="url"
                                    id="website"
                                    x-model="formData.website"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200 bg-white/80 backdrop-blur-sm"
                                    placeholder="https://yourwebsite.com"
                                >
                                <div id="website_error" class="mt-1 text-sm text-red-600 hidden"></div>
                            </div>

                            <div class="group">
                                <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                                    About Me
                                </label>
                                <textarea
                                    id="bio"
                                    x-model="formData.bio"
                                    rows="4"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-200 bg-white/80 backdrop-blur-sm resize-none"
                                    placeholder="Tell us about yourself..."
                                ></textarea>
                                <div id="bio_error" class="mt-1 text-sm text-red-600 hidden"></div>
                                <div class="mt-2 text-xs text-zinc-500">
                                    <span x-text="formData.bio ? formData.bio.length : 0"></span>/500 characters
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Settings Section -->
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
                                Account Settings
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 rounded-full text-sm font-medium border border-blue-200">
                                <span>Privacy</span>
                            </div>
                        </div>

                        <!-- Account Settings -->
                        <div class="space-y-6">
                            <label class="toggle-label enabled">
                                <span class="label-text">Profile Visibility</span>
                                <span class="text-xs text-zinc-500">Make your profile visible to other users</span>
                                <input
                                    type="checkbox"
                                    id="is_public"
                                    x-model="formData.is_public"
                                    class="toggle-input"
                                >
                                <span class="toggle-switch enabled success">
                                    <span class="toggle-knob enabled"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
                        <div class="flex items-center gap-4">
                            <!-- Loading Spinner -->
                            <div x-show="loading" x-cloak class="flex items-center gap-2 text-blue-600">
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
                            <a
                                href="/profile"
                                class="btn contrast btn-tr-rl"
                                :disabled="loading"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="btn primary"
                                :disabled="loading"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php'; ?>
