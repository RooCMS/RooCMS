<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

$page_title = 'Privacy policy RooCMS';
$page_description = 'Privacy policy RooCMS';

$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [];

ob_start();
?>

<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Privacy policy RooCMS</h1>
        <p class="text-lg text-gray-600">How we collect, use and protect your personal information</p>
    </div>

    <div class="bg-white shadow-lg rounded-lg border border-gray-200 p-8 space-y-8">

        <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">1. General information</h2>
            <div class="text-gray-700 space-y-3">
                <p>The present privacy policy (hereinafter — "Policy") describes how RooCMS collects, uses and protects your personal information.</p>
                <p>We respect your privacy and strive to protect your personal information in accordance with applicable data protection laws.</p>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">2. Collected information</h2>
            <div class="text-gray-700 space-y-3">
                <p>When using RooCMS we can collect the following types of information:</p>
                <ul class="list-disc list-inside space-y-2 ml-4">
                    <li><strong>Personal information:</strong> name, email, contact data at registration</li>
                    <li><strong>Technical information:</strong> IP address, browser type, operating system</li>
                    <li><strong>Usage data:</strong> pages you visit, time spent</li>
                    <li><strong>Cookies:</strong> to improve the work of the website and analyze traffic</li>
                </ul>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">3. Information use</h2>
            <div class="text-gray-700 space-y-3">
                <p>The collected information is used for:</p>
                <ul class="list-disc list-inside space-y-2 ml-4">
                    <li>Providing and improving our services</li>
                    <li>Ensuring system security</li>
                    <li>Analyzing usage to improve functionality</li>
                    <li>Sending important notifications about the system</li>
                    <li>Preventing fraud and abuse</li>
                </ul>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">4. Information transfer</h2>
            <div class="text-gray-700 space-y-3">
                <p>We do not sell, exchange or transfer your personal information to third parties, except in the following cases:</p>
                <ul class="list-disc list-inside space-y-2 ml-4">
                    <li>With your explicit consent</li>
                    <li>To comply with legal requirements</li>
                    <li>To protect our rights and security</li>
                    <li>When transferring business or assets</li>
                </ul>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">5. Cookies and tracking technologies</h2>
            <div class="text-gray-700 space-y-3">
                <p>We use cookies to improve your experience of using the website. You can disable cookies in the browser settings, however this may affect the functionality of the website.</p>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">6. Data security</h2>
            <div class="text-gray-700 space-y-3">
                <p>We take reasonable measures to protect your personal information from unauthorized access, modification, disclosure or destruction.</p>
                <p>However, no method of transmitting data through the internet or electronic storage is 100% secure.</p>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">7. Your rights</h2>
            <div class="text-gray-700 space-y-3">
                <p>You have the right:</p>
                <ul class="list-disc list-inside space-y-2 ml-4">
                    <li>Get access to your personal information</li>
                    <li>Correct incorrect information</li>
                    <li>Request deletion of your data</li>
                    <li>Refuse to certain types of data processing</li>
                </ul>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">8. Changes to the policy</h2>
            <div class="text-gray-700 space-y-3">
                <p>We may update the present Privacy Policy from time to time. Changes take effect immediately after publication on the website.</p>
                <p>We recommend periodically checking this page for the latest information.</p>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">9. Contacts</h2>
            <div class="text-gray-700 space-y-3">
                <p>If you have questions about the present Privacy Policy, please contact us:</p>
                <ul class="space-y-2">
                    <li><strong>Website:</strong> <a href="https://www.roocms.com" class="text-blue-600 hover:text-blue-500">www.roocms.com</a></li>
                    <li><strong>Email:</strong> <a href="mailto:info@roocms.com" class="text-blue-600 hover:text-blue-500">info@roocms.com</a></li>
                </ul>
            </div>
        </section>

        <div class="border-t border-gray-200 pt-8 mt-8">
            <p class="text-sm text-gray-500 text-center">
                © 2010-2025 alexandr Belov aka alex Roosso. All right reserved.
            </p>
        </div>
    </div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';