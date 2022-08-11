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
 * Trait ACP User Operation
 */
trait ACP_UserOperation {

	# vars
	protected $uid = 0;
	protected $gid = 0;


	/**
	 * Count user in group
	 *
	 * @param int $gid - group identificator
	 */
	private function count_users(int $gid) {

		global $db, $logger;

		if($gid != 0 && $db->check_id($gid, USERS_GROUP_TABLE, "gid")) {
			# count
			$c = $db->count(USERS_TABLE, "gid='".$gid."'");

			# update
			$db->query("UPDATE ".USERS_GROUP_TABLE." SET users='".$c."' WHERE gid='".$gid."'");

			# notice
			$logger->info("Информация о кол-ве пользователей для группы #".$gid." обновлена.");
		}
	}


	/**
	 * Check & init $this->uid
	 */
	private function check_var_uid() {

		global $db, $get;

		if(isset($get->_uid) && $db->check_id($get->_uid, USERS_TABLE, "uid")) {
			$this->uid = $get->_uid;
		}
	}


	/**
	 * Check & init $this->gid
	 */
	private function check_var_gid() {

		global $db, $get;

		if(isset($get->_gid) && $db->check_id($get->_gid, USERS_GROUP_TABLE, "gid")) {
			$this->gid = $get->_gid;
		}
	}
}
