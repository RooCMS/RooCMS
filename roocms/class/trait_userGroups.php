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
 * Trait User Groups
 */
trait UserGroups {

	# groups
	private $grouplist	= [];	# user groups list



	/**
	 * Get user group list
	 *
	 * @return array
	 */
	public function get_usergroups() {

		global $db;

		if(empty($this->grouplist)) {
			$q = $db->query("SELECT gid, title, users FROM ".USERS_GROUP_TABLE." ORDER BY gid");
			while($row = $db->fetch_assoc($q)) {
				$this->grouplist[] = $row;
			}
		}

		return $this->grouplist;
	}


	/**
	 * Get user id list from Group[s]
	 *
	 * @param array $gids - group id array
	 *
	 * @return array
	 */
	public function get_groupuids(array $gids) {

		global $db;

		$cond = "";
		foreach($gids AS $gid) {
			$cond = $db->qcond_or($cond);
			$cond .= " gid='".$gid."' ";
		}

		$list = [];
		$q = $db->query("SELECT uid, gid, status, ban, nickname FROM ".USERS_TABLE." WHERE ".$cond);
		while($data = $db->fetch_assoc($q)) {
			$list[$data['uid']] = $data;
		}

		return $list;
	}


	/**
	 * Get array with group ids access granted from data string
	 *
	 * @param string $data - group ids separeted by comma
	 *
	 * @return array
	 */
	public function get_gid_access_granted(string $data="0") {

		return array_flip(explode(",", $data));
	}
}
