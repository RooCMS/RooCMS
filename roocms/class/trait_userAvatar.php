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
 * Trait UserAvatar
 */
trait UserAvatar {

	/**
	 * Upload User Avatar
	 *
	 * @param int    $uid    - user unique id
	 * @param string $avatar - used user avatar (if have)
	 *
	 * @return string - filename avatar image
	 */
	public function upload_avatar(int $uid, string $avatar="") {

		global $config, $img;

		$av = $img->upload_image("avatar", "", array($config->users_avatar_width, $config->users_avatar_height), false, false, false, "av_".$uid);
		if(isset($av[0])) {
			if($avatar != "" && $avatar != $av[0]) {
				$img->erase_image(_UPLOADIMAGES."/".$avatar);
			}
			$avatar = $av[0];
		}

		return $avatar;
	}


	/**
	 * Remove user avatar
	 *
	 * @param int $uid - unique user id
	 */
	public function delete_avatar(int $uid) {

		global $db;

		if($db->check_id($uid, USERS_TABLE, "uid", "avatar!=''") && ($this->uid == $uid || $this->title == "a")) {

			$q = $db->query("SELECT avatar FROM ".USERS_TABLE." WHERE uid='".$uid."'");
			$data = $db->fetch_assoc($q);

			if(is_file(_UPLOADIMAGES."/".$data['avatar'])) {
				unlink(_UPLOADIMAGES."/".$data['avatar']);
				$db->query("UPDATE ".USERS_TABLE." SET avatar='' WHERE uid='".$uid."'");
			}
		}
	}
}
