<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

$page_title = 'Cookie Policy - RooCMS';
$page_description = 'Detailed information about how RooCMS uses cookies, their types and how to manage them to ensure security and comfort for users.';

$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [];

ob_start();
?>

<div class="min-h-full py-8 sm:py-16 lg:py-8">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Cookie Header -->
        <div class="text-center relative">
            <div class="absolute inset-0 bg-gradient-to-r from-amber-100/80 to-orange-50/80 rounded-3xl blur-3xl -z-10"></div>
            <div class="relative">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                    Cookie Policy
                </h1>
                <p class="mt-3 text-lg text-gray-600 max-w-2xl mx-auto">
                    How we use cookies to improve your experience on our site
                </p>
                <div class="mt-4 flex justify-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-amber-100 to-orange-100 text-amber-800 border border-amber-200">
                        <div class="w-2 h-2 bg-amber-400 rounded-full mr-2 animate-pulse"></div>
                        Cookie Policy
                    </span>
                </div>
            </div>
        </div>

        <!-- Cookie Content -->
        <div class="bg-white/50 backdrop-blur-sm py-8 px-6 shadow-sm rounded-2xl border border-gray-200/50">
            <div class="space-y-8">

                <!-- What are Cookies -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-amber-600/5 to-orange-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                What are cookies?
                            </h3>
                            <span class="text-xs font-medium text-amber-600 bg-amber-100 px-2 py-1 rounded-full">Basics</span>
                        </div>
                        <p class="text-gray-700 leading-relaxed">
                            Cookies are small text files that are stored on your device when you visit our site. They help the site remember your preferences, ensure security, and improve the user experience.
                        </p>
                        <div class="mt-4 bg-white/60 rounded-lg p-4 border border-amber-200/30">
                            <p class="text-sm text-gray-600">
                                <strong>Last updated:</strong> 2025-11-02
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Types of Cookies -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-amber-600/5 to-orange-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                                What cookies do we use?
                            </h3>
                            <span class="text-xs font-medium text-amber-600 bg-amber-100 px-2 py-1 rounded-full">Categories</span>
                        </div>

                        <div class="space-y-6">
                            <!-- Essential Cookies -->
                            <div class="bg-white/60 rounded-lg p-4 border border-amber-200/30">
                                <div class="flex items-center mb-3">
                                    <div class="w-3 h-3 bg-red-500 rounded-full mr-3"></div>
                                    <h4 class="font-semibold text-gray-900">Essential cookies</h4>
                                    <span class="ml-auto text-xs font-medium text-red-600 bg-red-100 px-2 py-1 rounded-full">Required</span>
                                </div>
                                <p class="text-gray-700 text-sm mb-3">These cookies are essential for the site to work and cannot be disabled:</p>
                                <ul class="text-sm text-gray-600 space-y-1 ml-6">
                                    <li>• Site security support</li>
                                    <li>• User session management</li>
                                    <li>• Ensuring the operation of authentication and authorization systems</li>
                                </ul>
                            </div>

                            <!-- Analytics Cookies -->
                            <div class="bg-white/60 rounded-lg p-4 border border-amber-200/30">
                                <div class="flex items-center mb-3">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                    <h4 class="font-semibold text-gray-900">Analytical cookies</h4>
                                    <span class="ml-auto text-xs font-medium text-blue-600 bg-blue-100 px-2 py-1 rounded-full">Required</span>
                                </div>
                                <p class="text-gray-700 text-sm mb-3">Help understand how visitors interact with the site:</p>
                                <ul class="text-sm text-gray-600 space-y-1 ml-6">
                                    <li>• Number of visitors and popular pages</li>
                                    <li>• Time spent on the site</li>
                                    <li>• Traffic sources</li>
                                    <li>• Another analytics services for improving the service</li>
                                </ul>
                            </div>

                            <!-- Functional Cookies -->
                            <div class="bg-white/60 rounded-lg p-4 border border-amber-200/30">
                                <div class="flex items-center mb-3">
                                    <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                    <h4 class="font-semibold text-gray-900">Functional cookies</h4>
                                    <span class="ml-auto text-xs font-medium text-green-600 bg-green-100 px-2 py-1 rounded-full">Required</span>
                                </div>
                                <p class="text-gray-700 text-sm mb-3">Allow the site to remember your preferences:</p>
                                <ul class="text-sm text-gray-600 space-y-1 ml-6">
                                    <li>• Selected language and regional settings</li>
                                    <li>• Content display settings</li>
                                    <li>• Preferences in forms and interface</li>
                                </ul>
                            </div>

                            <!-- Marketing Cookies -->
                            <div class="bg-white/60 rounded-lg p-4 border border-amber-200/30">
                                <div class="flex items-center mb-3">
                                    <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                                    <h4 class="font-semibold text-gray-900">Marketing cookies</h4>
                                    <span class="ml-auto text-xs font-medium text-purple-600 bg-purple-100 px-2 py-1 rounded-full">Optional</span>
                                </div>
                                <p class="text-gray-700 text-sm">Used to show relevant advertising and may be set by our partners. You can always change your preferences in the settings of your browser.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Managing Cookies -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-amber-600/5 to-orange-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                How to manage cookies?
                            </h3>
                            <span class="text-xs font-medium text-amber-600 bg-amber-100 px-2 py-1 rounded-full">Management</span>
                        </div>

                        <!-- Browser Settings -->
                        <div class="mb-6">
                            <h4 class="font-semibold text-gray-900 mb-3">Browser settings</h4>
                            <p class="text-gray-700 text-sm mb-4">You can control cookies through the settings of your browser:</p>
                            <div class="grid md:grid-cols-2 gap-3">
                                <div class="bg-white/60 rounded-lg p-3 border border-amber-200/30">
                                    <h5 class="font-medium text-gray-900 text-sm">Chrome</h5>
                                    <p class="text-xs text-gray-600">Settings → Privacy → Cookies</p>
                                </div>
                                <div class="bg-white/60 rounded-lg p-3 border border-amber-200/30">
                                    <h5 class="font-medium text-gray-900 text-sm">Firefox</h5>
                                    <p class="text-xs text-gray-600">Settings → Privacy → Cookies</p>
                                </div>
                                <div class="bg-white/60 rounded-lg p-3 border border-amber-200/30">
                                    <h5 class="font-medium text-gray-900 text-sm">Safari</h5>
                                    <p class="text-xs text-gray-600">Settings → Privacy → Data Management</p>
                                </div>
                                <div class="bg-white/60 rounded-lg p-3 border border-amber-200/30">
                                    <h5 class="font-medium text-gray-900 text-sm">Edge</h5>
                                    <p class="text-xs text-gray-600">Settings → Cookies → Cookie Management</p>
                                </div>
                            </div>
                        </div>

                        <!-- Consent Tool -->
                        <div class="bg-white/60 rounded-lg p-4 border border-amber-200/30">
                            <h4 class="font-semibold text-gray-900 mb-3">Consent tool</h4>
                            <p class="text-gray-700 text-sm mb-3">On our site, you can accept or reject different categories of cookies:</p>
                            <ul class="text-sm text-gray-600 space-y-1 ml-4">
                                <li>Accept or reject different categories of cookies</li>
                                <li>Configure preferences by categories</li>
                                <li>Change settings at any time</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Your Consent -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-amber-600/5 to-orange-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                Your consent
                            </h3>
                            <span class="text-xs font-medium text-amber-600 bg-amber-100 px-2 py-1 rounded-full">Consent</span>
                        </div>
                        <p class="text-gray-700 leading-relaxed">
                            By continuing to use our site, you agree to the use of cookies in accordance with this policy. You can change your preferences or withdraw your consent at any time through the site settings or browser.
                        </p>
                    </div>
                </div>

                <!-- Contact -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-amber-600/5 to-orange-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                Contacts
                            </h3>
                            <span class="text-xs font-medium text-amber-600 bg-amber-100 px-2 py-1 rounded-full">Contact</span>
                        </div>
                        <p class="text-gray-700 text-sm mb-4">
                            If you have any questions about our cookie usage policy, please contact us:
                        </p>
                        <div class="space-y-2">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Email: <span x-text="$store.siteSettings.site_feedback_email"></span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Through the feedback form on the site
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Protection Laws -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-amber-600/5 to-orange-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                Main laws on data protection
                            </h3>
                            <span class="text-xs font-medium text-amber-600 bg-amber-100 px-2 py-1 rounded-full">Compliance</span>
                        </div>
                        <p class="text-gray-700 leading-relaxed mb-6">
                            Our cookie policy complies with the requirements of various international and national laws on the protection of personal data. These laws establish standards for the processing and protection of personal information of users.
                        </p>
                        <div class="space-y-4">
                            <div class="bg-white/60 rounded-lg p-4 border border-amber-200/30">
                                <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                    International standards
                                </h4>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                        <div>
                                            <span class="font-medium text-gray-900">GDPR (EU)</span>
                                            <span class="text-sm text-gray-600 ml-2">General Data Protection Regulation</span>
                                        </div>
                                        <a href="https://gdpr.eu" target="_blank" class="text-amber-600 hover:text-amber-700 text-sm font-medium flex items-center">
                                            More information
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                        <div>
                                            <span class="font-medium text-gray-900">CCPA (California)</span>
                                            <span class="text-sm text-gray-600 ml-2">California Consumer Privacy Act</span>
                                        </div>
                                        <a href="https://oag.ca.gov/privacy/ccpa" target="_blank" class="text-amber-600 hover:text-amber-700 text-sm font-medium flex items-center">
                                            More information
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                        <div>
                                            <span class="font-medium text-gray-900">LGPD (Brazil)</span>
                                            <span class="text-sm text-gray-600 ml-2">Lei Geral de Proteção de Dados</span>
                                        </div>
                                        <a href="https://www.gov.br/lgpd" target="_blank" class="text-amber-600 hover:text-amber-700 text-sm font-medium flex items-center">
                                            More information
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                        <div>
                                            <span class="font-medium text-gray-900">PIPEDA (Canada)</span>
                                            <span class="text-sm text-gray-600 ml-2">Personal Information Protection and Electronic Documents Act</span>
                                        </div>
                                        <a href="https://www.priv.gc.ca/en/privacy-topics/privacy-laws-in-canada/the-personal-information-protection-and-electronic-documents-act-pipeda/" target="_blank" class="text-amber-600 hover:text-amber-700 text-sm font-medium flex items-center">
                                            More information
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                        <div>
                                            <span class="font-medium text-gray-900">UK GDPR (United Kingdom)</span>
                                            <span class="text-sm text-gray-600 ml-2">UK General Data Protection Regulation</span>
                                        </div>
                                        <a href="https://www.gov.uk/data-protection" target="_blank" class="text-amber-600 hover:text-amber-700 text-sm font-medium flex items-center">
                                            More information
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                        <div>
                                            <span class="font-medium text-gray-900">Privacy Act (Australia)</span>
                                            <span class="text-sm text-gray-600 ml-2">Privacy Act 1988</span>
                                        </div>
                                        <a href="https://www.oaic.gov.au/privacy/privacy-legislation/the-privacy-act" target="_blank" class="text-amber-600 hover:text-amber-700 text-sm font-medium flex items-center">
                                            More information
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                        <div>
                                            <span class="font-medium text-gray-900">PDPL (UAE)</span>
                                            <span class="text-sm text-gray-600 ml-2">Personal Data Protection Law</span>
                                        </div>
                                        <a href="https://u.ae/en/about-the-uae/digital-uae/data/data-protection-laws" target="_blank" class="text-amber-600 hover:text-amber-700 text-sm font-medium flex items-center">
                                            More information
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                        <div>
                                            <span class="font-medium text-gray-900">PIPL (China)</span>
                                            <span class="text-sm text-gray-600 ml-2">Personal Information Protection Law</span>
                                        </div>
                                        <a href="https://personalinformationprotectionlaw.com/" target="_blank" class="text-amber-600 hover:text-amber-700 text-sm font-medium flex items-center">
                                            More information
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                        <div>
                                            <span class="font-medium text-gray-900">APPI (Japan)</span>
                                            <span class="text-sm text-gray-600 ml-2">Act on the Protection of Personal Information</span>
                                        </div>
                                        <a href="https://www.ppc.go.jp/en/legal/" target="_blank" class="text-amber-600 hover:text-amber-700 text-sm font-medium flex items-center">
                                            More information
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                        <div>
                                            <span class="font-medium text-gray-900">PIPA (South Korea)</span>
                                            <span class="text-sm text-gray-600 ml-2">Personal Information Protection Act</span>
                                        </div>
                                        <a href="https://www.pipc.go.kr" target="_blank" class="text-amber-600 hover:text-amber-700 text-sm font-medium flex items-center">
                                            More information
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                        <div>
                                            <span class="font-medium text-gray-900">152-Federal Law (Russia)</span>
                                            <span class="text-sm text-gray-600 ml-2">Law on personal data</span>
                                        </div>
                                        <a href="https://pd.rkn.gov.ru" target="_blank" class="text-amber-600 hover:text-amber-700 text-sm font-medium flex items-center">
                                            More information
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 bg-amber-50/80 rounded-lg p-4 border border-amber-200/50">
                            <p class="text-sm text-gray-700">
                                <strong>Note:</strong> We strive to comply with the strictest data protection standards, regardless of the geographical location of our users. If necessary, we can add support for additional regional laws.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Updates -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-amber-600/5 to-orange-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </div>
                                Policy updates
                            </h3>
                            <span class="text-xs font-medium text-amber-600 bg-amber-100 px-2 py-1 rounded-full">History</span>
                        </div>
                        <p class="text-gray-700 leading-relaxed">
                            We may update this policy from time to time to reflect changes in our service or legislation. All changes will be published on this page with the date of the last update.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../../layouts/base.php';