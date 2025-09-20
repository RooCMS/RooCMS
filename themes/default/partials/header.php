<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }
?>
<header class="sticky top-0 z-50 w-full border-b border-amber-200/30 bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/80">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="/" class="flex items-center space-x-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-blue-600 to-purple-600">
                        <span class="text-lg font-bold text-white">R</span>
                    </div>
                    <span class="text-xl font-bold text-zinc-900">RooCMS</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex md:items-center md:space-x-8">
                <a href="/" class="text-sm font-medium text-zinc-700 transition-colors hover:text-zinc-900">
                    Home
                </a>
                <a href="/about" class="text-sm font-medium text-zinc-700 transition-colors hover:text-zinc-900">
                    403
                </a>
                <a href="/403" class="text-sm font-medium text-zinc-700 transition-colors hover:text-zinc-900">
                    Access denied
                </a>
                <a href="/blog" class="text-sm font-medium text-zinc-700 transition-colors hover:text-zinc-900">
                    Blog
                </a>
                <a href="/contact" class="text-sm font-medium text-zinc-700 transition-colors hover:text-zinc-900">
                    Contact
                </a>
            </nav>

            <!-- Auth Buttons -->
            <div class="flex items-center space-x-4">
                <a href="/login" class="text-sm font-medium text-zinc-700 transition-colors hover:text-zinc-900">
                    Login
                </a>
                <a href="/register" class="inline-flex items-center justify-center rounded-md bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-medium text-white transition-all hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-sm hover:shadow-md">
                    Register
                </a>
            </div>

            <!-- Mobile menu button (placeholder for future JS implementation) -->
            <div class="md:hidden">
                <button type="button" class="inline-flex items-center justify-center rounded-md p-2 text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-zinc-900">
                    <span class="sr-only">Open main menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="md:hidden">
            <div class="space-y-1 pb-3 pt-2">
                <a href="/" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-zinc-700 hover:border-zinc-300 hover:bg-zinc-50 hover:text-zinc-900">
                    Home
                </a>
                <a href="/about" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-zinc-700 hover:border-zinc-300 hover:bg-zinc-50 hover:text-zinc-900">
                    About
                </a>
                <a href="/403" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-zinc-700 hover:border-zinc-300 hover:bg-zinc-50 hover:text-zinc-900">
                    403
                </a>
                <a href="/blog" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-zinc-700 hover:border-zinc-300 hover:bg-zinc-50 hover:text-zinc-900">
                    Blog
                </a>
                <a href="/contact" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-zinc-700 hover:border-zinc-300 hover:bg-zinc-50 hover:text-zinc-900">
                    Contact
                </a>
            </div>
        </div>
    </div>
</header>
