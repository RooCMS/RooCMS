<?php
/**
* @package      RooCMS
* @subpackage	Engine RooCMS classes
* @subpackage	Structure Class
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.2.3
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*	RooCMS - Russian free content managment system
*   Copyright (C) 2010-2014 alex Roosso aka alexandr Belov info@roocms.com
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
*   along with this program.  If not, see <http://www.gnu.org/licenses/
*
*
*   RooCMS - Русская бесплатная система управления сайтом
*   Copyright (C) 2010-2014 alex Roosso (александр Белов) info@roocms.com
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
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


class Structure {

	# vars
	public $page_types	= array('html'	=> array('enable'	=> true, 'title'	=> 'HTML страница'),
								'php'	=> array('enable'	=> true, 'title'	=> 'PHP страница'),
								'feed'	=> array('enable'	=> true, 'title'	=> 'Лента'));

	public $mites		= array();

	public $sitetree	= array();

	# page vars
	public $page_id				= 1;							# [int]		page id
	public $page_pid			= 1;							# [int]		id content
	public $page_parent			= 0;							# [int]		Parent id
	public $page_alias			= "index";						# [string]	alias name
	public $page_title			= "Добро пожловать [RooCMS]";	# [string]	title page
	public $page_meta_desc		= "";							# [string]	Meta description
	public $page_meta_keys		= "";							# [string]	Meta keywords
	public $page_noindex		= 0;							# [bool?]	Meta noindex
	public $page_type			= "html";						# [string]	page type
	public $page_rss			= 0;							# [bool?]	on/off RSS feed
	public $page_items_per_page	= 10;							# [int]		show items on per page
	public $page_items			= 0;							# [int]		show amount items on feed



	/**
	* Запускаем класс
	*
	* @param boolean $tree - true для инициализации структуры сайта
	* @param boolean $ui - использовать true только в пользовательском интерфейсе.
	*/
	function Structure($tree=true, $ui=true) {

		global $db, $GET, $smarty;

		# load site tree
		if($tree)
        	$this->sitetree = $this->load_tree();

        # user interface loaded
        if($ui) {
			# const for default structure id
			if(!defined('PAGEID')) define('PAGEID', $this->page_id);

			# init page vars
			if(isset($GET->_page)) {
				$q = $db->query("SELECT id, page_id, parent_id, alias, title, meta_description, meta_keywords, noindex, type, rss, items_per_page, items FROM ".STRUCTURE_TABLE." WHERE id='".$GET->_page."' OR alias='".$GET->_page."'");
				$row = $db->fetch_assoc($q);
				if(!empty($row)) $this->set_page_vars($row);
				# load index page
				else {
					$q = $db->query("SELECT id, page_id, parent_id, alias, title, meta_description, meta_keywords, noindex, type, rss, items_per_page, items FROM ".STRUCTURE_TABLE." WHERE id='".PAGEID."'");
					$row = $db->fetch_assoc($q);
					$this->set_page_vars($row);
				}
			}
			# deafult load index
			else {
				$q = $db->query("SELECT id, page_id, parent_id, alias, title, meta_description, meta_keywords, noindex, type, rss, items_per_page, items FROM ".STRUCTURE_TABLE." WHERE id='".PAGEID."'");
				$row = $db->fetch_assoc($q);
				$this->set_page_vars($row);
			}

			# mites
			if($this->page_parent != 0) {
				$this->construct_mites($this->page_id);
				krsort($this->mites);
			}

			$smarty->assign('mites', $this->mites);
		}
	}


	/**
	* Собираем дерево "сайта" (шаг 1)
	*
	* @param int $parent - идентификатор родителя от которого расчитываем "дерево". Указываем его только если хотим не все дерево расчитать, а лишь его часть
	* @param int $maxlevel - указываем уровень глубины построения дерева, только если не хотим выводить все дерево.
	* @param boolean $child - укажите false если не хотите расчитывать подуровни.
	*/
	public function load_tree($parent=0, $maxlevel=0, $child=true) {

		global $db;
		static $use = false;

		if(!$use) {
			$q = $db->query("SELECT id, alias, parent_id, sort, title, noindex, type, childs, page_id, rss, items_per_page, items FROM ".STRUCTURE_TABLE." ORDER BY sort ASC");
			while($row = $db->fetch_assoc($q)) {
				$row['level']	= 0;
				$tree[] 		= $row;
			}

			$use = true;
		}
		else $tree = $this->sitetree;

		# construct tree
		if(isset($tree)) {
			$tree = $this->construct_tree($tree, $parent, $maxlevel, $child);

			# be back
			return $tree;
		}
		else return false;
	}


	/**
	* Собираем дерево "помощи" (шаг 2)
	*
	* @param array $unit - массив данных "дерева"
	* @param int $parent - идентификатор родителя от которого расчитываем "дерево". Указываем его только если хотим не все дерево расчитать, а лишь его часть
	* @param int $maxlevel - указываем уровень глубины построения дерева, только если не хотим выводить все дерево.
	* @param boolean $child - укажите false если не хотите расчитывать подуровни.
	* @param int $level - текущий обрабатываемый уровень (используется прирасчете дочерних страниц)
	*/
	private function construct_tree($unit, $parent=0, $maxlevel=0, $child=true, $level=0) {

		# create array
		if($level == 0) $tree = array();

		foreach($unit AS $i=>$value) {
			if($unit[$i]['parent_id'] == $parent) {
				# update level
				$unit[$i]['level'] = $level;

				# add branch(s)
				$tree[] = $unit[$i];

				# check child
				if($child && ($maxlevel == 0 || $level+1 <= $maxlevel)) {
					$subtree = $this->construct_tree($unit, $unit[$i]['id'], $maxlevel, $child, $level + 1);
					if(is_array($subtree)) $tree = array_merge($tree, $subtree);
				}
			}
		}


		# be back
		if(!empty($tree)) return $tree;
	}


	/**
	* Устанавливает основные переменные класса
	* В так же передает в шаблоны глобальные перенменные:
	*   Идентификатор структурной еденицы
	*   Алиас структурной еденицы
	*   Заголовок структурной еденицы
	*
	* @param array $data - параметры вызванной страницы
	*/
	private function set_page_vars($data) {

		global $config, $smarty;

        # set vars
		$this->page_id 				= $data['id'];
		$this->page_pid 			= $data['page_id'];
		$this->page_parent 			= $data['parent_id'];
		$this->page_alias 			= $data['alias'];
		$this->page_title 			= $data['title'];
		if(isset($config->meta_description))
			$this->page_meta_desc 	= (trim($data['meta_description']) != "") ? $data['meta_description'] : $config->meta_description;
		if(isset($config->meta_keywords))
			$this->page_meta_keys 	= (trim($data['meta_keywords']) != "") ? $data['meta_keywords'] : $config->meta_keywords;
        $this->page_noindex         = $data['noindex'];
		$this->page_type 			= $data['type'];
		$this->page_rss 			= $data['rss'];
		$this->page_items_per_page 	= $data['items_per_page'];
		$this->page_items 			= $data['items'];

        # set smarty vars
        $smarty->assign("page_id",      $data['id']);
        $smarty->assign("page_alias",   $data['alias']);
        $smarty->assign("page_title",   $data['title']);
	}


	/**
	* Собираем хлебные крошки
	*
	* @param int $id - идентификатор текущей страницы
	*/
	private function construct_mites($id = 1) {
		if($id != 1) {
			foreach($this->sitetree AS $k=>$v) {
				if($v['id'] == $id) {
					$this->mites[] = array('id'		=> $v['id'],
										   'alias'	=> $v['alias'],
										   'title'	=> $v['title']);

					if($v['parent_id'] != 0) $this->construct_mites($v['parent_id']);
				}
			}
		}
	}


	/**
	* Загружаем присоедененные изображения
	*
	* @param string $where - параметр указывающий на элемент к которому прикреплены изображения
	* @param int $from - стартовая позиция для загрузки изображений
	* @param int $limit - лимит загружаемых изображений
	*
	* @return array $data - массив с данными об изображениях.
	*/
	public function load_images($where, $from = 0, $limit = 0) {

		global $db;

		$data = array();

		$l = ($limit != 0) ? "LIMIT ".$from.",".$limit : "" ;

		$q = $db->query("SELECT id, filename, sort, alt FROM ".IMAGES_TABLE." WHERE attachedto='".$where."' ORDER BY sort ASC ".$l);
		while($img = $db->fetch_assoc($q)) {
			$data[] = $img;
		}

		return $data;
	}

}

?>