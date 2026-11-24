<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

$page_title = 'UI Kit — Admin Control Panel — RooCMS';
$page_description = 'UI components for RooCMS ACP';

$theme_name = basename(dirname(__DIR__, 2));
$theme_base = '/themes/'.$theme_name;
$page_scripts = [$theme_base.'/assets/js/pages/acp-ui-kit.js'];

ob_start();
?>
<div class="py-10 px-4 sm:px-6" id="acp-ui-kit">
	<div class="grid grid-cols-1 gap-8 lg:grid-cols-[260px_1fr]">
		<?php require __DIR__ . '/../../partials/acp-nav.php'; ?>

		<section class="min-w-0">
			<header class="mb-8">
				<nav class="mb-3 text-sm text-zinc-500" aria-label="Хлебные крошки">
					<ol class="flex items-center gap-2">
						<li><a href="/" class="hover:text-zinc-700">Главная</a></li>
						<li aria-hidden="true" class="text-zinc-400">/</li>
						<li><a href="/!/acp" class="hover:text-zinc-700">ACP</a></li>
						<li aria-hidden="true" class="text-zinc-400">/</li>
						<li><a href="/!/acp/ui-kit" aria-current="page" class="text-zinc-700">UI Kit</a></li>
					</ol>
				</nav>
				<h1 class="text-2xl font-semibold tracking-tight text-zinc-900">UI Kit</h1>
				<p class="mt-2 text-sm text-zinc-600">Компоненты админ-панели и интерактивные примеры. Все данные демонстрационные и не сохраняются на сервере.</p>
				<nav class="mt-4 flex flex-wrap gap-2" aria-label="Разделы UI Kit">
					<a class="btn secondary small" href="#kit-buttons">Кнопки</a>
					<a class="btn secondary small" href="#kit-form">Формы</a>
					<a class="btn secondary small" href="#kit-tabs">Вкладки</a>
					<a class="btn secondary small" href="#kit-modals">Модальные окна</a>
					<a class="btn secondary small" href="#kit-orders">Таблица</a>
				</nav>
			</header>

			<div class="space-y-10">
				<!-- Buttons -->
				<section id="kit-buttons">
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Кнопки</h2>
					<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<div class="flex flex-wrap gap-3">
							<button type="button" class="btn primary">Primary</button>
							<button type="button" class="btn secondary">Secondary</button>
							<button type="button" class="btn success">Success</button>
							<button type="button" class="btn warning">Warning</button>
							<button type="button" class="btn danger">Danger</button>
							<button type="button" class="btn outline">Ghost</button>
							<button type="button" disabled class="btn secondary">Disabled</button>
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

				<section>
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Статусы</h2>
					<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<div class="flex flex-wrap items-center gap-3 text-xs font-medium">
							<span class="rounded-sm bg-zinc-100 border border-zinc-200 px-2.5 py-1 text-zinc-700">Normal</span>
							<span class="rounded-sm bg-sky-100 border border-sky-200 px-2.5 py-1 text-sky-700">New</span>
							<span class="rounded-sm bg-emerald-100 border border-emerald-200 px-2.5 py-1 text-emerald-700">Hot</span>
							<span class="rounded-sm bg-amber-100 border border-amber-200 px-2.5 py-1 text-amber-700">Updated</span>
							<span class="rounded-sm bg-rose-100 border border-rose-200 px-2.5 py-1 text-rose-700">On hold</span>
							<span class="rounded-sm bg-blue-100 border border-blue-200 px-2.5 py-1 text-blue-700">Draft</span>
							<span class="rounded-sm bg-purple-100 border border-purple-200 px-2.5 py-1 text-purple-700">Pending</span>
							<span class="rounded-sm bg-orange-100 border border-orange-200 px-2.5 py-1 text-orange-700">Archived</span>
							<span class="rounded-sm bg-gray-100 border border-gray-200 px-2.5 py-1 text-gray-700">Cancelled</span>
							<span class="rounded-sm bg-pink-100 border border-pink-200 px-2.5 py-1 text-pink-700">Rejected</span>
							<span class="rounded-sm bg-teal-100 border border-teal-200 px-2.5 py-1 text-teal-700">Scheduled</span>
							<span class="rounded-sm bg-indigo-100 border border-indigo-200 px-2.5 py-1 text-indigo-700">Verified</span>
							<span class="rounded-sm bg-lime-100 border border-lime-200 px-2.5 py-1 text-lime-700">Completed</span>
							<span class="rounded-sm bg-cyan-100 border border-cyan-200 px-2.5 py-1 text-cyan-700">In progress</span>
							<span class="rounded-sm bg-fuchsia-100 border border-fuchsia-200 px-2.5 py-1 text-fuchsia-700">Deleted</span>
							<span class="rounded-sm bg-violet-100 border border-violet-200 px-2.5 py-1 text-violet-700">Inactive</span>
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
							<div class="border-t border-zinc-200/80 p-3 text-right"><a href="#kit-form" class="text-sm font-medium text-zinc-700 hover:text-zinc-900">Действие</a></div>
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
								<div class="h-2 w-full overflow-hidden rounded-md bg-zinc-200" role="progressbar" aria-label="Прогресс выполнения" aria-valuemin="0" aria-valuemax="100" aria-valuenow="45" aria-valuetext="Загрузка: 45%">
									<div class="h-full w-[45%] rounded-md bg-zinc-900"></div>
								</div>
							</div>
							<div>
								<div class="mb-1 flex items-center justify-between text-xs text-zinc-600">
									<span>Обработка</span>
									<span class="text-emerald-700">70%</span>
								</div>
								<div class="h-2 w-full overflow-hidden rounded-full bg-emerald-100" role="progressbar" aria-label="Прогресс выполнения" aria-valuemin="0" aria-valuemax="100" aria-valuenow="70" aria-valuetext="Обработка: 70%">
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
						<nav class="flex flex-wrap items-center justify-between gap-3" aria-label="Пагинация" data-pagination>
							<button type="button" class="btn secondary small" data-previous>Назад</button>
							<ul class="flex flex-wrap items-center gap-1">
								<?php foreach ([1, 2, 3, 4, 10] as $demo_page): ?>
									<li><button type="button" class="btn <?php render_html($demo_page === 2 ? 'primary' : 'secondary'); ?> small" data-page="<?php render_html((string)$demo_page); ?>" <?php if ($demo_page === 2): ?>aria-current="page"<?php endif; ?>><?php render_html((string)$demo_page); ?></button></li>
								<?php endforeach; ?>
							</ul>
							<button type="button" class="btn secondary small" data-next>Вперёд</button>
						</nav>
						<p class="mt-3 text-sm text-zinc-600" role="status" data-page-status>Страница 2 из 10</p>
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
								<input type="checkbox" role="switch" class="peer sr-only">
								<span aria-hidden="true" class="relative inline-block h-6 w-11 rounded-full bg-zinc-300 transition peer-checked:bg-sky-900 after:absolute after:left-0.5 after:top-1/2 after:-translate-y-1/2 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
							</label>
							<label class="flex cursor-pointer items-center justify-between gap-4">
								<span class="text-sm text-zinc-800">Автосохранение</span>
								<input type="checkbox" role="switch" class="peer sr-only" checked>
								<span aria-hidden="true" class="relative inline-block h-6 w-11 rounded-full bg-zinc-300 transition peer-checked:bg-emerald-600 after:absolute after:left-0.5 after:top-1/2 after:-translate-y-1/2 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
							</label>
						</div>
					</div>
				</section>

				<!-- Forms -->
				<section id="kit-form">
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Формы</h2>
					<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<form class="space-y-4" data-demo-form>
							<div>
								<label for="f-name" class="mb-1 block text-sm font-medium text-zinc-800">Имя</label>
								<input id="f-name" name="name" type="text" required autocomplete="name" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-sky-500 focus:outline-none" placeholder="Иван Иванов">
							</div>
							<div>
								<label for="f-email" class="mb-1 block text-sm font-medium text-zinc-800">Email</label>
								<input id="f-email" name="email" type="email" required autocomplete="email" aria-describedby="f-email-help" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-sky-500 focus:outline-none" placeholder="you@example.com">
								<p id="f-email-help" class="mt-1 text-xs text-zinc-500">Имя и email обязательны. Данные остаются в этом примере.</p>
							</div>
							<div>
								<label for="f-role" class="mb-1 block text-sm font-medium text-zinc-800">Роль</label>
								<select id="f-role" name="role" class="select-custom block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none">
									<option>Администратор</option>
									<option>Редактор</option>
									<option>Гость</option>
								</select>
							</div>
							<fieldset>
								<legend class="mb-2 text-sm font-medium text-zinc-800">Подписки</legend>
								<label class="mb-2 flex items-center gap-3 text-sm text-zinc-800"><input name="subscriptions[]" value="news" type="checkbox" class="h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500"> Новости</label>
								<label class="mb-2 flex items-center gap-3 text-sm text-zinc-800"><input name="subscriptions[]" value="updates" type="checkbox" class="h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500"> Обновления</label>
							</fieldset>
							<fieldset>
								<legend class="mb-2 text-sm font-medium text-zinc-800">Уровень доступа</legend>
								<label class="mb-2 flex items-center gap-3 text-sm text-zinc-800"><input name="access" value="limited" type="radio" class="h-4 w-4 border-zinc-300 text-zinc-900 focus:ring-zinc-500"> Полный</label>
								<label class="mb-2 flex items-center gap-3 text-sm text-zinc-800"><input name="access" type="radio" class="h-4 w-4 border-zinc-300 text-zinc-900 focus:ring-zinc-500"> Ограниченный</label>
							</fieldset>
							<div>
								<label for="f-notes" class="mb-1 block text-sm font-medium text-zinc-800">Заметки</label>
								<textarea id="f-notes" name="notes" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-sky-500 focus:outline-none" rows="3" placeholder="Краткое описание..."></textarea>
							</div>
							<div class="flex flex-wrap items-center justify-between gap-3">
								<p class="text-sm text-emerald-700" role="status" data-form-status></p>
								<div class="flex gap-2">
									<button type="reset" class="btn secondary">Сбросить</button>
									<button type="submit" class="btn primary">Проверить форму</button>
								</div>
							</div>
						</form>
					</div>
				</section>

				<!-- Quote blocks -->
				<section>
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Состояния полей</h2>
					<div class="grid grid-cols-1 gap-4 md:grid-cols-2 rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm">
						<label class="block text-sm text-zinc-800">Только чтение
							<input type="text" value="ID-1024" readonly class="mt-1 block w-full rounded-lg border border-zinc-300 bg-zinc-50 px-3 py-2">
						</label>
						<label class="block text-sm text-zinc-500">Недоступное поле
							<input type="text" value="Недоступно для вашей роли" disabled class="mt-1 block w-full rounded-lg border border-zinc-200 bg-zinc-100 px-3 py-2 cursor-not-allowed">
						</label>
						<label class="block text-sm text-zinc-800">Поле с ошибкой
							<input type="email" value="invalid-email" aria-invalid="true" aria-describedby="kit-field-error" class="mt-1 block w-full rounded-lg border border-rose-500 bg-white px-3 py-2">
							<span id="kit-field-error" class="mt-1 block text-xs text-rose-700">Введите email в формате name@example.com.</span>
						</label>
						<label class="block text-sm text-zinc-800">Корректное поле
							<input type="email" value="editor@example.com" aria-describedby="kit-field-success" class="mt-1 block w-full rounded-lg border border-emerald-500 bg-white px-3 py-2">
							<span id="kit-field-success" class="mt-1 block text-xs text-emerald-700">Адрес указан верно.</span>
						</label>
					</div>
				</section>

				<section id="kit-modals">
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Модальные окна</h2>
					<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm">
						<div class="flex flex-wrap gap-3">
							<button type="button" class="btn info" data-modal-type="notice">Информация</button>
							<button type="button" class="btn success" data-modal-type="success">Успех</button>
							<button type="button" class="btn warning" data-modal-type="warning">Предупреждение</button>
							<button type="button" class="btn danger" data-modal-type="alert">Подтверждение</button>
						</div>
						<p class="mt-3 text-sm text-zinc-600" role="status" data-modal-status></p>
					</div>
				</section>

				<section>
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Пустое состояние и раскрывающийся блок</h2>
					<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
						<div class="rounded-xl border border-dashed border-zinc-300 bg-white/80 p-5 text-center">
							<h3 class="text-sm font-semibold text-zinc-900">Пока нет записей</h3>
								<p class="mt-2 mb-4 text-sm text-zinc-600">Добавьте первую запись, чтобы начать работу.</p>
							<a href="#kit-form" class="btn primary">Заполнить пример</a>
						</div>
						<details class="rounded-xl border border-zinc-200 bg-white/80 p-5">
							<summary class="cursor-pointer text-sm font-semibold text-zinc-900">Как использовать этот UI Kit?</summary>
							<p class="mt-3 text-sm text-zinc-600">Копируйте разметку нужного компонента. Кнопки используют общие классы btn и модификаторы primary, secondary, success, warning, danger, info; размеры — small и large.</p>
						</details>
					</div>
				</section>

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
				<section id="kit-tabs">
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Вкладки</h2>
					<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<div class="flex flex-wrap gap-2" role="tablist" aria-label="Пример вкладок">
							<button type="button" id="kit-tab-overview" role="tab" aria-selected="true" aria-controls="kit-panel-overview" class="btn primary">Обзор</button>
							<button type="button" id="kit-tab-profile" role="tab" aria-selected="false" aria-controls="kit-panel-profile" tabindex="-1" class="btn secondary">Профиль</button>
							<button type="button" id="kit-tab-settings" role="tab" aria-selected="false" aria-controls="kit-panel-settings" tabindex="-1" class="btn secondary">Настройки</button>
						</div>
						<div id="kit-panel-overview" role="tabpanel" aria-labelledby="kit-tab-overview" tabindex="0" class="mt-4 text-sm text-zinc-600">Обзор компонентов админ-панели. Переключайте вкладки мышью или стрелками на клавиатуре.</div>
						<div id="kit-panel-profile" role="tabpanel" aria-labelledby="kit-tab-profile" tabindex="0" class="mt-4 text-sm text-zinc-600" hidden>Профиль: Иван Иванов, редактор. Пример содержимого второй вкладки.</div>
						<div id="kit-panel-settings" role="tabpanel" aria-labelledby="kit-tab-settings" tabindex="0" class="mt-4 text-sm text-zinc-600" hidden>Настройки: уведомления включены. Пример содержимого третьей вкладки.</div>
					</div>
				</section>

				<!-- Skeletons -->
				<section>
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Состояние загрузки</h2>
					<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<div class="animate-pulse space-y-3" aria-hidden="true">
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
							<span role="img" aria-label="A" class="h-10 w-10 rounded-full object-cover transition hover:scale-[1.03] hover:ring-2 hover:ring-zinc-300 inline-flex items-center justify-center bg-sky-100 text-sm font-semibold text-sky-800">АИ</span>
							<span role="img" aria-label="B" class="h-10 w-10 rounded-full object-cover transition hover:scale-[1.03] hover:ring-2 hover:ring-zinc-300 inline-flex items-center justify-center bg-sky-100 text-sm font-semibold text-sky-800">МП</span>
							<span role="img" aria-label="C" class="h-10 w-10 rounded-full object-cover transition hover:scale-[1.03] hover:ring-2 hover:ring-zinc-300 inline-flex items-center justify-center bg-sky-100 text-sm font-semibold text-sky-800">ЕК</span>
						</div>
						<div class="mt-4 flex -space-x-2">
							<span role="img" aria-label="A" class="h-8 w-8 rounded-full ring-2 ring-white inline-flex items-center justify-center bg-sky-100 text-sm font-semibold text-sky-800">АИ</span>
							<span role="img" aria-label="B" class="h-8 w-8 rounded-full ring-2 ring-white inline-flex items-center justify-center bg-sky-100 text-sm font-semibold text-sky-800">МП</span>
							<span role="img" aria-label="C" class="h-8 w-8 rounded-full ring-2 ring-white inline-flex items-center justify-center bg-sky-100 text-sm font-semibold text-sky-800">ЕК</span>
							<span class="flex h-8 w-8 items-center justify-center rounded-full bg-zinc-200 text-xs text-zinc-700 ring-2 ring-white">+5</span>
						</div>

						<!-- Avatars with status/counter -->
						<div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
							<div class="relative inline-block h-12 w-12">
								<span role="img" aria-label="Online" class="h-12 w-12 rounded-full object-cover ring-2 ring-white transition hover:scale-[1.03] hover:ring-zinc-300 inline-flex items-center justify-center bg-sky-100 text-sm font-semibold text-sky-800">АИ</span>
								<span class="absolute bottom-0 right-0 inline-block h-3.5 w-3.5 translate-x-[2px] translate-y-[2px] rounded-full bg-emerald-500 ring-2 ring-white animate-pulse"></span>
							</div>
							<div class="relative inline-block h-12 w-12">
								<span role="img" aria-label="Offline" class="h-12 w-12 rounded-full object-cover ring-2 ring-white transition hover:scale-[1.03] hover:ring-zinc-300 inline-flex items-center justify-center bg-sky-100 text-sm font-semibold text-sky-800">МП</span>
								<span class="absolute bottom-0 right-0 inline-block h-3.5 w-3.5 translate-x-[2px] translate-y-[2px] rounded-full bg-zinc-400 ring-2 ring-white"></span>
							</div>
							<div class="relative inline-block h-12 w-12">
								<span role="img" aria-label="Messages" class="h-12 w-12 rounded-full object-cover ring-2 ring-white transition hover:scale-[1.03] hover:ring-zinc-300 inline-flex items-center justify-center bg-sky-100 text-sm font-semibold text-sky-800">ЕК</span>
								<span class="absolute top-0 right-0 inline-flex -translate-y-[2px] translate-x-[2px] items-center justify-center rounded-full bg-rose-600 px-1.5 text-[10px] font-medium text-white ring-2 ring-white">12</span>
							</div>
						</div>
					</div>
				</section>

				<!-- Input group -->
				<section>
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Группы полей</h2>
					<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
							<label class="block">
								<span class="mb-1 block text-sm font-medium text-zinc-800">URL</span>
								<div class="flex rounded-lg border border-zinc-300 focus-within:border-sky-500">
									<span class="inline-flex items-center rounded-l-lg border-r border-zinc-300 bg-zinc-50 px-3 text-sm text-zinc-500">https://</span>
									<input type="text" class="min-w-0 flex-1 rounded-r-lg bg-white px-3 py-2 text-sm text-zinc-900 outline-none" placeholder="dev.roocms.com">
								</div>
							</label>
							<label class="block">
								<span class="mb-1 block text-sm font-medium text-zinc-800">Поиск</span>
								<div class="flex rounded-lg border border-zinc-300 focus-within:border-sky-500">
									<input type="search" id="kit-search" class="min-w-0 flex-1 rounded-l-lg bg-white px-3 py-2 text-sm text-zinc-900 outline-none" placeholder="Запрос...">
									<button type="button" data-search class="btn primary">Найти</button>
								</div>
							</label>
						</div>
					</div>
				</section>

				<!-- Chips & Divider -->
				<section>
					<h2 class="mb-4 text-base font-semibold text-zinc-900">Метки и разделитель</h2>
					<div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<div class="mb-4 flex flex-wrap gap-2" data-chips>
							<?php foreach (['Design', 'UX', 'CMS'] as $demo_chip): ?>
								<span class="chip" data-chip>
									<?php render_html($demo_chip); ?>
									<button type="button" class="remove-btn" aria-label="Удалить метку <?php render_html($demo_chip); ?>" data-remove-chip="<?php render_html($demo_chip); ?>">×</button>
								</span>
							<?php endforeach; ?>
						</div>
						<button type="button" class="btn secondary small" data-restore-chips>Восстановить метки</button>
						<p class="mt-2 text-sm text-zinc-600" role="status" data-chip-status></p>
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

					<div id="kit-orders" class="mt-10 rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
						<div class="mb-4 flex items-center justify-between">
							<h2 class="text-base font-semibold text-zinc-900">Последние заказы</h2>
							<span class="badge neutral">Демо</span>
						</div>
						<p class="mb-3 text-sm text-zinc-600" role="status" data-search-status>Показано заказов: 3</p>
						<div class="-mx-5 overflow-x-auto px-5" role="region" aria-label="Таблица заказов с горизонтальной прокруткой" tabindex="0">
							<table id="kit-orders-table" class="min-w-full text-left text-sm" aria-label="Последние заказы">
								<caption class="sr-only">Демонстрационные заказы. Поиск доступен в разделе «Группы полей».</caption>
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


