<?php declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
if (!preg_match('~^/api/(README\.md|v1/docs/[a-z-]+\.md)$~i', $path, $matches)) {
    http_response_code(404);
    exit('Документ не найден');
}

$document = strcasecmp($matches[1], 'README.md') === 0 ? 'README.md' : $matches[1];
$file = __DIR__.'/'.$document;
if (!is_file($file)) {
    http_response_code(404);
    exit('Документ не найден');
}

$markdown = file_get_contents($file);
header('Cache-Control: no-cache');
if (isset($_GET['raw'])) {
    header('Content-Type: text/plain; charset=utf-8');
    echo $markdown;
    exit;
}

$overview = $document === 'README.md' ? $markdown : file_get_contents(__DIR__.'/README.md');
preg_match_all('~\[([^\]]+)\]\((v1/docs/[a-z-]+\.md)\)~', $overview, $sections, PREG_SET_ORDER);
$navigation = ['README.md' => 'Обзор'] + array_column($sections, 1, 2);
preg_match('/^# (.+)$/m', $markdown, $heading);
$title = trim($heading[1] ?? 'RooCMS API');

require dirname(__DIR__).'/roocms/vendor/parsedown/Parsedown.php';
$content = (new Parsedown())->setSafeMode(true)->text($markdown);
$escape = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

header('Content-Type: text/html; charset=utf-8');
header("Content-Security-Policy: default-src 'none'; style-src 'self' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; base-uri 'none'; frame-ancestors 'none'");

require dirname(__DIR__).'/themes/default/pages/api-docs.php';
