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
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('ACP')) {
	die('Access Denied');
}
//#########################################################


nocache();

# Security check
require_once _ROOCMS."/acp/security_check.php";


if($acpsecurity->access) {
	# Load admin menu
	require_once _ROOCMS."/acp/menu.php";

	if(is_file(_ROOCMS."/acp/".$roocms->act.".php")) {
		require_once _ROOCMS."/acp/".$roocms->act.".php";
	}
	else {
		require_once _ROOCMS."/acp/index.php";
	}
}
else {
	$smarty->assign("no_footer", true);
	require_once _ROOCMS."/acp/login.php";
}
