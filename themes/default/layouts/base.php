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
    <title><?php echo htmlspecialchars($page_title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">

    <link rel="stylesheet" href="<?php echo $theme_base; ?>/assets/css/normalize.min.css">
    <link rel="stylesheet" href="<?php echo $theme_base; ?>/assets/css/pico.css">
    <link rel="stylesheet" href="<?php echo $theme_base; ?>/assets/css/app.css">

    <script src="<?php echo $theme_base; ?>/assets/js/app/alpine-defer.js"></script>
    <?php foreach($page_scripts as $script_path): ?>
        <?php $resolved = (strpos($script_path, '/') === 0) ? $script_path : ($theme_base.'/'.ltrim($script_path, '/')); ?>
        <script type="module" src="<?php echo htmlspecialchars($resolved, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>"></script>
    <?php endforeach; ?>
    <script defer src="<?php echo $theme_base; ?>/assets/js/alpine.csp.min.js"></script>
    <script type="module" src="<?php echo $theme_base; ?>/assets/js/app/config.js"></script>
    <script type="module" src="<?php echo $theme_base; ?>/assets/js/app/api.js"></script>
    <script type="module" src="<?php echo $theme_base; ?>/assets/js/app/auth.js"></script>
    <script type="module" src="<?php echo $theme_base; ?>/assets/js/app/main.js"></script>
    <script src="<?php echo $theme_base; ?>/assets/js/app/alpine-start.js"></script>
</head>
<body>

    <header>
        <?php require __DIR__ . '/../partials/header.php'; ?>
    </header>

    <main class="container">
        <?php echo isset($page_content) ? $page_content : ''; ?>
    </main>

    <footer>
        <?php require __DIR__ . '/../partials/footer.php'; ?>
    </footer>

</body>
</html>


