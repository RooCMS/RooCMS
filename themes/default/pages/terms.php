<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

$page_title = 'Terms of Use - RooCMS Content Management System';
$page_description = 'Official terms of use for RooCMS - free, open-source content management system. Learn about licensing, usage guidelines, technical support, and data privacy policies.';
$page_keywords = 'RooCMS, terms of use, license, GPL, content management system, free software, open source, technical support, privacy policy';

$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [];

ob_start();
?>

<div class="min-h-full py-8 sm:py-16 lg:py-8">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Terms Header -->
        <div class="text-center relative">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-100/80 to-indigo-50/80 rounded-3xl blur-3xl -z-10"></div>
            <div class="relative">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                    Terms of Use
                </h1>
                <p class="mt-3 text-lg text-gray-600 max-w-2xl mx-auto">
                    Rules and conditions for using the RooCMS content management system
                </p>
                <div class="mt-4 flex justify-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 border border-blue-200">
                        <div class="w-2 h-2 bg-blue-400 rounded-full mr-2 animate-pulse"></div>
                        Legal Document
                    </span>
                </div>
            </div>
    </div>

        <!-- Terms Content -->
        <div class="bg-white/50 backdrop-blur-sm py-8 px-6 shadow-sm rounded-2xl border border-gray-200/50">
            <div class="space-y-8">
        
                <!-- General Provisions -->
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
                                1. General Provisions
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 rounded-full text-sm font-medium border border-blue-200">
                                Foundation
                            </div>
                        </div>

                        <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm">
                            <div class="text-gray-700 space-y-4">
                <p>The present conditions of use (hereinafter — "Conditions") regulate the use of your content management system RooCMS (hereinafter — "System").</p>
                <p>By using RooCMS, you agree to the present Conditions. If you do not agree with the Conditions, please do not use the System.</p>
            </div>
                        </div>
                    </div>
                </div>

                <!-- License -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-600/5 to-emerald-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-6 border border-green-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-green-500 to-emerald-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                2. License & Rights
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 rounded-full text-sm font-medium border border-green-200">
                                GPLv3
                            </div>
                        </div>

                        <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm space-y-4">
                            <p>RooCMS is distributed under the <a href="https://www.gnu.org/licenses/gpl-3.0.html" class="text-blue-600 hover:text-blue-800 underline font-medium" target="_blank" rel="noopener noreferrer">GNU General Public License version 3 (GPLv3)</a> - a free software license that guarantees end users the freedom to run, study, share and modify the software.</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-900 text-lg">Your Rights:</h4>
                                    <ul class="space-y-2">
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Use:</strong> Run the system for any purpose without restrictions</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Study:</strong> Access and examine the source code to understand how it works</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Share:</strong> Distribute copies of the software to others</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-900 text-lg">Your Responsibilities:</h4>
                                    <ul class="space-y-2">
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-emerald-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Modify:</strong> Adapt and improve the system to meet your needs</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-emerald-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Distribute modifications:</strong> Share your modified versions under the same license</span>
                                        </li>
                </ul>
                                </div>
                            </div>

                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                                <div class="flex items-start">
                                    <div class="w-2 h-2 bg-yellow-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                    <div>
                    <strong>Important:</strong> If you distribute modified versions of RooCMS, you must make the source code available under the GPLv3 license and keep copyright notices intact.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Usage -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-600/5 to-pink-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-purple-500 to-pink-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                3. Usage Guidelines
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 rounded-full text-sm font-medium border border-purple-200">
                                Rules
                            </div>
            </div>

                        <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm space-y-6">
                            <p class="text-gray-700 font-medium">When using RooCMS you agree to:</p>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <h4 class="font-semibold text-gray-900 text-lg flex items-center">
                                        <div class="w-2 h-2 bg-green-400 rounded-full mr-3"></div>
                                        Acceptable Use
                                    </h4>
                                    <ul class="space-y-3">
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Legal compliance:</strong> Not violate local, national or international laws and regulations</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Security:</strong> Not attempt to gain unauthorized access to system resources or other users' accounts</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Content responsibility:</strong> Take full responsibility for all content published through the system</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Copyright:</strong> Respect intellectual property rights and not infringe on third-party copyrights</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>System integrity:</strong> Not modify or interfere with the normal operation of the system</span>
                                        </li>
                </ul>
                                </div>

                                <div class="space-y-4">
                                    <h4 class="font-semibold text-gray-900 text-lg flex items-center">
                                        <div class="w-2 h-2 bg-red-400 rounded-full mr-3"></div>
                                        Prohibited Activities
                                    </h4>
                                    <ul class="space-y-3">
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-red-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Malware:</strong> Not use the system to distribute viruses, trojans, or other malicious software</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-red-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Spam:</strong> Not send unsolicited commercial communications or spam</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-red-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Exploitation:</strong> Not exploit security vulnerabilities in the system</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-red-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Resource abuse:</strong> Not overload system resources or engage in denial-of-service attacks</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-red-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span><strong>Commercial restrictions:</strong> Not use the system for commercial purposes without proper licensing</span>
                                        </li>
                </ul>
                                </div>
                            </div>

                            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                                <div class="flex items-start">
                                    <div class="w-2 h-2 bg-red-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                    <div>
                    <strong>Violation consequences:</strong> Users who violate these terms may have their access suspended or terminated without prior notice. In severe cases, violations may be reported to appropriate authorities.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technical Support -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-amber-600/5 to-orange-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl p-6 border border-amber-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                4. Technical Support
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-amber-100 to-orange-100 text-amber-800 rounded-full text-sm font-medium border border-amber-200">
                                Community
                            </div>
                        </div>

                        <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm space-y-4">
                            <p class="text-gray-700">Technical support for RooCMS is provided on a voluntary basis through official communication channels.</p>
                            <p class="text-gray-700">Developers are not responsible for direct or indirect losses associated with the use of the System.</p>

                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                                <div class="flex items-start">
                                    <div class="w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                    <div>
                                        <strong>Support Channels:</strong> Get help through our community forums, documentation, or GitHub issues. Our volunteer community is here to assist with questions and troubleshooting.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>

                <!-- Changes to Conditions -->
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
                                5. Changes to Conditions
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-cyan-100 to-teal-100 text-cyan-800 rounded-full text-sm font-medium border border-cyan-200">
                                Updates
                            </div>
            </div>

                        <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm space-y-4">
                            <p class="text-gray-700">The present Conditions may be changed by the developers at any time without prior notice.</p>
                            <p class="text-gray-700">Continuing to use RooCMS after changes means your agreement with the new conditions.</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-900 text-lg">Notification Process</h4>
                                    <p class="text-gray-600 text-sm">While not required, we strive to:</p>
                                    <ul class="space-y-2">
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-cyan-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm">Announce major changes through our website and communication channels</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-cyan-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm">Provide reasonable transition periods for significant modifications</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="space-y-3">
                                    <h4 class="font-semibold text-gray-900 text-lg">Version Control</h4>
                                    <p class="text-gray-600 text-sm">We maintain:</p>
                                    <ul class="space-y-2">
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-teal-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm">A changelog of important updates</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-teal-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm">Clearly mark the last updated date on this page</span>
                                        </li>
                </ul>
                                </div>
                            </div>

                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                                <div class="flex items-start">
                                    <div class="w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                    <div>
                    <strong>Recommendation:</strong> We recommend periodically reviewing these terms to stay informed about any changes. The "Last Updated" date at the bottom of this page indicates when the latest revision was made.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Privacy -->
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
                                6. Data Privacy & Protection
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-800 rounded-full text-sm font-medium border border-emerald-200">
                                Security
                            </div>
            </div>

                        <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm space-y-6">
                            <p class="text-gray-700">RooCMS respects your privacy and is committed to protecting your personal information. Our approach to data privacy includes:</p>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <h4 class="font-semibold text-gray-900 text-lg flex items-center">
                                        <div class="w-2 h-2 bg-emerald-400 rounded-full mr-3"></div>
                                        Data Collection
                                    </h4>
                                    <p class="text-gray-600 text-sm">We may collect limited information necessary for system operation:</p>
                                    <ul class="space-y-3">
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-emerald-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm"><strong>System logs:</strong> Technical information for debugging and security purposes</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-emerald-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm"><strong>User accounts:</strong> Registration information provided voluntarily</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-emerald-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm"><strong>Usage statistics:</strong> Anonymous analytics to improve system performance</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-emerald-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm"><strong>Error reports:</strong> Technical data when issues occur</span>
                                        </li>
                </ul>
                                </div>

                                <div class="space-y-4">
                                    <h4 class="font-semibold text-gray-900 text-lg flex items-center">
                                        <div class="w-2 h-2 bg-teal-400 rounded-full mr-3"></div>
                                        Data Protection
                                    </h4>
                                    <p class="text-gray-600 text-sm">We implement reasonable security measures to protect your information:</p>
                                    <ul class="space-y-3">
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-teal-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm">Encryption of sensitive data in transit and at rest</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-teal-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm">Regular security updates and patches</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-teal-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm">Access controls and authentication mechanisms</span>
                                        </li>
                                        <li class="flex items-start">
                                            <div class="w-2 h-2 bg-teal-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                            <span class="text-sm">Secure coding practices and regular security audits</span>
                                        </li>
                </ul>
                                </div>
                            </div>

                            <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
                                <div class="flex items-start">
                                    <div class="w-2 h-2 bg-green-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                    <div>
                                        <strong>Privacy Policy:</strong> For detailed information about how we handle personal data, please review our <a href="/privacy" class="text-blue-600 hover:text-blue-800 underline font-medium">Privacy Policy</a>.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-600/5 to-gray-600/5 rounded-2xl"></div>
                    <div class="relative bg-gradient-to-br from-slate-50 to-gray-50 rounded-2xl p-6 border border-slate-200/50">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-slate-500 to-gray-500 mr-3 shadow-sm">
                                    <svg class="w-5 h-5 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                7. Contact Information
                            </h3>
                            <div class="px-3 py-1 bg-gradient-to-r from-slate-100 to-gray-100 text-slate-800 rounded-full text-sm font-medium border border-slate-200">
                                Support
                            </div>
                        </div>

                        <div class="bg-white/80 backdrop-blur-sm p-5 rounded-xl border border-gray-200/50 shadow-sm space-y-4">
                            <p class="text-gray-700">For questions related to the use of RooCMS, please contact:</p>
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
                            <a href="https://www.roocms.com" target="_blank" class="group flex items-center justify-center p-4 bg-white/80 backdrop-blur-sm rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                                <div class="text-center">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-blue-500 to-indigo-500 mx-auto mb-2 shadow-sm group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">Visit Website</div>
                                    <div class="text-xs text-gray-500 mt-1">Official site</div>
                                </div>
                            </a>

                            <a href="mailto:info@roocms.com" class="group flex items-center justify-center p-4 bg-white/80 backdrop-blur-sm rounded-xl border border-gray-200/50 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1">
                                <div class="text-center">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-r from-green-500 to-emerald-500 mx-auto mb-2 shadow-lg group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-white py-0 translate-y-0.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">Contact Us</div>
                                    <div class="text-xs text-gray-500 mt-1">Get support</div>
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