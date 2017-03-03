<?php
/**
 *   RooCMS - Russian free content managment system
 *   Copyright © 2010-2017 alexandr Belov aka alex Roosso. All rights reserved.
 *   Contacts: <info@roocms.com>
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see http://www.gnu.org/licenses/
 *
 *
 *   RooCMS - Русская бесплатная система управления контентом
 *   Copyright © 2010-2017 александр Белов (alex Roosso). Все права защищены.
 *   Для связи: <info@roocms.com>
 *
 *   Это программа является свободным программным обеспечением. Вы можете
 *   распространять и/или модифицировать её согласно условиям Стандартной
 *   Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
 *   Обеспечения, версии 3 или, по Вашему желанию, любой более поздней версии.
 *
 *   Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
 *   ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
 *   ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
 *   Общественную Лицензию GNU для получения дополнительной информации.
 *
 *   Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
 *   с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
 */

/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Ajax Functions
* @author       alex Roosso
* @copyright    2010-2017 (c) RooCMS
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


class ACP_AJAX {


	/**
	* Start
	*
	*/
	public function __construct() {

		global $roocms;

		// turn on ajax
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

		global $db, $GET, $img, $logger;

		if(isset($GET->_id) && $db->check_id($GET->_id, IMAGES_TABLE)) {

			$img->delete_images($GET->_id);
			$logger->log("Изображение #".$GET->_id." удалено");

			echo "<small class=\"text-success btn btn-xs delete_image\"><span class=\"fa fa-trash-o\"></span> Удалено!</small>";
		}
	}


	/**
	 * Удаление файлов посредством AJAX
	 *
	 */
	private function delete_attached_file() {

		global $db, $GET, $files, $logger;

		if(isset($GET->_id) && $db->check_id($GET->_id, FILES_TABLE)) {

			$files->delete_files($GET->_id);
			$logger->log("Файл #".$GET->_id." удален");

			echo "<small class=\"text-success btn btn-xs delete_image\"><span class=\"fa fa-trash-o\"></span> Удалено!</small>";
		}
	}


	/**
	 * Удаление пользовательского аватара посредством AJAX
	 *
	 */
	private function delete_user_avatar() {

		global $db, $GET, $users;

		if(isset($GET->_uid) && $db->check_id($GET->_uid, USERS_TABLE, "uid", "avatar!=''")) {

			$users->delete_avatar($GET->_uid);

			echo "<small class=\"text-success btn btn-xs\"><span class=\"fa fa-trash-o\"></span> Удалено!</small>";
		}
	}

	/**
	 * Удаление изображения из настроек сайта посредством AJAX
	 *
	 */
	private function delete_config_image() {

		global $db, $GET;

		if(isset($GET->_option) && $db->check_id($GET->_option, CONFIG_TABLE, "option_name", "value!=''")) {

			$q = $db->query("SELECT value FROM ".CONFIG_TABLE." WHERE option_name='".$GET->_option."'");
			$data = $db->fetch_assoc($q);

			unlink(_UPLOADIMAGES."/".$data['value']);
			$db->query("UPDATE ".CONFIG_TABLE." SET value='' WHERE option_name='".$GET->_option."'");

			echo "<small class=\"text-success btn btn-xs\"><span class=\"fa fa-trash-o\"></span> Удалено!</small>";
		}
	}
}

/**
 * Init Class
 */
$acp_ajax = new ACP_AJAX;

?>