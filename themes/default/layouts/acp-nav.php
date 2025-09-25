<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

// Current page for highlighting active link
$current_page = $current_page ?? basename(env('REQUEST_URI') ?? '', '.php');
$current_page = str_replace('/acp/', '', $current_page);
$current_page = $current_page === 'acp' || $current_page === '' ? 'index' : $current_page;

?>
<aside class="hidden lg:block pr-6">
    <nav aria-label="Админ-меню" class="sticky top-24">
        <h2 class="sr-only">Меню администратора</h2>
        <ul class="space-y-1">
            <li class="px-2 pb-1 text-xs font-semibold uppercase tracking-wide text-zinc-500/80">Общее</li>
            <li>
                <a href="/acp" <?php echo $current_page === 'index' ? 'aria-current="page"' : ''; ?> class="flex items-center gap-3 rounded-lg <?php echo $current_page === 'index' ? 'border border-zinc-200 bg-white/80 backdrop-blur px-3 py-2 text-sm font-medium text-zinc-900 shadow-sm hover:bg-white' : 'px-3 py-2 text-sm font-medium text-zinc-700 border border-transparent hover:text-zinc-900 hover:bg-white hover:border-zinc-200'; ?>">
                    <span class="inline-block h-2 w-2 rounded-full <?php echo $current_page === 'index' ? 'bg-zinc-900' : 'bg-zinc-300'; ?>"></span>
                    <span>Панель управления</span>
                </a>
            </li>
            <li>
                <a href="/acp/users" <?php echo $current_page === 'users' ? 'aria-current="page"' : ''; ?> class="flex items-center gap-3 rounded-lg <?php echo $current_page === 'users' ? 'border border-zinc-200 bg-white/80 backdrop-blur px-3 py-2 text-sm font-medium text-zinc-900 shadow-sm hover:bg-white' : 'px-3 py-2 text-sm font-medium text-zinc-700 border border-transparent hover:text-zinc-900 hover:bg-white hover:border-zinc-200'; ?>">
                    <span class="inline-block h-2 w-2 rounded-full <?php echo $current_page === 'users' ? 'bg-zinc-900' : 'bg-zinc-300'; ?>"></span>
                    <span>Пользователи</span>
                </a>
            </li>
            <li>
                <a href="/acp/content" <?php echo $current_page === 'content' ? 'aria-current="page"' : ''; ?> class="flex items-center gap-3 rounded-lg <?php echo $current_page === 'content' ? 'border border-zinc-200 bg-white/80 backdrop-blur px-3 py-2 text-sm font-medium text-zinc-900 shadow-sm hover:bg-white' : 'px-3 py-2 text-sm font-medium text-zinc-700 border border-transparent hover:text-zinc-900 hover:bg-white hover:border-zinc-200'; ?>">
                    <span class="inline-block h-2 w-2 rounded-full <?php echo $current_page === 'content' ? 'bg-zinc-900' : 'bg-zinc-300'; ?>"></span>
                    <span>Контент</span>
                </a>
            </li>
            <li class="px-2 pt-4 pb-1 text-xs font-semibold uppercase tracking-wide text-zinc-500/80">Система</li>
            <li>
                <a href="/acp/media" <?php echo $current_page === 'media' ? 'aria-current="page"' : ''; ?> class="flex items-center gap-3 rounded-lg <?php echo $current_page === 'media' ? 'border border-zinc-200 bg-white/80 backdrop-blur px-3 py-2 text-sm font-medium text-zinc-900 shadow-sm hover:bg-white' : 'px-3 py-2 text-sm font-medium text-zinc-700 border border-transparent hover:text-zinc-900 hover:bg-white hover:border-zinc-200'; ?>">
                    <span class="inline-block h-2 w-2 rounded-full <?php echo $current_page === 'media' ? 'bg-zinc-900' : 'bg-zinc-300'; ?>"></span>
                    <span>Медиа</span>
                </a>
            </li>
            <li>
                <a href="/acp/settings" <?php echo $current_page === 'settings' ? 'aria-current="page"' : ''; ?> class="flex items-center gap-3 rounded-lg <?php echo $current_page === 'settings' ? 'border border-zinc-200 bg-white/80 backdrop-blur px-3 py-2 text-sm font-medium text-zinc-900 shadow-sm hover:bg-white' : 'px-3 py-2 text-sm font-medium text-zinc-700 border border-transparent hover:text-zinc-900 hover:bg-white hover:border-zinc-200'; ?>">
                    <span class="inline-block h-2 w-2 rounded-full <?php echo $current_page === 'settings' ? 'bg-zinc-900' : 'bg-zinc-300'; ?>"></span>
                    <span>Настройки</span>
                </a>
            </li>
            <li>
                <a href="/acp/logs" <?php echo $current_page === 'logs' ? 'aria-current="page"' : ''; ?> class="flex items-center gap-3 rounded-lg <?php echo $current_page === 'logs' ? 'border border-zinc-200 bg-white/80 backdrop-blur px-3 py-2 text-sm font-medium text-zinc-900 shadow-sm hover:bg-white' : 'px-3 py-2 text-sm font-medium text-zinc-700 border border-transparent hover:text-zinc-900 hover:bg-white hover:border-zinc-200'; ?>">
                    <span class="inline-block h-2 w-2 rounded-full <?php echo $current_page === 'logs' ? 'bg-zinc-900' : 'bg-zinc-300'; ?>"></span>
                    <span>Логи</span>
                </a>
            </li>
            <li>
                <a href="/acp/support" <?php echo $current_page === 'support' ? 'aria-current="page"' : ''; ?> class="flex items-center gap-3 rounded-lg <?php echo $current_page === 'support' ? 'border border-zinc-200 bg-white/80 backdrop-blur px-3 py-2 text-sm font-medium text-zinc-900 shadow-sm hover:bg-white' : 'px-3 py-2 text-sm font-medium text-zinc-700 border border-transparent hover:text-zinc-900 hover:bg-white hover:border-zinc-200'; ?>">
                    <span class="inline-block h-2 w-2 rounded-full <?php echo $current_page === 'support' ? 'bg-zinc-900' : 'bg-zinc-300'; ?>"></span>
                    <span>Поддержка</span>
                </a>
            </li>
            <li>
                <a href="/acp/ui-kit" <?php echo $current_page === 'ui-kit' ? 'aria-current="page"' : ''; ?> class="flex items-center gap-3 rounded-lg <?php echo $current_page === 'ui-kit' ? 'border border-zinc-200 bg-white/80 backdrop-blur px-3 py-2 text-sm font-medium text-zinc-900 shadow-sm hover:bg-white' : 'px-3 py-2 text-sm font-medium text-zinc-700 border border-transparent hover:text-zinc-900 hover:bg-white hover:border-zinc-200'; ?>">
                    <span class="inline-block h-2 w-2 rounded-full <?php echo $current_page === 'ui-kit' ? 'bg-zinc-900' : 'bg-zinc-300'; ?>"></span>
                    <span>UI Kit</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<!-- Мобильная навигация ACP -->
<div class="mt-6 px-4 lg:hidden">
    <nav aria-label="Навигация ACP" class="flex gap-2 overflow-x-auto">
        <a href="/acp" class="<?php echo $current_page === 'index' ? 'whitespace-nowrap rounded-lg border border-zinc-200 bg-white/90 px-3 py-2 text-sm font-medium text-zinc-900' : 'whitespace-nowrap rounded-lg border border-zinc-200 bg-white/70 px-3 py-2 text-sm text-zinc-700'; ?>">Панель</a>
        <a href="/acp/users" class="<?php echo $current_page === 'users' ? 'whitespace-nowrap rounded-lg border border-zinc-200 bg-white/90 px-3 py-2 text-sm font-medium text-zinc-900' : 'whitespace-nowrap rounded-lg border border-zinc-200 bg-white/70 px-3 py-2 text-sm text-zinc-700'; ?>">Пользователи</a>
        <a href="/acp/content" class="<?php echo $current_page === 'content' ? 'whitespace-nowrap rounded-lg border border-zinc-200 bg-white/90 px-3 py-2 text-sm font-medium text-zinc-900' : 'whitespace-nowrap rounded-lg border border-zinc-200 bg-white/70 px-3 py-2 text-sm text-zinc-700'; ?>">Контент</a>
        <a href="/acp/media" class="<?php echo $current_page === 'media' ? 'whitespace-nowrap rounded-lg border border-zinc-200 bg-white/90 px-3 py-2 text-sm font-medium text-zinc-900' : 'whitespace-nowrap rounded-lg border border-zinc-200 bg-white/70 px-3 py-2 text-sm text-zinc-700'; ?>">Медиа</a>
        <a href="/acp/settings" class="<?php echo $current_page === 'settings' ? 'whitespace-nowrap rounded-lg border border-zinc-200 bg-white/90 px-3 py-2 text-sm font-medium text-zinc-900' : 'whitespace-nowrap rounded-lg border border-zinc-200 bg-white/70 px-3 py-2 text-sm text-zinc-700'; ?>">Настройки</a>
        <a href="/acp/logs" class="<?php echo $current_page === 'logs' ? 'whitespace-nowrap rounded-lg border border-zinc-200 bg-white/90 px-3 py-2 text-sm font-medium text-zinc-900' : 'whitespace-nowrap rounded-lg border border-zinc-200 bg-white/70 px-3 py-2 text-sm text-zinc-700'; ?>">Логи</a>
        <a href="/acp/support" class="<?php echo $current_page === 'support' ? 'whitespace-nowrap rounded-lg border border-zinc-200 bg-white/90 px-3 py-2 text-sm font-medium text-zinc-900' : 'whitespace-nowrap rounded-lg border border-zinc-200 bg-white/70 px-3 py-2 text-sm text-zinc-700'; ?>">Поддержка</a>
        <a href="/acp/ui-kit" class="<?php echo $current_page === 'ui-kit' ? 'whitespace-nowrap rounded-lg border border-zinc-200 bg-white/90 px-3 py-2 text-sm font-medium text-zinc-900' : 'whitespace-nowrap rounded-lg border border-zinc-200 bg-white/70 px-3 py-2 text-sm text-zinc-700'; ?>">UI Kit</a>
    </nav>
</div>
