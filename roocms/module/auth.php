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
* @subpackage   Module
* @author       alex Roosso
* @copyright    2010-2019 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.2
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class Module_Auth
 */
class Module_Auth {

	# Название
	public $title = "Авторизация пользователя";

	# buffer out
	private $out = "";


	/**
	 * Start
	 */
	public function __construct() {

		global $db, $users, $tpl, $smarty;

		if($users->uid != 0) {
			$newpm = $db->count(USERS_PM_TABLE, "to_uid='".$users->uid."' AND see='0'");
		}

		# draw
		if(isset($newpm)) {
			$smarty->assign("pm", $newpm);
		}
		$smarty->assign("userdata", $users->userdata);
		$this->out .= $tpl->load_template("module_auth", true);
	}


	/**
	 * Finish
	 */
	public function __destruct() {
		echo $this->out;
	}
}

/**
 * Init class
 */
$module_auth = new Module_Auth;