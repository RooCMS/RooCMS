<?php declare(strict_types=1);
if(!defined('RooCMS')) { http_response_code(403); header('Content-Type: text/plain; charset=utf-8'); exit('403:Access denied'); }

/**
 * Current page for highlighting active link
 */
$current_page = $current_page ?? basename(env('REQUEST_URI') ?? '', '.php');
$current_page = str_replace('/acp/', '', $current_page);
$current_page = $current_page === 'acp' || $current_page === '' ? 'index' : $current_page;

/**
 * Get attributes and classes for the menu link
 */
function get_nav_link_attrs(string $page_key): array {
    global $current_page;

    $is_active = $current_page === $page_key;

    return [
        'aria_current' => $is_active ? 'aria-current="page"' : '',
        'classes' => $is_active
            ? 'border border-zinc-200 bg-white/80 backdrop-blur px-3 py-2 text-sm font-medium text-zinc-900 shadow-sm hover:bg-white'
            : 'px-3 py-2 text-sm font-medium text-zinc-700 border border-transparent hover:text-zinc-900 hover:bg-white hover:border-zinc-200',
        'dot_classes' => $is_active ? 'bg-zinc-900' : 'bg-zinc-300',
        'mobile_classes' => $is_active
            ? 'whitespace-nowrap rounded-lg border border-zinc-200 bg-white/90 px-3 py-2 text-sm font-medium text-zinc-900'
            : 'whitespace-nowrap rounded-lg border border-zinc-200 bg-white/70 px-3 py-2 text-sm text-zinc-700'
    ];
}

/**
 * Nav menu
 */
$nav_menu = [
    'general' => [
        ['key' => 'index', 'title' => 'Dashboard', 'url' => '/acp'],
        ['key' => 'users', 'title' => 'Users', 'url' => '/acp/users'],
        ['key' => 'content', 'title' => 'Content', 'url' => '/acp/content']
    ],
    'system' => [
        ['key' => 'settings', 'title' => 'Settings', 'url' => '/acp/settings'],
        ['key' => 'logs', 'title' => 'Logs', 'url' => '/acp/logs'],
        ['key' => 'ui-kit', 'title' => 'UI Kit', 'url' => '/acp/ui-kit']
    ]
];

?>
<aside class="hidden lg:block pr-6">
    <nav aria-label="Админ-меню" class="sticky top-24">
        <h2 class="sr-only">Admin menu</h2>
        <ul class="space-y-1">
            <?php foreach ($nav_menu as $section_key => $section_items): ?>
                <li class="px-2 <?php echo $section_key === 'general' ? 'pt-0' : 'pt-4'; ?> pb-1 text-xs font-semibold uppercase tracking-wide text-zinc-500/80">
                    <?php echo $section_key === 'general' ? 'General' : 'System'; ?>
                </li>
                <?php foreach ($section_items as $item): ?>
                    <?php $attrs = get_nav_link_attrs($item['key']); ?>
                    <li>
                        <a href="<?php echo $item['url']; ?>" <?php echo $attrs['aria_current']; ?> class="flex items-center gap-3 rounded-lg <?php echo $attrs['classes']; ?>">
                            <span class="inline-block h-2 w-2 rounded-full <?php echo $attrs['dot_classes']; ?>"></span>
                            <span><?php echo $item['title']; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>

<!-- Mobile navigation ACP -->
<div class="mt-6 px-4 lg:hidden">
    <nav aria-label="ACP navigation" class="flex gap-2 overflow-x-auto">
        <?php foreach ($nav_menu as $section_items): ?>
            <?php foreach ($section_items as $item): ?>
                <?php $attrs = get_nav_link_attrs($item['key']); ?>
                <a href="<?php echo $item['url']; ?>" class="<?php echo $attrs['mobile_classes']; ?>"><?php echo $item['title']; ?></a>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </nav>
</div>
