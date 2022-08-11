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
 * Class Module_Express_Reg
 */
class Module_express_Reg extends Modules {

	# Title
	public $title = "Экспресс регистрация";

	# buffer out
	protected $out = "";


	/**
	 * Start
	 */
	protected function begin() {

		global $users, $tpl, $smarty;

		# form show/hide
		$hide =  false;

		# if user subscribed
		if(($users->uid != 0 && $users->userdata['mailing'] == 1) || isset($_COOKIE['mailing'])) {
			$hide = true;
		}

		# template
		$smarty->assign("hide", $hide);
		$this->out = $tpl->load_template("module/express_reg", true);
	}
}
