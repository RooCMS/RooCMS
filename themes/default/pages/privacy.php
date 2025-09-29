<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

$page_title = 'Privacy policy RooCMS';
$page_description = 'Privacy policy RooCMS';

$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [$theme_base.'/assets/js/pages/privacy.js'];

ob_start();
?>

<div class="min-h-full py-8 sm:py-16 lg:py-8">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Privacy Header -->
        <div class="text-center relative">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-100/80 to-teal-50/80 rounded-3xl blur-3xl -z-10"></div>
            <div class="relative">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                    Privacy Policy
                </h1>
                <p class="mt-3 text-lg text-gray-600 max-w-2xl mx-auto">
                    How we collect, use and protect your personal information
                </p>
                <div class="mt-4 flex justify-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-800 border border-emerald-200">
                        <div class="w-2 h-2 bg-emerald-400 rounded-full mr-2 animate-pulse"></div>
                        Privacy Protected
                    </span>
                </div>
            </div>
        </div>

        <!-- Privacy Content -->
        <div class="bg-white/50 backdrop-blur-sm py-8 px-6 shadow-sm rounded-2xl border border-gray-200/50">
            <div class="space-y-8">

                <!-- General Information -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-emerald-600/5 to-teal-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-6 border border-emerald-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-emerald-500 to-teal-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                1. General Information
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-800 rounded-full text-sm font-medium border border-emerald-200">
                                Overview
                            </div>
                        </div>

                        <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm space-y-4">
                            <p class="text-gray-700">The present privacy policy (hereinafter — "Policy") describes how RooCMS collects, uses and protects your personal information.</p>
                            <p class="text-gray-700">We respect your privacy and strive to protect your personal information in accordance with applicable data protection laws.</p>

                            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
                                <div class="flex items-start">
                                    <div class="w-2 h-2 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                    <div>
                                        <strong>Commitment:</strong> Your privacy is our priority. We are committed to transparency and protecting your personal data in compliance with international privacy standards.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Collected Information -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600/5 to-indigo-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-indigo-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                2. Information We Collect
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 rounded-full text-sm font-medium border border-blue-200">
                                Data Types
                            </div>
    </div>

                        <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm space-y-4">
                            <p class="text-gray-700">When using RooCMS we can collect the following types of information:</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-900 text-lg flex items-center">
                                        <div class="w-2 h-2 bg-blue-400 rounded-full mr-3"></div>
                                        Personal Data
                                    </h4>
                                    <ul class="space-y-2">
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm"><strong>Registration:</strong> Name, email, contact data provided voluntarily</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm"><strong>Profile:</strong> Personal information you choose to share</span>
                                        </li>
                                    </ul>
            </div>

                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-900 text-lg flex items-center">
                                        <div class="w-2 h-2 bg-indigo-400 rounded-full mr-3"></div>
                                        Technical Data
                                    </h4>
                                    <ul class="space-y-2">
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-indigo-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm"><strong>Device info:</strong> IP address, browser type, operating system</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-indigo-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm"><strong>Usage patterns:</strong> Pages visited, time spent, interactions</span>
                                        </li>
                </ul>
            </div>
                            </div>

                            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-lg">
                                <div class="flex items-start">
                                    <div class="w-2 h-2 bg-amber-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                    <div>
                                        <strong>Cookies:</strong> We use cookies to improve website functionality and analyze traffic. You can manage cookie preferences in your browser settings.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Information Use -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600/5 to-pink-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                3. How We Use Your Information
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 rounded-full text-sm font-medium border border-purple-200">
                                Purpose
                            </div>
                        </div>

                        <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm space-y-4">
                            <p class="text-gray-700">The collected information is used for:</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-900 text-lg flex items-center">
                                        <div class="w-2 h-2 bg-purple-400 rounded-full mr-3"></div>
                                        Service Delivery
                                    </h4>
                                    <ul class="space-y-2">
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-purple-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm">Providing and improving our services</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-purple-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm">Ensuring system security and stability</span>
                                        </li>
                </ul>
            </div>

                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-900 text-lg flex items-center">
                                        <div class="w-2 h-2 bg-pink-400 rounded-full mr-3"></div>
                                        System Enhancement
                                    </h4>
                                    <ul class="space-y-2">
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-pink-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm">Analyzing usage to improve functionality</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-pink-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm">Preventing fraud and abuse</span>
                                        </li>
                </ul>
            </div>
                            </div>

                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                                <div class="flex items-start">
                                    <div class="w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                    <div>
                                        <strong>Communications:</strong> We may send important notifications about system updates, security alerts, and service-related announcements to keep you informed.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Information Transfer -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-600/5 to-emerald-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-green-500 to-emerald-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                </div>
                                4. Information Sharing
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 rounded-full text-sm font-medium border border-green-200">
                                Privacy
                            </div>
            </div>

                        <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm space-y-4">
                            <p class="text-gray-700">We do not sell, exchange or transfer your personal information to third parties, except in the following cases:</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-900 text-lg flex items-center">
                                        <div class="w-2 h-2 bg-green-400 rounded-full mr-3"></div>
                                        Authorized Sharing
                                    </h4>
                                    <ul class="space-y-2">
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm"><strong>Consent:</strong> With your explicit consent</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm"><strong>Legal compliance:</strong> To comply with legal requirements</span>
                                        </li>
                                    </ul>
            </div>

                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-900 text-lg flex items-center">
                                        <div class="w-2 h-2 bg-emerald-400 rounded-full mr-3"></div>
                                        Security & Business
                                    </h4>
                                    <ul class="space-y-2">
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-emerald-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm"><strong>Protection:</strong> To protect our rights and security</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-emerald-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm"><strong>Business transfer:</strong> When transferring business or assets</span>
                                        </li>
                </ul>
            </div>
                            </div>

                            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
                                <div class="flex items-start">
                                    <div class="w-2 h-2 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                    <div>
                                        <strong>Your Data, Your Control:</strong> We respect your privacy and only share your information when absolutely necessary or with your explicit permission.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cookies and Tracking -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-amber-600/5 to-orange-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                5. Cookies & Tracking
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-amber-100 to-orange-100 text-amber-800 rounded-full text-sm font-medium border border-amber-200">
                                Preferences
                            </div>
                        </div>

                        <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm space-y-4">
                            <p class="text-gray-700">We use cookies to improve your experience of using the website. You can disable cookies in the browser settings, however this may affect the functionality of the website.</p>

                            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-lg">
                                <div class="flex items-start">
                                    <div class="w-2 h-2 bg-amber-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                    <div>
                                        <strong>Cookie Control:</strong> You have full control over cookie settings. Visit your browser's privacy settings to manage or disable cookies. Note that some features may not work properly without cookies enabled.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Security -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-600/5 to-gray-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-slate-50 to-gray-50 rounded-2xl p-6 border border-slate-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-slate-500 to-gray-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                6. Data Security
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-slate-100 to-gray-100 text-slate-800 rounded-full text-sm font-medium border border-slate-200">
                                Protection
                            </div>
                        </div>

                        <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm space-y-4">
                            <p class="text-gray-700">We take reasonable measures to protect your personal information from unauthorized access, modification, disclosure or destruction.</p>
                            <p class="text-gray-700">However, no method of transmitting data through the internet or electronic storage is 100% secure.</p>

                            <div class="bg-slate-50 border-l-4 border-slate-400 p-4 rounded-lg">
                                <div class="flex items-start">
                                    <div class="w-2 h-2 bg-slate-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                    <div>
                                        <strong>Security Limitations:</strong> While we implement industry-standard security measures, absolute security cannot be guaranteed. We continuously work to improve our security practices.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Your Rights -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-600/5 to-purple-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-6 border border-indigo-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-indigo-500 to-purple-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                7. Your Rights
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-800 rounded-full text-sm font-medium border border-indigo-200">
                                Control
                            </div>
            </div>

                        <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm space-y-4">
                            <p class="text-gray-700">You have the right:</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <ul class="space-y-3">
                                    <li class="flex items-start">
                                        <div class="w-2 h-2 bg-indigo-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                        <span class="text-sm"><strong>Access:</strong> Get access to your personal information</span>
                                    </li>
                                    <li class="flex items-start">
                                        <div class="w-2 h-2 bg-indigo-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                        <span class="text-sm"><strong>Correction:</strong> Correct incorrect information</span>
                                    </li>
                                </ul>
                                <ul class="space-y-3">
                                    <li class="flex items-start">
                                        <div class="w-2 h-2 bg-purple-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                        <span class="text-sm"><strong>Deletion:</strong> Request deletion of your data</span>
                                    </li>
                                    <li class="flex items-start">
                                        <div class="w-2 h-2 bg-purple-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                        <span class="text-sm"><strong>Objection:</strong> Refuse to certain types of data processing</span>
                                    </li>
                </ul>
            </div>
                        </div>
                    </div>
                </div>

                <!-- Policy Changes -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-600/5 to-teal-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-cyan-50 to-teal-50 rounded-2xl p-6 border border-cyan-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-cyan-500 to-teal-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </div>
                                8. Policy Changes
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-cyan-100 to-teal-100 text-cyan-800 rounded-full text-sm font-medium border border-cyan-200">
                                Updates
                            </div>
                        </div>

                        <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm space-y-4">
                            <p class="text-gray-700">We may update the present Privacy Policy from time to time. Changes take effect immediately after publication on the website.</p>
                            <p class="text-gray-700">We recommend periodically checking this page for the latest information.</p>

                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                                <div class="flex items-start">
                                    <div class="w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                    <div>
                                        <strong>Stay Informed:</strong> We will notify you of significant changes through our communication channels. Check back regularly for the most current version.
                                    </div>
                                </div>
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
                                9. Contact Us
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-800 rounded-full text-sm font-medium border border-emerald-200">
                                Support
                            </div>
                        </div>

                        <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm space-y-4">
                            <p class="text-gray-700">If you have questions about the present Privacy Policy, please contact us:</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <a href="https://www.roocms.com" target="_blank" class="group flex items-center p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-indigo-500 mr-3 group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-4 h-4 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">Website</div>
                                        <div class="text-sm text-gray-600">www.roocms.com</div>
                                    </div>
                                </a>

                                <a href="mailto:info@roocms.com" class="group flex items-center p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200 hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-green-500 to-emerald-500 mr-3 group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-4 h-4 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">Email</div>
                                        <div class="text-sm text-gray-600">info@roocms.com</div>
                                    </div>
                                </a>
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

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <a href="/terms" class="group flex items-center justify-center p-4 bg-white/80 backdrop-blur-sm rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                                <div class="text-center">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-indigo-500 mx-auto mb-2 shadow-sm group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">Terms of Use</div>
                                    <div class="text-xs text-gray-500 mt-1">Legal terms</div>
                                </div>
                            </a>

                            <a href="mailto:info@roocms.com" class="group flex items-center justify-center p-4 bg-white/80 backdrop-blur-sm rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                                <div class="text-center">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-emerald-500 mx-auto mb-2 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">Contact Support</div>
                                    <div class="text-xs text-gray-500 mt-1">Get help</div>
                                </div>
                            </a>
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

<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';