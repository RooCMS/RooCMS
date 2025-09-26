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
                <a href="/" class="nav-link">
                    Home
                </a>
                <a href="/about" class="nav-link">
                    About
                </a>
                <a href="/403" class="nav-link">
                    Access denied
                </a>
                <a href="/contact" class="nav-link">
                    Contact
                </a>
            </nav>

            <!-- Auth Buttons -->
            <div class="flex items-center space-x-4" x-data="authButtons">

                <!-- Not authenticated users - shows Login/Register buttons -->
                <div id="auth-guest" class="flex items-center space-x-4">
                    <a href="/login" class="nav-link">
                        Login
                    </a>
                    <a href="/register" class="btn primary">
                        Register
                    </a>
                </div>

                <!-- Authenticated users - shows Profile/Admin/Logout buttons -->
                <div id="auth-user" class="flex items-center space-x-4 hidden">
                    <a href="/profile" class="inline-flex items-center nav-link">
                        My Profile
                    </a>
                    <!-- Admin Panel Button - Shows only for admins and super admins -->
                    <a href="/acp" x-show="$store.auth.isAdmin()" class="btn warning" title="Admin Panel">
                        <svg class="mt-0.5 align-middle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Admin Panel
                    </a>
                    <a href="#" @click.prevent="logout" class="btn danger" title="Logout">
                        <svg class="mt-0.5 align-middle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Logout
                    </a>
                </div>
            </div>

            <script nonce="<?php render_html($csp_nonce); ?>">
            // Show correct auth buttons immediately to prevent flickering
            const isAuth = !!localStorage.getItem('access_token');
            ['auth-guest', 'mobile-auth-guest'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.toggle('hidden', isAuth);
            });
            ['auth-user', 'mobile-auth-user'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.classList.toggle('hidden', !isAuth);
            });
            </script>

            <!-- Mobile menu button (placeholder for future JS implementation) -->
            <div class="md:hidden">
                <button type="button" class="inline-flex items-center justify-center rounded-md p-2 text-zinc-700 hover:bg-zinc-100 hover:text-zinc-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-zinc-900" x-on:click="$store.mobileMenu.toggle()">
                    <span class="sr-only">Open main menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="md:hidden" x-show="$store.mobileMenu.open">
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
                <a href="/contact" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-zinc-700 hover:border-zinc-300 hover:bg-zinc-50 hover:text-zinc-900">
                    Contact
                </a>

                <!-- Mobile Auth Links -->
                <div class="border-t border-zinc-200 pt-2 mt-2" x-data="authButtons">
                    <!-- Mobile not authenticated users -->
                    <div id="mobile-auth-guest">
                        <a href="/login" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-zinc-700 hover:border-zinc-300 hover:bg-zinc-50 hover:text-zinc-900">
                            Login
                        </a>
                        <a href="/register" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-zinc-700 hover:border-zinc-300 hover:bg-zinc-50 hover:text-zinc-900">
                            Register
                        </a>
                    </div>

                    <!-- Mobile authenticated users -->
                    <div id="mobile-auth-user" class="hidden">
                        <a href="/profile" class="flex items-center border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-zinc-700 hover:border-zinc-300 hover:bg-zinc-50 hover:text-zinc-900 transition-colors duration-200">
                            My Profile
                        </a>
                        <!-- Admin Panel Button - Shows only for admins and super admins -->
                        <a href="/acp" x-show="$store.auth.isAdmin()" class="flex items-center border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-zinc-700 hover:border-amber-300 hover:bg-amber-50 hover:text-amber-900 transition-colors duration-200" title="Access Admin Panel">
                            <div class="flex items-center justify-center w-8 h-8 rounded-md bg-gradient-to-r from-amber-500 to-orange-600 mr-3">
                                <svg class="w-4 h-4 text-white align-middle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            Admin Panel
                        </a>
                        <a href="#" @click.prevent="logout" class="flex items-center border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-red-600 hover:border-red-300 hover:bg-red-50 hover:text-red-900 cursor-pointer transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2 align-middle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
