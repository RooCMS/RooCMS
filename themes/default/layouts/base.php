<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }
/**
 * RooCMS Theme Layout
 */

$page_title = isset($page_title) && $page_title !== '' ? (string)$page_title : 'RooCMS';
$page_description = isset($page_description) && $page_description !== '' ? (string)$page_description : 'RooCMS website';

// Optional: array of page-specific module scripts
$page_scripts = isset($page_scripts) && is_array($page_scripts) ? $page_scripts : [];
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($page_title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">

    <link rel="stylesheet" href="/themes/default/assets/css/normalize.min.css">
    <link rel="stylesheet" href="/themes/default/assets/css/pico.css">
    <link rel="stylesheet" href="/themes/default/assets/css/app.css">

    <script src="/themes/default/assets/js/app/alpine-defer.js"></script>
    <?php foreach($page_scripts as $script_path): ?>
        <script type="module" src="<?php echo htmlspecialchars($script_path, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>"></script>
    <?php endforeach; ?>
    <script defer src="/themes/default/assets/js/alpine.csp.min.js"></script>
    <script type="module" src="/themes/default/assets/js/app/config.js"></script>
    <script type="module" src="/themes/default/assets/js/app/api.js"></script>
    <script type="module" src="/themes/default/assets/js/app/auth.js"></script>
    <script type="module" src="/themes/default/assets/js/app/main.js"></script>
    <script src="/themes/default/assets/js/app/alpine-start.js"></script>
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


