<?php declare(strict_types=1);
if(!defined('RooCMS')) {roocms_protect();}

$page_title = 'License — Admin Control Panel — RooCMS';
$page_description = 'License for RooCMS';

$theme_name = basename(dirname(__DIR__, 2));
$theme_base = '/themes/'.$theme_name;

$page_scripts = [
	$theme_base.'/assets/js/app/acp.js',
    $theme_base.'/assets/js/app/acp-access.js'
];

ob_start();
?>



<?php $page_content = ob_get_clean();
require __DIR__ . '/../../layouts/base.php';
