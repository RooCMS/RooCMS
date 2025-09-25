<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

$page_title = 'Settings — RooCMS';
$page_description = 'System Settings for RooCMS';

$theme_name = basename(dirname(dirname(__DIR__)));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [
	$theme_base.'/assets/js/app/acp.js',
    $theme_base.'/assets/js/app/acp-settings.js',
    $theme_base.'/assets/js/app/acp-access.js'
];

ob_start();
?>

<div class="py-10">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-[260px_1fr] px-4 sm:px-6 lg:px-8 space-y-8">
        <?php require __DIR__ . '/../../layouts/acp-nav.php'; ?>

        <section>
            <header class="mb-8">
                <nav class="mb-3 text-sm text-zinc-500" aria-label="Хлебные крошки">
                    <ol class="flex items-center gap-2">
                        <li><a href="/" class="hover:text-zinc-700">Главная</a></li>
                        <li aria-hidden="true" class="text-zinc-400">/</li>
                        <li><a href="/acp" class="hover:text-zinc-700">ACP</a></li>
                        <li aria-hidden="true" class="text-zinc-400">/</li>
                        <li><span class="text-zinc-700">Настройки</span></li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-semibold tracking-tight text-zinc-900">Настройки системы</h1>
                <p class="mt-2 text-sm text-zinc-600">Управление конфигурацией RooCMS</p>
            </header>

            <div class="space-y-10">
                <!-- General Settings Section -->
                <section>
                    <h2 class="mb-4 text-base font-semibold text-zinc-900">Основные настройки</h2>
                    <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
                        <form class="space-y-4">
                            <!-- Site Name (string) -->
                            <div>
                                <label for="site_name" class="mb-1 block text-sm font-medium text-zinc-800">Название сайта</label>
                                <input type="text" id="site_name" name="site_name" value="RooCMS" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-zinc-500 focus:outline-none">
                                <p class="mt-1 text-xs text-zinc-500">Название сайта, отображаемое в заголовке и метатегах</p>
                            </div>

                            <!-- Site Description (text) -->
                            <div>
                                <label for="site_description" class="mb-1 block text-sm font-medium text-zinc-800">Описание сайта</label>
                                <textarea id="site_description" name="site_description" rows="3" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-zinc-500 focus:outline-none">Современная система управления контентом</textarea>
                                <p class="mt-1 text-xs text-zinc-500">Краткое описание сайта для поисковиков</p>
                            </div>

                            <!-- Maintenance Mode (boolean) -->
                            <div>
                                <label class="flex cursor-pointer items-center justify-between gap-4">
                                    <span class="text-sm text-zinc-800">Режим обслуживания</span>
                                    <input type="checkbox" id="maintenance_mode" name="maintenance_mode" class="peer sr-only">
                                    <span class="relative inline-block h-6 w-11 rounded-full bg-zinc-300 transition peer-checked:bg-zinc-900 after:absolute after:left-0.5 after:top-1/2 after:-translate-y-1/2 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
                                </label>
                                <p class="mt-1 text-xs text-zinc-500">Включить режим обслуживания сайта</p>
                            </div>

                            <!-- Default Language (select) -->
                            <div>
                                <label for="default_language" class="mb-1 block text-sm font-medium text-zinc-800">Язык по умолчанию</label>
                                <select id="default_language" name="default_language" class="select-custom block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-zinc-500 focus:outline-none">
                                    <option value="ru">Русский</option>
                                    <option value="en">English</option>
                                    <option value="es">Español</option>
                                    <option value="fr">Français</option>
                                    <option value="de">Deutsch</option>
                                </select>
                                <p class="mt-1 text-xs text-zinc-500">Основной язык интерфейса</p>
                            </div>

                            <!-- Timezone (select) -->
                            <div>
                                <label for="timezone" class="mb-1 block text-sm font-medium text-zinc-800">Часовой пояс</label>
                                <select id="timezone" name="timezone" class="select-custom block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-zinc-500 focus:outline-none">
                                    <option value="Europe/Moscow">Europe/Moscow (+03:00)</option>
                                    <option value="Europe/London">Europe/London (+00:00)</option>
                                    <option value="America/New_York">America/New_York (-05:00)</option>
                                    <option value="Asia/Tokyo">Asia/Tokyo (+09:00)</option>
                                    <option value="UTC">UTC (+00:00)</option>
                                </select>
                                <p class="mt-1 text-xs text-zinc-500">Часовой пояс сервера</p>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- Security Settings Section -->
                <section>
                    <h2 class="mb-4 text-base font-semibold text-zinc-900">Безопасность</h2>
                    <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
                        <form class="space-y-4">
                            <!-- Session Timeout (integer) -->
                            <div>
                                <label for="session_timeout" class="mb-1 block text-sm font-medium text-zinc-800">Время сессии (минуты)</label>
                                <input type="number" id="session_timeout" name="session_timeout" value="60" min="5" max="1440" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-zinc-500 focus:outline-none">
                                <p class="mt-1 text-xs text-zinc-500">Время бездействия до автоматического выхода</p>
                            </div>

                            <!-- Max Login Attempts (integer) -->
                            <div>
                                <label for="max_login_attempts" class="mb-1 block text-sm font-medium text-zinc-800">Максимум попыток входа</label>
                                <input type="number" id="max_login_attempts" name="max_login_attempts" value="5" min="1" max="20" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-zinc-500 focus:outline-none">
                                <p class="mt-1 text-xs text-zinc-500">Количество неудачных попыток входа до блокировки</p>
                            </div>

                            <!-- Enable 2FA (boolean) -->
                            <div>
                                <label class="flex cursor-pointer items-center justify-between gap-4">
                                    <span class="text-sm text-zinc-800">Включить двухфакторную аутентификацию</span>
                                    <input type="checkbox" id="enable_2fa" name="enable_2fa" class="peer sr-only">
                                    <span class="relative inline-block h-6 w-11 rounded-full bg-zinc-300 transition peer-checked:bg-zinc-900 after:absolute after:left-0.5 after:top-1/2 after:-translate-y-1/2 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
                                </label>
                                <p class="mt-1 text-xs text-zinc-500">Требовать 2FA для всех пользователей</p>
                            </div>

                            <!-- Password Policy (select) -->
                            <div>
                                <label for="password_policy" class="mb-1 block text-sm font-medium text-zinc-800">Политика паролей</label>
                                <select id="password_policy" name="password_policy" class="select-custom block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-zinc-500 focus:outline-none">
                                    <option value="basic">Базовая (минимум 6 символов)</option>
                                    <option value="medium">Средняя (8+ символов, цифры, буквы)</option>
                                    <option value="strong">Строгая (12+ символов, спецсимволы)</option>
                                </select>
                                <p class="mt-1 text-xs text-zinc-500">Требования к сложности паролей</p>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- Appearance Settings Section -->
                <section>
                    <h2 class="mb-4 text-base font-semibold text-zinc-900">Внешний вид</h2>
                    <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
                        <form class="space-y-4">
                            <!-- Theme (select) -->
                            <div>
                                <label for="theme" class="mb-1 block text-sm font-medium text-zinc-800">Тема оформления</label>
                                <select id="theme" name="theme" class="select-custom block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-zinc-500 focus:outline-none">
                                    <option value="default">Default (светлая)</option>
                                    <option value="dark">Dark (темная)</option>
                                    <option value="blue">Blue</option>
                                    <option value="green">Green</option>
                                    <option value="purple">Purple</option>
                                </select>
                                <p class="mt-1 text-xs text-zinc-500">Цветовая схема интерфейса</p>
                            </div>

                            <!-- Primary Color (color) -->
                            <div>
                                <label for="primary_color" class="mb-1 block text-sm font-medium text-zinc-800">Основной цвет</label>
                                <div class="mt-1 flex items-center space-x-3">
                                    <input type="color" id="primary_color" name="primary_color" value="#3b82f6" class="h-10 w-16 rounded border border-zinc-300">
                                    <input type="text" value="#3b82f6" class="block w-24 rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-zinc-500 focus:outline-none" readonly>
                                </div>
                                <p class="mt-1 text-xs text-zinc-500">HEX код основного цвета сайта</p>
                            </div>

                            <!-- Logo Upload (image) -->
                            <div>
                                <label for="logo_upload" class="mb-1 block text-sm font-medium text-zinc-800">Логотип сайта</label>
                                <div class="mt-1 flex items-center">
                                    <input type="file" id="logo_upload" name="logo_upload" accept="image/*" class="sr-only">
                                    <label for="logo_upload" class="cursor-pointer inline-flex items-center justify-center rounded-lg border border-zinc-300 bg-white px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50">
                                        <svg class="w-5 h-5 mr-2 align-middle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Загрузить логотип
                                    </label>
                                    <div id="logo_preview" class="ml-4 hidden">
                                        <img src="" alt="Logo preview" class="h-10 w-auto object-contain">
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-zinc-500">Изображение логотипа в формате PNG, JPG, SVG (максимум 2MB)</p>
                            </div>

                            <!-- Favicon Upload (image) -->
                            <div>
                                <label for="favicon_upload" class="mb-1 block text-sm font-medium text-zinc-800">Favicon</label>
                                <div class="mt-1 flex items-center">
                                    <input type="file" id="favicon_upload" name="favicon_upload" accept="image/*" class="sr-only">
                                    <label for="favicon_upload" class="cursor-pointer inline-flex items-center justify-center rounded-lg border border-zinc-300 bg-white px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50">
                                        <svg class="w-5 h-5 mr-2 align-middle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Загрузить favicon
                                    </label>
                                </div>
                                <p class="mt-1 text-xs text-zinc-500">Иконка сайта в формате ICO, PNG (16x16, 32x32)</p>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- System Settings Section -->
                <section>
                    <h2 class="mb-4 text-base font-semibold text-zinc-900">Система</h2>
                    <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
                        <form class="space-y-4">
                            <!-- Debug Mode (boolean) -->
                            <div>
                                <label class="flex cursor-pointer items-center justify-between gap-4">
                                    <span class="text-sm text-zinc-800">Режим отладки</span>
                                    <input type="checkbox" id="debug_mode" name="debug_mode" class="peer sr-only">
                                    <span class="relative inline-block h-6 w-11 rounded-full bg-zinc-300 transition peer-checked:bg-zinc-900 after:absolute after:left-0.5 after:top-1/2 after:-translate-y-1/2 after:h-5 after:w-5 after:rounded-full after:bg-white after:transition peer-checked:after:translate-x-5"></span>
                                </label>
                                <p class="mt-1 text-xs text-zinc-500">Показывать подробную информацию об ошибках</p>
                            </div>

                            <!-- Cache TTL (integer) -->
                            <div>
                                <label for="cache_ttl" class="mb-1 block text-sm font-medium text-zinc-800">Время жизни кэша (секунды)</label>
                                <input type="number" id="cache_ttl" name="cache_ttl" value="3600" min="60" max="86400" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-zinc-500 focus:outline-none">
                                <p class="mt-1 text-xs text-zinc-500">Время хранения кэшированных данных</p>
                            </div>

                            <!-- Max Upload Size (integer) -->
                            <div>
                                <label for="max_upload_size" class="mb-1 block text-sm font-medium text-zinc-800">Максимальный размер загрузки (MB)</label>
                                <input type="number" id="max_upload_size" name="max_upload_size" value="10" min="1" max="100" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-zinc-500 focus:outline-none">
                                <p class="mt-1 text-xs text-zinc-500">Максимальный размер файлов для загрузки</p>
                            </div>

                            <!-- Backup Frequency (select) -->
                            <div>
                                <label for="backup_frequency" class="mb-1 block text-sm font-medium text-zinc-800">Частота резервного копирования</label>
                                <select id="backup_frequency" name="backup_frequency" class="select-custom block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 focus:border-zinc-500 focus:outline-none">
                                    <option value="daily">Ежедневно</option>
                                    <option value="weekly">Еженедельно</option>
                                    <option value="monthly">Ежемесячно</option>
                                    <option value="manual">Вручную</option>
                                </select>
                                <p class="mt-1 text-xs text-zinc-500">Автоматическое создание резервных копий</p>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- Email Settings Section -->
                <section>
                    <h2 class="mb-4 text-base font-semibold text-zinc-900">Электронная почта</h2>
                    <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
                        <form class="space-y-4">
                            <!-- SMTP Host (string) -->
                            <div>
                                <label for="smtp_host" class="mb-1 block text-sm font-medium text-zinc-800">SMTP сервер</label>
                                <input type="text" id="smtp_host" name="smtp_host" value="smtp.gmail.com" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-zinc-500 focus:outline-none">
                                <p class="mt-1 text-xs text-zinc-500">Адрес SMTP сервера для отправки почты</p>
                            </div>

                            <!-- SMTP Port (integer) -->
                            <div>
                                <label for="smtp_port" class="mb-1 block text-sm font-medium text-zinc-800">SMTP порт</label>
                                <input type="number" id="smtp_port" name="smtp_port" value="587" min="1" max="65535" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-zinc-500 focus:outline-none">
                                <p class="mt-1 text-xs text-zinc-500">Порт SMTP сервера (обычно 587 для TLS)</p>
                            </div>

                            <!-- SMTP Username (string) -->
                            <div>
                                <label for="smtp_username" class="mb-1 block text-sm font-medium text-zinc-800">SMTP логин</label>
                                <input type="text" id="smtp_username" name="smtp_username" value="" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-zinc-500 focus:outline-none">
                                <p class="mt-1 text-xs text-zinc-500">Имя пользователя для SMTP аутентификации</p>
                            </div>

                            <!-- SMTP Password (string) -->
                            <div>
                                <label for="smtp_password" class="mb-1 block text-sm font-medium text-zinc-800">SMTP пароль</label>
                                <input type="password" id="smtp_password" name="smtp_password" value="" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-zinc-500 focus:outline-none">
                                <p class="mt-1 text-xs text-zinc-500">Пароль для SMTP аутентификации</p>
                            </div>

                            <!-- Email From (email) -->
                            <div>
                                <label for="email_from" class="mb-1 block text-sm font-medium text-zinc-800">Email отправителя</label>
                                <input type="email" id="email_from" name="email_from" value="noreply@roocms.com" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-zinc-500 focus:outline-none">
                                <p class="mt-1 text-xs text-zinc-500">Email адрес, от которого будут отправляться письма</p>
                            </div>

                            <!-- Email From Name (string) -->
                            <div>
                                <label for="email_from_name" class="mb-1 block text-sm font-medium text-zinc-800">Имя отправителя</label>
                                <input type="text" id="email_from_name" name="email_from_name" value="RooCMS" class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 focus:border-zinc-500 focus:outline-none">
                                <p class="mt-1 text-xs text-zinc-500">Имя, отображаемое в поле "От кого"</p>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- Actions Section -->
                <section>
                    <div class="rounded-xl border border-zinc-200/80 bg-white/80 p-5 shadow-sm backdrop-blur">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-zinc-600">Сохранить все настройки или сбросить к значениям по умолчанию</p>
                            <div class="flex gap-2">
                                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-zinc-300 bg-white px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50">
                                    Сбросить
                                </button>
                                <button type="button" class="inline-flex items-center justify-center rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800">
                                    <svg class="w-4 h-4 mr-2 align-middle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Сохранить настройки
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </div>
</div>

<?php $page_content = ob_get_clean();
require __DIR__ . '/../../layouts/base.php';
