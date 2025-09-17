# Система шаблонизатора RooCMS

## Обзор

Новая система шаблонизатора RooCMS предоставляет гибкую и мощную архитектуру для работы с темами. 
Поддерживаются два типа шаблонов:

- **PHP шаблоны** - полная мощь PHP с поддержкой логики
- **HTML шаблоны** - простые шаблоны с плейсхолдерами и базовыми конструкциями

## Архитектура

### Основные компоненты

1. **Themes** — главный класс управления темами
2. **TemplateRenderer** — интерфейс для рендереров (`TemplateRendererPhp`, `TemplateRendererHtml`)
3. **ThemeConfig** — конфигурация тем (`get_theme_*`, `get_renderer_type()`)

### Структура директорий темы

```
themes/
├── theme_name/
│   ├── theme.json          # Конфигурация темы
│   ├── layouts/
│   │   └── base.php/html   # Базовый layout
│   ├── pages/              # Страницы
│   │   ├── index.php/html
│   │   └── page.php/html
│   ├── partials/           # Переиспользуемые части
│   │   ├── header.php/html
│   │   └── footer.php/html
│   └── assets/             # Статические файлы
```

## Использование

### Базовое использование

```php
// Фронт‑контроллер (index.php)
const RooCMS = true;
require __DIR__.'/roocms/init.php';

// Получаем Themes из DI
/** @var DependencyContainer $container */
$themes = $container->get(Themes::class);

// Активная тема из конфигурации
$active_theme = $site['theme'] ?? 'default_html';
$themes->set_theme($active_theme);

// Рендеринг текущего пути
$themes->render('/', [
    'page_title' => 'Главная страница',
    'user_name'  => 'Иван',
]);
```

### PHP шаблоны

#### Структура страницы (pages/index.php)

```php
<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); exit('403:Access denied'); }

// Доступны все переменные из $data и глобальные переменные
$page_title = isset($page_title) ? $page_title : 'Главная';
$page_scripts = ['pages/home.js'];

ob_start();
?>
<section>
    <h1>Добро пожаловать, <?php echo htmlspecialchars($user_name ?? 'Гость'); ?>!</h1>
    <p>Текущий год: <?php echo $current_year; ?></p>

    <?php if (!empty($articles)): ?>
        <?php foreach ($articles as $article): ?>
            <article>
                <h2><?php echo htmlspecialchars($article['title']); ?></h2>
                <p><?php echo htmlspecialchars($article['content']); ?></p>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
<?php
$page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
```

#### Layout (layouts/base.php)

```php
<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); exit('403:Access denied'); }

$page_title = isset($page_title) ? $page_title : 'RooCMS';
$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/' . $theme_name;
?>
<!doctype html>
<html lang="ru">
<head>
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link rel="stylesheet" href="<?php echo $theme_base; ?>/assets/css/app.css">
    <?php foreach($page_scripts ?? [] as $script): ?>
        <script type="module" src="<?php echo htmlspecialchars($script, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>"></script>
    <?php endforeach; ?>
</head>
<body>
    <header><?php require __DIR__ . '/../partials/header.php'; ?></header>
    <main><?php echo $page_content ?? ''; ?></main>
    <footer><?php require __DIR__ . '/../partials/footer.php'; ?></footer>
</body>
</html>
```

### HTML шаблоны

#### Структура страницы (pages/index.html)

```html
<!-- meta: {
    "title": "Главная страница",
    "description": "Добро пожаловать в RooCMS",
    "scripts": ["pages/home.js"],
    "vars": {
        "welcome_message": "Привет, мир!",
        "show_sidebar": true
    }
} -->

<section>
    <h1>{{title}}</h1>
    <p>{{welcome_message}}</p>

    <!-- if: show_sidebar -->
    <aside>
        <h3>Боковая панель</h3>
        <nav>
            <a href="/">Главная</a>
            <a href="/about">О нас</a>
        </nav>
    </aside>
    <!-- endif -->

    <!-- include: partials/news.html -->

    <div x-data="{ count: 0 }">
        <button @click="count++">Счётчик: {{count}}</button>
    </div>
</section>
```

#### Переменные в HTML шаблонах

```html
<!-- Экранированный вывод -->
<h1>{{page_title}}</h1>

<!-- Неэкранированный вывод (для HTML контента) -->
<div>{{{raw_html_content}}}</div>

<!-- Ассеты -->
<link rel="stylesheet" href="{{asset: css/app.css}}">
<script type="module" src="{{asset: js/app.js}}"></script>

<!-- Глобальные переменные -->
<p>Текущий год: {{current_year}}</p>
<p>Базовый URL: {{base_url}}</p>
```

#### Условные блоки

```html
<!-- if: user_logged_in -->
<div class="user-menu">
    <span>Добро пожаловать, {{user_name}}!</span>
    <a href="/logout">Выйти</a>
</div>
<!-- endif -->

<!-- if: has_items -->
<ul>
    <!-- foreach: items as item -->
    <li>{{item}}</li>
    <!-- endforeach -->
</ul>
<!-- endif -->
```

#### Инклуды

```html
<!-- include: partials/header.html -->
<!-- include: partials/sidebar.html -->
<!-- include: partials/footer.html -->
```

### Конфигурация темы (theme.json)

```json
{
    "name": "My Custom Theme",
    "version": "1.0.0",
    "description": "Описание темы",
    "type": "html",
    "author": "Автор",
    "assets": {
        "css": ["normalize.css", "style.css"],
        "js": ["app.js"]
    },
    "features": {
        "responsive": true,
        "dark_mode": true
    }
}
```

## API

### Themes

```php

// установка темы  
$themes->set_theme(string $theme_name): bool; 
$themes->render(string $path, array $data = []): bool; // рендеринг страницы
// рендеринг с указанной темой
$themes->render_with_theme(string $theme_name, string $path, array $data = []): bool; 
// проверка существования шаблона
$themes->template_exists(string $path, ?string $theme_name = null): bool; 
// получение списка доступных тем
$themes->get_available_themes(): array; 
// установка глобальной переменной
$themes->set_global_var(string $key, mixed $value): void;
// получение глобальной переменной
$themes->get_global_var(string $key, mixed $default = null): mixed;
```

## Производительность

### Оптимизации

- Ленивая загрузка конфигураций
- Минимум файловых операций
- Быстрая обработка переменных в шаблонах


### Чистый API

```php
// DI
$themes = $container->get(Themes::class);
$themes->set_theme('default_html');
$themes->render('/', ['page_title' => 'Главная']);
$themes->render_with_theme('default', '/users', ['filter' => 'active']);
```

### Современный подход

- Чистый, последовательный API без legacy методов
- Только JSON формат метаданных в HTML шаблонах
- Упрощенная логика без излишних проверок безопасности
- Оптимизированная производительность

## Расширение системы

### Создание кастомного рендерера

```php
class CustomRenderer implements TemplateRenderer {
    public function render(string $theme_base, string $path, array $data = []): bool { /* ... */ }
    public function template_exists(string $theme_base, string $path): bool { /* ... */ }
    public function get_supported_extensions(): array { return ['custom']; }
    public function get_type(): string { return 'custom'; }
}

// Подключение через Themes сейчас статически не регистрируется.
// Добавление новых рендереров — через код ядра (DI/инициализация Themes).
```

## Лучшие практики

1. **Используйте PHP шаблоны** для сложной логики
2. **Используйте HTML шаблоны** для простых страниц
3. **Кэшируйте** часто используемые шаблоны
4. **Валидируйте** входные данные в шаблонах
5. **Организуйте** код с помощью partials
6. **Документируйте** переменные в theme.json

## Отладка

### Включение отладки

```php
// Включение подробного логирования
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Проверка существования шаблона
if (!$themes->template_exists('/page')) {
    error_log("Template /page not found");
}
```
