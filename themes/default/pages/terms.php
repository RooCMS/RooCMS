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

<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <!-- Breadcrumbs -->
    <nav class="mb-8" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2 text-sm text-gray-500" itemscope itemtype="https://schema.org/BreadcrumbList">
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="/" class="hover:text-gray-700" itemprop="item">
                    <span itemprop="name">Home</span>
                </a>
                <meta itemprop="position" content="1">
            </li>
            <li aria-hidden="true"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
            <li class="text-gray-900 font-medium" aria-current="page" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <span itemprop="name">Terms of Use</span>
                <meta itemprop="position" content="2">
            </li>
        </ol>
    </nav>

    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Conditions of use RooCMS</h1>
        <p class="text-lg text-gray-600">Rules and conditions of using the content management system RooCMS</p>
    </div>

    <div class="bg-white shadow-lg rounded-lg border border-gray-200 p-8 space-y-8">
        
        <section id="general" aria-labelledby="general-heading">
            <h2 id="general-heading" class="text-2xl font-semibold text-gray-900 mb-4">1. General provisions</h2>
            <div class="text-gray-700 space-y-3">
                <p>The present conditions of use (hereinafter — "Conditions") regulate the use of your content management system RooCMS (hereinafter — "System").</p>
                <p>By using RooCMS, you agree to the present Conditions. If you do not agree with the Conditions, please do not use the System.</p>
            </div>
        </section>

        <section id="license" aria-labelledby="license-heading">
            <h2 id="license-heading" class="text-2xl font-semibold text-gray-900 mb-4">2. License</h2>
            <div class="text-gray-700 space-y-3">
                <p>RooCMS is distributed under the <a href="https://www.gnu.org/licenses/gpl-3.0.html" class="text-blue-600 hover:text-blue-800 underline" target="_blank" rel="noopener noreferrer">GNU General Public License version 3 (GPLv3)</a> - a free software license that guarantees end users the freedom to run, study, share and modify the software.</p>
                <p>You have the right to:</p>
                <ul class="list-disc list-inside space-y-2 ml-4">
                    <li><strong>Use:</strong> Run the system for any purpose without restrictions</li>
                    <li><strong>Study:</strong> Access and examine the source code to understand how it works</li>
                    <li><strong>Share:</strong> Distribute copies of the software to others</li>
                    <li><strong>Modify:</strong> Adapt and improve the system to meet your needs</li>
                    <li><strong>Distribute modifications:</strong> Share your modified versions under the same license</li>
                </ul>
                <p class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-4">
                    <strong>Important:</strong> If you distribute modified versions of RooCMS, you must make the source code available under the GPLv3 license and keep copyright notices intact.
                </p>
            </div>
        </section>

        <section id="usage" aria-labelledby="usage-heading">
            <h2 id="usage-heading" class="text-2xl font-semibold text-gray-900 mb-4">3. Using the system</h2>
            <div class="text-gray-700 space-y-3">
                <p>When using RooCMS you agree to:</p>

                <h3 class="text-lg font-semibold text-gray-900 mt-6 mb-3">Acceptable Use</h3>
                <ul class="list-disc list-inside space-y-2 ml-4">
                    <li><strong>Legal compliance:</strong> Not violate local, national or international laws and regulations</li>
                    <li><strong>Security:</strong> Not attempt to gain unauthorized access to system resources or other users' accounts</li>
                    <li><strong>Content responsibility:</strong> Take full responsibility for all content published through the system</li>
                    <li><strong>Copyright:</strong> Respect intellectual property rights and not infringe on third-party copyrights</li>
                    <li><strong>System integrity:</strong> Not modify or interfere with the normal operation of the system</li>
                </ul>

                <h3 class="text-lg font-semibold text-gray-900 mt-6 mb-3">Prohibited Activities</h3>
                <ul class="list-disc list-inside space-y-2 ml-4">
                    <li><strong>Malware:</strong> Not use the system to distribute viruses, trojans, or other malicious software</li>
                    <li><strong>Spam:</strong> Not send unsolicited commercial communications or spam</li>
                    <li><strong>Exploitation:</strong> Not exploit security vulnerabilities in the system</li>
                    <li><strong>Resource abuse:</strong> Not overload system resources or engage in denial-of-service attacks</li>
                    <li><strong>Commercial restrictions:</strong> Not use the system for commercial purposes without proper licensing</li>
                </ul>

                <p class="bg-red-50 border-l-4 border-red-400 p-4 mt-4">
                    <strong>Violation consequences:</strong> Users who violate these terms may have their access suspended or terminated without prior notice. In severe cases, violations may be reported to appropriate authorities.
                </p>
            </div>
        </section>

        <section id="support" aria-labelledby="support-heading">
            <h2 id="support-heading" class="text-2xl font-semibold text-gray-900 mb-4">4. Technical support</h2>
            <div class="text-gray-700 space-y-3">
                <p>Technical support for RooCMS is provided on a voluntary basis through official communication channels.</p>
                <p>Developers are not responsible for direct or indirect losses associated with the use of the System.</p>
            </div>
        </section>

        <section id="changes" aria-labelledby="changes-heading">
            <h2 id="changes-heading" class="text-2xl font-semibold text-gray-900 mb-4">5. Changes to the conditions</h2>
            <div class="text-gray-700 space-y-3">
                <p>The present Conditions may be changed by the developers at any time without prior notice.</p>
                <p>Continuing to use RooCMS after changes means your agreement with the new conditions.</p>

                <h3 class="text-lg font-semibold text-gray-900 mt-6 mb-3">Notification Process</h3>
                <p>While not required, we strive to:</p>
                <ul class="list-disc list-inside space-y-2 ml-4">
                    <li>Announce major changes through our website and communication channels</li>
                    <li>Provide reasonable transition periods for significant modifications</li>
                    <li>Maintain a changelog of important updates</li>
                    <li>Clearly mark the last updated date on this page</li>
                </ul>

                <p class="bg-blue-50 border-l-4 border-blue-400 p-4 mt-4">
                    <strong>Recommendation:</strong> We recommend periodically reviewing these terms to stay informed about any changes. The "Last Updated" date at the bottom of this page indicates when the latest revision was made.
                </p>
            </div>
        </section>

        <section id="privacy" aria-labelledby="privacy-heading">
            <h2 id="privacy-heading" class="text-2xl font-semibold text-gray-900 mb-4">6. Data privacy and protection</h2>
            <div class="text-gray-700 space-y-3">
                <p>RooCMS respects your privacy and is committed to protecting your personal information. Our approach to data privacy includes:</p>

                <h3 class="text-lg font-semibold text-gray-900 mt-6 mb-3">Data Collection</h3>
                <p>We may collect limited information necessary for system operation:</p>
                <ul class="list-disc list-inside space-y-2 ml-4">
                    <li><strong>System logs:</strong> Technical information for debugging and security purposes</li>
                    <li><strong>User accounts:</strong> Registration information provided voluntarily</li>
                    <li><strong>Usage statistics:</strong> Anonymous analytics to improve system performance</li>
                    <li><strong>Error reports:</strong> Technical data when issues occur</li>
                </ul>

                <h3 class="text-lg font-semibold text-gray-900 mt-6 mb-3">Data Protection</h3>
                <p>We implement reasonable security measures to protect your information:</p>
                <ul class="list-disc list-inside space-y-2 ml-4">
                    <li>Encryption of sensitive data in transit and at rest</li>
                    <li>Regular security updates and patches</li>
                    <li>Access controls and authentication mechanisms</li>
                    <li>Secure coding practices and regular security audits</li>
                </ul>

                <p class="bg-green-50 border-l-4 border-green-400 p-4 mt-4">
                    <strong>Privacy Policy:</strong> For detailed information about how we handle personal data, please review our <a href="/privacy" class="text-blue-600 hover:text-blue-800 underline">Privacy Policy</a>.
                </p>
            </div>
        </section>

        <section id="contact" aria-labelledby="contact-heading">
            <h2 id="contact-heading" class="text-2xl font-semibold text-gray-900 mb-4">7. Contact information</h2>
            <div class="text-gray-700 space-y-3">
                <p>For questions related to the use of RooCMS, please contact:</p>
                <ul class="space-y-2">
                    <li><strong>Website:</strong> <a href="https://www.roocms.com" class="text-blue-600 hover:text-blue-500">www.roocms.com</a></li>
                    <li><strong>Email:</strong> <a href="mailto:info@roocms.com" class="text-blue-600 hover:text-blue-500">info@roocms.com</a></li>
                </ul>
            </div>
        </section>

        <div class="border-t border-gray-200 pt-8 mt-8">
            <p class="text-sm text-gray-500 text-center">
                © 2010-2025 alexandr Belov aka alex Roosso. All rights reserved.
            </p>
        </div>
    </div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';