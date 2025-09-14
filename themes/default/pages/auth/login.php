<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

$page_title = 'Вход — RooCMS';
$page_scripts = ['/themes/default/assets/js/pages/auth_login.js'];

ob_start();
?>
<article>
    <h1>Вход</h1>
    <form x-data="loginForm()" @submit.prevent="onSubmit()">
        <label>
            Логин
            <input type="text" x-model="login" name="login" placeholder="Ваш логин" required>
        </label>
        <label>
            Пароль
            <input type="password" x-model="password" name="password" placeholder="••••••" required>
        </label>
        <div>
            <button type="submit">Войти</button>
            <span x-show="loading">Загрузка...</span>
        </div>
        <p x-show="error" role="alert"><mark x-text="error"></mark></p>
    </form>
</article>
<?php $page_content = ob_get_clean();
require __DIR__ . '/../../layouts/base.php';


