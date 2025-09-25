<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

$page_title = 'Admin Control Panel — RooCMS';
$page_description = 'Control Panel for RooCMS';

$theme_name = basename(dirname(dirname(__DIR__)));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [
	$theme_base.'/assets/js/app/acp.js',
    $theme_base.'/assets/js/app/acp-access.js',
	$theme_base.'/assets/js/pages/acp-dashboard.js'
];

ob_start();
?>

<div class="py-10">
	<div class="grid grid-cols-1 gap-8 lg:grid-cols-[260px_1fr] px-4 sm:px-6 lg:px-8 space-y-8">
		<?php require __DIR__ . '/../../layouts/acp-nav.php'; ?>

		<section>
			<header class="mb-8">
				<nav class="mb-3 text-sm text-zinc-500" aria-label="Breadcrumbs">
					<ol class="flex items-center gap-2">
						<li><a href="/" class="hover:text-zinc-700">Home</a></li>
						<li aria-hidden="true" class="text-zinc-400">/</li>
						<li><a href="/acp" aria-current="page" class="text-zinc-700">ACP</a></li>
					</ol>
				</nav>
				<h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Dashboard</h1>
				<p class="mt-2 text-sm text-zinc-600">Overview of key metrics and latest actions.</p>
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
				<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur" x-data="systemStatus">
					<div class="flex items-center justify-between mb-4">
						<h2 class="text-base font-semibold text-zinc-900">Системный статус</h2>
						<div x-show="loading" class="flex items-center text-sm text-zinc-500">
							<svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
								<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
								<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
							</svg>
							Обновление...
						</div>
						<div x-show="!loading && lastUpdated" class="flex items-center gap-3 text-xs">
							<span class="text-zinc-400" x-text="'Обновлено: ' + formatTimeOnly(lastUpdated)"></span>
							<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono border border-neutral-300 bg-transparent text-zinc-400">
								<span x-text="countdown + 's'"></span>
							</span>
						</div>
					</div>
					<ul class="space-y-2 text-sm">
						<li class="flex items-center justify-between">
							<span class="text-zinc-600">API</span>
							<span class="rounded-md px-2 py-0.5 text-xs font-medium"
							      :class="apiStatus === 'ok' ? 'bg-emerald-50 text-emerald-700' : (apiStatus === 'error' ? 'bg-red-50 text-red-700' : 'bg-gray-50 text-gray-700')"
							      x-text="apiStatus === 'ok' ? 'OK' : (apiStatus === 'error' ? 'Ошибка' : 'Загрузка...')">
							</span>
						</li>
						<li class="flex items-center justify-between">
							<span class="text-zinc-600">База данных</span>
							<span class="rounded-md px-2 py-0.5 text-xs font-medium"
							      :class="databaseStatus === 'ok' ? 'bg-emerald-50 text-emerald-700' : (databaseStatus === 'error' ? 'bg-red-50 text-red-700' : 'bg-gray-50 text-gray-700')"
							      x-text="databaseStatus === 'ok' ? 'OK' : (databaseStatus === 'error' ? 'Ошибка' : 'Загрузка...')">
							</span>
						</li>
					</ul>

					<!-- Системная информация -->
					<div class="mt-4 pt-4 border-t border-zinc-200/50">
						<h3 class="text-sm font-medium text-zinc-700 mb-3">Системная информация</h3>
						<div class="grid grid-cols-2 gap-4 text-sm">
							<div class="space-y-2">
								<div class="flex items-center justify-between">
									<span class="text-zinc-600">RooCMS</span>
									<span class="text-xs text-zinc-500" x-text="roocmsVersion"></span>
								</div>
								<div class="flex items-center justify-between">
									<span class="text-zinc-600">PHP</span>
									<span class="text-xs text-zinc-500" x-text="phpVersion"></span>
								</div>
								<div class="flex items-center justify-between">
									<span class="text-zinc-600">Часовой пояс</span>
									<span class="text-xs text-zinc-500" x-text="timezone"></span>
								</div>
							</div>
							<div class="space-y-2">
								<div class="flex items-center justify-between">
									<span class="text-zinc-600">API время</span>
									<span class="text-xs text-zinc-500" x-text="apiResponseTime"></span>
								</div>
								<div class="flex items-center justify-between">
									<span class="text-zinc-600">Память</span>
									<span class="text-xs text-zinc-500" x-text="memoryUsage + ' / ' + memoryLimit"></span>
								</div>
								<div class="flex items-center justify-between">
									<span class="text-zinc-600">Макс. время</span>
									<span class="text-xs text-zinc-500" x-text="maxExecutionTime"></span>
								</div>
							</div>
						</div>
					</div>

					<div x-show="error" class="mt-3 text-xs text-red-600" x-text="error"></div>
                </div>
            </div>
		</section>
    </div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../../layouts/base.php'; 