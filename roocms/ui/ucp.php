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
if(!defined('RooCMS') || !defined('UI')) {
	die('Access Denied');
}
//#########################################################


//#########################################################
// Initialisation User CP identification
//---------------------------------------------------------
const UCP = true;
//#########################################################


nocache();

# Security check
require_once _UI."/ucp/security_check.php";

if($ucpsecurity->access) {
	if(is_file(_UI."/ucp/".$roocms->act.".php")) {
		require_once _UI."/ucp/".$roocms->act.".php";
	}
	else {
		require_once _UI."/ucp/ucp.php";
	}
}
else {
	require_once _UI."/ucp/login.php";
}
