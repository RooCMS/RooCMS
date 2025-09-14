<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }
?>
<nav class="container" aria-label="Основная навигация">
    <ul>
        <li><strong><a href="/">RooCMS</a></strong></li>
    </ul>
    <ul>
        <li><a href="/" aria-current="page">Главная</a></li>
        <li><a href="/users">Пользователи</a></li>
        <li><a href="/auth/login">Войти</a></li>
    </ul>
</nav>


