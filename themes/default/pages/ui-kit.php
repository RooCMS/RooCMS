<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

$page_title = 'UI Kit — Public — RooCMS';
$page_description = 'Public UI components for RooCMS theme';

$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [$theme_base.'/assets/js/pages/ui-kit.js'];

ob_start();
?>
<div class="py-10">
	<div class="mx-auto max-w-8xl space-y-10 px-4">
		<header>
			<nav class="mb-4 text-sm text-zinc-500" aria-label="Хлебные крошки">
				<ol class="flex items-center gap-2">
					<li><a href="/" class="hover:text-sky-700">Главная</a></li>
					<li aria-hidden="true" class="text-zinc-400">/</li>
					<li><a href="/ui-kit" aria-current="page" class="text-zinc-700">UI Kit</a></li>
				</ol>
			</nav>
			<h1 class="text-3xl font-bold tracking-tight text-zinc-900">Пользовательский UI Kit</h1>
			<p class="mt-2 text-sm text-zinc-600">Компоненты для публичной части сайта RooCMS.</p>
		</header>

		<!-- Hero -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Hero</h2>
			<div class="relative overflow-hidden rounded-3xl border border-zinc-200 bg-gradient-to-r from-amber-100/80 to-sky-50/80 p-10 shadow-sm">
				<div class="absolute inset-0 -z-10 blur-3xl"></div>
				<h3 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">Build with RooCMS</h3>
				<p class="mt-3 max-w-xl text-zinc-600">Лёгкая публика CMS без фреймворков. Простые компоненты, аккуратные градиенты, чистая типографика.</p>
				<div class="mt-6 flex gap-3">
					<a href="#" class="inline-flex items-center rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 px-5 py-2.5 text-sm font-medium text-white hover:from-blue-700 hover:to-purple-700">Начать</a>
					<a href="#" class="inline-flex items-center rounded-lg border border-zinc-200 bg-white px-5 py-2.5 text-sm font-medium text-zinc-900 hover:bg-zinc-50">Документация</a>
				</div>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Дизайн-токены</h2>
			<div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
				<div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
					<h3 class="text-sm font-semibold text-zinc-900">Цветовая палитра</h3>
					<p class="mt-1 text-xs text-zinc-500">Базовые цвета для темы RooCMS.</p>
					<div class="mt-4 grid grid-cols-2 gap-3 text-xs">
						<div class="space-y-2">
							<div class="h-14 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600"></div>
							<div class="font-semibold text-zinc-700">Primary</div>
							<div class="text-zinc-500">#2563eb → #7c3aed</div>
						</div>
						<div class="space-y-2">
							<div class="h-14 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500"></div>
							<div class="font-semibold text-zinc-700">Positive</div>
							<div class="text-zinc-500">#10b981 → #14b8a6</div>
						</div>
						<div class="space-y-2">
							<div class="h-14 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500"></div>
							<div class="font-semibold text-zinc-700">Warning</div>
							<div class="text-zinc-500">#f59e0b → #f97316</div>
						</div>
						<div class="space-y-2">
							<div class="h-14 rounded-xl bg-gradient-to-r from-rose-500 to-red-500"></div>
							<div class="font-semibold text-zinc-700">Danger</div>
							<div class="text-zinc-500">#f43f5e → #ef4444</div>
						</div>
					</div>
				</div>
				<div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
					<h3 class="text-sm font-semibold text-zinc-900">Типографика</h3>
					<p class="mt-1 text-xs text-zinc-500">Основные размеры и начертания.</p>
					<div class="mt-4 space-y-3 text-zinc-900">
						<div>
							<div class="text-xs uppercase tracking-wide text-zinc-500">Заголовок</div>
							<div class="text-3xl font-bold">Display 32px / 1.25</div>
						</div>
						<div>
							<div class="text-xs uppercase tracking-wide text-zinc-500">Подзаголовок</div>
							<div class="text-xl font-semibold">Heading 20px / 1.4</div>
						</div>
						<div>
							<div class="text-xs uppercase tracking-wide text-zinc-500">Основной текст</div>
							<div class="text-sm text-zinc-600">Body 14px / 1.6 — используется для большинства параграфов.</div>
						</div>
						<div>
							<div class="text-xs uppercase tracking-wide text-zinc-500">Подпись</div>
							<div class="text-xs text-zinc-500">Caption 12px / 1.5 для второстепенных текстов.</div>
						</div>
					</div>
				</div>
				<div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
					<h3 class="text-sm font-semibold text-zinc-900">Отступы и тени</h3>
					<p class="mt-1 text-xs text-zinc-500">Базовый scale и глубина.</p>
					<div class="mt-4 space-y-4 text-sm text-zinc-600">
						<div class="flex items-center justify-between rounded-xl border border-dashed border-zinc-300 p-3">
							<span class="font-medium text-zinc-700">Spacing scale</span>
							<span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs text-zinc-500">4 / 8 / 12 / 16 / 24 / 32</span>
						</div>
						<div class="grid grid-cols-3 gap-3">
							<div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm">
								<div class="h-14 rounded-lg bg-zinc-100 shadow-sm"></div>
								<div class="mt-2 text-xs text-zinc-500">shadow-sm</div>
							</div>
							<div class="rounded-xl border border-zinc-200 bg-white p-4 shadow">
								<div class="h-14 rounded-lg bg-zinc-100 shadow"></div>
								<div class="mt-2 text-xs text-zinc-500">shadow</div>
							</div>
							<div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-lg">
								<div class="h-14 rounded-lg bg-zinc-100 shadow-lg"></div>
								<div class="mt-2 text-xs text-zinc-500">shadow-lg</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Навигация</h2>
			<div class="space-y-6">
				<div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
					<header class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
						<div class="flex items-center gap-3">
							<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold">R</div>
							<div>
								<p class="text-sm font-semibold text-zinc-900">RooCMS</p>
								<p class="text-xs text-zinc-500">Админ &middot; CMS &middot; Документация</p>
							</div>
						</div>
						<nav class="flex flex-wrap items-center gap-2">
							<a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-zinc-600 transition hover:text-zinc-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">Главная</a>
							<a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-zinc-900 bg-zinc-100">Публикации</a>
							<a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-zinc-600 transition hover:text-zinc-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">Категории</a>
							<a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-zinc-600 transition hover:text-zinc-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">Теги</a>
							<a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-zinc-600 transition hover:text-zinc-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">Контакты</a>
						</nav>
						<div class="flex items-center gap-2">
							<a href="#" class="rounded-md px-3 py-2 text-sm font-medium text-zinc-600 hover:text-zinc-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">Войти</a>
							<a href="#" class="inline-flex items-center rounded-md bg-gradient-to-r from-blue-600 to-purple-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:from-blue-700 hover:to-purple-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-500">Регистрация</a>
						</div>
					</header>
				</div>

				<div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
					<nav class="space-y-4">
						<div class="flex items-center justify-between">
							<h3 class="text-sm font-semibold text-zinc-900">Раздел документации</h3>
							<button type="button" class="rounded-full border border-zinc-200 bg-white px-3 py-1 text-xs font-medium text-zinc-600 hover:bg-zinc-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">Фильтр</button>
						</div>
						<ul class="flex flex-wrap gap-2 text-xs font-medium">
							<li><a href="#" class="nav-tag nav-tag--active">Гиды<span class="nav-tag__badge">12</span></a></li>
							<li><a href="#" class="nav-tag nav-tag--default">API</a></li>
							<li><a href="#" class="nav-tag nav-tag--default">CLI</a></li>
							<li><a href="#" class="nav-tag nav-tag--default">Справка</a></li>
						</ul>
						<div class="flex flex-wrap items-center gap-4 border-t border-zinc-100 pt-4 text-sm">
							<a href="#" class="status-link"><span class="indicator published"></span>Опубликовано</a>
							<a href="#" class="status-link"><span class="indicator draft"></span>В работе</a>
							<a href="#" class="status-link"><span class="indicator archived"></span>Архив</a>
						</div>
					</nav>
				</div>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Боковое меню и карточка профиля</h2>
			<div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
				<aside class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm">
					<div class="flex items-center gap-3">
						<img src="https://i.pravatar.cc/64?img=24" alt="avatar" class="h-12 w-12 rounded-full object-cover">
						<div>
							<p class="text-sm font-semibold text-zinc-900">Alexandr Roo</p>
							<p class="text-xs text-emerald-600">Статус: активен</p>
						</div>
					</div>
					<nav class="mt-5 space-y-1 text-sm">
						<a href="#" class="sidebar-nav-link active">Обзор<span class="badge">5</span></a>
						<a href="#" class="sidebar-nav-link">Публикации<span class="badge">12</span></a>
						<a href="#" class="sidebar-nav-link">Комментарии<span class="badge">48</span></a>
						<a href="#" class="sidebar-nav-link">Настройки<span class="badge">&rsaquo;</span></a>
					</nav>
					<div class="mt-5 rounded-xl border border-dashed border-sky-200 bg-sky-50 p-4 text-xs text-sky-900">
						<p class="font-semibold">Планы публикаций</p>
						<p class="mt-1 text-sky-700">Запланировано 3 поста на эту неделю.</p>
					</div>
				</aside>
				<div class="lg:col-span-2 space-y-4">
					<div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm">
						<h3 class="text-sm font-semibold text-zinc-900">Краткое содержание</h3>
						<p class="mt-2 text-sm text-zinc-600">Используйте эти блоки для представления метаданных автора, записи или раздела личного кабинета.</p>
						<dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
							<div class="rounded-xl border border-zinc-100 bg-zinc-50 p-4">
								<dt class="text-xs text-zinc-500">Роль</dt>
								<dd class="text-sm font-medium text-zinc-900">Редактор</dd>
							</div>
							<div class="rounded-xl border border-zinc-100 bg-zinc-50 p-4">
								<dt class="text-xs text-zinc-500">Создано</dt>
								<dd class="text-sm font-medium text-zinc-900">15 мая 2024</dd>
							</div>
							<div class="rounded-xl border border-zinc-100 bg-zinc-50 p-4">
								<dt class="text-xs text-zinc-500">Активность</dt>
								<dd class="text-sm font-medium text-zinc-900">Онлайн · 5 мин назад</dd>
							</div>
							<div class="rounded-xl border border-zinc-100 bg-zinc-50 p-4">
								<dt class="text-xs text-zinc-500">Черновики</dt>
								<dd class="text-sm font-medium text-zinc-900">2 черновика</dd>
							</div>
						</dl>
					</div>
					<div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-900 shadow-sm">
						<p class="font-semibold">Совет по оптимизации</p>
						<p class="mt-1">Добавьте описание профиля и ссылку на сайт, чтобы улучшить доверие читателей.</p>
					</div>
				</div>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Кнопки и ссылки</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
				<div class="flex flex-wrap gap-3">
					<a href="#" class="btn primary">Primary</a>
					<a href="#" class="btn danger">Danger</a>
					<a href="#" class="btn success">Success</a>
					<a href="#" class="btn info">Info</a>
					<a href="#" class="btn warning">Warning</a>
					<a href="#" class="btn contrast">Contrast</a>
					<a href="#" class="btn secondary">Secondary</a>
					<a href="#" class="link-text">Текстовая ссылка</a>
				</div>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Состояния кнопок</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm space-y-4">
				<div class="flex flex-wrap gap-3">
					<button class="btn primary">Hover / Focus</button>
					<button class="btn primary" disabled aria-disabled="true">Disabled</button>
					<button class="btn outline btn-tr-lr">
						<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
						Primary link
					</button>
				</div>
				<div class="rounded-lg border border-dashed border-zinc-200 bg-zinc-50 p-4 text-xs text-zinc-600">
					<p class="font-semibold text-zinc-900">Рекомендации</p>
					<ul class="mt-2 list-disc space-y-1 pl-4">
						<li><span class="text-zinc-700">Используйте <code class="rounded bg-white px-1">focus-visible</code> и контрастную обводку для клавиатурной навигации.</span></li>
						<li><span class="text-zinc-700">Состояние <em>disabled</em> делайте визуально очевидным и не используйте его для важных действий.</span></li>
						<li><span class="text-zinc-700">Добавляйте <code class="rounded bg-white px-1">aria-label</code> к кнопкам без текста.</span></li>
					</ul>
				</div>
			</div>
		</section>

		<!-- Buttons: sizes & icons -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Кнопки: размеры и иконки</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm space-y-4">
				<div class="flex flex-wrap items-center gap-3">
					<a href="#" class="btn primary small">
						<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
						Small
					</a>
					<a href="#" class="btn primary">
						<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
						Medium
					</a>
					<a href="#" class="btn primary large">
						<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
						Large
					</a>
				</div>

				<div class="flex flex-wrap items-center gap-3">
					<a href="/" class="btn contrast btn-tr-rl">
						<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
						Back to Home
					</a>
					<a href="#" class="btn secondary btn-tr-lr">
						<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
						With icon
					</a>
				</div>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Формы: состояния и валидация</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm space-y-6">
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					<label class="flex flex-col gap-2">
						<span class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Нормальное состояние</span>
						<input type="text" placeholder="Название" class="rounded-md border border-zinc-300 px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 transition focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-200/50">
					</label>
					<label class="flex flex-col gap-2">
						<span class="text-xs font-semibold uppercase tracking-wide text-zinc-500">С ошибкой</span>
						<input type="text" value="" aria-invalid="true" class="rounded-md border border-rose-400 bg-rose-50 px-3 py-2 text-sm text-rose-900 placeholder-rose-300 focus:border-rose-500 focus:outline-none focus:ring focus:ring-rose-200/50">
						<span class="text-xs text-rose-600">Поле обязательно для заполнения.</span>
					</label>
				</div>
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					<label class="flex flex-col gap-2">
						<span class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Disabled</span>
						<select class="rounded-md border border-zinc-300 bg-zinc-100 px-3 py-2 text-sm text-zinc-500" disabled>
							<option>Недоступно</option>
						</select>
					</label>
					<label class="flex flex-col gap-2">
						<span class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Textarea</span>
						<textarea rows="3" class="rounded-md border border-zinc-300 px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 transition focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-200/50">Текст...</textarea>
					</label>
				</div>
				<div class="rounded-lg border border-dashed border-zinc-200 bg-zinc-50 p-4 text-xs text-zinc-600">
					<p class="font-semibold text-zinc-900">Рекомендации</p>
					<ul class="mt-2 list-disc space-y-1 pl-4">
						<li><span class="text-zinc-700">Используйте явные подписи и подсказки вместо placeholder как единственного источника.</span></li>
						<li><span class="text-zinc-700">Цвет ошибки комбинируйте с текстом и иконкой, чтобы сохранить понятность при дальтонизме.</span></li>
						<li><span class="text-zinc-700">Для обязательных полей добавляйте <code class="rounded bg-white px-1">aria-required="true"</code>.</span></li>
					</ul>
				</div>
			</div>
		</section>

		<!-- Containers -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Контейнеры</h2>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
				<div class="container-panel frosted">
					<h3 class="text-sm font-semibold text-zinc-900 mb-1">Frosted</h3>
					<p class="text-sm text-zinc-600">Полупрозрачная панель на светлом фоне.</p>
				</div>
				<div class="container-panel classic">
					<h3 class="text-sm font-semibold text-zinc-900 mb-1">Panel</h3>
					<p class="text-sm text-zinc-600">Классическая панель контента.</p>
				</div>
				<div class="container-panel gradient">
					<h3 class="text-sm font-semibold text-zinc-900 mb-1">Gradient Panel</h3>
					<p class="text-sm text-zinc-600">Как на profile/terms секциях.</p>
				</div>
			</div>
		</section>

		<!-- Metrics -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Карточки статистики</h2>
			<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
				<div class="stats-card">
					<div class="label">Пользователи</div>
					<div class="value">1,248</div>
					<div class="change positive">+3.1% за неделю</div>
				</div>
				<div class="stats-card">
					<div class="label">Публикации</div>
					<div class="value">328</div>
					<div class="change neutral">−0.8% за неделю</div>
				</div>
				<div class="stats-card">
					<div class="label">Просмотры</div>
					<div class="value">82,304</div>
					<div class="change positive">+12.4% за неделю</div>
				</div>
				<div class="stats-card">
					<div class="label">Комментарии</div>
					<div class="value">5,231</div>
					<div class="change positive">+1.2% за неделю</div>
				</div>
			</div>
		</section>

		<!-- Progress bars -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Прогресс</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm space-y-4">
				<div class="progress-item">
					<div class="header"><span>Загрузка</span><span>45%</span></div>
					<div class="progress-track primary"><div class="progress-bar primary w-[45%]"></div></div>
				</div>
				<div class="progress-item">
					<div class="header"><span>Обработка</span><span class="percentage success">70%</span></div>
					<div class="progress-track success"><div class="progress-bar success w-[70%]"></div></div>
				</div>
			</div>
		</section>

		<!-- Section headers -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Заголовки секций</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm space-y-4">
				<div class="flex items-center justify-between">
					<h3 class="flex items-center gap-2 text-base font-semibold text-zinc-900"><span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-indigo-500 text-white">A</span> Аккаунт</h3>
					<span class="rounded-full border border-blue-200 bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-800">Active</span>
				</div>
				<div class="flex items-center justify-between">
					<h3 class="flex items-center gap-2 text-base font-semibold text-zinc-900"><span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-r from-emerald-500 to-teal-500 text-white">C</span> Контакты</h3>
					<span class="rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-800">Complete</span>
				</div>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Бейджи и алерты</h2>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
					<div class="flex flex-wrap items-center gap-3">
						<span class="badge neutral">Neutral</span>
						<span class="badge info">Info</span>
						<span class="badge success">Success</span>
						<span class="badge warning">Warning</span>
						<span class="badge danger">Danger</span>
					</div>
				</div>
				<div class="space-y-3">
					<div class="alert info">Инфо: полезное уведомление.</div>
					<div class="alert success">Успех: действие завершено.</div>
					<div class="alert warning">Предупреждение: проверьте данные.</div>
					<div class="alert danger">Ошибка: что-то пошло не так.</div>
				</div>
			</div>
		</section>

		<!-- Article preview & layout -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Превью статьи и лэйаут</h2>
			<div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
				<article class="article-card small">
					<div class="thumbnail"></div>
					<h3 class="title">Заголовок статьи</h3>
					<p class="description">Короткое описание содержания статьи...</p>
					<div class="meta"><span>12 мая 2024</span><span>•</span><span>12 мин</span></div>
				</article>
				<article class="article-card large lg:col-span-2 max-w-none">
					<div class="thumbnail"></div>
					<h3 class="title">Заголовок статьи</h3>
					<p class="description">Короткое описание содержания статьи...</p>
					<div class="meta"><span>12 мая 2024</span><span>•</span><span>12 мин</span></div>
				</article>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Карточки и типографика</h2>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<article class="content-card simple">
					<h3 class="title">Заголовок карточки</h3>
					<p class="text">Этот текст демонстрирует базовую типографику публичной темы. Используются аккуратные отступы, комфортные размеры шрифтов и мягкие цвета.</p>
				</article>
				<article class="content-card structured">
					<header class="header">Заголовок</header>
					<div class="body">Содержимое карточки: абзацы, ссылки, список.</div>
					<footer class="footer"><a href="#" class="link-text">Подробнее</a></footer>
				</article>
			</div>
		</section>

		<!-- Comments list & form -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Комментарии</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
				<div class="space-y-4">
					<div class="comment">
						<img src="https://i.pravatar.cc/40?img=11" alt="avatar" class="avatar">
						<div class="content">
							<div class="header">Leslie Alexander <span class="date">12 мая 2024</span></div>
							<p class="text">Отличная статья! Очень понравилась подача и примеры.</p>
						</div>
					</div>
					<div class="comment">
						<img src="https://i.pravatar.cc/40?img=12" alt="avatar" class="avatar">
						<div class="content">
							<div class="header">Michael Foster <span class="date">10 мая 2024</span></div>
							<p class="text">Спасибо, жду продолжения серии.</p>
						</div>
					</div>
				</div>
				<form class="mt-6 space-y-3">
					<div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
						<input type="text" placeholder="Имя" class="rounded-md border border-zinc-300 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none">
						<input type="email" placeholder="Email" class="rounded-md border border-zinc-300 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none">
					</div>
					<textarea rows="3" placeholder="Ваш комментарий..." class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none"></textarea>
					<div class="text-right">
						<button type="submit" class="inline-flex items-center rounded-md bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:from-blue-700 hover:to-purple-700">Отправить</button>
					</div>
				</form>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-зinc-900">Комментарии: состояния</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm space-y-4">
				<div class="flex flex-col gap-3">
					<div class="comment-state success">Комментарий отправлен. Модератор проверит его в течение 10 минут.</div>
					<div class="comment-state pending">Комментарий ожидает модерации.</div>
					<div class="comment-state error"><span class="font-semibold">Ошибка:</span> текст комментария не должен превышать 500 символов.</div>
				</div>
				<form class="space-y-3">
					<label class="flex flex-col gap-1 text-sm">
						<span class="text-zinc-700">Ответ</span>
						<textarea rows="3" class="rounded-md border border-zinc-300 px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 transition focus:border-blue-500 focus:outline-none focus:ring focus:ring-blue-200/50">@Leslie спасибо!</textarea>
					</label>
					<div class="flex flex-wrap items-center justify-between gap-3 text-xs text-zinc-500">
						<span>Осталось символов: <strong class="text-zinc-700">120</strong></span>
						<div class="flex gap-2">
							<button type="button" class="rounded-md border border-zinc-200 px-3 py-1.5 text-sm font-medium text-zinc-600 hover:bg-zinc-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-zinc-400">Отмена</button>
							<button type="submit" class="inline-flex items-center rounded-md bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:from-blue-700 hover:to-purple-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-500">Ответить</button>
						</div>
					</div>
				</form>
			</div>
		</section>

		<!-- Docs breadcrumbs/subtitles -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Документация: хлебные крошки и подзаголовки</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm space-y-4">
				<nav class="breadcrumb" aria-label="Breadcrumb">
					<ol>
						<li><a href="#">Docs</a></li>
						<li aria-hidden="true" class="separator">/</li>
						<li><a href="#">Guides</a></li>
						<li aria-hidden="true" class="separator">/</li>
						<li><span class="current">Getting started</span></li>
					</ol>
				</nav>
				<div class="section-header">
					<h3 class="title">Getting started</h3>
					<span class="meta-badge">Last updated: 2024-05-12</span>
				</div>
			</div>
		</section>

		<!-- Blog cards -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Карточки блога</h2>
			<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
				<article class="blog-card">
					<div class="thumbnail"></div>
					<div class="tags">
						<span class="tag primary">Tailwind</span>
						<span class="tag neutral">Design</span>
					</div>
					<h3 class="title">Создаём чистые UI без фреймворков</h3>
					<div class="meta">
						<img src="https://i.pravatar.cc/28?img=15" class="avatar" alt="author">
						<span>Alex</span>
						<span>•</span>
						<span>12 мая 2024</span>
						<span>•</span>
						<span>8 мин</span>
					</div>
				</article>
				<article class="blog-card">
					<div class="thumbnail"></div>
					<div class="tags">
						<span class="tag success">PHP</span>
						<span class="tag neutral">Backend</span>
					</div>
					<h3 class="title">Чистый PHP: быстро и понятно</h3>
					<div class="meta">
						<img src="https://i.pravatar.cc/28?img=16" class="avatar" alt="author">
						<span>Irina</span>
						<span>•</span>
						<span>10 мая 2024</span>
						<span>•</span>
						<span>6 мин</span>
					</div>
				</article>
				<article class="blog-card">
					<div class="thumbnail"></div>
					<div class="tags">
						<span class="tag warning">Guide</span>
						<span class="tag neutral">UX</span>
					</div>
					<h3 class="title">UX‑шаблоны для RooCMS</h3>
					<div class="meta">
						<img src="https://i.pravatar.cc/28?img=17" class="avatar" alt="author">
						<span>Oleg</span>
						<span>•</span>
						<span>8 мая 2024</span>
						<span>•</span>
						<span>5 мин</span>
					</div>
				</article>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-зinc-900">Списки и элементы</h2>
			<div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
				<div class="list-card">
					<h3 class="title">Маркированный список</h3>
					<ul class="list bulleted">
						<li>Поддержка вложенных страниц</li>
						<li>Версия для печати</li>
						<li>SEO-метаданные</li>
					</ul>
				</div>
				<div class="list-card">
					<h3 class="title">Нумерованный список</h3>
					<ol class="list numbered">
						<li>Создайте запись</li>
						<li>Добавьте медиа</li>
						<li>Опубликуйте контент</li>
					</ol>
				</div>
				<div class="list-card">
					<h3 class="title">Элемент списка с индикатором</h3>
					<ul class="list">
						<li class="list-item">
							<span class="indicator success"></span>
							<span>Сайт доступен 99.9% времени.</span>
						</li>
						<li class="list-item">
							<span class="indicator info"></span>
							<span>Все изображения оптимизированы.</span>
						</li>
						<li class="list-item">
							<span class="indicator warning"></span>
							<span>Нужно обновить документацию.</span>
						</li>
					</ul>
				</div>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Формы</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
				<form class="space-y-4">
					<div>
						<label for="p-name" class="mb-1 block text-sm font-medium text-zinc-800">Имя</label>
						<input id="p-name" type="text" class="block w-full rounded-md border border-zinc-300 px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-sky-500 focus:outline-none">
					</div>
					<div>
						<label for="p-email" class="mb-1 block text-sm font-medium text-zinc-800">Email</label>
						<input id="p-email" type="email" class="block w-full rounded-md border border-zinc-300 px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-sky-500 focus:outline-none">
					</div>
					<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
						<label class="block">
							<span class="mb-1 block text-sm font-medium text-zinc-800">Выбор</span>
							<select class="block w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-sky-500 focus:outline-none">
								<option>Вариант A</option>
								<option>Вариант B</option>
							</select>
						</label>
						<label class="block">
							<span class="mb-1 block text-sm font-medium text-zinc-800">Поиск</span>
							<div class="flex rounded-md border border-zinc-300 focus-within:border-sky-500">
								<input type="text" class="min-w-0 flex-1 rounded-l-md bg-white px-3 py-2 text-sm text-zinc-900 outline-none" placeholder="Запрос...">
								<button type="button" class="rounded-r-md bg-gradient-to-r from-blue-600 to-purple-600 px-3 py-2 text-sm font-medium text-white hover:from-blue-700 hover:to-purple-700">Найти</button>
							</div>
						</label>
					</div>
					<div>
						<label for="p-msg" class="mb-1 block text-sm font-medium text-zinc-800">Сообщение</label>
						<textarea id="p-msg" rows="3" class="block w-full rounded-md border border-zinc-300 px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-sky-500 focus:outline-none"></textarea>
					</div>
					<div class="flex items-center justify-end gap-2">
						<button type="button" class="rounded-md px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100">Отмена</button>
						<button type="submit" class="rounded-md bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:from-blue-700 hover:to-purple-700">Отправить</button>
					</div>
				</form>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Пагинация</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
				<nav class="pagination" aria-label="Пагинация">
					<a href="#" class="nav-button">Назад</a>
					<ul class="pages">
						<li><a href="#" class="page-item default">1</a></li>
						<li><a aria-current="page" href="#" class="page-item active">2</a></li>
						<li><a href="#" class="page-item default">3</a></li>
						<li><span class="page-item ellipsis">…</span></li>
						<li><a href="#" class="page-item default">10</a></li>
					</ul>
					<a href="#" class="nav-button">Вперёд</a>
				</nav>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Чипсы, аватары и цитаты</h2>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<!-- Chips -->
				<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
					<div class="mb-3 flex flex-wrap gap-2">
						<span class="chip">Design<button type="button" class="remove-btn">×</button></span>
						<span class="chip">UX<button type="button" class="remove-btn">×</button></span>
						<span class="chip">CMS<button type="button" class="remove-btn">×</button></span>
					</div>
					<blockquote class="rounded-md border-l-4 border-sky-500 bg-sky-50 p-4 text-sm text-sky-900 shadow-sm">
						<p class="mb-1 font-medium">Info</p>
						<p>Подсказка с дополнительной информацией.</p>
					</blockquote>
				</div>
				<!-- Avatars -->
				<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
					<div class="flex items-center gap-3">
						<img alt="A" src="https://i.pravatar.cc/40?img=1" class="avatar small">
						<img alt="B" src="https://i.pravatar.cc/40?img=2" class="avatar small">
						<img alt="C" src="https://i.pravatar.cc/40?img=3" class="avatar small">
					</div>
					<div class="mt-4 grid grid-cols-3 gap-4">
						<div class="avatar-with-status medium">
							<img alt="Online" src="https://i.pravatar.cc/64?img=7" class="avatar medium">
							<span class="status-indicator online"></span>
						</div>
						<div class="avatar-with-status medium">
							<img alt="Offline" src="https://i.pravatar.cc/64?img=8" class="avatar medium">
							<span class="status-indicator offline"></span>
						</div>
						<div class="avatar-with-status medium">
							<img alt="Messages" src="https://i.pravatar.cc/64?img=9" class="avatar medium">
							<span class="notification-badge">12</span>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Вкладки</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
				<nav class="flex gap-2" aria-label="Вкладки">
					<a href="#" class="rounded-md bg-gradient-to-r from-blue-600 to-purple-600 px-3 py-1.5 text-sm font-medium text-white">Обзор</a>
					<a href="#" class="rounded-md border border-zinc-200 px-3 py-1.5 text-sm text-zinc-700 hover:bg-zinc-50">Профиль</a>
					<a href="#" class="rounded-md border border-zinc-200 px-3 py-1.5 text-sm text-zinc-700 hover:bg-zinc-50">Контакты</a>
				</nav>
				<div class="mt-4 text-sm text-zinc-600">Контент активной вкладки.</div>
			</div>
		</section>

		<!-- Features -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Features</h2>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
				<div class="feature-card blue">
					<div class="icon blue">
						<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
					</div>
					<h3 class="title">Безопасность</h3>
					<p class="description">Современные практики и простые обновления.</p>
				</div>
				<div class="feature-card green">
					<div class="icon green">
						<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
					</div>
					<h3 class="title">Контент</h3>
					<p class="description">Фокус на тексте, чистая типографика.</p>
				</div>
				<div class="feature-card orange">
					<div class="icon orange">
						<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
					</div>
					<h3 class="title">Скорость</h3>
					<p class="description">Минимум JS, максимум скорости.</p>
				</div>
			</div>
		</section>

		<!-- CTA -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">CTA</h2>
			<div class="rounded-2xl border border-zinc-200 bg-gradient-to-br from-sky-50 to-blue-50 p-6 shadow-sm">
				<div class="flex flex-col items-start gap-3 sm:flex-row sm:items-center sm:justify-between">
					<div>
						<h3 class="text-lg font-semibold text-zinc-900">Готовы начать?</h3>
						<p class="text-sm text-zinc-600">Установите RooCMS и начните публиковать уже сегодня.</p>
					</div>
					<div class="flex gap-2">
						<a href="#" class="inline-flex items-center rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:from-blue-700 hover:to-purple-700">Установить</a>
						<a href="#" class="inline-flex items-center rounded-lg border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-900 hover:bg-zinc-50">Документация</a>
					</div>
				</div>
			</div>
		</section>

		<!-- Typography Extended -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Расширенная типографика</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm space-y-4">
				<h1 class="text-3xl font-bold">H1 Заголовок</h1>
				<h2 class="text-2xl font-semibold">H2 Заголовок</h2>
				<h3 class="text-xl font-semibold">H3 Заголовок</h3>
				<p class="text-zinc-700">Абзац с <a href="#" class="text-sky-700 hover:text-sky-900 underline">ссылкой</a> и <code class="rounded bg-zinc-100 px-1 py-0.5 text-[90%]">inline code</code>.</p>
				<ul class="list-disc pl-5 text-zinc-700 space-y-1">
					<li>Маркированный пункт</li>
					<li>Ещё один пункт</li>
				</ul>
				<pre class="overflow-auto rounded-lg border border-zinc-200 bg-zinc-50 p-3 text-sm text-zinc-800"><code>// Пример кода
function hello() {
  console.log('Hello RooCMS');
}
</code></pre>
				<blockquote class="rounded-md border-l-4 border-amber-500 bg-amber-50 p-4 text-sm text-amber-900 shadow-sm">Важная примечание к тексту.</blockquote>
			</div>
		</section>

		<!-- Timeline -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Timeline</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
				<div class="space-y-3">
					<div class="flex items-center gap-3 text-sm">
						<div class="h-3 w-3 rounded-full bg-amber-400"></div>
						<span class="text-zinc-700">Account created</span>
						<span class="ml-auto text-zinc-500">2024-01-15</span>
					</div>
					<div class="flex items-center gap-3 text-sm">
						<div class="h-3 w-3 rounded-full bg-emerald-400"></div>
						<span class="text-zinc-700">Email verified</span>
						<span class="ml-auto text-zinc-500">2024-01-20</span>
					</div>
					<div class="flex items-center gap-3 text-sm">
						<div class="h-3 w-3 rounded-full bg-sky-400"></div>
						<span class="text-zinc-700">Profile completed</span>
						<span class="ml-auto text-zinc-500">2024-02-01</span>
					</div>
				</div>
			</div>
		</section>

		<!-- Table -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Таблица</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
				<div class="-mx-5 overflow-x-auto px-5">
					<table class="min-w-full text-left text-sm">
						<thead class="border-b border-zinc-200 text-zinc-500">
							<tr>
								<th scope="col" class="py-3 pr-6 font-medium">Имя</th>
								<th scope="col" class="py-3 pr-6 font-medium">Email</th>
								<th scope="col" class="py-3 pr-0 font-medium text-right">Статус</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-zinc-100">
							<tr class="hover:bg-zinc-50">
								<td class="py-3 pr-6 text-zinc-900">Leslie Alexander</td>
								<td class="py-3 pr-6 text-zinc-600">leslie@example.com</td>
								<td class="py-3 pr-0 text-right"><span class="rounded-full bg-emerald-50 px-2 py-0.5 text-emerald-700">Active</span></td>
							</tr>
							<tr class="hover:bg-zinc-50">
								<td class="py-3 pr-6 text-zinc-900">Michael Foster</td>
								<td class="py-3 pr-6 text-zinc-600">michael@example.com</td>
								<td class="py-3 pr-0 text-right"><span class="rounded-full bg-zinc-100 px-2 py-0.5 text-zinc-700">Pending</span></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</section>

		<!-- Empty State -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Empty state</h2>
			<div class="rounded-2xl border border-dashed border-zinc-300 bg-white p-8 text-center shadow-sm">
				<div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-r from-zinc-500 to-slate-500 text-white">
					<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
				</div>
				<h3 class="text-lg font-semibold text-zinc-900">Пока здесь пусто</h3>
				<p class="mt-1 text-sm text-zinc-600">Создайте первый элемент, чтобы увидеть список.</p>
				<div class="mt-4">
					<a href="#" class="inline-flex items-center rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:from-blue-700 hover:to-purple-700">Создать</a>
				</div>
			</div>
		</section>

		<!-- Pagination: Show More -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Пагинация: Показать ещё</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm text-center">
				<button class="inline-flex items-center rounded-md border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-900 hover:bg-zinc-50">Показать ещё</button>
				<p class="mt-2 text-xs text-zinc-500">Показано 20 из 120</p>
			</div>
		</section>

		<!-- Filters / Tags -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Фильтры и теги</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
				<div class="flex flex-wrap gap-2">
					<button class="filter-btn default">Все</button>
					<button class="filter-btn primary">UI</button>
					<button class="filter-btn success">Backend</button>
					<button class="filter-btn warning">Guides</button>
					<button class="filter-btn danger">Errors</button>
				</div>
				<div class="mt-4 flex flex-wrap gap-2">
					<span class="chip">Selected: UI<button type="button" class="remove-btn">×</button></span>
					<span class="chip">Selected: Backend<button type="button" class="remove-btn">×</button></span>
				</div>
			</div>
		</section>

		<!-- Search bar -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Поиск</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm space-y-4">
				<!-- Search input -->
				<div class="flex rounded-md border border-zinc-300 focus-within:border-sky-500">
					<input type="text" class="min-w-0 flex-1 rounded-l-md bg-white px-3 py-2 text-sm text-zinc-900 outline-none" placeholder="Искать статьи, теги...">
					<button class="rounded-r-md bg-gradient-to-r from-blue-600 to-purple-600 px-3 py-2 text-sm font-medium text-white hover:from-blue-700 hover:to-purple-700">Искать</button>
				</div>

				<!-- Results -->
				<div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
					<a href="#" class="rounded-lg border border-zinc-200 bg-white p-3 text-sm text-zinc-700 hover:bg-zinc-50">
						<span class="block font-medium text-zinc-900">Создаём чистые UI</span>
						<span class="text-xs text-zinc-500">Tag: Design • 8 мин</span>
					</a>
					<a href="#" class="rounded-lg border border-zinc-200 bg-white p-3 text-sm text-zinc-700 hover:bg-zinc-50">
						<span class="block font-medium text-zinc-900">Чистый PHP</span>
						<span class="text-xs text-zinc-500">Tag: Backend • 6 мин</span>
					</a>
				</div>

				<!-- Empty results -->
				<div class="rounded-lg border border-dashed border-zinc-300 p-6 text-center">
					<p class="text-sm text-zinc-600">Ничего не найдено. Попробуйте изменить критерии поиска.</p>
				</div>
			</div>
		</section>

		<!-- FAQ / Accordion (static) -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">FAQ</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm space-y-3">
				<div class="rounded-lg border border-zinc-200 p-3">
					<div class="text-sm font-medium text-zinc-900">Как установить RooCMS?</div>
					<p class="mt-1 text-sm text-zinc-600">Скачайте архив, распакуйте на сервер и следуйте мастеру установки.</p>
				</div>
				<div class="rounded-lg border border-zinc-200 p-3">
					<div class="text-sm font-medium text-zinc-900">Где искать документацию?</div>
					<p class="mt-1 text-sm text-zinc-600">Посетите официальный сайт и раздел Docs.</p>
				</div>
			</div>
		</section>

		<!-- Stepper -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Stepper</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
				<ol class="stepper">
					<li class="stepper-item">
						<span class="number active">1</span>
						<span class="label">Загрузка</span>
					</li>
					<li class="stepper-item">
						<span class="number completed">2</span>
						<span class="label">Проверка</span>
					</li>
					<li class="stepper-item">
						<span class="number pending">3</span>
						<span class="label">Готово</span>
					</li>
				</ol>
			</div>
		</section>

		<!-- Toasts (static) -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Toasts</h2>
			<div class="space-y-2">
				<div class="mx-auto max-w-md rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-900 shadow-sm">Сохранено успешно.</div>
				<div class="mx-auto max-w-md rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900 shadow-sm">Внимание: требуется действие.</div>
				<div class="mx-auto max-w-md rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-900 shadow-sm">Ошибка при сохранении.</div>
			</div>
		</section>

		<!-- Banner -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Banner</h2>
			<div class="rounded-2xl border border-sky-200 bg-sky-50 p-5 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
				<div>
					<div class="text-sm font-medium text-sky-900">Новая версия RooCMS доступна</div>
					<p class="text-xs text-sky-800">Обновитесь до последнего релиза, чтобы получить улучшения.</p>
				</div>
				<a href="#" class="inline-flex items-center rounded-md bg-gradient-to-r from-blue-600 to-purple-600 px-3 py-2 text-xs font-medium text-white hover:from-blue-700 hover:to-purple-700">Обновить</a>
			</div>
		</section>

		<!-- Skeletons -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Skeletons</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
				<div class="skeleton-container">
					<div class="skeleton-line title"></div>
					<div class="skeleton-line full"></div>
					<div class="skeleton-line large"></div>
					<div class="skeleton-line medium"></div>
				</div>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Контентные блоки CMS</h2>
			<div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
				<article class="cms-card">
					<header class="header">
						<span>Страница</span>
						<span class="status published">Опубликовано</span>
					</header>
					<div class="content">
						<h3 class="title">Политика конфиденциальности</h3>
						<p class="description">Документ описывает правила сбора, хранения и использования личных данных.</p>
					</div>
					<footer class="footer">
						<span>Обновлено: 12.05.2024</span>
						<a href="#" class="action-link">Перейти<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
					</footer>
				</article>
				<article class="cms-card">
					<header class="header">
						<span>Шаблон</span>
						<span class="status draft">Черновик</span>
					</header>
					<div class="content">
						<h3 class="title">Состояние пустого раздела</h3>
						<p class="description">Используйте этот блок, чтобы показывать подсказки при отсутствии контента.</p>
					</div>
					<footer class="footer">
						<span>Изменено: 08.05.2024</span>
						<a href="#" class="action-link">Редактировать<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
					</footer>
				</article>
				<article class="cms-card">
					<header class="header">
						<span>Блок</span>
						<span class="status unpublished">Не опубликовано</span>
					</header>
					<div class="content">
						<h3 class="title">Промо баннер</h3>
						<p class="description">Привлекайте внимание к новым релизам и акциям RooCMS.</p>
					</div>
					<footer class="footer">
						<span>Автор: RooCMS</span>
						<a href="#" class="action-link">Предпросмотр<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></a>
					</footer>
				</article>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Элементы форм</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm space-y-6">
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					<label class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-zinc-50 p-4 text-sm text-zinc-700">
						<input type="checkbox" class="h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500" checked>
						<span class="flex-1">Отправлять уведомления об обновлениях</span>
					</label>
					<label class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-zinc-50 p-4 text-sm text-zinc-700">
						<input type="checkbox" class="h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500">
						<span class="flex-1">Получать отчёт на email</span>
					</label>
				</div>
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					<label class="flex items-center gap-3 rounded-lg border border-zinc-200 p-4 text-sm text-zinc-700">
						<input name="plan" type="radio" class="h-4 w-4 border-zinc-300 text-blue-600 focus:ring-blue-500" checked>
						<span class="flex-1">
							<p class="font-semibold text-zinc-900">Базовый</p>
							<p class="text-xs text-zinc-500">Поддержка по email</p>
						</span>
					</label>
					<label class="flex items-center gap-3 rounded-lg border border-zinc-200 p-4 text-sm text-zinc-700">
						<input name="plan" type="radio" class="h-4 w-4 border-zinc-300 text-blue-600 focus:ring-blue-500">
						<span class="flex-1">
							<p class="font-semibold text-zinc-900">Про</p>
							<p class="text-xs text-zinc-500">Расширенные отчёты</p>
						</span>
					</label>
				</div>
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					<label class="block text-sm text-zinc-700">
						<span class="mb-1 block font-medium text-zinc-900">URL</span>
						<div class="flex rounded-md border border-zinc-300 focus-within:border-blue-500 focus-within:ring focus-within:ring-blue-200/50">
							<span class="inline-flex items-center border-r border-zinc-200 bg-zinc-50 px-3 text-xs uppercase tracking-wide text-zinc-500">https://</span>
							<input type="text" class="min-w-0 flex-1 rounded-r-md bg-white px-3 py-2 text-sm text-zinc-900 outline-none" placeholder="dev.roocms.com/page">
						</div>
					</label>
					<label class="block text-sm text-zinc-700">
						<span class="mb-1 block font-medium text-zinc-900">Файл</span>
						<div class="flex items-center gap-3 rounded-lg border border-dashed border-zinc-300 bg-zinc-50 px-4 py-3">
							<svg class="h-5 w-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M4 12V8a2 2 0 012-2h3m10 6V8a2 2 0 00-2-2h-3m-4-4l4 4m0 0l-4 4m4-4H9"/></svg>
							<div class="flex-1 text-xs">
								<p class="font-medium text-zinc-800">Загрузите обложку</p>
								<p class="text-zinc-500">PNG, JPG до 5 МБ</p>
							</div>
							<button type="button" class="rounded-md border border-zinc-200 bg-white px-3 py-1.5 text-xs font-medium text-zinc-700 hover:bg-zinc-100">Выбрать</button>
						</div>
					</label>
				</div>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Списки задач</h2>
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
                    <h3 class="text-sm font-semibold text-zinc-900">Чек-лист публикации</h3>
                    <div class="mt-3 space-y-2 text-sm text-zinc-600">
                        <label class="flex items-start gap-3 rounded-lg border border-zinc-200/70 bg-zinc-50 px-3 py-2">
                            <input type="checkbox" class="mt-1 h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500" checked>
                            <span>Заголовок и описание заполнены</span>
                        </label>
                        <label class="flex items-start gap-3 rounded-lg border border-zinc-200/70 bg-zinc-50 px-3 py-2">
                            <input type="checkbox" class="mt-1 h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500" checked>
                            <span>Обложка загружена</span>
                        </label>
                        <label class="flex items-start gap-3 rounded-lg border border-zinc-200/70 bg-zinc-50 px-3 py-2">
                            <input type="checkbox" class="mt-1 h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500">
                            <span>SEO-поля заполнены</span>
                        </label>
                        <label class="flex items-start gap-3 rounded-lg border border-zinc-200/70 bg-zinc-50 px-3 py-2">
                            <input type="checkbox" class="mt-1 h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500">
                            <span>Просмотрено вторым редактором</span>
                        </label>
					</div>
				</div>
				<div class="list-card">
					<h3 class="title">Канбан задачи</h3>
                    <ul class="list">
                        <li class="alert info">
                            <p class="text-title">Новый релиз 2.1</p>
                            <p class="text-meta">Статус: В работе</p>
                        </li>
                        <li class="alert success">
                            <p class="text-title">Добавить локализацию</p>
                            <p class="text-meta">Статус: Готово</p>
                        </li>
                        <li class="alert warning">
                            <p class="text-title">Проверить резервные копии</p>
                            <p class="text-meta">Статус: В ожидании</p>
                        </li>
                        <li class="alert danger">
                            <p class="text-title">Обновить документацию</p>
                            <p class="text-meta">Статус: Требует внимания</p>
                        </li>
                    </ul>
				</div>
			</div>		
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Свитчи (Toggle)</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm space-y-4">
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
					<label class="toggle-label enabled">
						<span class="label-text">Email уведомления</span>
						<input type="checkbox" class="toggle-input" checked>
						<span class="toggle-switch enabled primary">
							<span class="toggle-knob enabled"></span>
						</span>
					</label>
					<label class="toggle-label enabled">
						<span class="label-text">Публичный профиль</span>
						<input type="checkbox" class="toggle-input" checked>
						<span class="toggle-switch enabled success">
							<span class="toggle-knob enabled"></span>
						</span>
					</label>
					<label class="toggle-label enabled">
						<span class="label-text">Режим обслуживания</span>
						<input type="checkbox" class="toggle-input">
						<span class="toggle-switch enabled danger">
							<span class="toggle-knob enabled"></span>
						</span>
					</label>
					<label class="toggle-label disabled">
						<span class="label-text">Автообновления</span>
						<input type="checkbox" class="toggle-input" disabled>
						<span class="toggle-switch disabled">
							<span class="toggle-knob"></span>
						</span>
					</label>
				</div>
				<p class="text-xs text-зинк-500">Стили выравнивают бегунок без JS с помощью <code class="rounded bg-зинк-100 px-1">peer</code> и flex-расположения.</p>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Tree View</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm space-y-3">
				<details open class="rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm text-zinc-700">
					<summary class="flex cursor-pointer items-center gap-2 font-medium text-zinc-900">
						<svg class="h-4 w-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/></svg>
						Контент
					</summary>
					<div class="mt-3 space-y-2 pl-6">
						<label class="flex items-center gap-2">
							<input type="checkbox" class="h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500">
							<span>Новости</span>
						</label>
						<details open class="space-y-2">
							<summary class="flex items-center gap-2 text-zinc-700">
								<svg class="h-4 w-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6"/></svg>
								Справочник
							</summary>
							<div class="space-y-2 pl-6">
								<label class="flex items-center gap-2">
									<input type="checkbox" class="h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500" checked>
									<span>Документация</span>
								</label>
								<label class="flex items-center gap-2">
									<input type="checkbox" class="h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500">
									<span>FAQ</span>
								</label>
							</div>
						</details>
					</div>
				</details>
				<details class="rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm text-zinc-700">
					<summary class="flex cursor-pointer items-center gap-2 font-medium text-zinc-900">
						<svg class="h-4 w-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
						Маркетинг
					</summary>
				</details>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Модальные окна</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
				<div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
					<!-- Базовое модальное окно -->
					<div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4">
						<h3 class="mb-3 text-sm font-semibold text-zinc-900">Базовое подтверждение</h3>
						<p class="mb-4 text-xs text-zinc-600">Простое модальное окно с подтверждением действия.</p>
						<button @click="window.modalExamples.showWarning()" class="inline-flex items-center rounded-md bg-gradient-to-r from-amber-500 to-orange-500 px-3 py-2 text-xs font-medium text-white hover:from-amber-600 hover:to-orange-600">Предупреждение</button>
					</div>

					<!-- Модальное окно с формой -->
					<div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4">
						<h3 class="mb-3 text-sm font-semibold text-zinc-900">Форма обратной связи</h3>
						<p class="mb-4 text-xs text-zinc-600">Модальное окно с формой для отправки сообщения.</p>
						<button @click="window.modalExamples.showFeedback()" class="inline-flex items-center rounded-md bg-gradient-to-r from-sky-500 to-blue-500 px-3 py-2 text-xs font-medium text-white hover:from-sky-600 hover:to-blue-600">Форма</button>
					</div>

					<!-- Модальное окно с изображением -->
					<div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4">
						<h3 class="mb-3 text-sm font-semibold text-zinc-900">Просмотр изображения</h3>
						<p class="mb-4 text-xs text-zinc-600">Модальное окно для отображения изображений и медиа.</p>
						<button @click="window.modalExamples.showImage()" class="inline-flex items-center rounded-md bg-gradient-to-r from-emerald-500 to-teal-500 px-3 py-2 text-xs font-medium text-white hover:from-emerald-600 hover:to-teal-600">Изображение</button>
					</div>
				</div>

				<div class="mt-6 rounded-lg border border-dashed border-zinc-200 bg-zinc-50 p-4 text-xs text-zinc-600">
					<p class="font-semibold text-zinc-900">Примеры использования</p>
					<ul class="mt-2 list-disc space-y-1 pl-4">
						<li><span class="text-zinc-700">Используйте <code class="rounded bg-white px-1">window.modalExamples.show*()</code> для вызова модальных окон.</span></li>
						<li><span class="text-zinc-700">Поддерживаются типы: <code class="rounded bg-white px-1">alert</code>, <code class="rounded bg-white px-1">warning</code>, <code class="rounded bg-white px-1">notice</code>.</span></li>
						<li><span class="text-zinc-700">Функция возвращает Promise с результатом выбора пользователя.</span></li>
						<li><span class="text-zinc-700">Для CSP совместимости все модальные окна используют глобальные функции.</span></li>
					</ul>
				</div>
			</div>
		</section>

		<!-- Продвинутые примеры модальных окон -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Расширенные модальные окна</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
				<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
					<!-- Модальное окно с формой входа -->
					<div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4">
						<h3 class="mb-3 text-sm font-semibold text-zinc-900">Форма входа</h3>
						<p class="mb-4 text-xs text-zinc-600">Модальное окно с формой аутентификации пользователя.</p>
						<button @click="window.modalExamples.loginFlow()" class="inline-flex items-center rounded-md bg-gradient-to-r from-purple-500 to-pink-500 px-3 py-2 text-xs font-medium text-white hover:from-purple-600 hover:to-pink-600">Вход</button>
					</div>

					<!-- Модальное окно с подтверждением удаления -->
					<div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4">
						<h3 class="mb-3 text-sm font-semibold text-zinc-900">Подтверждение удаления</h3>
						<p class="mb-4 text-xs text-zinc-600">Критическое действие с дополнительным подтверждением.</p>
						<button @click="window.modalExamples.deleteFlow()" class="inline-flex items-center rounded-md bg-gradient-to-r from-rose-500 to-red-500 px-3 py-2 text-xs font-medium text-white hover:from-rose-600 hover:to-red-600">Удалить</button>
					</div>
				</div>

				<div class="mt-6 rounded-lg border border-dashed border-zinc-200 bg-zinc-50 p-4 text-xs text-zinc-600">
					<p class="font-semibold text-zinc-900">Продвинутые возможности</p>
					<ul class="mt-2 list-disc space-y-1 pl-4">
						<li><span class="text-zinc-700">Используйте цепочки вызовов для многошаговых процессов.</span></li>
						<li><span class="text-zinc-700">Комбинируйте разные типы модальных окон в одном сценарии.</span></li>
						<li><span class="text-zinc-700">Обработка ошибок и валидация формы внутри модального окна.</span></li>
					</ul>
				</div>
			</div>
		</section>


		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Рейтинг</h2>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
				<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
					<fieldset class="space-y-3" aria-label="Рейтинг звёздами">
						<legend class="text-sm font-medium text-zinc-900">Звёзды</legend>
						<div class="rating-group text-amber-400" aria-hidden="true">
							<input type="radio" name="rating-stars" id="rating-star-5" value="5">
							<label for="rating-star-5">
								<svg class="h-6 w-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
							</label>
							<input type="radio" name="rating-stars" id="rating-star-4" value="4" checked>
							<label for="rating-star-4">
								<svg class="h-6 w-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
							</label>
							<input type="radio" name="rating-stars" id="rating-star-3" value="3">
							<label for="rating-star-3">
								<svg class="h-6 w-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
							</label>
							<input type="radio" name="rating-stars" id="rating-star-2" value="2">
							<label for="rating-star-2">
								<svg class="h-6 w-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
							</label>
							<input type="radio" name="rating-stars" id="rating-star-1" value="1">
							<label for="rating-star-1">
								<svg class="h-6 w-6 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
							</label>
						</div>
						<p class="text-xs text-zinc-500">Выбор звезды окрашивает текущий рейтинг и все правее.</p>
					</fieldset>
				</div>
				<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
					<p class="text-sm font-medium text-zinc-900">Смайлы</p>
					<div class="mt-3 flex items-center gap-4" role="radiogroup" aria-label="Рейтинг смайлами">
						<label class="flex cursor-pointer flex-col items-center gap-1 text-2xl text-zinc-400 hover:text-rose-500">
							<input type="radio" name="rating-emoji" class="sr-only">
							<span aria-hidden="true">😞</span>
							<span class="text-xs text-zinc-500">Плохо</span>
						</label>
						<label class="flex cursor-pointer flex-col items-center gap-1 text-2xl text-emerald-500">
							<input type="radio" name="rating-emoji" class="sr-only" checked>
							<span aria-hidden="true">😊</span>
							<span class="text-xs text-zinc-500">Отлично</span>
						</label>
						<label class="flex cursor-pointer flex-col items-center gap-1 text-2xl text-purple-500 hover:text-purple-600">
							<input type="radio" name="rating-emoji" class="sr-only">
							<span aria-hidden="true">😍</span>
							<span class="text-xs text-zinc-500">Люблю</span>
						</label>
					</div>
				</div>
				<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
					<p class="text-sm font-medium text-zinc-900">Кастомные иконки + половинки</p>
					<div class="mt-3 flex items-center gap-1 text-rose-200">
						<span class="relative">
							<svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 6 4 4 6.5 4c1.74 0 3.41.81 4.5 2.09C12.09 4.81 13.76 4 15.5 4 18 4 20 6 20 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
							<svg class="absolute inset-0 h-6 w-6 text-rose-500 half-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 6 4 4 6.5 4c1.74 0 3.41.81 4.5 2.09C12.09 4.81 13.76 4 15.5 4 18 4 20 6 20 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
						</span>
						<svg class="h-6 w-6 text-rose-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 6 4 4 6.5 4c1.74 0 3.41.81 4.5 2.09C12.09 4.81 13.76 4 15.5 4 18 4 20 6 20 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
						<svg class="h-6 w-6 text-rose-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 6 4 4 6.5 4c1.74 0 3.41.81 4.5 2.09C12.09 4.81 13.76 4 15.5 4 18 4 20 6 20 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
						<svg class="h-6 w-6 text-zinc-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 6 4 4 6.5 4c1.74 0 3.41.81 4.5 2.09C12.09 4.81 13.76 4 15.5 4 18 4 20 6 20 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
						<span class="text-sm text-zinc-600">3.5 / 5</span>
					</div>
					<p class="mt-2 text-xs text-zinc-500">Для половин используйте две иконки: базовую и цветную с обрезкой по clip-path.</p>
				</div>
			</div>
		</section>

	</div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';