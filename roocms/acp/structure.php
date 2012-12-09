<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Structure site settings
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.11
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
*   Данное программное обеспечение является свободным и распространяется
*   по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
*   При любом использовании данного ПО вы должны соблюдать все условия
*   лицензии.
*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('ACP')) die('Access Denied');
//#########################################################

$acp_structure = new ACP_STRUCTURE;

class ACP_STRUCTURE {

	# vars
	private	$engine;
	private $unit;



	/**
    * Lets mortal kombat begin
    *
    */
	function __construct() {

		require_once _CLASS."/class_structure.php";
		$this->engine = new Structure();

		// initialise
		$this->init();
	}


	/**
    * initialisation action
    *
    */
	private function init() {

		global $roocms, $db, $tpl, $smarty, $GET;

		# read tree
		$tree = $this->engine->load_tree();
		$smarty->assign('tree', $tree);

		# read page types
		$page_types = array();
		foreach($this->engine->page_types AS $key=>$value) {
			if($value['enable']) $page_types[$key] = $value['title'];
		}
		$smarty->assign('page_types', $page_types);

		# create
		if($roocms->part == "create") {
			if(@$_REQUEST['create_unit']) $this->create_unit();
			else $content = $tpl->load_template("structure_create", true);
		}
		# delete
		elseif($roocms->part == "delete" && isset($GET->_id) && $db->check_id($GET->_id, STRUCTURE_TABLE)) {
			$this->delete_unit($GET->_id);
		}
		# edit
		elseif($roocms->part == "edit" && isset($GET->_id) && $db->check_id($GET->_id, STRUCTURE_TABLE)) {
			if(@$_REQUEST['update_unit']) $this->update_unit($GET->_id);
			else {
				$q = $db->query("SELECT id, parent_id, alias, title, meta_description, meta_keywords, noindex, sort, type FROM ".STRUCTURE_TABLE." WHERE id='".$GET->_id."'");
				$data = $db->fetch_assoc($q);

				$smarty->assign("data", $data);
				$content = $tpl->load_template("structure_edit", true);
			}
		}
		else {
			$content = $tpl->load_template("structure_tree", true);
		}

		# draw template
		$smarty->assign('content',		$content);

		$tpl->load_template("structure");
	}


	/**
    * Создаем новый структурный элемент
    *
    */
	private function create_unit() {

		global $db, $parse, $POST;

        # предупреждаем возможные ошибки с алиасом структурной еденицы
        if(isset($POST->alias) && trim($POST->alias) != "") {

            # избавляем URI от возможных конвульсий
            $POST->alias = strtr($POST->alias, array('-'=>'_','='=>'_'));

            # а так же проверяем что бы алиас не оказался числом
            if(is_numeric($POST->alias)) $POST->alias = randcode(3, "abcdefghijklmnopqrstuvwxyz").$POST->alias;
        }

		if(trim($POST->title) == "") 								$parse->msg("Не указано название страницы.", false);
		if(trim($POST->alias) == "" && round($POST->alias) != 0) 	$parse->msg("Не указан алиас страницы.", false);
		elseif(!$this->check_alias($POST->alias)) 					$parse->msg("Алиас страницы не уникален.", false);


		if(!isset($_SESSION['error'])) {
			$POST->sort = round($POST->sort);

			# проверяем тип родителя
			$q = $db->query("SELECT type FROM ".STRUCTURE_TABLE." WHERE id='".$POST->parent_id."'");
			$d = $db->fetch_assoc($q);

			# Нельзя к лентам добавлять другие дочерние элементы, кроме таких же лент.
			if($d['type'] == "feed" && $POST->type != "feed") {
				$parse->msg("Вы не можете установить для ленты в качестве дочерней страницы другой структурный элемент, кроме ленты.", false);
				goback();
			}

			# добавляем структурную еденицу
			$db->query("INSERT INTO ".STRUCTURE_TABLE." (alias, title, parent_id, type, meta_description, meta_keywords, noindex, sort, date_create, date_modified)
											 VALUES ('".$POST->alias."', '".$POST->title."', '".$POST->parent_id."', '".$POST->type."', '".$POST->meta_description."', '".$POST->meta_keywords."', '".$POST->noindex."', '".$POST->sort."', '".time()."', '".time()."')");
			$sid = $db->insert_id();

			# create body unit for html & php pages
			switch($POST->type) {
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

			# recount childs
			$this->count_childs($POST->parent_id);

			# notice
			$parse->msg("Структурная еденица успешно добавлена.");

			# go
			if($POST->type == "feed") go(CP."?act=feeds&page=".$sid);
			else go(CP."?act=pages&part=edit&page=".$sid);
		}
		else goback();
	}


	/**
    * Обновляем элемент структуры
    *
    * @param int $id - Идентификатор структурной еденицы
    */
	private function update_unit($id) {

		global $db, $parse, $POST;

		if(trim($POST->title) == "") 								$parse->msg("Не указано название страницы.", false);
		if(trim($POST->alias) == "" && round($POST->alias) != 0) 	$parse->msg("Не указан алиас страницы.", false);
		elseif(!$this->check_alias($POST->alias, $POST->old_alias)) $parse->msg("Алиас страницы не уникален.", false);

        # предупреждаем возможные ошибки с алиасом структурной еденицы
        if(isset($POST->alias) && trim($POST->alias) != "") {

            # избавляем URI от возможных конвульсий
            $POST->alias = strtr($POST->alias, array('-'=>'_','='=>'_'));

            # а так же проверяем что бы алиас не оказался числом
            if(is_numeric($POST->alias)) $POST->alias = randcode(3, "abcdefghijklmnopqrstuvwxyz").$POST->alias;
        }

		if(!isset($_SESSION['error'])) {
			$POST->sort = round($POST->sort);

			# Нельзя менять родителя у главной страницы
			If($id == 1) $POST->parent_id = 0;

			# Если мы назначаем нового родителя
			if($POST->parent_id != $POST->now_parent_id) {

				# Проверим, что не пытаемся быть родителем самим себе
				if($POST->parent_id == $id) {
					$POST->parent_id = $POST->now_parent_id;
					$parse->msg("Не удалось изменить иерархию! Вы не можете изменить иерархию директории назначив её родителем самой себе!", false);
				}
				# ... и что новый родитель это не наш ребенок
				else {
					$childs = $this->engine->load_tree($id);

					for($i=0;$i<=count($childs)-1;$i++) {
						if($POST->parent_id == $childs[$i]['id']) {
							$POST->parent_id = $POST->now_parent_id;
							$parse->msg("Не удалось изменить иерархию! Вы не можете изменить иерархию директории переместив её в свой дочерний элемент!", false);
						}
					}
				}
			}

			# проверяем тип родителя
			$q = $db->query("SELECT type FROM ".STRUCTURE_TABLE." WHERE id='".$POST->parent_id."'");
			$p = $db->fetch_assoc($q);

			# проверяем тип текущей страницы
			$q = $db->query("SELECT type FROM ".STRUCTURE_TABLE." WHERE id='".$id."'");
			$n = $db->fetch_assoc($q);

			# Нельзя к лентам добавлять другие дочерние элементы, кроме таких же лент.
			if($p['type'] == "feed" && $n['type'] != "feed") {
				$parse->msg("Вы не можете установить для ленты в качестве дочерней страницы другой структурный элемент, кроме ленты.", false);
				$POST->parent_id = $POST->now_parent_id;
			}

			# Нельзя изменять алиас главной страницы
			if($id == 1 && $POST->alias != "index") {
				$POST->alias = "index";
				$parse->msg("Нельзя изменять алиас главной страницы!", false);
			}

			$POST->alias = strtr($POST->alias, array('-'=>'_','='=>'_'));

			# DB
			$db->query("UPDATE ".STRUCTURE_TABLE." SET alias='".$POST->alias."', title='".$POST->title."', parent_id='".$POST->parent_id."', meta_description='".$POST->meta_description."', meta_keywords='".$POST->meta_keywords."', noindex='".$POST->noindex."',sort='".$POST->sort."', date_modified='".time()."' WHERE id='".$id."'");


			# Если мы назначаем нового родителя
			if($POST->parent_id != $POST->now_parent_id) {
				# recount childs
				$this->count_childs($POST->parent_id);
				$this->count_childs($POST->now_parent_id);
			}


			# notice
			$parse->msg("Страница успешно обновлена.");


			go(CP."?act=structure");
		}
		else goback();
	}


	//#####################################################
	//	Delete unit
	private function delete_unit($id) {

		global $db, $parse;

		$q = $db->query("SELECT childs, parent_id, page_id, type FROM ".STRUCTURE_TABLE." WHERE id='".$id."'");
		$c = $db->fetch_assoc($q);

		if($c['childs'] == 0) {
			# del content html
			if($c['type'] == "html") {
				require_once _ROOCMS."/acp/pages_html.php";
				$this->unit = new ACP_PAGES_HTML;

				$this->unit->delete($id);
			}
			# del content php
			elseif($c['type'] == "php")	{
				require_once _ROOCMS."/acp/pages_php.php";
				$this->unit = new ACP_PAGES_PHP;

				$this->unit->delete($id);
			}
			# del content feed
			elseif($c['type'] == "feed") {
				require_once _ROOCMS."/acp/feeds_feed.php";
				$this->unit = new ACP_FEEDS_FEED;

				$this->unit->delete_feed($id);
			}


			# structure unit
			$db->query("DELETE FROM ".STRUCTURE_TABLE." WHERE id='".$id."'");


			# notice
			$parse->msg("Страница успешно удалена");

			# recount parent childs
			$this->count_childs($c['parent_id']);
		}
		else $parse->msg("Невозможно удалить страницу, по причине имеющихся у страницы дочерних связей. Сначала перенесите или удалите дочерние страницы.", false);

		# go
		goback();
	}


	//#####################################################
	//	Check unique alias
	private function check_alias($name, $without="") {

		global $db;

		$w = "";
		if(trim($without) != "") $w = " AND alias!='".$without."'";

		$q = $db->query("SELECT count(*) FROM ".STRUCTURE_TABLE." WHERE alias='".$name."'".$w);
		$c = $db->fetch_row($q);


		if($c[0] == 0) return true;
		else return false;
	}


	//#####################################################
	//	Recount childs
	private function count_childs($id) {

		global $db, $debug, $parse;

		$q = $db->query("SELECT count(*) FROM ".STRUCTURE_TABLE." WHERE parent_id='".$id."'");
		$c = $db->fetch_row($q);

		$db->query("UPDATE ".STRUCTURE_TABLE." SET childs='".$c[0]."' WHERE id='".$id."'");

		# notice
		if($debug->debug) $parse->msg("Информация о детях страницы {$id} обновлена.");
	}
}

?>