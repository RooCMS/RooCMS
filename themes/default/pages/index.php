<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

$page_title = 'Главная — RooCMS';
$page_description = 'Добро пожаловать в RooCMS';
$page_scripts = [];

ob_start();
?>
<section>
    <h1>Добро пожаловать</h1>
    <p>Это стартовая страница темы RooCMS на Alpine.js + Pico.css.</p>
    <!-- Alpine.js CSP Test -->
    <div x-data="{ count: 0, cspTest: 'CSP работает!' }">
        <button @click="count++" role="button" class="secondary">Счётчик: <strong x-text="count"></strong></button>
        <p x-text="cspTest" class="text-success"></p>
    </div>

    <div x-data="{ visible: false, text: '' }">
        <button @click="text='Сохранено в CSP режиме'" class="primary">Показать уведомление</button>
        <div class="toast" x-show="visible" x-transition>
            <mark x-text="text"></mark>
            <button class="close" @click="text=''" aria-label="Скрыть">×</button>
        </div>
        <span x-effect="visible = (text !== '')"></span>
    </div>

    <!-- Debug info -->
    <details>
        <summary class="text-success">Debug информация</summary>
        <div x-data="{ alpineLoaded: false }" x-init="alpineLoaded = !!window.Alpine">
            <p><strong>Alpine.js загружен:</strong> <span x-text="alpineLoaded ? 'Yes' : 'No'"></span></p>
            <p><strong>Версия Alpine:</strong> <span x-text="(window.Alpine && window.Alpine.version) ? window.Alpine.version : 'Unknown version'"></span></p>
        </div>
    </details>
<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';


