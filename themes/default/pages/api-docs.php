<?php declare(strict_types=1);
if (!isset($content, $navigation, $escape)) {
    http_response_code(404);
    exit;
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $escape($title) ?> — Документация RooCMS</title>
    <link rel="stylesheet" href="/themes/default/assets/css/roocms.min.css?v=<?= filemtime(__DIR__.'/../assets/css/roocms.min.css') ?>">
</head>
<body class="font-pt-sans min-h-screen bg-gradient-to-r from-orange-50 via-teal-50 to-sky-50 text-zinc-900">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:py-10">
        <header class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <a href="/api/README.md" class="text-xl font-bold tracking-tight">RooCMS <span class="text-zinc-400">/ API</span></a>
            <div class="flex items-center gap-3">
                <a href="?raw=1" class="btn secondary">Markdown</a>
                <a href="/" class="btn secondary">На сайт</a>
            </div>
        </header>
        <div class="grid items-start gap-6 lg:grid-cols-[14rem_minmax(0,1fr)]">
            <aside class="content-card p-3 lg:sticky lg:top-6">
                <p class="px-3 py-2 text-xs font-medium uppercase tracking-wider text-zinc-400">Документация</p>
                <nav aria-label="Разделы документации" class="space-y-1 text-sm">
                    <?php foreach ($navigation as $link => $label): $active = $document === $link; ?>
                        <a href="/api/<?= $escape($link) ?>" class="sidebar-nav-link<?= $active ? ' active' : '' ?>"<?= $active ? ' aria-current="page"' : '' ?>><?= $escape($label) ?></a>
                    <?php endforeach; ?>
                </nav>
            </aside>
            <main class="content-card min-w-0 p-5 sm:p-8">
                <article class="markdown-content"><?= $content ?></article>
            </main>
        </div>
    </div>
</body>
</html>
