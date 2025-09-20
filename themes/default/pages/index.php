<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

$page_title = 'Welcome to RooCMS';
$page_description = 'The modern content management system on pure PHP';
$page_scripts = [];

ob_start();
?>
<!-- Hero Section -->
<section class="relative py-10 sm:py-24 lg:py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl font-bold tracking-tight text-zinc-900 sm:text-6xl">
                Welcome to
                <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">RooCMS</span>
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-zinc-600">
                The modern content management system on pure PHP without frameworks.
                Simple, fast and secure platform for creating websites.
            </p>
            <div class="mt-10 flex items-center justify-center gap-x-6">
                <a href="/register" class="inline-flex items-center justify-center rounded-md bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-3 text-base font-semibold text-white transition-all hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-lg hover:shadow-xl transform hover:scale-105">
                    Start working
                </a>
                <a href="/about" class="text-base font-semibold leading-6 text-zinc-900 hover:text-zinc-700 transition-colors">
                    Learn more <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16 sm:py-20 bg-white/50 rounded-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold tracking-tight text-zinc-900 sm:text-4xl">
                Why RooCMS?
            </h2>
            <p class="mt-4 text-lg text-zinc-600 max-w-2xl mx-auto">
                Platform created with KISS principles for maximum simplicity and performance
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Feature 1 -->
            <div class="relative bg-white rounded-lg shadow-sm border border-zinc-200 p-8 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-r from-blue-500 to-purple-500 mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-zinc-900 mb-3">Fast performance</h3>
                <p class="text-zinc-600">
                    Written in pure PHP without heavy frameworks. Minimum server resource requirements.
                </p>
            </div>

            <!-- Feature 2 -->
            <div class="relative bg-white rounded-lg shadow-sm border border-zinc-200 p-8 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-r from-green-500 to-teal-500 mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-zinc-900 mb-3">Security in priority</h3>
                <p class="text-zinc-600">
                    Modern security standards, CSP protection and strict access control.
                </p>
            </div>

            <!-- Feature 3 -->
            <div class="relative bg-white rounded-lg shadow-sm border border-zinc-200 p-8 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-zinc-900 mb-3">Ease of use</h3>
                <p class="text-zinc-600">
                    Clear architecture and intuitive interface. Easily configurable and expandable.
                </p>
            </div>

            <!-- Feature 4 -->
            <div class="relative bg-white rounded-lg shadow-sm border border-zinc-200 p-8 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-r from-indigo-500 to-cyan-500 mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-zinc-900 mb-3">Powerful REST API</h3>
                <p class="text-zinc-600">
                    Full-featured API for integration with any applications. Standardized JSON responses and middleware system.
                </p>
            </div>

            <!-- Feature 5 -->
            <div class="relative bg-white rounded-lg shadow-sm border border-zinc-200 p-8 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-r from-emerald-500 to-lime-500 mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-zinc-900 mb-3">Multi-database</h3>
                <p class="text-zinc-600">
                    Support for MySQL, PostgreSQL and Firebird in one code. Migration system and Query Builder for safe work.
                </p>
            </div>

            <!-- Feature 6 -->
            <div class="relative bg-white rounded-lg shadow-sm border border-zinc-200 p-8 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-r from-violet-500 to-pink-500 mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-zinc-900 mb-3">Modern PHP 8.1+</h3>
                <p class="text-zinc-600">
                    Strict typing, modern language features and best practices without outdated code.
                </p>
            </div>

            <!-- Feature 7 -->
            <div class="relative bg-white rounded-lg shadow-sm border border-zinc-200 p-8 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-r from-rose-500 to-red-500 mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-zinc-900 mb-3">Health monitoring</h3>
                <p class="text-zinc-600">
                    Built-in Health Check endpoints for monitoring system and database state in real time.
                </p>
            </div>

            <!-- Feature 8 -->
            <div class="relative bg-white rounded-lg shadow-sm border border-zinc-200 p-8 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-r from-teal-500 to-cyan-500 mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-zinc-900 mb-3">Modular themes</h3>
                <p class="text-zinc-600">
                    Flexible theme system with support for multiple designs. Alpine.js integration for interactivity.
                </p>
            </div>

            <!-- Feature 9 -->
            <div class="relative bg-white rounded-lg shadow-sm border border-zinc-200 p-8 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-r from-slate-500 to-gray-500 mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-zinc-900 mb-3">CLI tools</h3>
                <p class="text-zinc-600">
                    Powerful CLI tools for database migrations, backups and automation tasks.
                </p>
            </div>

            <!-- Feature 10 -->
            <div class="relative bg-white rounded-lg shadow-sm border border-zinc-200 p-8 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-r from-orange-500 to-red-500 mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-zinc-900 mb-3">API-First approach</h3>
                <p class="text-zinc-600">
                    RooCMS - this is not only a CMS, but also a powerful API platform for creating any applications and integrations.
                </p>
            </div>

            <!-- Feature 11 -->
            <div class="relative bg-white rounded-lg shadow-sm border border-zinc-200 p-8 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-r from-cyan-500 to-blue-500 mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-zinc-900 mb-3">Injection protection</h3>
                <p class="text-zinc-600">
                    Query Builder provides full protection from SQL injections. All requests are safe and parameterized.
                </p>
            </div>

            <!-- Feature 12 -->
            <div class="relative bg-white rounded-lg shadow-sm border border-zinc-200 p-8 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-r from-lime-500 to-green-500 mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-zinc-900 mb-3">Transactional security</h3>
                <p class="text-zinc-600">
                    Full support for transactions to ensure data integrity in complex operations.
                </p>
    </div>

            <!-- Feature 13 -->
            <div class="relative bg-white rounded-lg shadow-sm border border-zinc-200 p-8 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-500 mb-6">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-zinc-900 mb-3">Flexible frontend choice</h3>
                <p class="text-zinc-600">
                    Use any frontend: React, Vue, Angular, vanilla HTML+JS or built-in PHP template engine.
                </p>
            </div>

            <!-- Feature 14 -->
            <div class="relative bg-white rounded-lg shadow-sm border border-zinc-200 p-8 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-r from-yellow-500 to-orange-500 mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-zinc-900 mb-3">Routing system</h3>
                <p class="text-zinc-600">
                    Flexible routing with support for dynamic parameters {id}, {param} for creating RESTful API.
                </p>
            </div>

            <!-- Feature 15 -->
            <div class="relative bg-white rounded-lg shadow-sm border border-zinc-200 p-8 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-center w-12 h-12 rounded-lg bg-gradient-to-r from-pink-500 to-rose-500 mb-6">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-zinc-900 mb-3">Data validation</h3>
                <p class="text-zinc-600">
                    Automatic validation and sanitization of all input data for security.
                </p>
            </div>

    </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 sm:py-12">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="text-center bg-gradient-to-r from-sky-100 to-purple-100 rounded-2xl p-8 sm:p-12">
            <h2 class="text-3xl font-bold tracking-tight text-zinc-900 sm:text-4xl mb-4">
                Ready to start?
            </h2>
            <p class="text-lg text-zinc-600 mb-8 max-w-2xl mx-auto">
                Create your first website on RooCMS today. Fast installation and easy content management.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/register" class="inline-flex items-center justify-center rounded-md bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-3 text-base font-semibold text-white transition-all hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-lg hover:shadow-xl transform hover:scale-105">
                    Create an account
                </a>
                <a href="/login" class="inline-flex items-center justify-center rounded-md border border-zinc-300 bg-white px-8 py-3 text-base font-semibold text-zinc-900 transition-all hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2 shadow-sm hover:shadow-md">
                    Login to the system
                </a>
            </div>
        </div>
    </div>
</section>
<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';


