<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Menu
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.15
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
*   Данное программное обеспечение является свободным и распространяется
*   по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
*   При любом использовании данного ПО вы должны соблюдать все условия
*   лицензии.
*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('ACP')) die('Access Denied');
//#########################################################


/**
* Structure
*/
if(file_exists(_ROOCMS."/acp/structure.php")) {
	$menu_items_left[] = array('link'=>CP.'?act=structure','icon'=>str_replace(_ROOT, "", _SKIN).'/acp/img/structure_16.png','text'=>'Структура','window'=>'_self');
}

/**
* Pages
*/
if(file_exists(_ROOCMS."/acp/pages.php")) {
	$menu_items_left[] = array('link'=>CP.'?act=pages','icon'=>str_replace(_ROOT, "", _SKIN).'/acp/img/pages_16.png','text'=>'Страницы','window'=>'_self');
}

/**
* Feeds
*/
if(file_exists(_ROOCMS."/acp/feeds.php")) {
	$menu_items_left[] = array('link'=>CP.'?act=feeds','icon'=>str_replace(_ROOT, "", _SKIN).'/acp/img/feeds_16.png','text'=>'Ленты','window'=>'_self');
}

/**
* Blocks
*/
if(file_exists(_ROOCMS."/acp/blocks.php")) {
	$menu_items_left[] = array('link'=>CP.'?act=blocks','icon'=>str_replace(_ROOT, "", _SKIN).'/acp/img/blocks_16.png','text'=>'Блоки','window'=>'_self');
}

/**
* Configuration
*/
if(file_exists(_ROOCMS."/acp/config.php")) {
	$menu_items_left[] = array('link'=>CP.'?act=config','icon'=>str_replace(_ROOT, "", _SKIN).'/acp/img/config_16.png','text'=>'Настройки','window'=>'_self');
}


/**
* Help System
*/
//if(file_exists(_ROOCMS."/acp/help.php")) {
//	$menu_items_right[] = array('link'=>CP.'?act=help','icon'=>str_replace(_ROOT, "", _SKIN).'/acp/img/help_16.png','text'=>'Помощь','window'=>'_self');
//}

/**
* On site
*/
$menu_items_right[] = array('link'=>'/','icon'=>str_replace(_ROOT, "", _SKIN).'/acp/img/index_16.png','text'=>'На сайт','window'=>'_blank');

/**
* Logout
*/
if(file_exists(_ROOCMS."/acp/logout.php")) {
	$menu_items_right[] = array('link'=>CP.'?act=logout','icon'=>str_replace(_ROOT, "", _SKIN).'/acp/img/logout_16.png','text'=>'Выход','window'=>'_self');
}


$date = date("d.m.Y",time());
$rdate	= $parse->date->unix_to_rus(time());

# assign vars
$smarty->assign('date',		$date);
$smarty->assign('rdate',	$rdate);
$smarty->assign('userip',	$roocms->userip);

$smarty->assign('menu_items_left',	$menu_items_left);
$smarty->assign('menu_items_right',	$menu_items_right);

# load template
$cpmenu = $tpl->load_template("menu", true);
$smarty->assign("cpmenu", $cpmenu);

?>