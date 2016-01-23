<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @author       alex Roosso
* @copyright    2010-2016 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.3.7
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
*   Copyright (C) 2010-2016 alex Roosso aka alexandr Belov info@roocms.com
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
*   RooCMS - Русская бесплатная система управления сайтом
*   Copyright (C) 2010-2016 alex Roosso (александр Белов) info@roocms.com
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

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('ACP')) die('Access Denied');
//#########################################################


/**
 * Class ACP_STRUCTURE
 */
class ACP_STRUCTURE {

	# vars
	private	$engine;
	private $unit;

	private $sid = 0;



	/**
	* Lets mortal kombat begin
	*/
	public function __construct() {

		require_once _CLASS."/class_structure.php";
		$this->engine = new Structure(true, false);

		# initialise
		$this->init();
	}


	/**
	* initialisation action
	*/
	private function init() {

		global $roocms, $config, $db, $tpl, $smarty, $GET, $POST;

		# считываем "дерево"
		$smarty->assign('tree', $this->engine->sitetree);

		# Проверяем разрешенные типы страниц для использования
		$page_types = array();
		foreach($this->engine->page_types AS $key=>$value) {
			if($value['enable']) $page_types[$key] = $value['title'];
		}
		$smarty->assign('page_types', $page_types);


		# default thumb size
		$default_thumb_size = array('width'	=> $config->gd_thumb_image_width,
					    'height'	=> $config->gd_thumb_image_height);
		$smarty->assign("default_thumb_size", $default_thumb_size);


		# Проверяем идентификатор
		if(isset($GET->_id) && $db->check_id($GET->_id, STRUCTURE_TABLE)) $this->sid = $GET->_id;


		# действуем
		switch($roocms->part) {
			# create
			case 'create':
				if(isset($POST->create_unit) || isset($POST->create_unit_ae)) $this->create_unit();
				else {
					# groups
					$groups = array();
					$q = $db->query("SELECT gid, title, users FROM ".USERS_GROUP_TABLE." ORDER BY gid ASC");
					while($row = $db->fetch_assoc($q)) {
						$groups[] = $row;
					}

					# шаблонизируем... (слово то какое...)
					$smarty->assign("groups", $groups);
					$content = $tpl->load_template("structure_create", true);
				}
				break;

			# edit and update
			case 'edit':
				if(isset($POST->update_unit) || isset($POST->update_unit_ae)) $this->update_unit($this->sid);
				elseif($this->sid != 0) $content = $this->edit_unit($this->sid);
				else go(CP);
				break;

			# delete
			case 'delete':
                		$this->delete_unit($this->sid);
				break;

			default:
				$content = $tpl->load_template("structure_tree", true);
				break;
		}

		# отрисовываем шаблон
		$smarty->assign('content', $content);
		$tpl->load_template("structure");
	}


	/**
	* Создаем новый структурный элемент
	*/
	private function create_unit() {

		global $db, $img, $parse, $POST;

		# предупреждаем возможные ошибки с алиасом структурной еденицы
		if(isset($POST->alias)) {

		    	# избавляем URI от возможных конвульсий
		    	$POST->alias = strtr($POST->alias, array('-'=>'_','='=>'_'));

		    	# а так же проверяем что бы алиас не оказался числом
		    	if(is_numeric($POST->alias)) $POST->alias .= randcode(3, "abcdefghijklmnopqrstuvwxyz");
		}

		# title
		if(!isset($POST->title) || trim($POST->title) == "") $parse->msg("Не указано название страницы.", false);
		# alias
		if(!isset($POST->alias) || trim($POST->alias) == "") {
			$POST->alias = $parse->text->transliterate($POST->title,"lower");
			$POST->alias = preg_replace(array('(\s\s+)','(\-\-+)','(__+)','([^a-zA-Z0-9\-_])'), array('','','',''), $POST->alias);
		}

		if(!$this->check_alias($POST->alias)) $parse->msg("Алиас страницы не уникален.", false);

		# group access
		if(isset($POST->gids) && is_array($POST->gids)) $POST->gids = implode(",", $POST->gids);
		else $POST->gids = 0;

		# thumbnail check
		$img->check_post_thumb_parametrs();


		if(!isset($_SESSION['error'])) {
			$POST->sort = round($POST->sort);

			// FIXME: replace this query
			# проверяем тип родителя
			$q = $db->query("SELECT page_type FROM ".STRUCTURE_TABLE." WHERE id='".$POST->parent_id."'");
			$d = $db->fetch_assoc($q);

			# Нельзя к лентам добавлять другие дочерние элементы, кроме таких же лент.
			if($d['page_type'] == "feed" && $POST->page_type != "feed") {
				$parse->msg("Вы не можете установить для ленты в качестве дочерней страницы другой структурный элемент, кроме ленты.", false);
				goback();
			}

			# добавляем структурную еденицу
			$db->query("INSERT INTO ".STRUCTURE_TABLE."    (alias, title, parent_id, group_access, page_type, meta_description, meta_keywords, noindex, sort, date_create, date_modified, thumb_img_width, thumb_img_height)
								VALUES ('".$POST->alias."', '".$POST->title."', '".$POST->parent_id."', '".$POST->gids."', '".$POST->page_type."', '".$POST->meta_description."', '".$POST->meta_keywords."', '".$POST->noindex."', '".$POST->sort."', '".time()."', '".time()."', '".$POST->thumb_img_width."', '".$POST->thumb_img_height."')");
			$sid = $db->insert_id();

			# create body unit for html & php pages
			switch($POST->page_type) {
				case 'html':
					$db->query("INSERT INTO ".PAGES_HTML_TABLE." (sid, date_modified) VALUE ('".$sid."', '".time()."')");
					# get body unit id
					$page_id = $db->insert_id();
					$db->query("UPDATE ".STRUCTURE_TABLE." SET page_id='".$page_id."', rss='0' WHERE id='".$sid."'");
					break;

				case 'php':
					$db->query("INSERT INTO ".PAGES_PHP_TABLE." (sid, date_modified) VALUE ('".$sid."', '".time()."')");
					# get body unit id
					$page_id = $db->insert_id();
					$db->query("UPDATE ".STRUCTURE_TABLE." SET page_id='".$page_id."', rss='0' WHERE id='".$sid."'");
					break;
			}

			# пересчитываем "детей"
			$this->count_childs($POST->parent_id);

			# уведомление
			$parse->msg("Структурная еденица успешно добавлена.");

			# переход
			if(isset($POST->create_unit_ae)) go(CP."?act=structure");
			else {
				if($POST->page_type == "feed") go(CP."?act=feeds&page=".$sid);
				else go(CP."?act=pages&part=edit&page=".$sid);
			}
		}
		else goback();
	}


	/**
	 * Функция редактирования элемента структуры
	 *
	 * @param int $sid - уникальный идентификатор структурной едеицы
	 *
	 * @return data
	 */
	private function edit_unit($sid) {

		global $db, $smarty, $tpl;

		$q = $db->query("SELECT id, parent_id, group_access, alias, title, meta_description, meta_keywords, noindex, sort, page_type, thumb_img_width, thumb_img_height FROM ".STRUCTURE_TABLE." WHERE id='".$sid."'");
		$data = $db->fetch_assoc($q);

		# check group access
		if(trim($data['group_access']) != "0") {
			$gids = explode(",", $data['group_access']);
			$gids = array_flip($gids);
		}
		else $gids[0] = 0;

		# list groups
		$groups = array();
		$q = $db->query("SELECT gid, title, users FROM ".USERS_GROUP_TABLE." ORDER BY gid ASC");
		while($row = $db->fetch_assoc($q)) {
			$groups[] = $row;
		}

		# шаблонизируем... (слово то какое...)
		$smarty->assign("gids", $gids);
		$smarty->assign("groups", $groups);
		$smarty->assign("data", $data);

		return $tpl->load_template("structure_edit", true);
	}


	/**
	 * Обновляем элемент структуры
	 *
	 * @param $sid
	 *
	 * @internal param int $id - Идентификатор структурной еденицы
	 */
	private function update_unit($sid) {

		global $db, $img, $parse, $POST;

		# Если идентификатор не прошел проверку
		if($sid == 0) go(CP."?act=structure");

		# предупреждаем возможные ошибки с алиасом структурной еденицы
		if(isset($POST->alias) && trim($POST->alias) != "") {

			    # избавляем URI от возможных конвульсий
			    $POST->alias = strtr($POST->alias, array('-'=>'_','='=>'_'));

			    # а так же проверяем что бы алиас не оказался числом
			    if(is_numeric($POST->alias)) $POST->alias .= randcode(3, "abcdefghijklmnopqrstuvwxyz");
		}

		# Проверяем на ошибки
		# title
		if(!isset($POST->title) || trim($POST->title) == "") $parse->msg("Не указано название страницы.", false);
		# alias
		if(!isset($POST->alias) || trim($POST->alias) == "") {
			$POST->alias = $parse->text->transliterate($POST->title,"lower");
			$POST->alias = preg_replace(array('(\s\s+)','(\-\-+)','(__+)','([^a-zA-Z0-9\-_])'), array('','','',''), $POST->alias);
		}
		elseif(!$this->check_alias($POST->alias, $POST->old_alias)) $parse->msg("Алиас страницы не уникален.", false);

		# group access
		if(isset($POST->gids) && is_array($POST->gids)) $POST->gids = implode(",", $POST->gids);
		else $POST->gids = 0;

		# thumbnail check
		$img->check_post_thumb_parametrs();

		if(!isset($_SESSION['error'])) {
			$POST->sort = round($POST->sort);

			# Нельзя менять родителя у главной страницы
			If($sid == 1) $POST->parent_id = 0;

			# Если мы назначаем нового родителя
			if($POST->parent_id != $POST->now_parent_id) {

				# Проверим, что не пытаемся быть родителем самим себе
				if($POST->parent_id == $sid) {
					$POST->parent_id = $POST->now_parent_id;
					$parse->msg("Не удалось изменить иерархию! Вы не можете изменить иерархию директории назначив её родителем самой себе!", false);
				}
				# ... и что новый родитель это не наш ребенок
				else {
					$childs = $this->engine->load_tree($sid);

					if($childs) {
						foreach($childs AS $k=>$v) {
							if($POST->parent_id == $v['id']) {
								$POST->parent_id = $POST->now_parent_id;
								$parse->msg("Не удалось изменить иерархию! Вы не можете изменить иерархию директории переместив её в свой дочерний элемент!", false);
							}
						}
					}
				}
			}

			# проверяем тип родителя
			$q = $db->query("SELECT page_type FROM ".STRUCTURE_TABLE." WHERE id='".$POST->parent_id."'");
			$p = $db->fetch_assoc($q);

			# проверяем тип текущей страницы
			$q = $db->query("SELECT page_type FROM ".STRUCTURE_TABLE." WHERE id='".$sid."'");
			$n = $db->fetch_assoc($q);

			# Нельзя к лентам добавлять другие дочерние элементы, кроме таких же лент.
			if($p['page_type'] == "feed" && $n['page_type'] != "feed") {
				$parse->msg("Вы не можете установить для ленты в качестве дочерней страницы другой структурный элемент, кроме ленты.", false);
				$POST->parent_id = $POST->now_parent_id;
			}

			# Нельзя изменять алиас главной страницы
			if($sid == 1 && $POST->alias != "index") {
				$POST->alias = "index";
				$parse->msg("Нельзя изменять алиас главной страницы!", false);
			}

			# DB
			$db->query("UPDATE ".STRUCTURE_TABLE."
					SET
						alias='".$POST->alias."',
						title='".$POST->title."',
						parent_id='".$POST->parent_id."',
						group_access='".$POST->gids."',
						meta_description='".$POST->meta_description."',
						meta_keywords='".$POST->meta_keywords."',
						noindex='".$POST->noindex."',
						sort='".$POST->sort."',
						date_modified='".time()."',
						thumb_img_width='".$POST->thumb_img_width."',
						thumb_img_height='".$POST->thumb_img_height."'
					WHERE
						id='".$sid."'");

			# Если мы назначаем нового родителя
			if($POST->parent_id != $POST->now_parent_id) {
				# пересчитываем "детей"
				$this->count_childs($POST->parent_id);
				$this->count_childs($POST->now_parent_id);
			}

			# уведомление
			$parse->msg("Страница успешно обновлена.");


			if(isset($POST->update_unit_ae)) go(CP."?act=structure");
			else go(CP."?act=structure&part=edit&id=".$sid);
		}
		else goback();
	}


	/**
	 * Удаляем структурный элемент
	 *
	 * @param $sid
	 *
	 * @internal param int $id
	 */
	private function delete_unit($sid) {

		global $db, $parse;

		# Если идентификатор не прошел проверку
		if($sid == 0) go(CP."?act=structure");

		$q = $db->query("SELECT childs, parent_id, page_id, page_type FROM ".STRUCTURE_TABLE." WHERE id='".$sid."'");
		$c = $db->fetch_assoc($q);

		if($c['childs'] == 0) {
			# del content html
			if($c['page_type'] == "html") {
				require_once _ROOCMS."/acp/pages_html.php";
				$this->unit = new ACP_PAGES_HTML;

				$this->unit->delete($sid);
			}
			# del content php
			elseif($c['page_type'] == "php") {
				require_once _ROOCMS."/acp/pages_php.php";
				$this->unit = new ACP_PAGES_PHP;

				$this->unit->delete($sid);
			}
			# del content feed
			elseif($c['page_type'] == "feed") {
				$feeds_data = array(
					'id'			=> $this->engine->page_id,
					'alias'			=> $this->engine->page_alias,
					'title'			=> $this->engine->page_title,
					'rss'			=> $this->engine->page_rss,
					'items_per_page'	=> $this->engine->page_items_per_page,
					'items_sorting'		=> $this->engine->page_items_sorting,
					'thumb_img_width'	=> $this->engine->page_thumb_img_width,
					'thumb_img_height'	=> $this->engine->page_thumb_img_height
				);

				require_once _ROOCMS."/acp/feeds_feed.php";
				$this->unit = new ACP_FEEDS_FEED($feeds_data);

				$this->unit->delete_feed($sid);
			}


			# structure unit
			$db->query("DELETE FROM ".STRUCTURE_TABLE." WHERE id='".$sid."'");


			# уведомление
			$parse->msg("Страница успешно удалена");

			# recount parent childs
			$this->count_childs($c['parent_id']);
		}
		else $parse->msg("Невозможно удалить страницу, по причине имеющихся у страницы дочерних связей. Сначала перенесите или удалите дочерние страницы.", false);

		# переход
		goback();
	}


	/**
	 * Проверяем "алиас" на уникальность
	 *
	 * ВНИМАНИЕ! Не расчитывайте на эту функцию, она временная.
	 *
	 * @param string $name    - алиас
	 * @param string $without - Выражение исключения для mysql запроса
	 *
	 * @return bool $res - true - если алиас не уникален, false - если алиас уникален
	 */
	private function check_alias($name, $without="") {

		global $db;

		$res = false;

		if(trim($without) != trim($name)) {

			$w = (trim($without) != "") ? "alias!='".$without."'" : "" ;

			if(!$db->check_id($name, STRUCTURE_TABLE, "alias", $w)) $res = true;
		}
		else $res = true;

		return $res;
	}


	/**
	* Пересчитываем "детей"
	*
	* @param int $id
	*/
	private function count_childs($id) {

		global $db, $parse;

		$q = $db->query("SELECT count(*) FROM ".STRUCTURE_TABLE." WHERE parent_id='".$id."'");
		$c = $db->fetch_row($q);

		$db->query("UPDATE ".STRUCTURE_TABLE." SET childs='".$c[0]."' WHERE id='".$id."'");

		# уведомление
		if(DEBUGMODE) $parse->msg("Информация о вложенных (подструктурных) страницах для страницы {$id} обновлена.");
	}
}

/**
 * Init Class
 */
$acp_structure = new ACP_STRUCTURE;

?>