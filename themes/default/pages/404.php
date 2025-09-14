<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

http_response_code(404);
$page_title = 'Страница не найдена — 404';

ob_start();
?>
<section>
    <h1>404 — Страница не найдена</h1>
    <p>К сожалению, запрошенная страница не существует.</p>
    <p><a href="/">На главную</a></p>
</section>
<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';


