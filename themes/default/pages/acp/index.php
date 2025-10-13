<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

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
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Quick actions</h2>
					<div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
						<a href="/acp/content/new" class="block rounded-lg border border-zinc-200/80 bg-white/90 px-4 py-3 text-sm font-medium text-zinc-700 hover:bg-white">New content</a>
						<a href="/acp/users/new" class="block rounded-lg border border-zinc-200/80 bg-white/90 px-4 py-3 text-sm font-medium text-zinc-700 hover:bg-white">New user</a>
						<a href="/acp/media" class="block rounded-lg border border-zinc-200/80 bg-white/90 px-4 py-3 text-sm font-medium text-zinc-700 hover:bg-white">Upload media</a>
						<a href="/acp/settings" class="block rounded-lg border border-zinc-200/80 bg-white/90 px-4 py-3 text-sm font-medium text-zinc-700 hover:bg-white">Settings</a>
                    </div>
                </div>
				<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur" x-data="systemStatus">
					<div class="flex items-center justify-between mb-4">
						<h2 class="text-base font-semibold text-zinc-900">System status</h2>
						<div x-show="loading" class="flex items-center text-sm text-zinc-500">
							<svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
								<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
								<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
							</svg>
							Updating...
						</div>
						<div x-show="!loading && lastUpdated" class="flex items-center gap-3 text-xs">
							<span class="text-zinc-400" x-text="'Updated: ' + formatTimeOnly(lastUpdated)"></span>
							<span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono border border-neutral-300 bg-transparent text-zinc-400">
								<span x-text="countdown + 's'"></span>
							</span>
						</div>
					</div>
					<ul class="space-y-2 text-sm">
						<li class="flex items-center justify-between">
							<span class="flex items-center text-zinc-600">
								<svg class="w-4 h-4 mr-2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
								</svg>
								API
							</span>
							<span class="rounded-md px-2 py-0.5 text-xs font-medium"
							      :class="apiStatus === 'ok' ? 'bg-emerald-50 text-emerald-700' : (apiStatus === 'error' ? 'bg-red-50 text-red-700' : 'bg-gray-50 text-gray-700')"
							      x-text="apiStatus === 'ok' ? 'OK' : (apiStatus === 'error' ? 'Error' : 'Loading...')">
							</span>
						</li>
						<li class="flex items-center justify-between">
							<span class="flex items-center text-zinc-600">
								<svg class="w-4 h-4 mr-2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
								</svg>
								Database
							</span>
							<span class="rounded-md px-2 py-0.5 text-xs font-medium"
							      :class="databaseStatus === 'ok' ? 'bg-emerald-50 text-emerald-700' : (databaseStatus === 'error' ? 'bg-red-50 text-red-700' : 'bg-gray-50 text-gray-700')"
							      x-text="databaseStatus === 'ok' ? 'OK' : (databaseStatus === 'error' ? 'Error' : 'Loading...')">
							</span>
						</li>
					</ul>

					<!-- System information -->
					<div class="mt-4 pt-4 border-t border-zinc-200/50">
						<h3 class="flex items-center text-sm font-medium text-zinc-700 mb-3">
							System information
						</h3>
						<div class="space-y-3 text-sm">
							<div class="flex items-center justify-between min-h-[1.5rem]">
								<span class="flex items-center text-zinc-600">
									<svg class="w-4 h-4 mr-2 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
									</svg>
									RooCMS
								</span>
								<span class="text-xs text-zinc-500 ml-2" x-text="roocmsVersion"></span>
							</div>
							<div class="flex items-center justify-between min-h-[1.5rem]">
								<span class="flex items-center text-zinc-600">
									<svg class="w-4 h-4 mr-2 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
									</svg>
									PHP
								</span>
								<span class="text-xs text-zinc-500 ml-2" x-text="phpVersion"></span>
							</div>
							<div class="flex items-center justify-between min-h-[1.5rem]">
								<span class="flex items-center text-zinc-600">
									<svg class="w-4 h-4 mr-2 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
									</svg>
									Timezone
								</span>
								<span class="text-xs text-zinc-500 ml-2" x-text="timezone"></span>
							</div>
							<div class="flex items-center justify-between min-h-[1.5rem]">
								<span class="flex items-center text-zinc-600">
									<svg class="w-4 h-4 mr-2 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
									</svg>
									API time
								</span>
								<span class="text-xs text-zinc-500 ml-2" x-text="apiResponseTime"></span>
							</div>
							<div class="flex items-center justify-between min-h-[1.5rem]">
								<span class="flex items-center text-zinc-600">
									<svg class="w-4 h-4 mr-2 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
									</svg>
									Memory
								</span>
								<span class="text-xs text-zinc-500 ml-2" x-text="memoryUsage + ' / ' + memoryLimit"></span>
							</div>
							<div class="flex items-center justify-between min-h-[1.5rem]">
								<span class="flex items-center text-zinc-600">
									<svg class="w-4 h-4 mr-2 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v2"/>
									</svg>
									Max execution time
								</span>
								<span class="text-xs text-zinc-500 ml-2" x-text="maxExecutionTime"></span>
							</div>
						</div>
					</div>

					<!-- Users statistics -->
					<div class="mt-4 pt-4 border-t border-zinc-200/50">
						<h3 class="flex items-center text-sm font-medium text-zinc-700 mb-3">
							Users statistics
						</h3>
						<div class="grid grid-cols-1 gap-3 text-sm">
							<div class="flex items-center justify-between">
								<span class="flex items-center text-zinc-600">
									<svg class="w-4 h-4 mr-2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
									</svg>
									Total registered users
								</span>
								<span class="text-sm  text-zinc-500 ml-2" x-text="usersCount"></span>
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