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
if(!defined('RooCMS') || !defined('ACP')) {
	die('Access Denied');
}
//#########################################################


class ACP_Ajax {


	/**
	* Start
	*
	*/
	public function __construct() {

		global $roocms;

		// turn "on" ajax
		$roocms->ajax = true;

		switch($roocms->part) {

			# delete
			case 'delete_attached_image':
				$this->delete_attached_image();
				break;

			case 'delete_attached_file':
				$this->delete_attached_file();
				break;

			case 'delete_user_avatar':
				$this->delete_user_avatar();
				break;

			case 'delete_config_image':
				$this->delete_config_image();
				break;
		}
	}


	/**
	* Remove attached images
	*
	*/
	private function delete_attached_image() {

		global $db, $get, $img, $logger;

		if(isset($get->_id) && $db->check_id($get->_id, IMAGES_TABLE)) {

			$img->remove_images($get->_id);
			$logger->log("Изображение #".$get->_id." удалено");

			echo "<small class=\"text-success btn btn-xs delete_image\"><span class=\"fa fa-trash-o\"></span> Удалено!</small>";
		}
	}


	/**
	 * Remove attached files
	 *
	 */
	private function delete_attached_file() {

		global $db, $get, $files, $logger;

		if(isset($get->_id) && $db->check_id($get->_id, FILES_TABLE)) {

			$files->remove_files($get->_id);
			$logger->log("Файл #".$get->_id." удален");

			echo "<small class=\"text-success btn btn-xs delete_image\"><span class=\"fa fa-trash-o\"></span> Удалено!</small>";
		}
	}


	/**
	 * Remove user avatar
	 *
	 */
	private function delete_user_avatar() {

		global $db, $get, $users;

		if(isset($get->_uid) && $db->check_id($get->_uid, USERS_TABLE, "uid", "avatar!=''")) {

			$users->delete_avatar($get->_uid);

			echo "<small class=\"text-success btn btn-xs\"><span class=\"fa fa-trash-o\"></span> Удалено!</small>";
		}
	}

	/**
	 * Remove images from site configuration
	 *
	 */
	private function delete_config_image() {

		global $db, $get, $img;

		if(isset($get->_option) && $db->check_id($get->_option, CONFIG_TABLE, "option_name", "value!=''")) {

			$q = $db->query("SELECT value FROM ".CONFIG_TABLE." WHERE option_name='".$get->_option."'");
			$data = $db->fetch_assoc($q);

			$img->erase_image(_UPLOADIMAGES."/".$data['value']);
			$db->query("UPDATE ".CONFIG_TABLE." SET value='' WHERE option_name='".$get->_option."'");

			echo "<small class=\"text-success btn btn-xs\"><span class=\"fa fa-trash-o\"></span> Удалено!</small>";
		}
	}
}

/**
 * Init Class
 */
$acp_ajax = new ACP_Ajax;
