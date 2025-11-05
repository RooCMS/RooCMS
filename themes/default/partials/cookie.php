<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/'.$theme_name;
?>

<!-- Cookie Consent Root -->
<div x-data="cookieConsent()" x-cloak>

    <!-- Cookie Consent Banner -->
    <div
        x-show="!isConsentGiven && !isRejected"
        class="hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-t border-gray-200/50 shadow-2xl transform transition ease-out duration-300"
        :class="{ 'opacity-100 translate-y-0 pointer-events-auto': true, 'opacity-0 translate-y-full pointer-events-none': false }"
        :aria-hidden="isConsentGiven || isRejected"
    >

    <!-- Main Banner -->
    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">

                <!-- Cookie Icon and Message -->
                <div class="flex items-start gap-4 flex-1">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            🍪 We use cookies
                        </h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            We use cookies to improve the website functionality, analytics and content personalization.
                            Detailed information in our <a href="/!/legal/cookie" class="text-amber-600 hover:text-amber-700 font-medium underline">cookie policy</a>.
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 sm:ml-6">
                    <button
                        @click="acceptAll()"
                        class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-medium rounded-lg hover:from-amber-600 hover:to-orange-600 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                    >
                        Accept all cookies
                    </button>
                    <button
                        @click="reject()"
                        class="px-6 py-2.5 bg-gray-100 text-gray-600 font-medium rounded-lg hover:bg-gray-200 transition-all duration-200"
                    >
                        Reject
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Rejection Overlay -->
    <div
        x-show="isRejected"
        class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity duration-300 ease-out hidden"
        :class="{ 'opacity-100 visible pointer-events-auto': isRejected, 'opacity-0 invisible pointer-events-none': !isRejected }"
        :aria-hidden="!isRejected"
    >
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>

            <h3 class="text-xl font-semibold text-gray-900 mb-2">Access restricted</h3>
            <p class="text-gray-600 mb-6">
                To use the website, you must accept the use of cookies.
                Without cookies, the website cannot ensure security and functionality.
            </p>

            <div class="flex gap-3 justify-center">
                <button
                    @click="resetRejection()"
                    class="px-6 py-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-medium rounded-lg hover:from-amber-600 hover:to-orange-600 transition-all duration-200"
                >
                    Return
                </button>
                <a
                    href="/!/legal/cookie"
                    class="px-6 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-all duration-200"
                >
                    Cookie policy
                </a>
            </div>
        </div>
    </div>


</div>
<script nonce="<?php render_html($csp_nonce); ?>">
document.addEventListener('alpine:init', () => {
    Alpine.data('cookieConsent', () => ({
        isConsentGiven: false,
        isRejected: false,

        init() {
            this.checkConsentStatus();
            // Remove hidden class after checking status
            this.$el.querySelector('[x-show="!isConsentGiven && !isRejected"]').classList.remove('hidden');
            this.$el.querySelector('[x-show="isRejected"]').classList.remove('hidden');
        },
        
        checkConsentStatus() {
            try {
                const consentGiven = localStorage.getItem('roocms_cookie_consent_given');
                const rejected = localStorage.getItem('roocms_cookie_rejected');
                this.isConsentGiven = consentGiven === 'true';
                this.isRejected = rejected === 'true';
            } catch (e) {
                this.isConsentGiven = false;
                this.isRejected = false;
            }
        },

        acceptAll() {
            try {
                localStorage.setItem('roocms_cookie_consent_given', 'true');
                this.isConsentGiven = true;
            } catch (e) {
                console.error('Failed to save consent:', e);
            }
        },

        reject() {
            try {
                localStorage.setItem('roocms_cookie_rejected', 'true');
                this.isRejected = true;
            } catch (e) {
                console.error('Failed to save rejection:', e);
            }
        },

        resetRejection() {
            try {
                localStorage.removeItem('roocms_cookie_rejected');
                this.isRejected = false;
            } catch (e) {
                console.error('Failed to reset rejection:', e);
            }
        }
    }));
});
</script>
