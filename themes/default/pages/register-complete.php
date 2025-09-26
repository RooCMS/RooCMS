<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

$page_title = 'Registration Complete — RooCMS';
$page_description = 'Registration completed successfully. Please check your email for verification link.';

$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [];

ob_start();
?>

<div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Success Card -->
        <div class="bg-white py-8 px-6 shadow-lg rounded-lg border border-gray-200">
            <!-- Success Icon -->
            <div class="text-center">
                <div class="flex justify-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Registration Complete!
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Your account has been created successfully
                </p>
            </div>

            <!-- Success Message -->
            <div class="mt-8 bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">
                            Check your email
                        </h3>
                        <div class="mt-2 text-sm text-green-700">
                            <p>
                                We've sent a verification link to your email address. Please check your inbox (and spam folder) and click the link to activate your account.
                            </p>
                            <p class="mt-2">
                                Once your email is verified, you'll be able to log in to your account.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 space-y-4">
                <!-- Resend Verification Link -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Didn't receive the email?
                        <button class="font-medium text-blue-600 hover:text-blue-500 transition-colors" x-on:click="resendVerification()">
                            Resend verification link
                        </button>
                    </p>
                </div>

                <!-- Back to Login -->
                <div>
                    <a href="/login" class="btn primary w-full justify-center">
                        <span>Back to Login</span>
                    </a>
                </div>
            </div>

            <!-- Help Text -->
            <div class="mt-6 text-center text-xs text-gray-500">
                <p>
                    Having trouble? Contact our support team.
                </p>
            </div>
        </div>
    </div>
</div>

<script nonce="<?php render_html($csp_nonce); ?>">
function resendVerification() {
    // TODO: Implement resend verification functionality
    alert('Resend verification functionality will be implemented soon.');
}
</script>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php'; ?>
