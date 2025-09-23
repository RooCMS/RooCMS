<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }
/**
 * RooCMS Theme Layout
 */

$page_title = isset($page_title) && $page_title !== '' ? (string)$page_title : 'RooCMS';
$page_description = isset($page_description) && $page_description !== '' ? (string)$page_description : 'RooCMS website';
$page_keywords = isset($page_keywords) && $page_keywords !== '' ? (string)$page_keywords : '';

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
    <?php if($page_keywords): ?>
        <meta name="keywords" content="<?php render_html($page_keywords); ?>">
    <?php endif; ?>

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php render_html($page_title); ?>">
    <meta property="og:description" content="<?php render_html($page_description); ?>">
    <meta property="og:site_name" content="RooCMS">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="<?php render_html($page_title); ?>">
    <meta property="twitter:description" content="<?php render_html($page_description); ?>">

    <!-- <link rel="stylesheet" href="<?php render_html($theme_base); ?>/assets/css/normalize.min.css"> -->
    <link rel="stylesheet" href="<?php render_html($theme_base); ?>/assets/css/roocms.min.css">

    <!-- Page-level modules should be loaded via $page_scripts when needed -->
    <script defer type="module" src="<?php render_html($theme_base); ?>/assets/js/app/main.js" nonce="<?php render_html($csp_nonce); ?>"></script>
    <?php foreach($page_scripts as $script_path): ?>
        <?php $resolved = (strpos($script_path, '/') === 0) ? $script_path : ($theme_base.'/'.ltrim($script_path, '/')); ?>
        <script defer type="module" src="<?php render_html($resolved); ?>" nonce="<?php render_html($csp_nonce); ?>"></script>
    <?php endforeach; ?>

    <script defer src="<?php render_html($theme_base); ?>/assets/js/alpine.csp.min.js" nonce="<?php render_html($csp_nonce); ?>"></script>

</head>
<body class="font-sans bg-gradient-to-r from-amber-100 to-sky-50 grid grid-rows-[auto_1fr_auto] min-h-screen" x-data="{}">

    <?php require __DIR__ . '/../partials/header.php'; ?>

    <main class="container mx-auto">
        <?php isset($page_content) ? render_html($page_content) : render_html(''); ?>
    </main>

    <div class="fixed inset-0 flex w-screen justify-center overflow-y-auto bg-zinc-950/25 px-2 py-2 transition duration-100 focus:outline-0 data-closed:opacity-0 data-enter:ease-out data-leave:ease-in sm:px-6 sm:py-8 lg:px-8 lg:py-16 dark:bg-zinc-950/50 opacity-0 modal-hidden" id="modal-backdrop" :class="{ 'opacity-100': $modal.isOpen, 'opacity-0': !$modal.isOpen, 'modal-hidden': !$modal.isOpen }" x-data="modalStore" x-show="$modal.isOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
    <div class="fixed inset-0 w-screen overflow-y-auto pt-6 sm:pt-0 modal-hidden" id="modal" x-data="modalStore" x-show="$modal.isOpen" :class="{ 'modal-hidden': !$modal.isOpen }" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
        <div class="grid min-h-full grid-rows-[1fr_auto] justify-items-center sm:grid-rows-[1fr_auto_3fr] sm:p-4" @click="cancel()">
            <div class="sm:max-w-lg row-start-2 w-full min-w-0 relative" @click.stop>
                
                <div class="absolute inset-0 bg-gradient-to-r from-red-600/5 to-orange-600/5 rounded-2xl" x-bind:class="{
                    'from-red-600/5 to-orange-600/5': $modal.type === 'alert',
                    'from-orange-600/5 to-yellow-600/5': $modal.type === 'warning',
                    'from-blue-600/5 to-indigo-600/5': $modal.type === 'notice'
                }"></div>

                <div class="relative bg-gradient-to-br from-white to-gray-50 rounded-2xl p-8 border border-gray-200/50 shadow-xl backdrop-blur-sm transition-all duration-300 transform scale-100" x-bind:class="{
                    'from-red-50 to-white': $modal.type === 'alert',
                    'from-orange-50 to-white': $modal.type === 'warning',
                    'from-blue-50 to-white': $modal.type === 'notice'
                }" data-headlessui-state="open" data-open="">
                   <div class="text-center">
                       <div class="flex justify-center">
                           <div class="flex h-16 w-16 items-center justify-center">
                               <div id="modal-icon" class="flex items-center justify-center w-full h-full">
                               </div>
                           </div>
                       </div>

                       <h2 class="text-2xl font-bold text-balance text-gray-900 mb-4" x-text="$modal.title"></h2>
                       <p class="text-pretty text-base text-gray-600 leading-relaxed max-w-md mx-auto" x-text="$modal.message"></p>
                   </div>

                   <div class="mt-8 flex flex-col-reverse items-center justify-center gap-4 sm:flex-row" x-bind:class="{ 'justify-center': !showCancelButton, 'justify-end': showCancelButton }">
                       <button x-show="showCancelButton" @click="cancel()" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-gray-500 to-gray-600 px-4 py-2 text-sm font-medium text-white transition-all duration-300 hover:from-gray-600 hover:to-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 shadow-md hover:shadow-lg cursor-pointer" type="button">
                           <span class="flex items-center gap-2">
                               <span class="leading-none">✕</span>
                               <span x-text="$modal.cancel_text"></span>
                           </span>
                       </button>
                       <button @click="confirm()" class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-red-500 to-red-600 px-4 py-2 text-sm font-medium text-white transition-all duration-300 hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 shadow-md hover:shadow-lg cursor-pointer min-w-[140px]" x-bind:class="{
                           'bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 focus:ring-red-500': $modal.type === 'alert',
                           'bg-gradient-to-r from-yellow-500 to-amber-500 hover:from-yellow-600 hover:to-amber-600 focus:ring-yellow-500': $modal.type === 'warning',
                           'bg-gradient-to-r from-sky-500 to-blue-500 hover:from-sky-600 hover:to-blue-600 focus:ring-sky-500': $modal.type === 'notice'
                       }" type="button">
                           <span class="flex items-center gap-2">
                               <span x-text="$modal.confirm_text"></span>
                               <span class="text-lg leading-none" style="font-size: 16px; line-height: 1;">✓</span>
                           </span>
                       </button>
                   </div>
            </div>
        </div>
    </div>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
    
</body>
</html>