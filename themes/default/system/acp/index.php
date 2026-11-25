<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

$page_title = 'Admin Control Panel — RooCMS';
$page_description = 'Admin Control Panel for RooCMS';

$theme_name = basename(dirname(__DIR__, 2));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [
	$theme_base.'/assets/js/app/acp.js',
    $theme_base.'/assets/js/app/acp-access.js',
	$theme_base.'/assets/js/pages/acp-dashboard.js'
];

$acp_heading = 'Dashboard';
$acp_intro = 'Overview of key metrics and latest actions.';
$acp_breadcrumb = 'ACP';
$acp_home_label = 'Home';

ob_start();
?>

<div class="acp-shell">
	<div class="acp-layout">
		<?php require __DIR__ . '/../partials/acp-nav.php'; ?>

		<section class="acp-content">
			<header class="mb-8">
                <?php require __DIR__ . '/../partials/acp-heading.php'; ?>
        	</header>

			<div class="acp-dashboard">
				<div class="ui-panel xl:col-span-2 xl:row-span-2">
					<h2 class="ui-section-title">Information</h2>
				</div>

				<div class="ui-panel col-span-1 row-span-1" x-data="systemStatus">
					<div class="flex items-center justify-between mb-4">
						<h2 class="text-base font-semibold text-zinc-900">System status</h2>
						<div x-show="loading" class="inline-flex items-center gap-3 text-sm text-zinc-500">
							<?php require __DIR__ . '/../../partials/ui-spinner.php'; ?>
							Updating...
						</div>
						<div x-show="!loading && lastUpdated" class="flex items-center gap-3 text-xs">
							<span class="text-zinc-400" x-text="'Updated: ' + formatTimeOnly(lastUpdated)"></span>
							<span class="badge neutral compact font-mono">
								<span x-text="countdown + 's'"></span>
							</span>
						</div>
					</div>
					<ul class="space-y-2 text-sm">
						<li class="flex items-center justify-between">
							<span class="flex items-center text-zinc-600">
								<svg class="size-4 mr-2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
								</svg>
								API
							</span>
							<span class="badge compact"
							      :class="apiStatus === 'ok' ? 'success' : (apiStatus === 'error' ? 'danger' : 'neutral')"
							      x-text="apiStatus === 'ok' ? 'OK' : (apiStatus === 'error' ? 'Error' : 'Loading...')">
							</span>
						</li>
						<li class="flex items-center justify-between">
							<span class="flex items-center text-zinc-600">
								<svg class="size-4 mr-2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
								</svg>
								Database
							</span>
							<span class="badge compact"
							      :class="databaseStatus === 'ok' ? 'success' : (databaseStatus === 'error' ? 'danger' : 'neutral')"
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
									<svg class="size-4 mr-2 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" d="M3 8.25V18a2.25 2.25 0 0 0 2.25 2.25h13.5A2.25 2.25 0 0 0 21 18V8.25m-18 0V6a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 6v2.25m-18 0h18M5.25 6h.008v.008H5.25V6ZM7.5 6h.008v.008H7.5V6Zm2.25 0h.008v.008H9.75V6Z" />
									</svg>
									RooCMS
								</span>
								<span class="text-zinc-500 ml-2" x-text="roocmsVersion"></span>
							</div>
							<div class="flex items-center justify-between min-h-[1.5rem]">
								<span class="flex items-center text-zinc-600">
									<svg class="size-4 mr-2 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
									</svg>
									PHP
								</span>
								<span class="text-zinc-500 ml-2" x-text="phpVersion"></span>
							</div>
							<div class="flex items-center justify-between min-h-[1.5rem]">
								<span class="flex items-center text-zinc-600">
									<svg class="size-4 mr-2 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
									</svg>
									Timezone
								</span>
								<span class="text-zinc-500 ml-2" x-text="timezone"></span>
							</div>
							<div class="flex items-center justify-between min-h-[1.5rem]">
								<span class="flex items-center text-zinc-600">
									<svg class="size-4 mr-2 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 10V3L4 14h7v7l9-11h-7z"/>
									</svg>
									API time
								</span>
								<span class="text-zinc-500 ml-2" x-text="apiResponseTime"></span>
							</div>
							<div class="flex items-center justify-between min-h-[1.5rem]">
								<span class="flex items-center text-zinc-600">
									<svg class="size-4 mr-2 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
									</svg>
									Memory
								</span>
								<span class="text-zinc-500 ml-2" x-text="memoryUsage + ' / ' + memoryLimit"></span>
							</div>
							<div class="flex items-center justify-between min-h-[1.5rem]">
								<span class="flex items-center text-zinc-600">
									<svg class="size-4 mr-2 text-zinc-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 2v2"/>
									</svg>
									Max execution time
								</span>
								<span class="text-zinc-500 ml-2" x-text="maxExecutionTime"></span>
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
									<svg class="size-4 mr-2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
									</svg>
									Total registered users
								</span>
								<span class="text-sm text-zinc-500 ml-2" x-text="usersCount"></span>
							</div>
						</div>
					</div>

					<div x-show="error" class="mt-3 text-xs text-red-600" x-text="error"></div>
                </div>

				<div class="ui-panel col-span-1 row-span-1">
					<h2 class="ui-section-title">Quick actions</h2>
					<div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
						<a href="/!/acp/structure" class="btn secondary full-width">Structure</a>
						<a href="/!/acp/users" class="btn secondary full-width">Users</a>
						<a href="/!/acp/debug" class="btn secondary full-width">Debug</a>
						<a href="/!/acp/settings" class="btn secondary full-width">Settings</a>
                    </div>
                </div>
            </div>
		</section>
    </div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../../layouts/base.php'; 
