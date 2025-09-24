<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

$page_title = 'UI Kit — Public — RooCMS';
$page_description = 'Public UI components for RooCMS theme';

$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/'.$theme_name;

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
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Кнопки и ссылки</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
				<div class="flex flex-wrap gap-3">
					<a href="#" class="inline-flex items-center justify-center rounded-md bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-medium text-white transition hover:from-blue-700 hover:to-purple-700">Primary</a>
					<a href="#" class="inline-flex items-center justify-center rounded-md bg-gradient-to-r from-rose-600 to-red-600 px-4 py-2 text-sm font-medium text-white transition hover:from-rose-700 hover:to-red-700">Danger</a>
					<a href="#" class="inline-flex items-center justify-center rounded-md bg-gradient-to-r from-emerald-600 to-green-600 px-4 py-2 text-sm font-medium text-white transition hover:from-emerald-700 hover:to-green-700">Success</a>
					<a href="#" class="inline-flex items-center justify-center rounded-md bg-gradient-to-r from-sky-600 to-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:from-sky-700 hover:to-blue-700">Info</a>
					<a href="#" class="inline-flex items-center justify-center rounded-md bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-2 text-sm font-medium text-white transition hover:from-amber-600 hover:to-orange-600">Warning</a>
					<a href="#" class="inline-flex items-center justify-center rounded-md bg-gradient-to-r from-gray-600 to-slate-600 px-4 py-2 text-sm font-medium text-white transition hover:from-gray-700 hover:to-slate-700">Contrast</a>
					<a href="#" class="inline-flex items-center justify-center rounded-md border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-900 hover:bg-zinc-50">Secondary</a>
					<a href="#" class="text-sm font-medium text-sky-700 hover:text-sky-900">Текстовая ссылка</a>
				</div>
			</div>
		</section>

		<!-- Buttons: sizes & icons -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Кнопки: размеры и иконки</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm space-y-4">
				<div class="flex flex-wrap items-center gap-3">
					<a href="#" class="inline-flex items-center gap-2 rounded-md bg-gradient-to-r from-blue-600 to-purple-600 px-3 py-1.5 text-xs font-medium text-white">
						<svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
						Small
					</a>
					<a href="#" class="inline-flex items-center gap-2 rounded-md bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-medium text-white">
						<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
						Medium
					</a>
					<a href="#" class="inline-flex items-center gap-3 rounded-md bg-gradient-to-r from-blue-600 to-purple-600 px-5 py-2.5 text-base font-semibold text-white">
						<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
						Large
					</a>
				</div>

				<div class="flex flex-wrap items-center gap-3">
					<a href="/" class="group inline-flex items-center gap-2 rounded-md bg-gradient-to-r from-gray-600 to-slate-600 px-5 py-2.5 text-sm font-medium text-white">
						<svg class="h-4 w-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
						Back to Home
					</a>
					<a href="#" class="inline-flex items-center gap-2 rounded-md border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-900 hover:bg-zinc-50">
						<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
						With icon
					</a>
				</div>
			</div>
		</section>

		<!-- Containers -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Контейнеры</h2>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
				<div class="rounded-2xl border border-gray-200/50 bg-white/50 py-8 px-6 shadow-sm backdrop-blur-sm">
					<h3 class="text-sm font-semibold text-zinc-900 mb-1">Frosted</h3>
					<p class="text-sm text-zinc-600">Полупрозрачная панель на светлом фоне.</p>
				</div>
				<div class="rounded-2xl border border-zinc-200 bg-white py-8 px-6 shadow-sm">
					<h3 class="text-sm font-semibold text-zinc-900 mb-1">Panel</h3>
					<p class="text-sm text-zinc-600">Классическая панель контента.</p>
				</div>
				<div class="relative rounded-2xl border border-blue-200/50 bg-gradient-to-br from-blue-50 to-indigo-50 py-8 px-6 shadow-sm">
					<div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-blue-600/5 to-indigo-600/5"></div>
					<div class="relative">
						<h3 class="text-sm font-semibold text-zinc-900 mb-1">Gradient Panel</h3>
						<p class="text-sm text-zinc-600">Как на profile/terms секциях.</p>
					</div>
				</div>
			</div>
		</section>

		<!-- Metrics -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Карточки статистики</h2>
			<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
				<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
					<div class="text-xs font-medium text-zinc-500">Пользователи</div>
					<div class="mt-1 text-2xl font-semibold text-zinc-900">1,248</div>
					<div class="mt-1 text-xs text-emerald-600">+3.1% за неделю</div>
				</div>
				<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
					<div class="text-xs font-medium text-zinc-500">Публикации</div>
					<div class="mt-1 text-2xl font-semibold text-zinc-900">328</div>
					<div class="mt-1 text-xs text-zinc-500">−0.8% за неделю</div>
				</div>
				<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
					<div class="text-xs font-medium text-zinc-500">Просмотры</div>
					<div class="mt-1 text-2xl font-semibold text-zinc-900">82,304</div>
					<div class="mt-1 text-xs text-emerald-600">+12.4% за неделю</div>
				</div>
				<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
					<div class="text-xs font-medium text-zinc-500">Комментарии</div>
					<div class="mt-1 text-2xl font-semibold text-zinc-900">5,231</div>
					<div class="mt-1 text-xs text-emerald-600">+1.2% за неделю</div>
				</div>
			</div>
		</section>

		<!-- Progress bars -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Прогресс</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm space-y-4">
				<div>
					<div class="mb-1 flex items-center justify-between text-xs text-zinc-600"><span>Загрузка</span><span>45%</span></div>
					<div class="h-2 w-full overflow-hidden rounded-full bg-zinc-200"><div class="h-full w-[45%] rounded-full bg-gradient-to-r from-blue-500 to-indigo-600"></div></div>
				</div>
				<div>
					<div class="mb-1 flex items-center justify-between text-xs text-zinc-600"><span>Обработка</span><span class="text-emerald-700">70%</span></div>
					<div class="h-2 w-full overflow-hidden rounded-full bg-emerald-100"><div class="h-full w-[70%] rounded-full bg-gradient-to-r from-emerald-500 to-green-600"></div></div>
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
					<div class="flex flex-wrap items-center gap-3 text-xs font-medium">
						<span class="rounded-full bg-zinc-100 px-2.5 py-1 text-zinc-700">Neutral</span>
						<span class="rounded-full bg-sky-100 px-2.5 py-1 text-sky-700">Info</span>
						<span class="rounded-full bg-emerald-100 px-2.5 py-1 text-emerald-700">Success</span>
						<span class="rounded-full bg-amber-100 px-2.5 py-1 text-amber-700">Warning</span>
						<span class="rounded-full bg-rose-100 px-2.5 py-1 text-rose-700">Danger</span>
					</div>
				</div>
				<div class="space-y-3">
					<div class="rounded-lg border border-sky-200 bg-sky-50 p-4 text-sm text-sky-900">Инфо: полезное уведомление.</div>
					<div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900">Успех: действие завершено.</div>
					<div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">Предупреждение: проверьте данные.</div>
					<div class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900">Ошибка: что-то пошло не так.</div>
				</div>
			</div>
		</section>

		<!-- Article preview & layout -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Превью статьи и лэйаут</h2>
			<div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
				<article class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm">
					<div class="aspect-[16/9] w-full rounded-lg bg-zinc-100"></div>
					<h3 class="mt-3 text-base font-semibold text-zinc-900">Заголовок статьи</h3>
					<p class="mt-1 text-sm text-zinc-600">Короткое описание содержания статьи...</p>
					<div class="mt-2 flex items-center gap-2 text-xs text-zinc-500"><span>12 мая 2024</span><span>•</span><span>12 мин</span></div>
				</article>
				<article class="lg:col-span-2 rounded-xl border border-zinc-200 bg-white p-6 shadow-sm max-w-none">
				<div class="aspect-[16/9] w-full rounded-lg bg-zinc-100"></div>
					<h3 class="mt-3 text-base font-semibold text-zinc-900">Заголовок статьи</h3>
					<p class="mt-1 text-sm text-zinc-600">Короткое описание содержания статьи...</p>
					<div class="mt-2 flex items-center gap-2 text-xs text-zinc-500"><span>12 мая 2024</span><span>•</span><span>12 мин</span></div>
				</article>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Карточки и типографика</h2>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<article class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm">
					<h3 class="mb-2 text-lg font-semibold text-zinc-900">Заголовок карточки</h3>
					<p class="text-sm text-zinc-600">Этот текст демонстрирует базовую типографику публичной темы. Используются аккуратные отступы, комфортные размеры шрифтов и мягкие цвета.</p>
				</article>
				<article class="rounded-xl border border-zinc-200 bg-white p-0 shadow-sm">
					<header class="border-b border-zinc-200 p-4 text-sm font-semibold text-zinc-900">Заголовок</header>
					<div class="p-4 text-sm text-zinc-600">Содержимое карточки: абзацы, ссылки, список.</div>
					<footer class="border-t border-zinc-200 p-3 text-right"><a href="#" class="text-sm font-medium text-sky-700 hover:text-sky-900">Подробнее</a></footer>
				</article>
			</div>
		</section>

		<!-- Comments list & form -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Комментарии</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
				<div class="space-y-4">
					<div class="flex gap-3">
						<img src="https://i.pravatar.cc/40?img=11" alt="avatar" class="h-10 w-10 rounded-full object-cover">
						<div>
							<div class="text-sm font-medium text-zinc-900">Leslie Alexander <span class="ml-2 text-xs font-normal text-zinc-500">12 мая 2024</span></div>
							<p class="text-sm text-zinc-700">Отличная статья! Очень понравилась подача и примеры.</p>
						</div>
					</div>
					<div class="flex gap-3">
						<img src="https://i.pravatar.cc/40?img=12" alt="avatar" class="h-10 w-10 rounded-full object-cover">
						<div>
							<div class="text-sm font-medium text-zinc-900">Michael Foster <span class="ml-2 text-xs font-normal text-zinc-500">10 мая 2024</span></div>
							<p class="text-sm text-zinc-700">Спасибо, жду продолжения серии.</p>
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

		<!-- Docs breadcrumbs/subtitles -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Документация: хлебные крошки и подзаголовки</h2>
			<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm space-y-4">
				<nav class="text-sm text-zinc-500" aria-label="Breadcrumb">
					<ol class="flex items-center gap-2">
						<li><a href="#" class="hover:text-sky-700">Docs</a></li>
						<li aria-hidden="true" class="text-zinc-400">/</li>
						<li><a href="#" class="hover:text-sky-700">Guides</a></li>
						<li aria-hidden="true" class="text-zinc-400">/</li>
						<li><span class="text-zinc-700">Getting started</span></li>
					</ol>
				</nav>
				<div class="flex items-center justify-between">
					<h3 class="text-lg font-semibold text-zinc-900">Getting started</h3>
					<span class="rounded-full border border-zinc-200 bg-zinc-50 px-2 py-0.5 text-xs text-zinc-600">Last updated: 2024-05-12</span>
				</div>
			</div>
		</section>

		<!-- Blog cards -->
		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Карточки блога</h2>
			<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
				<article class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm">
					<div class="aspect-[16/9] w-full rounded-lg bg-zinc-100"></div>
					<div class="mt-3 flex flex-wrap gap-2 text-xs">
						<span class="rounded-full border border-sky-200 bg-sky-50 px-2 py-0.5 text-sky-800">Tailwind</span>
						<span class="rounded-full border border-zinc-200 bg-zinc-50 px-2 py-0.5 text-zinc-700">Design</span>
					</div>
					<h3 class="mt-2 text-base font-semibold text-zinc-900">Создаём чистые UI без фреймворков</h3>
					<div class="mt-1 flex items-center gap-2 text-xs text-zinc-500">
						<img src="https://i.pravatar.cc/28?img=15" class="h-5 w-5 rounded-full" alt="author">
						<span>Alex</span>
						<span>•</span>
						<span>12 мая 2024</span>
						<span>•</span>
						<span>8 мин</span>
					</div>
				</article>
				<article class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm">
					<div class="aspect-[16/9] w-full rounded-lg bg-zinc-100"></div>
					<div class="mt-3 flex flex-wrap gap-2 text-xs">
						<span class="rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-emerald-800">PHP</span>
						<span class="rounded-full border border-zinc-200 bg-zinc-50 px-2 py-0.5 text-zinc-700">Backend</span>
					</div>
					<h3 class="mt-2 text-base font-semibold text-zinc-900">Чистый PHP: быстро и понятно</h3>
					<div class="mt-1 flex items-center gap-2 text-xs text-zinc-500">
						<img src="https://i.pravatar.cc/28?img=16" class="h-5 w-5 rounded-full" alt="author">
						<span>Irina</span>
						<span>•</span>
						<span>10 мая 2024</span>
						<span>•</span>
						<span>6 мин</span>
					</div>
				</article>
				<article class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm">
					<div class="aspect-[16/9] w-full rounded-lg bg-zinc-100"></div>
					<div class="mt-3 flex flex-wrap gap-2 text-xs">
						<span class="rounded-full border border-amber-200 bg-amber-50 px-2 py-0.5 text-amber-800">Guide</span>
						<span class="rounded-full border border-zinc-200 bg-zinc-50 px-2 py-0.5 text-zinc-700">UX</span>
					</div>
					<h3 class="mt-2 text-base font-semibold text-zinc-900">UX‑шаблоны для RooCMS</h3>
					<div class="mt-1 flex items-center gap-2 text-xs text-zinc-500">
						<img src="https://i.pravatar.cc/28?img=17" class="h-5 w-5 rounded-full" alt="author">
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
				<nav class="flex items-center justify-between" aria-label="Пагинация">
					<a href="#" class="rounded-md border border-zinc-200 px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-50">Назад</a>
					<ul class="flex items-center gap-1">
						<li><a href="#" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-zinc-200 text-sm text-zinc-700 hover:bg-zinc-50">1</a></li>
						<li><a aria-current="page" href="#" class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-gradient-to-r from-blue-600 to-purple-600 text-sm font-medium text-white">2</a></li>
						<li><a href="#" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-zinc-200 text-sm text-zinc-700 hover:bg-zinc-50">3</a></li>
						<li><span class="inline-flex h-9 w-9 items-center justify-center text-sm text-zinc-400">…</span></li>
						<li><a href="#" class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-zinc-200 text-sm text-zinc-700 hover:bg-zinc-50">10</a></li>
					</ul>
					<a href="#" class="rounded-md border border-zinc-200 px-3 py-2 text-sm text-zinc-700 hover:bg-zinc-50">Вперёд</a>
				</nav>
			</div>
		</section>

		<section>
			<h2 class="mb-3 text-base font-semibold text-zinc-900">Чипсы, аватары и цитаты</h2>
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
				<!-- Chips -->
				<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
					<div class="mb-3 flex flex-wrap gap-2">
						<span class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white pl-3 pr-1 py-1 text-xs text-zinc-700">Design<button type="button" class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-zinc-100 text-[12px] leading-none text-zinc-500 hover:bg-zinc-200">×</button></span>
						<span class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white pl-3 pr-1 py-1 text-xs text-zinc-700">UX<button type="button" class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-zinc-100 text-[12px] leading-none text-zinc-500 hover:bg-zinc-200">×</button></span>
						<span class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white pl-3 pr-1 py-1 text-xs text-zinc-700">CMS<button type="button" class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-zinc-100 text-[12px] leading-none text-zinc-500 hover:bg-zinc-200">×</button></span>
					</div>
					<blockquote class="rounded-xl border-l-4 border-sky-500 bg-sky-50 p-4 text-sm text-sky-900 shadow-sm">
						<p class="mb-1 font-medium">Info</p>
						<p>Подсказка с дополнительной информацией.</p>
					</blockquote>
				</div>
				<!-- Avatars -->
				<div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm">
					<div class="flex items-center gap-3">
						<img alt="A" src="https://i.pravatar.cc/40?img=1" class="h-10 w-10 rounded-full object-cover transition hover:scale-[1.03] hover:ring-2 hover:ring-zinc-300">
						<img alt="B" src="https://i.pravatar.cc/40?img=2" class="h-10 w-10 rounded-full object-cover transition hover:scale-[1.03] hover:ring-2 hover:ring-zinc-300">
						<img alt="C" src="https://i.pravatar.cc/40?img=3" class="h-10 w-10 rounded-full object-cover transition hover:scale-[1.03] hover:ring-2 hover:ring-zinc-300">
					</div>
					<div class="mt-4 grid grid-cols-3 gap-4">
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
				<div class="rounded-2xl border border-zinc-200 bg-gradient-to-br from-blue-50 to-indigo-50 p-5 shadow-sm">
					<div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-blue-500 to-indigo-500">
						<svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
					</div>
					<h3 class="text-sm font-semibold text-zinc-900">Безопасность</h3>
					<p class="mt-1 text-sm text-zinc-600">Современные практики и простые обновления.</p>
				</div>
				<div class="rounded-2xl border border-zinc-200 bg-gradient-to-br from-emerald-50 to-teal-50 p-5 shadow-sm">
					<div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-emerald-500 to-teal-500">
						<svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
					</div>
					<h3 class="text-sm font-semibold text-zinc-900">Контент</h3>
					<p class="mt-1 text-sm text-zinc-600">Фокус на тексте, чистая типографика.</p>
				</div>
				<div class="rounded-2xl border border-zinc-200 bg-gradient-to-br from-amber-50 to-orange-50 p-5 shadow-sm">
					<div class="mb-3 flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-orange-500">
						<svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
					</div>
					<h3 class="text-sm font-semibold text-zinc-900">Скорость</h3>
					<p class="mt-1 text-sm text-zinc-600">Минимум JS, максимум скорости.</p>
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
				<blockquote class="rounded-xl border-l-4 border-amber-500 bg-amber-50 p-4 text-sm text-amber-900 shadow-sm">Важная примечание к тексту.</blockquote>
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
					<button class="rounded-full border border-zinc-200 bg-white px-3 py-1 text-xs text-zinc-700 hover:bg-zinc-50">Все</button>
					<button class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-medium text-sky-800">UI</button>
					<button class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-800">Backend</button>
					<button class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-medium text-amber-800">Guides</button>
					<button class="rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs text-rose-800 hover:bg-rose-50">Errors</button>
				</div>
				<div class="mt-4 flex flex-wrap gap-2">
					<span class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white pl-3 pr-1 py-1 text-xs text-zinc-700">Selected: UI<button type="button" class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-zinc-100 text-[12px] leading-none text-zinc-500 hover:bg-zinc-200">×</button></span>
					<span class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white pl-3 pr-1 py-1 text-xs text-zinc-700">Selected: Backend<button type="button" class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-zinc-100 text-[12px] leading-none text-zinc-500 hover:bg-zinc-200">×</button></span>
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
				<ol class="grid grid-cols-1 gap-3 sm:grid-cols-3">
					<li class="flex items-center gap-2">
						<span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-gradient-to-r from-blue-600 to-purple-600 text-xs font-medium text-white">1</span>
						<span class="text-sm text-zinc-900">Загрузка</span>
					</li>
					<li class="flex items-center gap-2">
						<span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-gradient-to-r from-emerald-600 to-green-600 text-xs font-medium text-white">2</span>
						<span class="text-sm text-zinc-900">Проверка</span>
					</li>
					<li class="flex items-center gap-2">
						<span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-zinc-300 text-xs font-medium text-zinc-700">3</span>
						<span class="text-sm text-zinc-900">Готово</span>
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
				<div class="animate-pulse space-y-3">
					<div class="h-4 w-1/3 rounded bg-zinc-200"></div>
					<div class="h-3 w-full rounded bg-zinc-200"></div>
					<div class="h-3 w-5/6 rounded bg-zinc-200"></div>
					<div class="h-3 w-2/3 rounded bg-zinc-200"></div>
				</div>
			</div>
		</section>
	</div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';


