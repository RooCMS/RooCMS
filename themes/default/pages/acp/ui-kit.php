<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

$page_title = 'UI Kit — Admin Control Panel — RooCMS';
$page_description = 'UI components for RooCMS ACP';

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
						<a href="/acp" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-zinc-700 border border-transparent hover:text-zinc-900 hover:bg-white hover:border-zinc-200">
							<span class="inline-block h-2 w-2 rounded-full bg-zinc-300"></span>
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
					<li>
						<a href="/acp/ui-kit" aria-current="page" class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-white/80 backdrop-blur px-3 py-2 text-sm font-medium text-zinc-900 shadow-sm hover:bg-white">
							<span class="inline-block h-2 w-2 rounded-full bg-zinc-900"></span>
							<span>UI Kit</span>
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
						<li><a href="/acp" class="hover:text-zinc-700">ACP</a></li>
						<li aria-hidden="true" class="text-zinc-400">/</li>
						<li><a href="/acp/ui-kit" aria-current="page" class="text-zinc-700">UI Kit</a></li>
					</ol>
				</nav>
				<h1 class="text-2xl font-semibold tracking-tight text-zinc-900">UI Kit</h1>
				<p class="mt-2 text-sm text-zinc-600">Галерея базовых компонентов интерфейса для ACP.</p>
			</header>

			<div class="space-y-10">
				<!-- Buttons -->
				<section>
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Кнопки</h2>
					<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<div class="flex flex-wrap gap-3">
							<button type="button" class="inline-flex items-center justify-center rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800">Primary</button>
							<button type="button" class="inline-flex items-center justify-center rounded-lg border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-900 hover:bg-zinc-50">Secondary</button>
							<button type="button" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Success</button>
							<button type="button" class="inline-flex items-center justify-center rounded-lg bg-amber-500 px-4 py-2 text-sm font-medium text-white hover:bg-amber-600">Warning</button>
							<button type="button" class="inline-flex items-center justify-center rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-700">Danger</button>
							<button type="button" class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">Ghost</button>
							<button type="button" disabled class="inline-flex items-center justify-center rounded-lg border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-400">Disabled</button>
						</div>
					</div>
				</section>

				<!-- Badges -->
				<section>
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Бейджи</h2>
					<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<div class="flex flex-wrap items-center gap-3 text-xs font-medium">
							<span class="rounded-md bg-zinc-100 px-2.5 py-1 text-zinc-700">Neutral</span>
							<span class="rounded-md bg-sky-100 px-2.5 py-1 text-sky-700">Info</span>
							<span class="rounded-md bg-emerald-100 px-2.5 py-1 text-emerald-700">Success</span>
							<span class="rounded-md bg-amber-100 px-2.5 py-1 text-amber-700">Warning</span>
							<span class="rounded-md bg-rose-100 px-2.5 py-1 text-rose-700">Danger</span>
						</div>
					</div>
				</section>

				<!-- Cards -->
				<section>
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Карточки</h2>
					<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
						<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
							<h3 class="mb-2 text-sm font-semibold text-zinc-900">Базовая карточка</h3>
							<p class="text-sm text-zinc-600">Минимальная, чистая, с мягкой тенью.</p>
						</div>
						<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-0 shadow-sm backdrop-blur">
							<div class="border-b border-zinc-200/80 p-4 text-sm font-semibold text-zinc-900">Заголовок</div>
							<div class="p-4 text-sm text-zinc-600">Тело карточки с содержимым.</div>
							<div class="border-t border-zinc-200/80 p-3 text-right"><a href="#" class="text-sm font-medium text-zinc-700 hover:text-zinc-900">Действие</a></div>
						</div>
						<div class="rounded-xl border border-zinc-200/80 bg-gradient-to-br from-white/90 to-zinc-50/90 p-5 shadow-sm backdrop-blur">
							<h3 class="mb-2 text-sm font-semibold text-zinc-900">Карточка (gradient)</h3>
							<p class="text-sm text-zinc-600">Аккуратный градиент и стекло.</p>
						</div>
					</div>
				</section>

				<!-- Progress -->
				<section>
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Прогресс</h2>
					<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<div class="space-y-4">
							<div>
								<div class="mb-1 flex items-center justify-between text-xs text-zinc-600">
									<span>Загрузка</span>
									<span>45%</span>
								</div>
								<div class="h-2 w-full overflow-hidden rounded-md bg-zinc-200" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="45">
									<div class="h-full w-[45%] rounded-md bg-zinc-900"></div>
								</div>
							</div>
							<div>
								<div class="mb-1 flex items-center justify-between text-xs text-zinc-600">
									<span>Обработка</span>
									<span class="text-emerald-700">70%</span>
								</div>
								<div class="h-2 w-full overflow-hidden rounded-full bg-emerald-100" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="70">
									<div class="h-full w-[70%] rounded-full bg-emerald-600"></div>
								</div>
							</div>
						</div>
					</div>
				</section>

				<!-- Pagination -->
				<section>
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Пагинация</h2>
					<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<nav class="flex items-center justify-between" aria-label="Пагинация">
							<a href="#" class="rounded-lg border border-zinc-200 px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-50">Назад</a>
							<ul class="flex items-center gap-1">
								<li><a href="#" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-zinc-200 text-sm text-zinc-700 hover:bg-zinc-50">1</a></li>
								<li><a aria-current="page" href="#" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-zinc-900 text-sm font-medium text-white">2</a></li>
								<li><a href="#" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-zinc-200 text-sm text-zinc-700 hover:bg-zinc-50">3</a></li>
								<li><span class="inline-flex h-9 w-9 items-center justify-center text-sm text-zinc-400">…</span></li>
								<li><a href="#" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-zinc-200 text-sm text-zinc-700 hover:bg-zinc-50">10</a></li>
							</ul>
							<a href="#" class="rounded-lg border border-zinc-200 px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-50">Вперёд</a>
						</nav>
					</div>
				</section>

				<!-- Alerts -->
				<section>
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Оповещения</h2>
					<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
						<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-4 shadow-sm backdrop-blur">
							<div class="text-sm text-zinc-800">Neutral: простое уведомление.</div>
						</div>
						<div class="rounded-xl border border-sky-200 bg-sky-50 p-4 shadow-sm">
							<div class="text-sm text-sky-800">Info: информационное сообщение.</div>
						</div>
						<div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
							<div class="text-sm text-emerald-800">Success: успешно выполнено.</div>
						</div>
						<div class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
							<div class="text-sm text-amber-800">Warning: обратите внимание.</div>
						</div>
						<div class="rounded-xl border border-rose-200 bg-rose-50 p-4 shadow-sm md:col-span-2">
							<div class="text-sm text-rose-800">Danger: произошла ошибка.</div>
						</div>
					</div>
				</section>

				<!-- Toggles -->
				<section>
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Тумблеры</h2>
					<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
							<label class="flex cursor-pointer items-center justify-between gap-4">
								<span class="text-sm text-zinc-800">Уведомления</span>
								<input type="checkbox" class="peer sr-only">
								<span class="relative inline-block h-6 w-11 rounded-full bg-zinc-300 transition peer-checked:bg-zinc-900 after:absolute after:left-0.5 after:top-1/2 after:-translate-y-1/2 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
							</label>
							<label class="flex cursor-pointer items-center justify-between gap-4">
								<span class="text-sm text-zinc-800">Автосохранение</span>
								<input type="checkbox" class="peer sr-only" checked>
								<span class="relative inline-block h-6 w-11 rounded-full bg-zinc-300 transition peer-checked:bg-emerald-600 after:absolute after:left-0.5 after:top-1/2 after:-translate-y-1/2 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
							</label>
						</div>
					</div>
				</section>

				<!-- Forms -->
				<section>
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Формы</h2>
					<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<form action="#" method="post" class="space-y-4">
							<div>
								<label for="f-name" class="mb-1 block text-sm font-medium text-zinc-800">Имя</label>
								<input id="f-name" name="name" type="text" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-zinc-500 focus:outline-none" placeholder="Иван Иванов">
							</div>
							<div>
								<label for="f-email" class="mb-1 block text-sm font-medium text-zinc-800">Email</label>
								<input id="f-email" name="email" type="email" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-zinc-500 focus:outline-none" placeholder="you@example.com">
								<p class="mt-1 text-xs text-zinc-500">Мы никому не передаём ваш email.</p>
							</div>
							<div>
								<label for="f-role" class="mb-1 block text-sm font-medium text-zinc-800">Роль</label>
								<select id="f-role" name="role" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-zinc-500 focus:outline-none">
									<option>Администратор</option>
									<option>Редактор</option>
									<option>Гость</option>
								</select>
							</div>
							<fieldset>
								<legend class="mb-2 text-sm font-medium text-zinc-800">Подписки</legend>
								<label class="mb-2 flex items-center gap-3 text-sm text-zinc-800"><input type="checkbox" class="h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500"> Новости</label>
								<label class="mb-2 flex items-center gap-3 text-sm text-zinc-800"><input type="checkbox" class="h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500"> Обновления</label>
							</fieldset>
							<fieldset>
								<legend class="mb-2 text-sm font-medium text-zinc-800">Уровень доступа</legend>
								<label class="mb-2 flex items-center gap-3 text-sm text-zinc-800"><input name="access" type="radio" class="h-4 w-4 border-zinc-300 text-zinc-900 focus:ring-zinc-500"> Полный</label>
								<label class="mb-2 flex items-center gap-3 text-sm text-zinc-800"><input name="access" type="radio" class="h-4 w-4 border-zinc-300 text-zinc-900 focus:ring-zinc-500"> Ограниченный</label>
							</fieldset>
							<div>
								<label for="f-notes" class="mb-1 block text-sm font-medium text-zinc-800">Заметки</label>
								<textarea id="f-notes" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-zinc-500 focus:outline-none" rows="3" placeholder="Краткое описание..."></textarea>
							</div>
							<div class="flex items-center justify-between">
								<p class="text-xs text-rose-600">Пример ошибки: Email обязателен</p>
								<div class="flex gap-2">
									<button type="button" class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">Отмена</button>
									<button type="submit" class="inline-flex items-center justify-center rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800">Сохранить</button>
								</div>
							</div>
						</form>
					</div>
				</section>

				<!-- Quote blocks -->
				<section>
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Цветные цитаты / аннотации</h2>
					<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
						<blockquote class="rounded-xl border-l-4 border-sky-500 bg-sky-50/80 p-4 text-sm text-sky-900 shadow-sm">
							<p class="mb-1 font-medium">Info</p>
							<p>Подсказка с дополнительной информацией.</p>
						</blockquote>
						<blockquote class="rounded-xl border-l-4 border-emerald-500 bg-emerald-50/80 p-4 text-sm text-emerald-900 shadow-sm">
							<p class="mb-1 font-medium">Success</p>
							<p>Операция выполнена успешно.</p>
						</blockquote>
						<blockquote class="rounded-xl border-l-4 border-amber-500 bg-amber-50/80 p-4 text-sm text-amber-900 shadow-sm">
							<p class="mb-1 font-medium">Warning</p>
							<p>Обратите внимание на важный нюанс.</p>
						</blockquote>
						<blockquote class="rounded-xl border-l-4 border-rose-500 bg-rose-50/80 p-4 text-sm text-rose-900 shadow-sm">
							<p class="mb-1 font-medium">Danger</p>
							<p>Ошибка: требуется вмешательство.</p>
						</blockquote>
					</div>
				</section>

				<!-- Tabs -->
				<section>
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Вкладки</h2>
					<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<nav class="flex gap-2" aria-label="Вкладки">
							<a href="#" class="rounded-lg bg-zinc-900 px-3 py-1.5 text-sm font-medium text-white">Обзор</a>
							<a href="#" class="rounded-lg border border-zinc-200 px-3 py-1.5 text-sm text-zinc-700 hover:bg-zinc-50">Профиль</a>
							<a href="#" class="rounded-lg border border-zinc-200 px-3 py-1.5 text-sm text-zinc-700 hover:bg-zinc-50">Настройки</a>
						</nav>
						<div class="mt-4 text-sm text-zinc-600">Контент активной вкладки.</div>
					</div>
				</section>

				<!-- Skeletons -->
				<section>
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Skeleton загрузчики</h2>
					<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<div class="animate-pulse space-y-3">
							<div class="h-4 w-1/3 rounded bg-zinc-200"></div>
							<div class="h-3 w-full rounded bg-zinc-200"></div>
							<div class="h-3 w-5/6 rounded bg-zinc-200"></div>
							<div class="h-3 w-2/3 rounded bg-zinc-200"></div>
						</div>
					</div>
				</section>

				<!-- Avatars / Stack -->
				<section>
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Аватары и стек</h2>
					<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<div class="flex items-center gap-3">
							<img alt="A" src="https://i.pravatar.cc/40?img=1" class="h-10 w-10 rounded-full object-cover transition hover:scale-[1.03] hover:ring-2 hover:ring-zinc-300">
							<img alt="B" src="https://i.pravatar.cc/40?img=2" class="h-10 w-10 rounded-full object-cover transition hover:scale-[1.03] hover:ring-2 hover:ring-zinc-300">
							<img alt="C" src="https://i.pravatar.cc/40?img=3" class="h-10 w-10 rounded-full object-cover transition hover:scale-[1.03] hover:ring-2 hover:ring-zinc-300">
						</div>
						<div class="mt-4 flex -space-x-2">
							<img alt="A" src="https://i.pravatar.cc/40?img=4" class="h-8 w-8 rounded-full ring-2 ring-white">
							<img alt="B" src="https://i.pravatar.cc/40?img=5" class="h-8 w-8 rounded-full ring-2 ring-white">
							<img alt="C" src="https://i.pravatar.cc/40?img=6" class="h-8 w-8 rounded-full ring-2 ring-white">
							<span class="flex h-8 w-8 items-center justify-center rounded-full bg-zinc-200 text-xs text-zinc-700 ring-2 ring-white">+5</span>
						</div>

						<!-- Avatars with status/counter -->
						<div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
							<div class="relative inline-block h-12 w-12">
								<img alt="Online" src="https://i.pravatar.cc/64?img=7" class="block h-12 w-12 rounded-full object-cover ring-2 ring-white transition hover:scale-[1.03] hover:ring-zinc-300">
								<span class="absolute bottom-0 right-0 inline-block h-3.5 w-3.5 translate-x-[2px] translate-y-[2px] rounded-full bg-emerald-500 ring-2 ring-white"></span>
							</div>
							<div class="relative inline-block h-12 w-12">
								<img alt="Offline" src="https://i.pravatar.cc/64?img=8" class="block h-12 w-12 rounded-full object-cover ring-2 ring-white transition hover:scale-[1.03] hover:ring-zinc-300">
								<span class="absolute bottom-0 right-0 inline-block h-3.5 w-3.5 translate-x-[2px] translate-y-[2px] rounded-full bg-zinc-400 ring-2 ring-white"></span>
							</div>
							<div class="relative inline-block h-12 w-12">
								<img alt="Messages" src="https://i.pravatar.cc/64?img=9" class="block h-12 w-12 rounded-full object-cover ring-2 ring-white transition hover:scale-[1.03] hover:ring-zinc-300">
								<span class="absolute top-0 right-0 inline-flex -translate-y-[2px] translate-x-[2px] items-center justify-center rounded-full bg-rose-600 px-1.5 text-[10px] font-medium text-white ring-2 ring-white">12</span>
							</div>
						</div>
					</div>
				</section>

				<!-- Input group -->
				<section>
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Input group</h2>
					<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
							<label class="block">
								<span class="mb-1 block text-sm font-medium text-zinc-800">URL</span>
								<div class="flex rounded-lg border border-zinc-300 focus-within:border-zinc-500">
									<span class="inline-flex items-center rounded-l-lg border-r border-zinc-300 bg-zinc-50 px-3 text-sm text-zinc-500">https://</span>
									<input type="text" class="min-w-0 flex-1 rounded-r-lg bg-white px-3 py-2 text-sm text-zinc-900 outline-none" placeholder="dev.roocms.com">
								</div>
							</label>
							<label class="block">
								<span class="mb-1 block text-sm font-medium text-zinc-800">Поиск</span>
								<div class="flex rounded-lg border border-zinc-300 focus-within:border-zinc-500">
									<input type="text" class="min-w-0 flex-1 rounded-l-lg bg-white px-3 py-2 text-sm text-zinc-900 outline-none" placeholder="Запрос...">
									<button type="button" class="rounded-r-lg bg-zinc-900 px-3 py-2 text-sm font-medium text-white hover:bg-zinc-800">Найти</button>
								</div>
							</label>
						</div>
					</div>
				</section>

				<!-- Chips & Divider -->
				<section>
					<h2 class="mb-4 text-base font-semibold text-зinc-900">Чипсы и разделитель</h2>
					<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<div class="mb-4 flex flex-wrap gap-2">
							<span class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white pl-3 pr-1 py-1 text-xs text-zinc-700">
								Design
								<button type="button" class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-zinc-100 text-[12px] leading-none text-zinc-500 hover:bg-zinc-200">×</button>
							</span>
							<span class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white pl-3 pr-1 py-1 text-xs text-zinc-700">
								UX
								<button type="button" class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-zinc-100 text-[12px] leading-none text-zinc-500 hover:bg-zinc-200">×</button>
							</span>
							<span class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white pl-3 pr-1 py-1 text-xs text-zinc-700">
								CMS
								<button type="button" class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-zinc-100 text-[12px] leading-none text-zinc-500 hover:bg-zinc-200">×</button>
							</span>
						</div>
						<div class="relative py-3">
							<div class="absolute inset-0 flex items-center" aria-hidden="true">
								<div class="w-full border-t border-zinc-200"></div>
							</div>
							<div class="relative flex justify-center">
								<span class="bg-white px-2 text-xs text-zinc-500">Разделитель</span>
							</div>
						</div>
					</div>
				</section>
				<section>
					<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
						<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
							<div class="text-xs font-medium text-zinc-500">Выручка</div>
							<div class="mt-2 text-2xl font-semibold text-zinc-900">$2.6M</div>
							<div class="mt-1 text-xs text-emerald-600">+4.5% за неделю</div>
										</div>
						<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
							<div class="text-xs font-medium text-zinc-500">Средний чек</div>
							<div class="mt-2 text-2xl font-semibold text-zinc-900">$455</div>
							<div class="mt-1 text-xs text-zinc-500">−0.5% за неделю</div>
									</div>
						<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
							<div class="text-xs font-medium text-zinc-500">Продажи</div>
							<div class="mt-2 text-2xl font-semibold text-zinc-900">5,888</div>
							<div class="mt-1 text-xs text-emerald-600">+4.5% за неделю</div>
									</div>
						<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
							<div class="text-xs font-medium text-zinc-500">Просмотры</div>
							<div class="mt-2 text-2xl font-semibold text-zinc-900">823,067</div>
							<div class="mt-1 text-xs text-emerald-600">+21.2% за неделю</div>
						</div>
					</div>

					<div class="mt-10 rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<div class="mb-4 flex items-center justify-between">
							<h2 class="text-base font-semibold text-zinc-900">Последние заказы</h2>
							<a href="/acp/orders" class="text-sm font-medium text-zinc-700 hover:text-zinc-900">Все заказы</a>
						</div>
						<div class="-mx-5 overflow-x-auto px-5">
							<table class="min-w-full text-left text-sm" aria-label="Последние заказы">
								<thead class="border-b border-zinc-200/80 text-zinc-500">
									<tr>
										<th scope="col" class="py-3 pr-6 font-medium">Номер</th>
										<th scope="col" class="py-3 pr-6 font-medium">Дата</th>
										<th scope="col" class="py-3 pr-6 font-medium">Клиент</th>
										<th scope="col" class="py-3 pr-6 font-medium">Событие</th>
										<th scope="col" class="py-3 pr-0 font-medium text-right">Сумма</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-zinc-100/80">
									<tr class="hover:bg-zinc-50">
										<td class="py-3 pr-6 text-zinc-900">3000</td>
										<td class="py-3 pr-6 text-zinc-600">9 мая 2024</td>
										<td class="py-3 pr-6 text-zinc-900">Leslie Alexander</td>
										<td class="py-3 pr-6 text-zinc-900">Bear Hug: Live in Concert</td>
										<td class="py-3 pr-0 text-right text-zinc-900">US$80.00</td>
									</tr>
									<tr class="hover:bg-zinc-50">
										<td class="py-3 pr-6 text-zinc-900">3001</td>
										<td class="py-3 pr-6 text-zinc-600">5 мая 2024</td>
										<td class="py-3 pr-6 text-zinc-900">Michael Foster</td>
										<td class="py-3 pr-6 text-zinc-900">Six Fingers — DJ Set</td>
										<td class="py-3 pr-0 text-right text-zinc-900">US$299.00</td>
									</tr>
									<tr class="hover:bg-zinc-50">
										<td class="py-3 pr-6 text-zinc-900">3002</td>
										<td class="py-3 pr-6 text-zinc-600">28 апр 2024</td>
										<td class="py-3 pr-6 text-zinc-900">Dries Vincent</td>
										<td class="py-3 pr-6 text-zinc-900">We All Look The Same</td>
										<td class="py-3 pr-0 text-right text-zinc-900">US$150.00</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</section>
			</div>
		</section>
	</div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../../layouts/base.php';


