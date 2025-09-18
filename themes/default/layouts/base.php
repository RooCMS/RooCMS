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
    <link rel="stylesheet" href="<?php render_html($theme_base); ?>/assets/css/pico.css">
    <link rel="stylesheet" href="<?php render_html($theme_base); ?>/assets/css/roocms.css">

    <!-- <script src="<?php render_html($theme_base); ?>/assets/js/app/alpine-defer.js"></script> -->
    <script defer src="<?php render_html($theme_base); ?>/assets/js/alpine.csp.min.js"></script>

    <script src="<?php render_html($theme_base); ?>/assets/js/app/alpine-start.js"></script> <!-- Alpine start -->

    <script type="module" src="<?php render_html($theme_base); ?>/assets/js/app/main.js"></script>
    <script type="module" src="<?php render_html($theme_base); ?>/assets/js/app/config.js"></script>
    <script type="module" src="<?php render_html($theme_base); ?>/assets/js/app/api.js"></script>
    <script type="module" src="<?php render_html($theme_base); ?>/assets/js/app/auth.js"></script>
    <?php foreach($page_scripts as $script_path): ?>
        <?php $resolved = (strpos($script_path, '/') === 0) ? $script_path : ($theme_base.'/'.ltrim($script_path, '/')); ?>
        <script type="module" src="<?php render_html($resolved); ?>"></script>
    <?php endforeach; ?>
    
</head>
<body>

    <?php require __DIR__ . '/../partials/header.php'; ?>

    <main class="container">
        <?php isset($page_content) ? render_html($page_content) : render_html(''); ?>
    </main>

    <footer>
        <?php require __DIR__ . '/../partials/footer.php'; ?>
    </footer>
</body>
</html>