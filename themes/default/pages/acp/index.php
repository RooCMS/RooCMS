<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

$page_title = 'Admin Control Panel — RooCMS';
$page_description = 'Control Panel for RooCMS';

$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/'.$theme_name;

ob_start();
?>
<div class="py-10">
	<div class="grid grid-cols-1 gap-8 lg:grid-cols-[260px_1fr]">
		<aside class="hidden lg:block pr-6">
			<nav aria-label="Админ-меню" class="sticky top-24">
				<h2 class="sr-only">Меню администратора</h2>
				<ul class="space-y-1">
					<li class="px-2 pb-1 text-xs font-semibold uppercase tracking-wide text-zinc-500/80">Общее</li>
					<li>
					<a href="/acp" aria-current="page" class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-white/80 backdrop-blur px-3 py-2 text-sm font-medium text-zinc-900 shadow-sm hover:bg-white">
							<span class="inline-block h-2 w-2 rounded-full bg-zinc-900"></span>
							<span>Панель управления</span>
						</a>
					</li>
					<li>
						<a href="/acp/users" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 border border-transparent hover:text-zinc-900 hover:bg-white hover:border-zinc-200">
							<span class="inline-block h-2 w-2 rounded-full bg-zinc-300"></span>
							<span>Пользователи</span>
						</a>
					</li>
					<li>
						<a href="/acp/content" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 border border-transparent hover:text-zinc-900 hover:bg-white hover:border-zinc-200">
							<span class="inline-block h-2 w-2 rounded-full bg-zinc-300"></span>
							<span>Контент</span>
						</a>
					</li>
					<li class="px-2 pt-4 pb-1 text-xs font-semibold uppercase tracking-wide text-zinc-500/80">Система</li>
					<li>
						<a href="/acp/media" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 border border-transparent hover:text-zinc-900 hover:bg-white hover:border-zinc-200">
							<span class="inline-block h-2 w-2 rounded-full bg-zinc-300"></span>
							<span>Медиа</span>
						</a>
					</li>
					<li>
						<a href="/acp/settings" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 border border-transparent hover:text-zinc-900 hover:bg-white hover:border-zinc-200">
							<span class="inline-block h-2 w-2 rounded-full bg-zinc-300"></span>
							<span>Настройки</span>
						</a>
					</li>
					<li>
						<a href="/acp/logs" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 border border-transparent hover:text-zinc-900 hover:bg-white hover:border-zinc-200">
							<span class="inline-block h-2 w-2 rounded-full bg-zinc-300"></span>
							<span>Логи</span>
						</a>
					</li>
					<li>
						<a href="/acp/support" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 border border-transparent hover:text-zinc-900 hover:bg-white hover:border-zinc-200">
							<span class="inline-block h-2 w-2 rounded-full bg-zinc-300"></span>
							<span>Поддержка</span>
						</a>
					</li>
				</ul>
        	</nav>
		</aside>

		<section>
			<header class="mb-8">
				<nav class="mb-3 text-sm text-zinc-500" aria-label="Хлебные крошки">
					<ol class="flex items-center gap-2">
						<li><a href="/" class="hover:text-zinc-700">Главная</a></li>
						<li aria-hidden="true" class="text-zinc-400">/</li>
						<li><a href="/acp" aria-current="page" class="text-zinc-700">ACP</a></li>
					</ol>
				</nav>
				<h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Панель управления</h1>
				<p class="mt-2 text-sm text-zinc-600">Обзор ключевых метрик и последних действий.</p>
        	</header>

			<div class="mt-10 grid grid-cols-1 gap-6 lg:grid-cols-3">
				<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur lg:col-span-2">
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Быстрые действия</h2>
					<div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
						<a href="/acp/content/new" class="block rounded-lg border border-zinc-200/80 bg-white/90 px-4 py-3 text-sm font-medium text-zinc-700 hover:bg-white">Новая запись</a>
						<a href="/acp/users/new" class="block rounded-lg border border-zinc-200/80 bg-white/90 px-4 py-3 text-sm font-medium text-zinc-700 hover:bg-white">Новый пользователь</a>
						<a href="/acp/media" class="block rounded-lg border border-zinc-200/80 bg-white/90 px-4 py-3 text-sm font-medium text-zinc-700 hover:bg-white">Загрузить медиа</a>
						<a href="/acp/settings" class="block rounded-lg border border-zinc-200/80 bg-white/90 px-4 py-3 text-sm font-medium text-zinc-700 hover:bg-white">Настройки</a>
                                </div>
                            </div>
				<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Системный статус</h2>
					<ul class="space-y-2 text-sm">
						<li class="flex items-center justify-between">
							<span class="text-zinc-600">API</span>
							<span class="rounded-full bg-emerald-50 px-2 py-0.5 text-emerald-700">OK</span>
						</li>
						<li class="flex items-center justify-between">
							<span class="text-zinc-600">База данных</span>
							<span class="rounded-full bg-emerald-50 px-2 py-0.5 text-emerald-700">OK</span>
						</li>
						<li class="flex items-center justify-between">
							<span class="text-zinc-600">Очереди</span>
							<span class="rounded-full bg-amber-50 px-2 py-0.5 text-amber-700">Замедлено</span>
						</li>
					</ul>
                </div>
            </div>
		</section>
    </div>
</div>

<!-- Мобильная навигация ACP без JS -->
<div class="mt-6 px-4 lg:hidden">
	<nav aria-label="Навигация ACP" class="flex gap-2 overflow-x-auto">
		<a href="/acp" class="whitespace-nowrap rounded-lg border border-zinc-200 bg-white/90 px-3 py-2 text-sm font-medium text-zinc-700">Панель</a>
		<a href="/acp/users" class="whitespace-nowrap rounded-lg border border-zinc-200 bg-white/70 px-3 py-2 text-sm text-zinc-700">Пользователи</a>
		<a href="/acp/content" class="whitespace-nowrap rounded-lg border border-zinc-200 bg-white/70 px-3 py-2 text-sm text-zinc-700">Контент</a>
		<a href="/acp/media" class="whitespace-nowrap rounded-lg border border-zinc-200 bg-white/70 px-3 py-2 text-sm text-zinc-700">Медиа</a>
		<a href="/acp/settings" class="whitespace-nowrap rounded-lg border border-zinc-200 bg-white/70 px-3 py-2 text-sm text-zinc-700">Настройки</a>
		<a href="/acp/logs" class="whitespace-nowrap rounded-lg border border-zinc-200 bg-white/70 px-3 py-2 text-sm text-zinc-700">Логи</a>
		<a href="/acp/support" class="whitespace-nowrap rounded-lg border border-zinc-200 bg-white/70 px-3 py-2 text-sm text-zinc-700">Поддержка</a>
	</nav>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../../layouts/base.php'; 