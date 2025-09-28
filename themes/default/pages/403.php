<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

http_response_code(403);
$page_title = 'Access denied — 403';

ob_start();
?>
<div class="min-h-full flex items-center justify-center px-12 py-12">
    <div class="max-w-md w-full text-center">
        <!-- 404 Number -->
        <div class="mb-8">
            <div class="text-9xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-4">
                403
            </div>
            <h1 class="text-4xl font-bold text-zinc-900 mb-4">Access denied</h1>
            <p class="text-lg text-zinc-600 mb-8 leading-relaxed">
                Oops! You don't have permission to access this page.
            </p>
        </div>

        <!-- Actions -->
        <div class="space-y-4">
            <a href="/" class="inline-flex items-center justify-center w-full px-6 py-3 text-base font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Go Home
            </a>

            <button @click="history.back()" class="inline-flex items-center justify-center w-full px-6 py-3 text-base font-medium text-zinc-700 bg-white border border-zinc-300 rounded-lg hover:bg-zinc-50 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2 shadow-sm hover:shadow-md cursor-pointer">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Go Back
            </button>
        </div>

        <!-- Additional Help -->
        <div class="mt-12 pt-8 border-t border-zinc-200">
            <p class="text-sm text-zinc-500 mb-4">
                If you think this is an error, please contact our support team.
            </p>
            <div class="flex justify-center space-x-6">
                <a href="/contact" class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                    Contact Support
                </a>
                <a href="/sitemap" class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                    Sitemap
                </a>
            </div>
        </div>
    </div>
</div>
<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
