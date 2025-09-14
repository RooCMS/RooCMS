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
    <div x-data="{ count: 0 }">
        <button @click="count++" role="button">Счётчик: <strong x-text="count"></strong></button>
    </div>
    <div x-data="{ visible: false, text: '' }">
        <button @click="text='Сохранено'">Показать уведомление</button>
        <div class="toast" x-show="visible" x-transition>
            <mark x-text="text"></mark>
            <button class="close" @click="text=''" aria-label="Скрыть">×</button>
        </div>
        <span x-effect="visible = (text !== '')"></span>
    </div>
    <hr>
    <p><a href="/auth/login">Войти</a> | <a href="/users">Пользователи</a></p>
<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';


