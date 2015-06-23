<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @author       alex Roosso
* @copyrightt   2010-2015 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.2
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
*   Copyright (C) 2010-2016 alex Roosso aka alexandr Belov info@roocms.com
*
*   This program is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   This program is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with this program.  If not, see http://www.gnu.org/licenses/
*
*
*   RooCMS - Русская бесплатная система управления сайтом
*   Copyright (C) 2010-2016 alex Roosso (александр Белов) info@roocms.com
*
*   Это программа является свободным программным обеспечением. Вы можете
*   распространять и/или модифицировать её согласно условиям Стандартной
*   Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
*   Обеспечения, версии 3 или, по Вашему желанию, любой более поздней версии.
*
*   Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
*   ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
*   ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
*   Общественную Лицензию GNU для получения дополнительной информации.
*
*   Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
*   с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
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
	$menu_items_left[] = array('role'=>'navlink', 'link'=>CP.'?act=structure', 'act'=>'structure', 'icon'=>'fa fa-sitemap', 'text'=>'Структура', 'window'=>'_self');
}

/**
* Pages
*/
if(file_exists(_ROOCMS."/acp/pages.php")) {
	$menu_items_left[] = array('role'=>'navlink', 'link'=>CP.'?act=pages', 'act'=>'pages', 'icon'=>'fa fa-th-large', 'text'=>'Страницы', 'window'=>'_self');
}

/**
* Feeds
*/
if(file_exists(_ROOCMS."/acp/feeds.php")) {
	$menu_items_left[] = array('role'=>'navlink', 'link'=>CP.'?act=feeds', 'act'=>'feeds', 'icon'=>'fa fa-th-list', 'text'=>'Ленты', 'window'=>'_self');
}

/**
* Blocks
*/
if(file_exists(_ROOCMS."/acp/blocks.php")) {
	$menu_items_left[] = array('role'=>'navlink', 'link'=>CP.'?act=blocks', 'act'=>'blocks', 'icon'=>'fa fa-th', 'text'=>'Блоки', 'window'=>'_self');
}

/**
 * Users
 */
if(file_exists(_ROOCMS."/acp/users.php")) {
	$menu_items_left[] = array('role'=>'navlink', 'link'=>CP.'?act=users', 'act'=>'users', 'icon'=>'fa fa-users', 'text'=>'Пользователи', 'window'=>'_self');
}

/**
* Configuration
*/
if(file_exists(_ROOCMS."/acp/config.php")) {
	$menu_items_left[] = array('role'=>'navlink', 'link'=>CP.'?act=config', 'act'=>'config', 'icon'=>'fa fa-cogs', 'text'=>'Настройки', 'window'=>'_self');
}




/**
* Help System
*/
//if(file_exists(_ROOCMS."/acp/help.php")) {
//	$menu_items_right[] = array('role'=>'navlink', 'link'=>CP.'?act=help', 'act'=>'help', 'icon'=>'fa fa-support','text'=>'Помощь', 'window'=>'_self');
//}

/**
* On site
*/
	$menu_items_right[] = array('role'=>'navlink', 'link'=>'/', 'act'=>'RooCMS', 'icon'=>'fa fa-home', 'text'=>'На сайт', 'window'=>'_blank');

/**
 * Admin menu
 */
	$menu_items_right[] = array('role'=>'dropdown', 'icon'=>'fa fa-user', 'text'=>$users->nickname,
		array('role'=>'header', 'text'=>'Ваше личное меню'),												# header
		array('role'=>'navlink', 'link'=>CP.'?act=help', 'act'=>'help', 'icon'=>'fa fa-support','text'=>'Помощь', 'window'=>'_self'),
		array('role'=>'davider'), 															# davider
		array('role'=>'navlink', 'link'=>CP.'?act=logout', 'act'=>'logout', 'icon'=>'fa fa-sign-out', 'text'=>'Выход', 'window'=>'_self')		# logout
	);


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