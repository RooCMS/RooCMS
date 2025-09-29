<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

$page_title = 'Verify email — RooCMS';
$page_description = 'Verify your email address for RooCMS account';

$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [$theme_base.'/assets/js/app/api.js', $theme_base.'/assets/js/app/auth.js', $theme_base.'/assets/js/pages/verify-email.js'];

ob_start();
?>

<script nonce="<?php render_html($csp_nonce); ?>">
// Get verification code from URL parameters
function getVerificationCodeFromUrl() {
    const url = new URL(window.location);

    // First try to get named parameter
    let code = url.searchParams.get('verification_code');
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

const verificationCode = getVerificationCodeFromUrl();

// If verification code is in URL, auto-submit verification
if (verificationCode) {
    // Store the code for the JavaScript to use
    window.autoVerificationCode = verificationCode;
}
</script>

<div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Title -->
        <div class="text-center">
            <div class="flex justify-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-r from-green-600 to-blue-600">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Verify email
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Enter the verification code from your email to activate your account.
            </p>
        </div>

        <!-- Verify email form -->
        <div class="bg-white py-8 px-6 shadow-lg rounded-lg border border-gray-200" x-data="verifyEmailForm">
            <!-- Error messages -->
            <div id="error-message" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md text-red-700 text-sm" x-show="form_error" x-text="form_error">
            </div>

            <!-- Success messages -->
            <div id="success-message" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md text-green-700 text-sm" x-show="form_success" x-text="form_success">
            </div>

            <form id="verify-email-form" class="space-y-6" method="POST" x-on:submit.prevent="submitForm">
                <!-- Verification code field -->
                <div>
                    <label for="verification_code" class="block text-sm font-medium text-gray-700">
                        Verification code <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input
                            id="verification_code"
                            name="verification_code"
                            type="text"
                            autocomplete="off"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            placeholder="Enter code from email"
                            x-model="verification_code"
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
                        <span id="submit-text" x-show="!loading">Verify email</span>
                        <span id="loading-text" class="flex items-center" x-show="loading">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Verifying...
                        </span>
                    </button>
                </div>
            </form>

            <!-- Back to login -->
            <div class="mt-6 text-center" x-show="!isAuthenticated">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <a href="/login" class="font-medium text-blue-600 hover:text-blue-500 transition-colors">
                        Sign in
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>


<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php'; 
