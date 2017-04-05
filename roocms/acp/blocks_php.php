<?php
/**
 *   RooCMS - Russian Open Source Free Content Managment System
 *   Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
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
 *   RooCMS - Бесплатная система управления контентом с открытым исходным кодом
 *   Copyright © 2010-2018 александр Белов (alex Roosso). Все права защищены.
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
* @subpackage	Blocks settings
* @subpackage	PHP Blocks
* @author       alex Roosso
* @copyright    2010-2018 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.1.1
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


class ACP_Blocks_PHP {

	/**
	 * Создаем PHP блок
	 */
	public function create() {

		global $db, $tpl, $smarty, $POST, $logger;

		if(isset($POST->create_block)) {

			# check data post
			$this->check_block_parametrs();

			if(!isset($_SESSION['error'])) {
				$db->query("INSERT INTO ".BLOCKS_TABLE."   (title, alias, content, date_create, date_modified, block_type)
							VALUES ('".$POST->title."', '".$POST->alias."', '".$POST->content."', '".time()."', '".time()."', 'php')");

				$bid = $db->insert_id();

				$logger->info("Блок #".$bid." успешно добавлен!");

				go(CP."?act=blocks");
			}
			else {
				go(CP."?act=blocks&part=create&type=php");
			}
		}

		$content = $tpl->load_template("blocks_create_php", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Редактируем PHP блок
	 *
	 * @param $id - идентификатор блока
	 */
	public function edit($id) {

		global $db, $tpl, $smarty;

		$q = $db->query("SELECT id, title, alias, content FROM ".BLOCKS_TABLE." WHERE id='".$id."'");
		$data = $db->fetch_assoc($q);

		$smarty->assign("data",$data);
		$content = $tpl->load_template("blocks_edit_php", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Обновляем PHP блок
	 *
	 * @param $id - идентификатор блока
	 */
	public function update($id) {

		global $db, $POST, $GET, $logger;

		if(isset($POST->update_block)) {

			# check data post
			$this->check_block_parametrs();

			if(!isset($POST->id) || $POST->id != $GET->_block) {
				$logger->error("Системная ошибка...");
			}

			if(!isset($_SESSION['error'])) {

				$db->query("UPDATE ".BLOCKS_TABLE."
							SET
								title='".$POST->title."',
								alias='".$POST->alias."',
								content='".$POST->content."',
								date_modified='".time()."'
							WHERE
								id='".$id."'");

				$logger->info("Блок #".$id." успешно обновлен!");
			}

			go(CP."?act=blocks");
		}
		else {
			goback();
		}
	}


	/**
	 * Удаляем PHP блок
	 *
	 * @param $id - идентификатор блока
	 */
	public function delete($id) {

		global $db, $logger;

		$db->query("DELETE FROM ".BLOCKS_TABLE." WHERE id='".$id."'");
		$logger->info("Блок #".$id." успешно удален!");
		go(CP."?act=blocks");
	}


	/**
	 * Check Block Parametrs
	 */
	private function check_block_parametrs() {

		global $db, $parse, $POST, $logger;

		if(!isset($POST->title)) {
			$logger->error("Не указано название блока!", false);
		}

		if(!isset($POST->alias)) {
			$logger->error("Не указан алиас блока!", false);
		}
		else {
			$POST->alias = $parse->text->transliterate($POST->alias);
			$POST->alias = preg_replace(array('(\s\s+)','(\-\-+)','(__+)','([^a-zA-Z0-9\-_])'), array('','','',''), $POST->alias);

			if(is_numeric($POST->alias)) {
				$POST->alias .= randcode(3, "abcdefghijklmnopqrstuvwxyz");
			}


			$check_alias = (isset($POST->oldalias)) ? "alias!='".$POST->oldalias."'" : "" ;

			if($db->check_id($POST->alias, BLOCKS_TABLE, "alias", $check_alias)) {
				$logger->error("Алиас блока не уникален!", false);
			}
		}

		if(!isset($POST->content)) {
			$logger->error("Пустое тело блока!", false);
		}

	}
}

?>