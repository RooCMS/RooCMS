<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

$page_title = 'Terms of Use RooCMS';
$page_description = 'Terms for RooCMS';

$theme_name = basename(dirname(__DIR__, 2));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [
	$theme_base.'/assets/js/app/acp.js',
    $theme_base.'/assets/js/app/acp-access.js'
];

ob_start();
?>

<div class="py-10">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-[260px_1fr] px-4 sm:px-6 lg:px-8 space-y-8">
        <?php require __DIR__ . '/../../partials/acp-nav.php'; ?>

        <section>
            <header class="mb-8">
                <nav class="mb-3 text-sm text-zinc-500" aria-label="Breadcrumbs">
                    <ol class="flex items-center gap-2">
                        <li><a href="/" class="hover:text-zinc-700">Home</a></li>
                        <li aria-hidden="true" class="text-zinc-400">/</li>
                        <li><a href="/!/acp" class="hover:text-zinc-700">ACP</a></li>
                        <li aria-hidden="true" class="text-zinc-400">/</li>
                        <li><span class="text-zinc-700">Terms</span></li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Terms of Use</h1>
                <p class="mt-2 text-sm text-zinc-600">Rules and conditions for using RooCMS</p>
            </header>

            <!-- Terms Content Container -->
            <div class="space-y-8">

                <!-- Terms Content -->
                <div class="space-y-6">

                    <!-- General Provisions -->
                    <section>
                        <h2 class="mb-4 text-base font-semibold text-zinc-900">1. General Provisions</h2>
                        <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
                            <div class="text-sm text-zinc-600 space-y-3">
                                <p>These conditions of use (hereinafter — "Conditions") regulate the use of RooCMS (hereinafter — "System").</p>
                                <p>By using this system, you agree to these Conditions. If you do not agree with the Conditions, please do not use the System.</p>
                            </div>
                        </div>
                    </section>

                    <!-- License -->
                    <section>
                        <h2 class="mb-4 text-base font-semibold text-zinc-900">2. License & Rights</h2>
                        <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
                            <div class="space-y-4">
                                <p class="text-sm text-zinc-600">RooCMS is distributed under the <a href="https://www.gnu.org/licenses/gpl-3.0.html" class="text-blue-600 hover:text-blue-800 underline font-medium" target="_blank" rel="noopener noreferrer">GNU General Public License version 3 (GPLv3)</a> - a free software license that guarantees end users the freedom to run, study, share and modify the software.</p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-3">
                                        <h3 class="text-sm font-semibold text-zinc-900">Your Rights:</h3>
                                        <ul class="space-y-2 text-sm text-zinc-600">
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span><strong>Use:</strong> Run the system for any purpose without restrictions</span>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span><strong>Study:</strong> Access and examine the source code to understand how it works</span>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span><strong>Share:</strong> Distribute copies of the software to others</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="space-y-3">
                                        <h3 class="text-sm font-semibold text-zinc-900">Your Responsibilities:</h3>
                                        <ul class="space-y-2 text-sm text-zinc-600">
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span><strong>Modify:</strong> Adapt and improve the system to meet your needs</span>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span><strong>Distribute modifications:</strong> Share your modified versions under the same license</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <blockquote class="rounded-xl border-l-4 border-amber-500 bg-amber-50 p-4 text-sm text-amber-900">
                                    <p><strong>Important:</strong> If you distribute modified versions of RooCMS, you must make the source code available under the GPLv3 license and keep copyright notices intact.</p>
                                </blockquote>
                            </div>
                        </div>
                    </section>

                    <!-- Usage -->
                    <section>
                        <h2 class="mb-4 text-base font-semibold text-zinc-900">3. Usage Guidelines</h2>
                        <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
                            <div class="space-y-6">
                                <p class="text-sm text-zinc-600 font-medium">When using RooCMS you agree to:</p>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div class="space-y-4">
                                        <h3 class="text-sm font-semibold text-zinc-900 flex items-center">
                                            <div class="w-2 h-2 bg-emerald-400 rounded-full mr-3"></div>
                                            Acceptable Use
                                        </h3>
                                        <ul class="space-y-3 text-sm text-zinc-600">
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span><strong>Legal compliance:</strong> Not violate local, national or international laws and regulations</span>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span><strong>Security:</strong> Not attempt to gain unauthorized access to system resources or other users' accounts</span>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span><strong>Content responsibility:</strong> Take full responsibility for all content published through the system</span>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span><strong>Copyright:</strong> Respect intellectual property rights and not infringe on third-party copyrights</span>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span><strong>System integrity:</strong> Not modify or interfere with the normal operation of the system</span>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="space-y-4">
                                        <h3 class="text-sm font-semibold text-zinc-900 flex items-center">
                                            <div class="w-2 h-2 bg-rose-400 rounded-full mr-3"></div>
                                            Prohibited Activities
                                        </h3>
                                        <ul class="space-y-3 text-sm text-zinc-600">
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span><strong>Malware:</strong> Not use the system to distribute viruses, trojans, or other malicious software</span>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span><strong>Spam:</strong> Not send unsolicited commercial communications or spam</span>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span><strong>Exploitation:</strong> Not exploit security vulnerabilities in the system</span>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span><strong>Resource abuse:</strong> Not overload system resources or engage in denial-of-service attacks</span>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span><strong>Commercial restrictions:</strong> Not use the system for commercial purposes without proper licensing</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <blockquote class="rounded-xl border-l-4 border-rose-500 bg-rose-50 p-4 text-sm text-rose-900">
                                    <p><strong>Violation consequences:</strong> Users who violate these terms may have their access suspended or terminated without prior notice. In severe cases, violations may be reported to appropriate authorities.</p>
                                </blockquote>
                            </div>
                        </div>
                    </section>

                    <!-- Technical Support -->
                    <section>
                        <h2 class="mb-4 text-base font-semibold text-zinc-900">4. Technical Support</h2>
                        <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
                            <div class="space-y-4">
                                <p class="text-sm text-zinc-600">Technical support for RooCMS is provided on a voluntary basis through official communication channels.</p>
                                <p class="text-sm text-zinc-600">Developers are not responsible for direct or indirect losses associated with the use of the System.</p>

                                <blockquote class="rounded-xl border-l-4 border-sky-500 bg-sky-50 p-4 text-sm text-sky-900">
                                    <p><strong>Support Channels:</strong> Get help through our community forums, documentation, or GitHub issues. Our volunteer community is here to assist with questions and troubleshooting.</p>
                                </blockquote>
                            </div>
                        </div>
                    </section>

                    <!-- Data Privacy -->
                    <section>
                        <h2 class="mb-4 text-base font-semibold text-zinc-900">5. Data Privacy & Protection</h2>
                        <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
                            <div class="space-y-6">
                                <p class="text-sm text-zinc-600">RooCMS respects your privacy and is committed to protecting your personal information. Our approach to data privacy includes:</p>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div class="space-y-4">
                                        <h3 class="text-sm font-semibold text-zinc-900">Data Collection</h3>
                                        <p class="text-xs text-zinc-500">We may collect limited information necessary for system operation:</p>
                                        <ul class="space-y-3 text-sm text-zinc-600">
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span><strong>System logs:</strong> Technical information for debugging and security purposes</span>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span><strong>Usage statistics:</strong> Anonymous analytics to improve system performance</span>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span><strong>Error reports:</strong> Technical data when issues occur</span>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="space-y-4">
                                        <h3 class="text-sm font-semibold text-zinc-900">Data Protection</h3>
                                        <p class="text-xs text-zinc-500">We implement reasonable security measures to protect your information:</p>
                                        <ul class="space-y-3 text-sm text-zinc-600">
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span>Encryption of sensitive data in transit and at rest</span>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span>Regular security updates and patches</span>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span>Access controls and authentication mechanisms</span>
                                            </li>
                                            <li class="flex items-start">
                                                <div class="w-1.5 h-1.5 bg-zinc-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                                                <span>Secure coding practices and regular security audits</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Contact Information -->
                    <section>
                        <h2 class="mb-4 text-base font-semibold text-zinc-900">6. Contact Information</h2>
                        <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
                            <div class="space-y-4">
                                <p class="text-sm text-zinc-600">For questions related to the use of RooCMS, please contact:</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <a href="https://roocms.com" target="_blank" class="flex items-center p-4 border border-zinc-200 rounded-lg hover:bg-zinc-50 transition-colors">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-100 mr-3">
                                            <svg class="w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-zinc-900">Website</div>
                                            <div class="text-xs text-zinc-500">https://roocms.com</div>
                                        </div>
                                    </a>

                                    <a href="mailto:info@roocms.com" class="flex items-center p-4 border border-zinc-200 rounded-lg hover:bg-zinc-50 transition-colors">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-zinc-100 mr-3">
                                            <svg class="w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-zinc-900">Email</div>
                                            <div class="text-xs text-zinc-500">info@roocms.com</div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </section>
    </div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../../layouts/base.php';
?>
