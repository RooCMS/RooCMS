<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

$page_title = 'Forgot password — RooCMS';
$page_description = 'Reset your password for RooCMS account';

$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [$theme_base.'/assets/js/app/api.js', $theme_base.'/assets/js/app/auth.js', $theme_base.'/assets/js/pages/password-forgot.js'];

ob_start();
?>

<script nonce="<?php render_html($csp_nonce); ?>">
// Redirect if already authenticated
if (localStorage.getItem('access_token')) {
    window.location.href = '/profile';
}
</script>

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
                Forgot password
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Enter your email address and we'll send you a code to reset your password.
            </p>
        </div>

        <!-- Forgot password form -->
        <div class="bg-white py-8 px-6 shadow-lg rounded-lg border border-gray-200" x-data="forgotPasswordForm">
            <!-- Error messages -->
            <div id="error-message" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md text-red-700 text-sm" x-show="form_error" x-text="form_error">
            </div>

            <!-- Success messages -->
            <div id="success-message" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md text-green-700 text-sm" x-show="form_success" x-text="form_success">
            </div>

            <form id="forgot-password-form" class="space-y-6" method="POST" x-on:submit.prevent="submitForm">
                <!-- Email field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email address <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="your@email.com"
                            x-model="email"
                        >
                    </div>
                </div>

                <!-- Submit button -->
                <div>
                    <button
                        type="submit"
                        id="submit-btn"
                        class="btn primary full-width"
                        :disabled="loading"
                        x-on:click="submitForm"
                    >
                        <span id="submit-text" x-show="!loading">Send reset link</span>
                        <span id="loading-text" class="flex items-center" x-show="loading">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sending...
                        </span>
                    </button>
                </div>
            </form>

            <!-- Back to login -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Remember your password?
                    <a href="/login" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                        Back to login
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>


<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php'; 
