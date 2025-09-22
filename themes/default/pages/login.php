<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

$page_title = 'Enter to RooCMS';
$page_description = 'Enter to your account RooCMS';

$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [$theme_base.'/assets/js/app/api.js', $theme_base.'/assets/js/app/auth.js', $theme_base.'/assets/js/pages/login.js'];

ob_start();
?>

<div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Title -->
        <div class="text-center">
            <div class="flex justify-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-r from-blue-600 to-purple-600">
                    <span class="text-xl font-bold text-white">R</span>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Enter to account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                No account?
                <a href="/register" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                    Register
                </a>
            </p>
        </div>

        <!-- Login form -->
        <div class="bg-white py-8 px-6 shadow-lg rounded-lg border border-gray-200" x-data="loginForm">
            <!-- Error messages -->
            <div id="error-message" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md text-red-700 text-sm" x-show="form_error" x-text="form_error">
            </div>

            <!-- Success messages -->
            <div id="success-message" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md text-green-700 text-sm" x-show="form_success" x-text="form_success">
            </div>

            <form id="login-form" class="space-y-6" method="POST" x-on:submit.prevent="submitForm">
                <!-- Login field -->
                <div>
                    <label for="login" class="block text-sm font-medium text-gray-700">
                        Login or Email <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input
                            id="login"
                            name="login"
                            type="text"
                            autocomplete="username"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Enter login or email"
                            x-model="login"
                        >
                    </div>
                </div>

                <!-- Password field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Enter password"
                            x-model="password"
                        >
                    </div>
                </div>

                <!-- Forgot password -->
                <div class="flex items-center justify-between">
                    <div class="text-sm">
                        <a href="/forgot-password" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                            Forgot password?
                        </a>
                    </div>
                </div>

                <!-- Submit button -->
                <div>
                    <button
                        type="submit"
                        id="submit-btn"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-sm hover:shadow-md"
                        :disabled="loading"
                        x-on:click="submitForm"
                    >
                        <span id="submit-text" x-show="!loading">Enter</span>
                        <span id="loading-text" class="flex items-center" x-show="loading">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Entering...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
