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
 * @subpackage   Engine RooCMS classes
 * @author       alex Roosso
 * @copyright    2010-2018 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.0.7
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
	 * Функция возвращает ввиде массива полный список тегов
	 *
	 * @param bool   $with_zero - Если флаг true, то вернет список тегов включая, нулевые значения. Иначе вернет только используемые теги.
	 * @param int    $limit     - Кол-во тегов (срез) которые вернет запрос
	 *
	 * @return array
	 */
	public function list_tags($with_zero=false, $limit=0) {

		global $db;

		# condition
		$cond = "amount != '0' ";
		if($with_zero) {
			$cond = " amount > '0' ";
		}

		# limit condition
		$lcond = "";
		if($limit > 0) {
			$lcond = " LIMIT 0,".$limit;
		}

		# query
		$tags = array();
		$q = $db->query("SELECT title, amount FROM ".TAGS_TABLE." WHERE ".$cond." ORDER BY amount DESC ".$lcond);
		while($data = $db->fetch_assoc($q)) {
			$tags[] = $data;
		}

		return $tags;
	}


	/**
	 * Функция собирает теги объекта в строку разделенные запятыми.
	 *
	 * @param string $linkedto - ссылка на объект
	 *
	 * @return array
	 */
	public function read_tags($linkedto) {

		global $db;

		if(!is_array($linkedto)) {
			$linkedto = array($linkedto);
		}

		# create condition

		$cond = "";

		foreach($linkedto AS $value) {
			if($cond != "") {
				$cond .= " OR ";
			}

			$cond .= " l.linkedto='".$value."' ";
		}


		if($cond != "") {
			$cond = " (".$cond.")";
		}
		else {
			$cond .= " l.linkedto='0' ";
		}


		$tags = array();
		$q = $db->query("SELECT l.tag_id, t.title, t.amount, l.linkedto FROM ".TAGS_LINK_TABLE." AS l LEFT JOIN ".TAGS_TABLE." AS t ON (t.id = l.tag_id) WHERE".$cond." ORDER BY t.title ASC");
		while($data = $db->fetch_assoc($q)) {
			$tags[] = $data;
		}

		# return
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

		# Получаем текущие теги и разбираем
		$now_tags = array_map(array($this, "get_tag_title"), $this->read_tags($linkedto));

		# Разбираем строку с полученными тегами
		$new_tags = $this->parse_tags($tags);

		# Сравниваем старые и новые теги, манипулируем.
		$tags = $this->diff_tag($now_tags, $new_tags, $linkedto);

		# Если есть теги
		if(!empty($tags)) {
			$v = $db->check_array_ids($tags, TAGS_TABLE, "title");

			foreach($tags AS $value) {
				if($v[$value]['check']) {
					# Добавляем линк к уже имеющимуся тегу
					$this->add_instock_tag($v[$value]['id_value'], $linkedto);
				}
				else {
					# Создаем новый тег и линк к нему
					$this->add_new_tag($v[$value]['value'], $linkedto);
				}
			}
		}
	}


	/**
	 * Функция проводит сравнении данных в массивах определя какие теги трогать, как пропустить
	 *
	 * @param array  $now      - массив с имеющимися у объекта тегами
	 * @param array  $new      - массив с новыми тегами
	 * @param string $linkedto - ссылка на объект
	 *
	 * @return array
	 */
	public function diff_tag(array $now, array $new, $linkedto) {

		$tags = array();

		if(empty($new)) {     // Если теги удалили...
			$this->remove_tags($now, $linkedto);
		}
		elseif(empty($now)) { // Если тегов не было, то дальнейшие обработки не нужны. Возвращаем список новых тегов.
			$tags = $new;
		}
		else {                // Если в массивах есть что, проводим сравнение
			$tags = $new;

			# массив для устаревших тегов
			$old = array();

			foreach($now AS $value) {
				if(!in_array($value, $new)) {
					$old[] = $value;
				}

				if(($k = array_search($value, $new)) !== false) {
					unset($tags[$k]);
				}
			}

			# Удаляем ненужные теги
			$this->remove_tags($old, $linkedto);
		}

		# возвращаем список новых/обновленных тегов
		return $tags;
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
		$this->add_instock_tag($tag_id, $linkedto);
	}


	/**
	 * Добавляем Тег, который уже используется на сайте.
	 *
	 * @param string $tag_id   - Идентификатор теша
	 * @param string $linkedto - Указатель к чему прикреплен данный тег
	 */
	private function add_instock_tag($tag_id, $linkedto) {

		global $db;

		# linked
		$db->query("INSERT INTO ".TAGS_LINK_TABLE." (tag_id, linkedto) VALUES ('".$tag_id."', '".$linkedto."')");

		# recount
		$this->recount_tag($tag_id);
	}


	/**
	 * Функция удаляет теги у заданного объекта.
	 *
	 * @param array|string $tags     - массив или строка с тегами
	 * @param string       $linkedto - ссылка на объект
	 */
	private function remove_tags($tags, $linkedto) {

		global $db;

		# Если получили строку, преобразуем ее в массив.
		if(!is_array($tags)) {
			$tags = $this->parse_tags($tags);
		}

		# Если массив не пустой.
		if(!empty($tags)) {

			# составляем условие
			$cond1 = "";
			foreach($tags AS $value) {
				if(trim($cond1) != "") {
					$cond1 .= " OR ";
				}
				$cond1 .= "title='".$value."'";
			}

			# get tag id for condition unlinks
			$tr = array();
			$cond2 = "(";
			$q = $db->query("SELECT id FROM ".TAGS_TABLE." WHERE ".$cond1);
			while($data = $db->fetch_assoc($q)) {
				if(trim($cond2) != "(") {
					$cond2 .= " OR ";
				}
				$cond2 .= "tag_id='".$data['id']."'";

				$tr[] = $data['id'];
			}
			$cond2 .= ")";

			# unlinked
			$db->query("DELETE FROM ".TAGS_LINK_TABLE." WHERE ".$cond2." AND linkedto='".$linkedto."'");

			# recount
			foreach($tr AS $v) {
				$this->recount_tag($v);
			}
		}
	}


	/**
	 * Функция парсит и форматирует строку с тегами разделенными запятыми и преобразует её в массив.
	 *
	 * @param string $tags     - строка с тегами разделенными запятой
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
	 * @param int $amount - кол-во на текущий момент, если известно
	 */
	private function recount_tag($tag_id, $amount=-1) {

		global $db;

		# считаем
		$c = $db->count(TAGS_LINK_TABLE, "tag_id='".$tag_id."'");

		# обновляем если кол-во изменилось
		if(round($amount) != $c) {
			$db->query("UPDATE ".TAGS_TABLE." SET amount='".$c."' WHERE id='".$tag_id."'");
		}
	}


	/**
	 * Функция для array_map, которая вернет названия тега.
	 *
	 * @param $tag
	 *
	 * @return mixed
	 */
	static public function get_tag_title($tag) {

		return $tag['title'];
	}
}