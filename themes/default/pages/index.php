<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

$page_title = 'RooCMS';
$page_description = 'Welcome to RooCMS';
$page_scripts = [];

ob_start();
?>
<section>
    <h1>Welcome</h1>
    <p>This is the home page of the RooCMS theme</p>
    <!-- Alpine.js CSP Test -->
    <div x-data="{ count: 0, cspTest: 'CSP работает!' }">
        <button @click="count++" role="button" class="secondary">Counter: <strong x-text="count"></strong></button>
        <p x-text="cspTest" class="text-success"></p>
    </div>

    <div x-data="{ visible: false, text: '' }">
        <button @click="text='Saved in CSP mode'" class="primary">Show notification</button>
        <div class="toast" x-show="visible" x-transition>
            <mark x-text="text"></mark>
            <button class="close" @click="text=''" aria-label="Hide">×</button>
        </div>
        <span x-effect="visible = (text !== '')"></span>
    </div>

    <div x-data="{}">
        <button @click="console.log('Alpine OK')">Test Alpine</button>
    </div>
    <div>
        <a href="/login">Login</a>
        <a href="/logout">Logout</a>
        <a href="/register">Register</a>
    </div>

    <!-- Debug info -->
    <details>
        <summary class="text-success">Debug information</summary>
        <div x-data="{ alpineLoaded: false }" x-init="alpineLoaded = !!window.Alpine">
            <p><strong>Alpine.js загружен:</strong> <span x-text="alpineLoaded ? 'Yes' : 'No'"></span></p>
            <p><strong>Версия Alpine:</strong> <span x-text="(window.Alpine && window.Alpine.version) ? window.Alpine.version : 'Unknown version'"></span></p>
        </div>
    </details>
<?php $page_content = ob_get_clean();
require __DIR__ . '/../layouts/base.php';


