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


/**
 * Class UI_Unsubscrube
 */
class UI_Unsubscribe {


	public function __construct() {

		global $db, $get, $structure, $nav, $smarty, $tpl;

		# title
		$structure->page_title = "Рассылка";

		# breadcrumb
		$nav->breadcrumb[] = array('part'=>'unsubscribe', 'title'=>'Отключение рассылки');

		$result = false;

		# check access
		if(isset($get->_uid, $get->_code) && $db->check_id($get->_uid, USERS_TABLE, "uid", "secret_key='".$get->_code."'")) {

			# new secret key
			$nsk = randcode(16);

			# update
			$db->query("UPDATE ".USERS_TABLE." SET mailing='0', secret_key='".$nsk."' WHERE uid='".$get->_uid."'");

			$result = true;
		}

		# tpl
		$smarty->assign("result", $result);
		$tpl->load_template("unsubscribe");
	}
}


/**
 * init
 */
$uiunsubscribe = new UI_Unsubscribe;
