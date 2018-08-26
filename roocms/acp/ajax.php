<?php
/**
 *   RooCMS - Open Source Free Content Managment System
 *   Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 *   Contacts: <info@roocms.com>
 *
 *   You should have received a copy of the GNU General Public License v3
 *   along with this program.  If not, see http://www.gnu.org/licenses/
 */

/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Ajax Functions
* @author       alex Roosso
* @copyright    2010-2019 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.4
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
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
	* Удаление картинок посредством AJAX
	*
	*/
	private function delete_attached_image() {

		global $db, $get, $img, $logger;

		if(isset($get->_id) && $db->check_id($get->_id, IMAGES_TABLE)) {

			$img->delete_images($get->_id);
			$logger->log("Изображение #".$get->_id." удалено");

			echo "<small class=\"text-success btn btn-xs delete_image\"><span class=\"fa fa-trash-o\"></span> Удалено!</small>";
		}
	}


	/**
	 * Удаление файлов посредством AJAX
	 *
	 */
	private function delete_attached_file() {

		global $db, $get, $files, $logger;

		if(isset($get->_id) && $db->check_id($get->_id, FILES_TABLE)) {

			$files->delete_files($get->_id);
			$logger->log("Файл #".$get->_id." удален");

			echo "<small class=\"text-success btn btn-xs delete_image\"><span class=\"fa fa-trash-o\"></span> Удалено!</small>";
		}
	}


	/**
	 * Удаление пользовательского аватара посредством AJAX
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
	 * Удаление изображения из настроек сайта посредством AJAX
	 *
	 */
	private function delete_config_image() {

		global $db, $get;

		if(isset($get->_option) && $db->check_id($get->_option, CONFIG_TABLE, "option_name", "value!=''")) {

			$q = $db->query("SELECT value FROM ".CONFIG_TABLE." WHERE option_name='".$get->_option."'");
			$data = $db->fetch_assoc($q);

			unlink(_UPLOADIMAGES."/".$data['value']);
			$db->query("UPDATE ".CONFIG_TABLE." SET value='' WHERE option_name='".$get->_option."'");

			echo "<small class=\"text-success btn btn-xs\"><span class=\"fa fa-trash-o\"></span> Удалено!</small>";
		}
	}
}

/**
 * Init Class
 */
$acp_ajax = new ACP_Ajax;