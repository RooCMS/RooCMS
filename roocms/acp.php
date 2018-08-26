<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 * Contacts: <info@roocms.com>
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */

/**
 * @package      RooCMS
 * @subpackage	 Admin Control Panel
 * @author       alex Roosso
 * @copyright    2010-2019 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.1.1
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
 */

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


//#########################################################
// Initialisation Admin CP identification
//---------------------------------------------------------
if(!defined('ACP')) {
	define('ACP', true);
}
//#########################################################


nocache();

# Security check
require_once _ROOCMS."/acp/security_check.php";


if($acpsecurity->access) {
	# запускаем меню админа
	require_once _ROOCMS."/acp/menu.php";

	if(trim($roocms->act) != "" && file_exists(_ROOCMS."/acp/".$roocms->act.".php")) {
		require_once _ROOCMS."/acp/".$roocms->act.".php";
	}
	else require_once _ROOCMS."/acp/index.php";
}
else {
	$smarty->assign("no_footer", true);
	require_once _ROOCMS."/acp/login.php";
}