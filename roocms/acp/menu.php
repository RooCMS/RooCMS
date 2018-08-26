<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('ACP')) {
	die('Access Denied');
}
//#########################################################


/**
* Structure
*/
if(file_exists(_ROOCMS."/acp/structure.php")) {
	$menu_items_left[] = array('role'=>'navlink', 'link'=>CP.'?act=structure', 'act'=>'structure', 'icon'=>'fa fa-fw fa-sitemap', 'text'=>'Структура', 'window'=>'_self');
}

/**
* Blocks
*/
if(file_exists(_ROOCMS."/acp/blocks.php")) {
	$menu_items_left[] = array('role'=>'navlink', 'link'=>CP.'?act=blocks', 'act'=>'blocks', 'icon'=>'fa fa-fw fa-th', 'text'=>'Блоки', 'window'=>'_self');
}

/**
 * Users
 */
if(file_exists(_ROOCMS."/acp/users.php")) {
	$menu_items_left[] = array('role'=>'navlink', 'link'=>CP.'?act=users', 'act'=>'users', 'icon'=>'fa fa-fw fa-users', 'text'=>'Пользователи', 'window'=>'_self');
}

/**
* Configuration
*/
if(file_exists(_ROOCMS."/acp/config.php")) {
	$menu_items_left[] = array('role'=>'navlink', 'link'=>CP.'?act=config', 'act'=>'config', 'icon'=>'fa fa-fw fa-cogs', 'text'=>'Настройки', 'window'=>'_self');
}




/**
* On site
*/
	$menu_items_right[] = array('role'=>'navlink', 'link'=>'/', 'act'=>'RooCMS', 'icon'=>'fa fa-fw fa-home', 'text'=>'На сайт', 'window'=>'_blank');

/**
 * Admin menu
 */
	$menu_items_right[] = array('role'=>'dropdown', 'icon'=>'fa fa-fw fa-user', 'text'=>$users->nickname,
		array('role'=>'header', 'text'=>'Ваше личное меню'),												# header
		array('role'=>'navlink', 'link'=>CP.'?act=help', 'act'=>'help', 'icon'=>'fa fa-fw fa-support', 'text'=>'Помощь', 'window'=>'_self'),			# help
		array('role'=>'davider'), 															# davider
		array('role'=>'navlink', 'link'=>CP.'?act=logout', 'act'=>'logout', 'icon'=>'fa fa-fw fa-sign-out', 'text'=>'Выход', 'window'=>'_self')		# logout
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