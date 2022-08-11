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


define('INSTALL', true);
define('_SITEROOT', str_ireplace("install", "", dirname(__FILE__)));
require_once _SITEROOT."/roocms/init.php";


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################

nocache();

if(trim($db_info['user']) != "" && trim($db_info['base']) != "" && $db->check_id(1,USERS_TABLE,"uid")) {

	require_once _ROOCMS."/acp/security_check.php";

	if($acpsecurity->access) {

		require_once "check_requirement.php";
		require_once "extends.php";

		if(!empty($db_info['user']) && !empty($db_info['pass']) && !empty($db_info['base'])) {
			require_once "update.php";
			$update = new Update;
		}
		else {
			$site['title'] = "Установка RooCMS";
			require_once "install.php";
			$install = new Install;
		}
	}
	else {
		$smarty->assign("no_footer", true);
		require_once _ROOCMS."/acp/login.php";
	}
}
else {
	require_once "check_requirement.php";
	require_once "extends.php";
	require_once "install.php";
	$install = new Install;
}

# draw page
$tpl->out();