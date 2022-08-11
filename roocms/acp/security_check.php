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
if(!defined('RooCMS') || (!defined('ACP') && !defined('INSTALL'))) {
	die('Access Denied');
}
//#########################################################


/**
 * Class ACP_Security
 */
class ACP_Security {

	/**
	 * @var bool
	 */
	public $access = false;


	/**
	 * Check user access
	 */
	public function __construct() {

		global $users;

		# default: access denied
		$this->access = false;

		# check access
		if($users->uid != 0 && $users->title == "a" && $users->token != "") {
			# access granted
			$this->access = true;
		}
	}
}

/**
 * Init Class
 */
$acpsecurity = new ACP_Security;
