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
 * @subpackage   Engine RooCMS classes
 * @author       alex Roosso
 * @copyright    2010-2017 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.0
 * @since        $date$
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################



/**
 * Class Tags
 */
class Tags {



	/**
	 * Функция собирает теги объекта в строку разделенные запятыми.
	 *
	 * @param string $linkedto - ссылка на объект
	 *
	 * @return null|string
	 */
	public function read_tags($linkedto) {

		global $db;

		$tags = "";
		$q = $db->query("SELECT l.tag_id, t.title FROM ".TAGS_LINK_TABLE." AS l LEFT JOIN ".TAGS_TABLE." AS t ON (t.id = l.tag_id) WHERE l.linkedto='".$linkedto."'");
		while($data = $db->fetch_assoc($q)) {
			if(trim($tags) != "") {
				$tags .= ", ";
			}

			$tags .= $data['title'];
		}

		return $tags;
	}

	/**
	 * Функция сохраняет теги в БД
	 *
	 * @param string $tags     - строка с тегами разделенными запятой
	 * @param string $linkedto - ссылка на объект
	 */
	public function save_tags($tags, $linkedto) {

		global $db;

		# Разбираем строку с тегами
		$tags = $this->parse_tags($tags);

		# если есть теги
		if(!empty($tags)) {
			$v = $db->check_array_ids($tags, TAGS_TABLE, "title");
			foreach($tags AS $value) {
				if($v[$value]['check']) {
					# добавляем линк к уже имеющимуся тегу
					$this->add_instock_tag($v[$value]['id_value'], $linkedto);
				}
				else {
					# создаем новый те и линк к нему
					$this->add_new_tag($v[$value]['value'], $linkedto);
				}
			}
		}
	}


	/**
	 * Добавляем новый Тег
	 *
	 * @param string $tag      - Тег
	 * @param string $linkedto - Указатель к чему прикреплен данный тег
	 */
	private function add_new_tag($tag, $linkedto) {

		global $db;

		# create
		$db->query("INSERT INTO ".TAGS_TABLE." (title, amount) VALUES ('".$tag."', '1')");
		$tag_id = $db->insert_id();

		# linked
		$db->query("INSERT INTO ".TAGS_LINK_TABLE." (tag_id, linkedto) VALUES ('".$tag_id."', '".$linkedto."')");
	}


	/**
	 * Добавляем Тег, который уже используется на сайте.
	 *
	 * @param string $tag_id   - Идентификатор теша
	 * @param string $linkedto - Указатель к чему прикреплен данный тег
	 */
	private function add_instock_tag($tag_id, $linkedto) {

		global $db;

		# Если к данному объекту не прикреплен такой, то добавляем.
		if(!$db->check_id($tag_id, TAGS_LINK_TABLE, "tag_id", "linkedto='".$linkedto."'")) {
			# linked
			$db->query("INSERT INTO ".TAGS_LINK_TABLE." (tag_id, linkedto) VALUES ('".$tag_id."', '".$linkedto."')");

			# recount
			$this->recount_tag($tag_id);
		}
	}


	/**
	 * Функция парсит и форматирует строку с тегами разделенными запятыми и преобразует её в массив.
	 *
	 * @param string $tags - строка с тегами разделенными запятой
	 *
	 * @return array возврашает массив с тегами
	 */
	private function parse_tags($tags) {

		global $parse;

		# check
		$strtag = array_unique($parse->check_array(explode(",",mb_strtolower($tags))));

		$tag = array();
		foreach($strtag as $value) {
			if(trim($value) != "") {
				# чистим от мусорных символов
				$tag[] = $parse->clear_string($value);
			}
		}

		return $tag;
	}


	/**
	 * Пересчитываем кол-во использований тега.
	 *
	 * @param int $tag_id - Идентификатор Тега
	 */
	private function recount_tag($tag_id) {

		global $db;

		# считаем
		$amount = $db->count(TAGS_LINK_TABLE, "tag_id='".$tag_id."'");

		# обновляем
		$db->query("UPDATE ".TAGS_TABLE." SET amount='".$amount."' WHERE id='".$tag_id."'");
	}
}

?>