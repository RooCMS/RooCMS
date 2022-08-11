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
if(!defined('RooCMS') || !defined('UI') || !defined('UCP')) {
	die('Access Denied');
}
//#########################################################


class UCP_Security {

	/**
	 * @var bool
	 */
	public $access = false;


	/**
	 * Функция проверки текущего доступа пользователя.
	 * В случае успешной проверки функция изменяет флаг $access на true
	 */
	public function __construct() {

		global $users;

		$this->access = false;	# access denied

		if($users->uid != 0 && $users->token != "") {
			$this->access = true;	# access granted

		}
	}
}

/**
 * Init Class
 */
$ucpsecurity = new UCP_Security;
