<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }
/**
 * RooCMS Theme Layout
 */

$page_title = isset($page_title) && $page_title !== '' ? (string)$page_title : 'RooCMS';
$page_description = isset($page_description) && $page_description !== '' ? (string)$page_description : 'RooCMS website';

// Optional: array of page-specific module scripts
$page_scripts = isset($page_scripts) && is_array($page_scripts) ? $page_scripts : [];
// Compute web base for current theme to avoid hardcoded paths
$theme_name = basename(dirname(__DIR__));
$theme_base = '/themes/'.$theme_name;
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php render_html($page_title); ?></title>
    <meta name="description" content="<?php render_html($page_description); ?>">

    <link rel="stylesheet" href="<?php render_html($theme_base); ?>/assets/css/normalize.min.css">
    <link rel="stylesheet" href="<?php render_html($theme_base); ?>/assets/css/roocms.min.css">

    <!-- Page-level modules should be loaded via $page_scripts when needed -->
    <script defer type="module" src="<?php render_html($theme_base); ?>/assets/js/app/main.js" nonce="<?php render_html(CSPNONCE); ?>"></script>
    <?php foreach($page_scripts as $script_path): ?>
        <?php $resolved = (strpos($script_path, '/') === 0) ? $script_path : ($theme_base.'/'.ltrim($script_path, '/')); ?>
        <script defer type="module" src="<?php render_html($resolved); ?>" nonce="<?php render_html(CSPNONCE); ?>"></script>
    <?php endforeach; ?>

    <script defer src="<?php render_html($theme_base); ?>/assets/js/alpine.csp.min.js" nonce="<?php render_html(CSPNONCE); ?>"></script>
    
</head>
<body class="font-sans bg-gradient-to-r from-amber-100 to-sky-50 grid grid-rows-[auto_1fr_auto] min-h-screen" x-data="{}">

    <?php require __DIR__ . '/../partials/header.php'; ?>

    <main class="container mx-auto">
        <?php isset($page_content) ? render_html($page_content) : render_html(''); ?>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
    
</body>
</html>