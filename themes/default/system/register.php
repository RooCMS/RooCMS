<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

$page_title = 'Register — RooCMS';
$page_description = 'Create an account in RooCMS for access to the content management system';

$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [$theme_base.'/assets/js/app/api.js', $theme_base.'/assets/js/app/auth.js', $theme_base.'/assets/js/pages/register.js'];

ob_start();
?>

<script nonce="<?php render_html($csp_nonce); ?>">
// Redirect if already authenticated
if (localStorage.getItem('access_token')) {
    window.location.href = '/!/myprofile';
}
</script>

<div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Title -->
        <div class="text-center">
            <div class="flex justify-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-r from-blue-600 to-purple-600">
                    <svg class="w-8 h-8" viewBox="0 0 337.5 337.5" xmlns="http://www.w3.org/2000/svg">
                        <path fill="white" stroke-width=".940744" d="M260.468 23.386a32.59 32.59 0 0 0-32.588 32.589 32.6 32.6 0 0 0 5.415 17.99l-58.372 58.832a43 43 0 0 0-23.74-7.15 43.003 43.003 0 0 0-43.004 43.003 43.003 43.003 0 0 0 43.003 43.004 43 43 0 0 0 24.208-7.462l57.6 60.535a32.6 32.6 0 0 0-5.05 17.422 32.59 32.59 0 0 0 32.59 32.589 32.59 32.59 0 0 0 32.588-32.59 32.59 32.59 0 0 0-32.588-32.587 32.6 32.6 0 0 0-16.415 4.437l-57.722-60.66a43 43 0 0 0 7.79-24.688 43 43 0 0 0-8.116-25.144l58.58-59.04a32.6 32.6 0 0 0 15.82 4.097 32.59 32.59 0 0 0 32.588-32.588 32.59 32.59 0 0 0-32.587-32.59"/>
                        <rect fill="white" stroke="none" stroke-width=".871106" stroke-dasharray="none" width="10.518" height="25.982" x="202.685" y="134.49" transform="rotate(26.27)scale(1 .99929)skewX(2.16)" ry="0"/>
                        <rect fill="white" stroke="none" stroke-width=".871097" stroke-dasharray="none" width="10.369" height="42.64" x="-43.174" y="-171.036" transform="rotate(147.38)scale(.99929 1)skewY(2.157)" ry="0"/>
                        <circle fill="white" stroke-width=".806002" cx="81.833" cy="59.494" r="27.715"/>
                        <circle fill="white" fill-opacity="1" stroke="none" stroke-width=".806" stroke-dasharray="none" cx="107.015" cy="264.959" r="22.621"/>
                        <path fill="white" fill-opacity="1" stroke="none" stroke-width=".806002" stroke-dasharray="none" d="M63.608 148.27a20.38 20.38 0 0 0-20.38 20.38 20.38 20.38 0 0 0 20.38 20.382 20.38 20.38 0 0 0 19.598-14.782h18.03v-10.974H83.268a20.38 20.38 0 0 0-19.66-15.006"/>
                    </svg>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Create account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Already have an account?
                <a href="/!/login" class="font-medium link-text">
                    Login
                </a>
            </p>
        </div>

        <!-- Registration form -->
        <div class="bg-white py-8 px-6 shadow-lg rounded-lg border border-gray-200" x-data="registerForm">
            <!-- Error messages -->
            <div id="error-message" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md text-red-700 text-sm" x-show="form_error" x-text="form_error">
            </div>

            <!-- Success messages -->
            <div id="success-message" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md text-green-700 text-sm" x-show="form_success" x-text="form_success">
            </div>

            <form id="register-form" class="space-y-6" method="POST" x-on:submit.prevent="submitForm">
                <!-- Login field -->
                <div>
                    <label for="login" class="block text-sm font-medium text-gray-700">
                        Login <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input
                            id="login"
                            name="login"
                            type="text"
                            autocomplete="username"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Enter login"
                            x-model="login"
                        >
                    </div>
                    <div id="login-error" class="hidden mt-1 text-sm text-red-600"></div>
                </div>

                <!-- Email field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email <span class="text-red-500">*</span>
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
                    <div id="email-error" class="hidden mt-1 text-sm text-red-600"></div>
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
                            autocomplete="new-password"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Minimum 8 characters"
                            x-model="password"
                        >
                    </div>
                    <div id="password-error" class="hidden mt-1 text-sm text-red-600"></div>
                </div>

                <!-- Password confirmation field -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        Password confirmation <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Repeat password"
                            x-model="password_confirmation"
                        >
                    </div>
                    <div id="password-confirmation-error" class="hidden mt-1 text-sm text-red-600"></div>
                </div>

                <!-- Submit button -->
                <div>
                    <button
                        type="submit"
                        id="submit-btn"
                        class="btn primary full-width"
                        x-on:click="submitForm"
                    >
                        <span id="submit-text" x-show="!loading">Create account</span>
                        <span id="loading-text" class="flex items-center" x-show="loading">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Registration...
                        </span>
                    </button>
                </div>
            </form>

            <!-- Policy link -->
            <div class="mt-6 text-center text-sm text-gray-500">
                <p>
                    By creating an account, you agree to our
                    <br /><a href="/!/legal/terms" class="link-text font-medium">Terms of use</a>
                    and <a href="/!/legal/privacy" class="link-text font-medium">Privacy policy</a>
                </p>
            </div>
        </div>
    </div>
</div>


<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';