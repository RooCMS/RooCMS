<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }
?>
<footer class="container mx-auto text-center h-12">
    <small class="border-t border-zinc-200 py-4">
        © <?php render_html(date('Y')); ?> RooCMS. All rights reserved.
    </small>
</footer>
