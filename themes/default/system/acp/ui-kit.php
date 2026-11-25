<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

$page_title = 'UI Kit — Admin Control Panel — RooCMS';
$page_description = 'UI components for RooCMS ACP';

$theme_name = basename(dirname(__DIR__, 2));
$theme_base = '/themes/'.$theme_name;
$page_scripts = [$theme_base.'/assets/js/app/acp.js', $theme_base.'/assets/js/pages/acp-ui-kit.js'];

$acp_heading = 'UI Kit';
$acp_intro = 'Компоненты админ-панели и интерактивные примеры. Все данные демонстрационные и не сохраняются на сервере.';
$acp_breadcrumb = 'UI Kit';
$acp_home_label = 'Главная';

ob_start();
?>
<div class="acp-shell" id="acp-ui-kit">
	<div class="acp-layout">
		<?php require __DIR__ . '/../partials/acp-nav.php'; ?>

		<section class="acp-content">
			<header class="mb-8">
                <?php require __DIR__ . '/../partials/acp-heading.php'; ?>
				<nav class="mt-4 flex flex-wrap gap-2" aria-label="Разделы UI Kit">
					<a class="btn secondary small" href="#kit-buttons">Кнопки</a>
					<a class="btn secondary small" href="#kit-form">Формы</a>
					<a class="btn secondary small" href="#kit-tabs">Вкладки</a>
					<a class="btn secondary small" href="#kit-modals">Модальные окна</a>
					<a class="btn secondary small" href="#kit-orders">Таблица</a>
                    <a class="btn secondary small" href="#kit-records">Списки</a>
                    <a class="btn secondary small" href="#kit-structure">Структура</a>
                    <a class="btn secondary small" href="#kit-settings">Настройки</a>
				</nav>
			</header>

			<div class="space-y-10">
				<section id="kit-components">
					<h2 class="ui-section-title">Компоненты ACP</h2>
					<div class="ui-panel space-y-4">
						<p class="text-sm text-zinc-600">Эти же классы используются на рабочих страницах панели. Базовые стили определены в assets/css/components.css; меняйте компонент целиком, не копируйте его оформление на каждую страницу.</p>
						<dl class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
							<div class="ui-detail"><dt class="font-mono font-semibold">ui-panel</dt><dd class="ui-hint">Карточка; варианты spacious, flush и gradient.</dd></div>
							<div class="ui-detail"><dt class="font-mono font-semibold">ui-control / ui-label / ui-hint</dt><dd class="ui-hint">Поля и подписи; варианты compact, search, color, valid. Ошибка — aria-invalid="true".</dd></div>
							<div class="ui-detail"><dt class="font-mono font-semibold">btn / badge / alert</dt><dd class="ui-hint">Кнопки, бейджи и сообщения с именованными цветовыми вариантами.</dd></div>
							<div class="ui-detail"><dt class="font-mono font-semibold">ui-toolbar / ui-actions / ui-pagination</dt><dd class="ui-hint">Адаптивные панели фильтров, действий и навигации.</dd></div>
						</dl>
						<div class="ui-toolbar">
							<label class="ui-label">Компактный фильтр
								<select class="ui-control compact select-custom"><option>Все записи</option><option>Активные</option></select>
							</label>
							<div class="ui-actions">
								<button type="button" class="btn outline success small">Активировать</button>
								<button type="button" class="btn outline warning small">В черновики</button>
								<button type="button" class="btn outline danger small">Удалить</button>
								<button type="button" class="btn outline danger small" disabled>Удалить (недоступно)</button>
							</div>
						</div>
					</div>
				</section>

				<!-- Buttons -->
				<section id="kit-buttons">
					<h2 class="ui-section-title">Кнопки</h2>
					<div class="ui-panel">
						<div class="flex flex-wrap items-center gap-3">
							<button type="button" class="btn primary">Primary</button>
							<button type="button" class="btn secondary">Secondary</button>
							<button type="button" class="btn success">Success</button>
							<button type="button" class="btn warning">Warning</button>
							<button type="button" class="btn danger">Danger</button>
							<button type="button" class="btn outline">Outline</button>
							<button type="button" disabled class="btn secondary">Disabled</button>
							<button type="button" class="btn primary small">Small</button>
							<button type="button" class="btn primary large">Large</button>
							<button type="button" class="btn secondary small">Small secondary</button>
							<button type="button" class="btn secondary large">Large secondary</button>
							<button type="button" class="btn primary" disabled aria-busy="true"><?php require __DIR__ . '/../../partials/ui-spinner.php'; ?> Сохранение…</button>
						</div>
					</div>
				</section>

				<!-- Badges -->
				<section class="acp-content">
					<h2 class="ui-section-title">Бейджи</h2>
					<div class="ui-panel">
						<div class="flex flex-wrap items-center gap-3 text-xs font-medium">
							<span class="badge neutral">Neutral</span>
							<span class="badge info">Info</span>
							<span class="badge success">Success</span>
							<span class="badge warning">Warning</span>
							<span class="badge danger">Danger</span>
							<span class="badge success compact">Компактный</span>
						</div>
					</div>
				</section>

				<section class="acp-content">
					<h2 class="ui-section-title">Статусы</h2>
					<div class="ui-panel">
						<div class="flex flex-wrap items-center gap-3 text-xs font-medium">
							<span class="badge neutral outlined">Normal</span>
							<span class="badge info outlined">New</span>
							<span class="badge success outlined">Hot</span>
							<span class="badge warning outlined">Updated</span>
							<span class="badge danger outlined">On hold</span>
							<span class="badge info outlined">Draft</span>
							<span class="badge violet outlined">Pending</span>
							<span class="badge orange outlined">Archived</span>
							<span class="badge neutral outlined">Cancelled</span>
							<span class="badge pink outlined">Rejected</span>
							<span class="badge success outlined">Scheduled</span>
							<span class="badge indigo outlined">Verified</span>
							<span class="badge success outlined">Completed</span>
							<span class="badge info outlined">In progress</span>
							<span class="badge violet outlined">Deleted</span>
							<span class="badge violet outlined">Inactive</span>
						</div>
					</div>
				</section>

				<!-- Cards -->
				<section>
					<h2 class="ui-section-title">Карточки</h2>
					<div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
						<div class="ui-panel">
							<h3 class="mb-2 text-sm font-semibold text-zinc-900">Базовая карточка</h3>
							<p class="text-sm text-zinc-600">Минимальная, чистая, с мягкой тенью.</p>
						</div>
						<div class="ui-panel flush">
							<div class="border-b border-zinc-200/80 p-4 text-sm font-semibold text-zinc-900">Заголовок</div>
							<div class="p-4 text-sm text-zinc-600">Тело карточки с содержимым.</div>
							<div class="border-t border-zinc-200/80 p-3 text-right"><a href="#kit-form" class="text-sm font-medium text-zinc-700 hover:text-zinc-900">Действие</a></div>
						</div>
						<div class="ui-panel gradient">
							<h3 class="mb-2 text-sm font-semibold text-zinc-900">Карточка (gradient)</h3>
							<p class="text-sm text-zinc-600">Аккуратный градиент и стекло.</p>
						</div>
						<div class="ui-panel spacious">
							<h3 class="mb-2 text-sm font-semibold text-zinc-900">Увеличенные отступы</h3>
							<p class="text-sm text-zinc-600">Вариант spacious: отступы 24 px вместо стандартных 20 px.</p>
						</div>
					</div>
				</section>

				<!-- Progress -->
				<section>
					<h2 class="ui-section-title">Прогресс</h2>
					<div class="ui-panel">
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
					<h2 class="ui-section-title">Пагинация</h2>
					<div class="ui-panel">
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
					<h2 class="ui-section-title">Оповещения</h2>
					<p class="ui-hint mb-4">Уведомления о действиях появляются справа снизу, не сдвигают содержимое и автоматически исчезают через 15 секунд. Их можно закрыть вручную.</p>
					<div class="ui-actions mb-4">
						<button type="button" class="btn secondary" data-notice-type="success">Успешное уведомление</button>
						<button type="button" class="btn secondary" data-notice-type="danger">Уведомление об ошибке</button>
					</div>
					<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
						<div class="alert neutral">
							<div class="text-sm text-zinc-800">Neutral: простое уведомление.</div>
						</div>
						<div class="alert info">
							<div class="text-sm text-sky-800">Info: информационное сообщение.</div>
						</div>
						<div class="alert success">
							<div class="text-sm text-emerald-800">Success: успешно выполнено.</div>
						</div>
						<div class="alert warning">
							<div class="text-sm text-amber-800">Warning: обратите внимание.</div>
						</div>
						<div class="alert danger md:col-span-2">
							<div class="text-sm text-rose-800">Danger: произошла ошибка.</div>
						</div>
					</div>
				</section>

				<!-- Forms -->
				<section id="kit-form">
					<h2 class="ui-section-title">Формы</h2>
					<div class="space-y-6">
						<section id="kit-fields">
							<h3 class="ui-section-title">Поля и выбор</h3>
							<div class="ui-panel">
								<form class="space-y-4" data-demo-form>
									<div>
										<label for="f-name" class="ui-label">Имя</label>
										<input id="f-name" name="name" type="text" required autocomplete="name" class="ui-control" placeholder="Иван Иванов">
									</div>
									<div>
										<label for="f-email" class="ui-label">Email</label>
										<input id="f-email" name="email" type="email" required autocomplete="email" aria-describedby="f-email-help" class="ui-control" placeholder="you@example.com">
										<p id="f-email-help" class="ui-hint">Имя, email и роль обязательны. Данные остаются в этом примере.</p>
									</div>
									<div>
										<label for="f-role" class="ui-label">Роль</label>
										<select id="f-role" name="role" class="ui-control select-custom" required>
											<option value="">Выберите роль</option>
											<option>Администратор</option>
											<option>Редактор</option>
											<option>Гость</option>
										</select>
									</div>
									<fieldset>
										<legend class="mb-2 text-sm font-medium text-zinc-800">Подписки</legend>
										<label class="mb-2 flex items-center gap-3 text-sm text-zinc-800"><input name="subscriptions[]" value="news" type="checkbox"> Новости</label>
										<label class="mb-2 flex items-center gap-3 text-sm text-zinc-800"><input name="subscriptions[]" value="updates" type="checkbox"> Обновления</label>
									</fieldset>
									<fieldset>
										<legend class="mb-2 text-sm font-medium text-zinc-800">Уровень доступа</legend>
										<label class="mb-2 flex items-center gap-3 text-sm text-zinc-800"><input name="access" value="full" type="radio"> Полный</label>
										<label class="mb-2 flex items-center gap-3 text-sm text-zinc-800"><input name="access" value="limited" type="radio"> Ограниченный</label>
									</fieldset>
									<div class="grid gap-4 md:grid-cols-2">
										<label class="ui-label">Цвет
											<input id="kit-color" type="color" value="#0284c7" class="ui-control color mt-1">
										</label>
										<label class="ui-label">Файл (без отправки)
											<input id="kit-file" type="file" class="ui-file mt-1">
										</label>
										<label class="ui-label">Список с группами
											<select class="ui-control select-custom"><option value="">Выберите раздел</option><optgroup label="Контент"><option>Страницы</option><option disabled>Архив (недоступен)</option></optgroup><optgroup label="Управление"><option>Пользователи</option><option>Настройки</option></optgroup></select>
										</label>
									</div>
									<div>
										<label for="f-notes" class="ui-label">Заметки</label>
										<textarea id="f-notes" name="notes" class="ui-control" rows="3" placeholder="Краткое описание..."></textarea>
									</div>
									<div class="flex flex-wrap items-center justify-between gap-3">
										<p class="text-sm text-emerald-700" role="status" data-form-status></p>
										<div class="ui-actions">
											<button type="reset" class="btn secondary">Сбросить</button>
											<button type="submit" class="btn primary">Проверить форму</button>
										</div>
									</div>
								</form>
							</div>
						</section>
						<section id="kit-field-groups">
							<h3 class="ui-section-title">Группы полей</h3>
							<div class="ui-panel">
								<h4 class="mb-3 text-sm font-semibold text-zinc-900">Панель фильтров</h4>
								<div class="ui-filter-grid mb-6">
									<label class="ui-label ui-filter-search">Поиск по списку<input type="search" placeholder="Логин, email или имя" class="ui-control mt-1"></label>
									<label class="ui-label min-w-0">Фильтр роли<select class="ui-control select-custom mt-1"><option>Все роли</option><option>Admin</option><option>User</option></select></label>
									<label class="ui-label min-w-0">Фильтр статуса<select class="ui-control select-custom mt-1"><option>Все статусы</option><option>Активен</option><option>Заблокирован</option></select></label>
									<button type="button" class="btn secondary" disabled>Сбросить</button>
								</div>
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
						<section id="kit-switches">
							<h3 class="ui-section-title">Переключатели</h3>
							<div class="ui-panel">
								<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
									<label class="flex cursor-pointer items-center justify-between gap-4">
										<span class="text-sm text-zinc-800">Уведомления</span>
										<input type="checkbox" role="switch" class="peer sr-only">
										<span aria-hidden="true" class="ui-switch"></span>
									</label>
									<label class="flex cursor-pointer items-center justify-between gap-4">
										<span class="text-sm text-zinc-800">Автосохранение</span>
										<input type="checkbox" role="switch" class="peer sr-only" checked>
										<span aria-hidden="true" class="ui-switch success"></span>
									</label>
								</div>
							</div>
						</section>
						<section id="kit-field-states">
							<h3 class="ui-section-title">Состояния полей</h3>
							<div class="ui-panel grid grid-cols-1 gap-4 md:grid-cols-2">
								<label class="ui-label">Только чтение
									<input type="text" value="ID-1024" readonly class="ui-control mt-1">
								</label>
								<label class="block text-sm text-zinc-500">Недоступное поле
									<input type="text" value="Недоступно для вашей роли" disabled class="ui-control mt-1">
								</label>
								<label class="ui-label">Поле с ошибкой
									<input id="kit-invalid" type="email" value="invalid-email" aria-invalid="true" aria-describedby="kit-field-error" class="ui-control mt-1">
									<span id="kit-field-error" class="ui-error block">Введите email в формате name@example.com.</span>
								</label>
								<label class="ui-label">Корректное поле
									<input id="kit-valid" type="email" value="editor@example.com" aria-describedby="kit-field-success" class="ui-control valid mt-1">
									<span id="kit-field-success" class="mt-1 block text-xs text-emerald-700">Адрес указан верно.</span>
								</label>
								<label class="ui-label md:col-span-2">Недоступный список
									<select id="kit-disabled-select" class="ui-control select-custom mt-1" disabled><option>Недоступно</option></select>
								</label>
								<label class="flex items-center gap-3 text-sm"><input type="checkbox" disabled> Недоступный checkbox</label>
								<label class="flex items-center gap-3 text-sm"><input type="checkbox" disabled checked> Отмеченный недоступный checkbox</label>
								<label class="flex items-center gap-3 text-sm"><input type="radio" name="kit-disabled-radio" disabled> Недоступный radio</label>
								<label class="flex items-center gap-3 text-sm"><input type="radio" name="kit-disabled-radio" disabled checked> Выбранный недоступный radio</label>
								<label class="flex cursor-not-allowed items-center justify-between gap-4 text-sm text-zinc-500">
									<span>Недоступный переключатель</span>
									<input type="checkbox" role="switch" class="peer sr-only" disabled checked>
									<span aria-hidden="true" class="ui-switch success"></span>
								</label>
							</div>
						</section>
						<section id="kit-choice-controls">
							<h3 class="ui-section-title">Динамические поля</h3>
							<div class="ui-panel space-y-4" x-data="kitSelectDemo">
								<p class="ui-hint">Пример обновления значения и вариантов select, изменения доступности и повторного создания поля.</p>
								<template x-if="visible">
									<div>
										<label for="kit-dynamic-select" class="ui-label">Динамический выбор</label>
										<select id="kit-dynamic-select" class="ui-control select-custom" x-model="value" :disabled="disabled">
											<template x-for="choice in choices" :key="choice.value"><option :value="choice.value" x-text="choice.label"></option></template>
										</select>
									</div>
								</template>
								<p class="ui-hint">Значение x-model: <span data-select-model x-text="value"></span></p>
								<div class="ui-actions">
									<button type="button" class="btn secondary small" @click="changeValue">Изменить значение</button>
									<button type="button" class="btn secondary small" @click="addOption">Добавить вариант</button>
									<button type="button" class="btn secondary small" @click="removeOption">Удалить выбранный вариант</button>
									<button type="button" class="btn secondary small" @click="toggleDisabled">Переключить disabled</button>
									<button type="button" class="btn secondary small" @click="toggleVisible">Пересоздать поле</button>
								</div>
							</div>
						</section>
					</div>
				</section>

				<section id="kit-modals">
					<h2 class="ui-section-title">Модальные окна</h2>
					<div class="ui-panel">
						<div class="flex flex-wrap gap-3">
							<button type="button" class="btn info" data-modal-type="info">Информация</button>
							<button type="button" class="btn success" data-modal-type="success">Успех</button>
							<button type="button" class="btn warning" data-modal-type="warning">Предупреждение</button>
							<button type="button" class="btn danger" data-modal-type="danger">Подтверждение</button>
						</div>
						<p class="mt-3 text-sm text-zinc-600" role="status" data-modal-status></p>
					</div>
				</section>

				<section>
					<h2 class="ui-section-title">Пустое состояние и раскрывающийся блок</h2>
					<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
						<div class="ui-panel ui-empty border-dashed">
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
					<h2 class="ui-section-title">Цветные цитаты / аннотации</h2>
					<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
						<blockquote class="alert note info">
							<p class="mb-1 font-medium">Info</p>
							<p>Подсказка с дополнительной информацией.</p>
						</blockquote>
						<blockquote class="alert note success">
							<p class="mb-1 font-medium">Success</p>
							<p>Операция выполнена успешно.</p>
						</blockquote>
						<blockquote class="alert note warning">
							<p class="mb-1 font-medium">Warning</p>
							<p>Обратите внимание на важный нюанс.</p>
						</blockquote>
						<blockquote class="alert note danger">
							<p class="mb-1 font-medium">Danger</p>
							<p>Ошибка: требуется вмешательство.</p>
						</blockquote>
					</div>
				</section>

				<!-- Compact editor -->
				<section id="kit-editor">
					<h2 class="ui-section-title">Компактная форма редактирования</h2>
					<div class="ui-record-editor space-y-3">
						<details class="ui-editor-section" open>
							<summary class="ui-editor-summary"><span>Профиль<span class="ui-editor-caption">Дополнительные сведения и видимость</span></span></summary>
							<div class="ui-editor-body space-y-4">
								<label class="flex cursor-pointer items-center justify-between gap-4"><span class="text-sm text-zinc-800">Публичный профиль</span><input type="checkbox" role="switch" class="peer sr-only" checked><span aria-hidden="true" class="ui-switch"></span></label>
								<div><label for="kit-editor-bio" class="ui-label">О себе</label><textarea id="kit-editor-bio" rows="2" class="ui-control"></textarea></div>
							</div>
						</details>
						<div class="ui-editor-grid">
						<details class="ui-editor-section">
							<summary class="ui-editor-summary"><span>System Info</span></summary>
							<div class="ui-editor-body">
							<div class="grid gap-3 md:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
								<div><label for="kit-editor-email" class="ui-label">Email <span class="text-rose-600" aria-hidden="true">*</span></label><input id="kit-editor-email" type="email" required value="editor@example.com" class="ui-control"></div>
								<div><label for="kit-editor-role" class="ui-label">Роль</label><select id="kit-editor-role" class="ui-control select-custom"><option>Редактор</option><option>Пользователь</option></select></div>
							</div>
							<div class="mt-3 flex flex-wrap items-center gap-x-6 gap-y-3 border-t border-zinc-100 pt-3">
								<label class="flex cursor-pointer items-center gap-2"><span class="text-sm text-zinc-800">Активен</span><input type="checkbox" role="switch" class="peer sr-only" checked><span aria-hidden="true" class="ui-switch success"></span></label>
								<label class="flex cursor-pointer items-center gap-2"><span class="text-sm text-zinc-800">Email подтверждён</span><input type="checkbox" role="switch" class="peer sr-only" checked><span aria-hidden="true" class="ui-switch success"></span></label>
								<dl class="ui-editor-meta md:ml-auto"><div><dt>Создан</dt><dd>20.11.2026, 10:00</dd></div><div><dt>Обновлён</dt><dd>25.11.2026, 12:00</dd></div></dl>
							</div>
							</div>
						</details>
							<details class="ui-editor-section">
								<summary class="ui-editor-summary"><span>Блокировка</span></summary>
								<div class="ui-editor-body"><label class="flex cursor-pointer items-center justify-between gap-4"><span class="text-sm text-zinc-800">Пользователь заблокирован</span><input type="checkbox" role="switch" class="peer sr-only"><span aria-hidden="true" class="ui-switch"></span></label></div>
							</details>
							<details class="ui-editor-section">
								<summary class="ui-editor-summary"><span>Смена пароля</span></summary>
								<div class="ui-editor-body"><label for="kit-editor-password" class="ui-label">Новый пароль</label><input id="kit-editor-password" type="password" autocomplete="new-password" class="ui-control"></div>
							</details>
						</div>
						<div class="ui-editor-footer"><p class="ui-hint">Дополнительные секции раскрываются мышью, Enter или пробелом. На десктопах парные секции стоят рядом, на мобильных — друг под другом. Все значения сохраняются при сворачивании.</p></div>
					</div>
				</section>

				<section id="kit-tabs">
					<h2 class="ui-section-title">Вкладки</h2>
					<div class="ui-panel">
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
					<h2 class="ui-section-title">Состояние загрузки</h2>
					<div class="ui-loading" role="status"><?php require __DIR__ . '/../../partials/ui-spinner.php'; ?> Загрузка данных…</div>
					<div class="ui-panel">
						<div class="skeleton-container" aria-hidden="true">
							<div class="skeleton-line title"></div>
							<div class="skeleton-line full"></div>
							<div class="skeleton-line large"></div>
							<div class="skeleton-line medium"></div>
						</div>
					</div>
				</section>

				<!-- Avatars / Stack -->
				<section>
					<h2 class="ui-section-title">Аватары и стек</h2>
					<div class="ui-panel">
						<div class="flex items-center gap-3">
							<span role="img" aria-label="A" class="avatar small initials">АИ</span>
							<span role="img" aria-label="B" class="avatar small initials">МП</span>
							<span role="img" aria-label="C" class="avatar small initials">ЕК</span>
						</div>
						<div class="mt-4 flex -space-x-2">
							<span role="img" aria-label="A" class="avatar stack initials ring-2 ring-white">АИ</span>
							<span role="img" aria-label="B" class="avatar stack initials ring-2 ring-white">МП</span>
							<span role="img" aria-label="C" class="avatar stack initials ring-2 ring-white">ЕК</span>
							<span class="flex h-8 w-8 items-center justify-center rounded-full bg-zinc-200 text-xs text-zinc-700 ring-2 ring-white">+5</span>
						</div>

						<!-- Avatars with status/counter -->
						<div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
							<div class="avatar-with-status medium">
								<span role="img" aria-label="Online" class="avatar medium initials">АИ</span>
								<span class="status-indicator online"></span>
							</div>
							<div class="avatar-with-status medium">
								<span role="img" aria-label="Offline" class="avatar medium initials">МП</span>
								<span class="status-indicator offline"></span>
							</div>
							<div class="avatar-with-status medium">
								<span role="img" aria-label="Messages" class="avatar medium initials">ЕК</span>
								<span class="notification-badge">12</span>
							</div>
						</div>
					</div>
				</section>

                <section id="kit-records">
                    <h2 class="ui-section-title">Список записей</h2>
                    <p class="mb-4 text-sm text-zinc-600">Строки с действиями на широком экране, карточки с подписями на узком. Этот компонент используется в списке пользователей ACP.</p>
                    <div class="ui-panel flush overflow-hidden">
                        <div class="ui-panel-header ui-toolbar">
                            <div class="flex items-center gap-2"><h3 class="font-semibold text-zinc-900">Пользователи</h3><span class="badge neutral">2</span></div>
                            <span class="text-sm text-zinc-500">Демонстрационные данные</span>
                        </div>
                        <div class="ui-record-row ui-record-heading" aria-hidden="true">
                            <span>Пользователь</span><span>Роль</span><span>Статус</span><span>Активность</span><span class="text-right">Действия</span>
                        </div>
                        <div class="ui-record-list" role="list" aria-label="Пример списка пользователей">
                            <div class="ui-record-row" role="listitem">
                                <div class="ui-record-cell ui-record-identity">
                                    <span class="avatar small initials shrink-0" aria-hidden="true">АИ</span>
                                    <div class="min-w-0"><p class="font-semibold text-zinc-900">alex</p><p class="text-zinc-500">alex@example.com</p><p class="ui-hint">ID 1024 · @alex</p></div>
                                </div>
                                <div class="ui-record-cell"><span class="ui-record-label">Роль</span><span class="badge warning">Admin</span></div>
                                <div class="ui-record-cell"><span class="ui-record-label">Статус</span><span class="badge success">Активен</span></div>
                                <div class="ui-record-cell text-zinc-500"><span class="ui-record-label">Активность</span>5 минут назад</div>
                                <div class="ui-record-actions"><button type="button" class="btn secondary small" disabled>Edit</button><button type="button" class="btn outline danger small" disabled>Ban</button></div>
                            </div>
                            <div class="ui-record-row" role="listitem">
                                <div class="ui-record-cell ui-record-identity">
                                    <span class="avatar small initials shrink-0" aria-hidden="true">МП</span>
                                    <div class="min-w-0"><p class="font-semibold text-zinc-900">maria</p><p class="text-zinc-500">maria.long.email@example.com</p><p class="ui-hint">ID 1025</p></div>
                                </div>
                                <div class="ui-record-cell"><span class="ui-record-label">Роль</span><span class="badge neutral">User</span></div>
                                <div class="ui-record-cell"><span class="ui-record-label">Статус</span><span class="badge danger">Заблокирован</span><p class="ui-hint">Нарушение правил · бессрочно</p></div>
                                <div class="ui-record-cell text-zinc-500"><span class="ui-record-label">Активность</span>Вчера</div>
                                <div class="ui-record-actions"><button type="button" class="btn secondary small" disabled>Edit</button><button type="button" class="btn outline success small" disabled>Unban</button></div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="kit-settings">
                    <h2 class="ui-section-title">Настройки по разделам</h2>
                    <p class="mb-4 text-sm text-zinc-600">Один открытый раздел, подпись и подсказка рядом с полем. Переключение разделов сохраняет введённые значения. Панель действий закрепляется у нижнего края окна; ошибки открывают соответствующий раздел.</p>
                    <div class="ui-settings-layout">
                        <nav class="ui-settings-nav" aria-label="Пример разделов настроек">
                            <p class="mb-3 px-3 text-xs font-medium uppercase tracking-wider text-zinc-400">Конфигурация</p>
                            <button type="button" class="ui-settings-link is-active" aria-pressed="true" disabled>Сайт</button>
                            <button type="button" class="ui-settings-link" aria-pressed="false" disabled>Почта</button>
                        </nav>
                        <section class="ui-panel flush">
                            <header class="ui-panel-header"><h3 class="text-base font-semibold text-zinc-900">Настройки сайта</h3><p class="ui-hint">Название, домен и контактные данные.</p></header>
                            <div class="ui-settings-field">
                                <div><label for="kit-settings-name" class="ui-label">Название сайта <span class="text-rose-500" aria-hidden="true">*</span></label><p id="kit-settings-name-help" class="ui-hint">Отображается в заголовке сайта.</p></div>
                                <input id="kit-settings-name" aria-describedby="kit-settings-name-help" required class="ui-control" value="RooCMS">
                            </div>
                            <div class="ui-settings-field border-t border-zinc-100">
                                <div><label for="kit-settings-switch" class="ui-label">Уведомления</label><p id="kit-settings-switch-help" class="ui-hint">Получать сообщения о событиях сайта.</p></div>
                                <label class="ui-setting-row justify-start"><input id="kit-settings-switch" aria-describedby="kit-settings-switch-help" type="checkbox" role="switch" class="peer sr-only" checked><span class="ui-switch" aria-hidden="true"></span><span class="text-sm text-zinc-500">Включены</span></label>
                            </div>
                        </section>
                    </div>
                    <div class="ui-settings-actions mt-5">
                        <div class="ui-toolbar"><p class="text-xs text-zinc-500">Изменения всех разделов сохраняются вместе.</p><div class="ui-actions"><button type="button" class="btn secondary" disabled>По умолчанию</button><button type="button" class="btn primary" disabled>Сохранить</button></div></div>
                    </div>
                </section>

                <section id="kit-structure">
                    <h2 class="ui-section-title">Страницы и редактор</h2>
                    <p class="mb-4 text-sm text-zinc-600">Вариант списка structure: название, полный путь вложенности, публикация, навигация и действия. Родитель предшествует дочерним страницам; ui-tree-indent обозначает уровни (до четырёх визуальных отступов, полный путь не сокращается). Редактор раскрывается под выбранной строкой.</p>
                    <div class="ui-panel flush overflow-hidden">
                        <div class="ui-record-row structure ui-record-heading" aria-hidden="true">
                            <span>Страница</span><span>Публикация</span><span>Навигация</span><span class="text-right">Действия</span>
                        </div>
                        <div class="ui-record-row structure is-open">
                            <div class="ui-record-cell ui-record-identity">
                                <span class="flex shrink-0 self-stretch" aria-hidden="true"><span class="ui-tree-indent"></span><span class="ui-tree-indent"></span></span>
                                <span class="ui-record-icon" role="img" aria-label="Страница" title="Страница"><svg aria-hidden="true" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 3H6v18h13V8Zm0 0v5h5M9 13h6m-6 4h4"></path></svg></span>
                                <div class="min-w-0"><p class="font-semibold text-zinc-900">Веб-разработка</p><p class="mt-0.5 break-all font-mono text-xs text-zinc-500">/services/web-development</p></div>
                            </div>
                            <div class="ui-record-cell"><span class="ui-record-label">Публикация</span><span class="badge compact outlined success">Активна</span></div>
                            <div class="ui-record-cell text-xs text-zinc-500"><span class="ui-record-label">Навигация</span>В меню</div>
                            <div class="ui-record-actions"><button type="button" class="btn secondary small" disabled>Закрыть</button></div>
                        </div>
                        <div class="ui-record-editor">
                            <div class="mb-5"><h3 class="text-base font-semibold text-zinc-900">Настройки страницы</h3><p class="ui-hint">Основные сведения, публикация и SEO.</p></div>
                            <div class="grid grid-cols-1 items-start gap-4 lg:grid-cols-3">
                                <section class="ui-editor-section lg:col-span-2">
                                    <h4 class="ui-editor-heading">Данные страницы</h4>
                                    <div class="space-y-4 p-4">
                                        <div><label for="kit-page-title" class="ui-label">Название</label><input id="kit-page-title" class="ui-control" value="О компании"></div>
                                        <div><label for="kit-page-slug" class="ui-label">URL slug</label><input id="kit-page-slug" class="ui-control font-mono" value="about"><p class="ui-hint">Идентификатор страницы в URL.</p></div>
                                    </div>
                                </section>
                                <section class="ui-editor-section">
                                    <h4 class="ui-editor-heading">Публикация</h4>
                                    <div class="space-y-4 p-4">
                                        <div><label for="kit-page-status" class="ui-label">Статус</label><select id="kit-page-status" class="ui-control select-custom"><option>Активна</option><option>Черновик</option></select></div>
                                        <label class="ui-setting-row border-t border-zinc-100 pt-4"><span>Показывать в меню<span class="ui-hint">Включить в навигацию сайта.</span></span><input type="checkbox" role="switch" class="peer sr-only" checked><span aria-hidden="true" class="ui-switch success"></span></label>
                                    </div>
                                </section>
                            </div>
                            <div class="ui-editor-footer ui-toolbar mt-4"><p class="text-xs text-zinc-500">Изменения применяются после сохранения.</p><div class="ui-actions"><button type="button" class="btn secondary" disabled>Отмена</button><button type="button" class="btn primary" disabled>Сохранить</button></div></div>
                        </div>
                    </div>
                </section>

				<!-- Chips & Divider -->
				<section>
					<h2 class="ui-section-title">Метки и разделитель</h2>
					<div class="ui-panel">
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
						<div class="ui-panel">
							<div class="text-xs font-medium text-zinc-500">Выручка</div>
							<div class="mt-2 text-2xl font-semibold text-zinc-900">$2.6M</div>
							<div class="mt-1 text-xs text-emerald-600">+4.5% за неделю</div>
										</div>
						<div class="ui-panel">
							<div class="text-xs font-medium text-zinc-500">Средний чек</div>
							<div class="mt-2 text-2xl font-semibold text-zinc-900">$455</div>
							<div class="ui-hint">−0.5% за неделю</div>
									</div>
						<div class="ui-panel">
							<div class="text-xs font-medium text-zinc-500">Продажи</div>
							<div class="mt-2 text-2xl font-semibold text-zinc-900">5,888</div>
							<div class="mt-1 text-xs text-emerald-600">+4.5% за неделю</div>
									</div>
						<div class="ui-panel">
							<div class="text-xs font-medium text-zinc-500">Просмотры</div>
							<div class="mt-2 text-2xl font-semibold text-zinc-900">823,067</div>
							<div class="mt-1 text-xs text-emerald-600">+21.2% за неделю</div>
						</div>
					</div>

					<div id="kit-orders" class="ui-panel mt-10">
						<div class="mb-4 flex items-center justify-between">
							<h2 class="text-base font-semibold text-zinc-900">Последние заказы</h2>
							<span class="badge neutral">Демо</span>
						</div>
						<p class="mb-3 text-sm text-zinc-600" role="status" data-search-status>Показано заказов: 3</p>
						<div class="-mx-5 overflow-x-auto px-5" role="region" aria-label="Таблица заказов с горизонтальной прокруткой" tabindex="0">
							<table id="kit-orders-table" class="ui-table" aria-label="Последние заказы">
								<caption class="sr-only">Демонстрационные заказы. Поиск доступен в разделе «Группы полей».</caption>
								<thead >
									<tr>
										<th scope="col" class="font-medium">Номер</th>
										<th scope="col" class="font-medium">Дата</th>
										<th scope="col" class="font-medium">Клиент</th>
										<th scope="col" class="font-medium">Событие</th>
										<th scope="col" class="font-medium text-right">Сумма</th>
									</tr>
								</thead>
								<tbody >
									<tr >
										<td class="text-zinc-900">3000</td>
										<td class="text-zinc-600">9 мая 2024</td>
										<td class="text-zinc-900">Leslie Alexander</td>
										<td class="text-zinc-900">Bear Hug: Live in Concert</td>
										<td class="text-right text-zinc-900">US$80.00</td>
									</tr>
									<tr >
										<td class="text-zinc-900">3001</td>
										<td class="text-zinc-600">5 мая 2024</td>
										<td class="text-zinc-900">Michael Foster</td>
										<td class="text-zinc-900">Six Fingers — DJ Set</td>
										<td class="text-right text-zinc-900">US$299.00</td>
									</tr>
									<tr >
										<td class="text-zinc-900">3002</td>
										<td class="text-zinc-600">28 апр 2024</td>
										<td class="text-zinc-900">Dries Vincent</td>
										<td class="text-zinc-900">We All Look The Same</td>
										<td class="text-right text-zinc-900">US$150.00</td>
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


