<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

$page_title = 'Terms of use RooCMS';
$page_description = 'Terms of use RooCMS';

$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [];

ob_start();
?>

<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Conditions of use RooCMS</h1>
        <p class="text-lg text-gray-600">Rules and conditions of using the content management system RooCMS</p>
    </div>

    <div class="bg-white shadow-lg rounded-lg border border-gray-200 p-8 space-y-8">
        
        <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. General provisions</h2>
            <div class="text-gray-700 space-y-3">
                <p>The present conditions of use (hereinafter — "Conditions") regulate the use of your content management system RooCMS (hereinafter — "System").</p>
                <p>By using RooCMS, you agree to the present Conditions. If you do not agree with the Conditions, please do not use the System.</p>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. License</h2>
            <div class="text-gray-700 space-y-3">
                <p>RooCMS distributed under the GNU General Public License version 3 (GPLv3).</p>
                <p>You have the right to use, copy, modify and distribute the System in accordance with the terms of this license.</p>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. Using the system</h2>
            <div class="text-gray-700 space-y-3">
                <p>When using RooCMS you agree to:</p>
                <ul class="list-disc list-inside space-y-2 ml-4">
                    <li>Not violate laws and regulations</li>
                    <li>Not use the System to distribute harmful software</li>
                    <li>Not try to bypass security systems</li>
                    <li>Respect the copyright of third parties</li>
                </ul>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Technical support</h2>
            <div class="text-gray-700 space-y-3">
                <p>Technical support for RooCMS is provided on a voluntary basis through official communication channels.</p>
                <p>Developers are not responsible for direct or indirect losses associated with the use of the System.</p>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Changes to the conditions</h2>
            <div class="text-gray-700 space-y-3">
                <p>The present Conditions may be changed by the developers at any time without prior notice.</p>
                <p>Continuing to use RooCMS after changes means your agreement with the new conditions.</p>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Contact information</h2>
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