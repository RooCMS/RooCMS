<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

$page_title = 'Пользователи — RooCMS';
$page_scripts = ['/themes/default/assets/js/pages/users_index.js'];

ob_start();
?>
<section x-data="{ items: [], loading: true, error: '', page:1, limit:10 }">
    <h1>Пользователи</h1>
    <div x-show="loading">Загрузка...</div>
    <div x-show="error"><mark x-text="error"></mark></div>
    <table role="grid" x-show="!loading && !error && items.length">
        <thead>
            <tr><th>ID</th><th>Логин</th><th>Email</th><th>Роль</th></tr>
        </thead>
        <tbody>
            <template x-for="u in items" :key="u.id">
                <tr>
                    <td x-text="u.id"></td>
                    <td x-text="u.login"></td>
                    <td x-text="u.email"></td>
                    <td x-text="u.role"></td>
                </tr>
            </template>
        </tbody>
    </table>
    <nav>
        <button @click="page = Math.max(1, page-1); $dispatch('users:fetch')">Назад</button>
        <span x-text="page"></span>
        <button @click="page = page+1; $dispatch('users:fetch')">Вперёд</button>
    </nav>
</section>
<?php $page_content = ob_get_clean();
require __DIR__ . '/../../layouts/base.php';


