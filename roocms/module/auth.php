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


/**
 * Class Module_Auth
 */
class Module_Auth extends Modules {

	# Title
	public $title = "Авторизация пользователя";

	# buffer out
	protected $out = "";


	/**
	 * Start
	 */
	protected function begin() {

		global $db, $users, $tpl, $smarty;

		if($users->uid != 0) {
			$newpm = $db->count(USERS_PM_TABLE, "to_uid='".$users->uid."' AND see='0'");
			$smarty->assign("pm", $newpm);
		}

		# draw
		$this->out = $tpl->load_template("module/auth", true);
	}
}
