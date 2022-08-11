<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2023 alexandr Belov aka alex Roosso. All rights reserved.
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
if(is_file(_ROOCMS."/acp/structure.php")) {
	$menu_items_left[] = array('role'=>'navlink', 'link'=>CP.'?act=structure', 'act'=>'structure', 'icon'=>'fas fa-fw fa-sitemap', 'text'=>'Структура сайта', 'window'=>'_self');
}

/**
* Blocks
*/
if(is_file(_ROOCMS."/acp/blocks.php")) {
	$menu_items_left[] = array('role'=>'navlink', 'link'=>CP.'?act=blocks', 'act'=>'blocks', 'icon'=>'fas fa-fw fa-th', 'text'=>'Блоки', 'window'=>'_self');
}

/**
 * Users
 */
if(is_file(_ROOCMS."/acp/users.php")) {
	$menu_items_left[] = array('role'=>'navlink', 'link'=>CP.'?act=users', 'act'=>'users', 'icon'=>'fas fa-fw fa-users', 'text'=>'Пользователи', 'window'=>'_self');
}

/**
 * Logs
 */
if(is_file(_ROOCMS."/acp/logs.php")) {
	$menu_items_left[] = array('role'=>'navlink', 'link'=>CP.'?act=logs', 'act'=>'logs', 'icon'=>'fas fa-fw fa-list', 'text'=>'Логи', 'window'=>'_self');
}

/**
* Configuration
*/
if(is_file(_ROOCMS."/acp/config.php")) {
	$menu_items_left[] = array('role'=>'navlink', 'link'=>CP.'?act=config', 'act'=>'config', 'icon'=>'fas fa-fw fa-cogs', 'text'=>'Настройки', 'window'=>'_self');
}




/**
* On site
*/
	$menu_items_right[] = array('role'=>'navlink', 'link'=>'/', 'act'=>'RooCMS', 'icon'=>'fas fa-fw fa-home', 'text'=>'На сайт', 'window'=>'_blank');

/**
 * Admin menu
 */
	$menu_items_right[] = array('role'=>'dropdown', 'icon'=>'fas fa-fw fa-user', 'text'=>$users->nickname,
		array('role'=>'header', 'text'=>'Ваше личное меню'),												# header
		array('role'=>'navlink', 'link'=>CP.'?act=help', 'act'=>'help', 'icon'=>'fas fa-fw fa-question-circle', 'text'=>'Помощь', 'window'=>'_self'),	# help
		array('role'=>'davider'), 															# davider
		array('role'=>'navlink', 'link'=>CP.'?act=logout', 'act'=>'logout', 'icon'=>'fas fa-fw fa-sign-out-alt', 'text'=>'Выход', 'window'=>'_self')	# logout
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
