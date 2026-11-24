<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}
?>
<nav class="breadcrumb mb-3" aria-label="Breadcrumbs">
    <ol class="flex-wrap">
        <li><a href="/"><?php render_html($acp_home_label); ?></a></li>
        <li class="separator" aria-hidden="true">/</li>
        <?php if (get_current_page() === 'index'): ?>
            <li><span class="current" aria-current="page">ACP</span></li>
        <?php else: ?>
            <li><a href="/!/acp">ACP</a></li>
            <li class="separator" aria-hidden="true">/</li>
            <li><span class="current" aria-current="page"><?php render_html($acp_breadcrumb); ?></span></li>
        <?php endif; ?>
    </ol>
</nav>
<h1 class="text-2xl font-semibold tracking-tight text-zinc-900"><?php render_html($acp_heading); ?></h1>
<p class="mt-2 text-sm text-zinc-600"><?php render_html($acp_intro); ?></p>
