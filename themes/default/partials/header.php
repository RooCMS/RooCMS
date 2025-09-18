<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }
?>
<header id="main-header" class="modern-header" x-data="{ mobileMenuOpen: false }">
    <div id="header-container" class="header-container">
        <!-- Логотип и брендинг -->
        <div id="brand-section" class="brand">
            <a href="/" class="brand-link">
                <div class="brand-logo">
                    <span class="logo-icon">🚀</span>
                    <span class="logo-text">RooCMS</span>
                </div>
            </a>
        </div>

        <!-- Десктопная навигация -->
        <nav id="main-navigation" class="main-nav" x-show="!mobileMenuOpen" x-transition>
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="/" class="nav-link" :class="{ 'active': window.location.pathname === '/' }">
                        <span class="nav-icon">🏠</span>
                        Главная
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/users" class="nav-link" :class="{ 'active': window.location.pathname.startsWith('/users') }">
                        <span class="nav-icon">👥</span>
                        Пользователи
                    </a>
                </li>
                <li class="nav-item" x-show="!(window.headerUtils && window.headerUtils.isLoggedIn)">
                    <a href="/auth/login" class="nav-link" :class="{ 'active': window.location.pathname.startsWith('/auth') }">
                        <span class="nav-icon">🔐</span>
                        Войти
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Панель действий -->
        <div id="header-actions" class="header-actions">
            <!-- Поиск -->
            <div class="search-container" x-show="!mobileMenuOpen">
                <div class="search-wrapper" x-data="{ open: false }">
                    <button @click="open = !open" class="search-toggle" aria-label="Поиск">
                        <span class="search-icon">🔍</span>
                    </button>
                    <div class="search-dropdown" x-show="open" @click.outside="open = false" x-transition>
                        <form @submit="open = false">
                            <input
                                type="text"
                                placeholder="Поиск..."
                                class="search-input"
                            >
                        </form>
                    </div>
                </div>
            </div>

            <!-- Пользовательское меню -->
            <div class="user-menu" x-show="window.headerUtils && window.headerUtils.isLoggedIn" x-data="{ open: false }">
                <button @click="open = !open" class="user-toggle" aria-label="Меню пользователя">
                    <div class="user-avatar">
                        <span class="avatar-icon">👤</span>
                    </div>
                    <span class="user-name">Пользователь</span>
                    <span class="dropdown-arrow" :class="{ 'open': open }">▼</span>
                </button>
                <div class="user-dropdown" x-show="open" @click.outside="open = false" x-transition>
                    <a href="/profile" class="dropdown-item" @click="open = false">
                        <span class="item-icon">⚙️</span>
                        Профиль
                    </a>
                    <a href="/settings" class="dropdown-item" @click="open = false">
                        <span class="item-icon">🔧</span>
                        Настройки
                    </a>
                    <hr class="dropdown-divider">
                    <button @click="open = false" class="dropdown-item logout-btn">
                        <span class="item-icon">🚪</span>
                        Выйти
                    </button>
                </div>
            </div>

            <!-- Кнопка мобильного меню -->
            <button
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="mobile-menu-toggle"
                :aria-expanded="mobileMenuOpen"
                aria-label="Мобильное меню"
            >
                <span class="hamburger-icon" :class="{ 'open': mobileMenuOpen }">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </button>
        </div>
    </div>

    <!-- Мобильная навигация -->
    <div id="mobile-navigation" class="mobile-nav" x-show="mobileMenuOpen" x-transition>
        <nav class="mobile-nav-content">
            <ul class="mobile-nav-list">
                <li class="mobile-nav-item">
                    <a href="/" class="mobile-nav-link" @click="mobileMenuOpen = false">
                        <span class="nav-icon">🏠</span>
                        Главная
                    </a>
                </li>
                <li class="mobile-nav-item">
                    <a href="/users" class="mobile-nav-link" @click="mobileMenuOpen = false">
                        <span class="nav-icon">👥</span>
                        Пользователи
                    </a>
                </li>
                <li class="mobile-nav-item" x-show="!(window.headerUtils && window.headerUtils.isLoggedIn)">
                    <a href="/auth/login" class="mobile-nav-link" @click="mobileMenuOpen = false">
                        <span class="nav-icon">🔐</span>
                        Войти
                    </a>
                </li>
                <li class="mobile-nav-item" x-show="window.headerUtils && window.headerUtils.isLoggedIn">
                    <a href="/profile" class="mobile-nav-link" @click="mobileMenuOpen = false">
                        <span class="nav-icon">⚙️</span>
                        Профиль
                    </a>
                </li>
                <li class="mobile-nav-item" x-show="window.headerUtils && window.headerUtils.isLoggedIn">
                    <button @click="mobileMenuOpen = false; localStorage.removeItem('access_token'); document.cookie='access_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;'; window.location.href='/'" class="mobile-nav-link logout-mobile">
                        <span class="nav-icon">🚪</span>
                        Выйти
                    </button>
                </li>
            </ul>

            <!-- Мобильный поиск -->
            <div class="mobile-search">
                <form @submit="mobileMenuOpen = false">
                    <div class="mobile-search-wrapper">
                        <input
                            type="text"
                            placeholder="Поиск..."
                            class="mobile-search-input"
                        >
                        <button type="submit" class="mobile-search-btn">
                            <span class="search-icon">🔍</span>
                        </button>
                    </div>
                </form>
            </div>
        </nav>
    </div>

    <!-- Затемнение фона для мобильного меню -->
    <div
        id="mobile-overlay"
        class="mobile-overlay"
        x-show="mobileMenuOpen"
        @click="mobileMenuOpen = false"
        x-transition
    ></div>
</header>

