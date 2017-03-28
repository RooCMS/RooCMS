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
* @subpackage	Engine RooCMS classes
* @author       alex Roosso
* @copyright    2010-2018 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.5
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
 * Class Structure
 */
class Structure {

	# vars
	public $content_types		= array('html'	=> array('enable' => true, 'title' => 'HTML страница'),
						'php'	=> array('enable' => true, 'title' => 'PHP страница'),
						'feed'	=> array('enable' => true, 'title' => 'Лента'));

	# bread cumb
	public $breadcumb		= array();

	# site tree
	public $sitetree		= array();

	# on/off (release in future)
	public $access			= true;

	# aliases
	private $aliases 		= array();

	# page vars
	public $page_id			= 1;				# [int]		page sid
	public $page_pid		= 1;				# [int]		id content
	public $page_parent		= 0;				# [int]		Parent id
	public $page_alias		= "index";			# [string]	unique alias name
	public $page_title		= "Добро пожаловать [RooCMS]";	# [string]	title page
	public $page_meta_desc		= "";				# [string]	Meta description
	public $page_meta_keys		= "";				# [string]	Meta keywords
	public $page_noindex		= false;			# [bool]	Meta noindex
	public $page_type		= "html";			# [string]	page type
	public $page_group_access	= array(0);			# [array]	allowed acces to user group (sep. comma)
	public $page_rss		= false;			# [bool]	on/off RSS feed
	public $page_show_child_feeds	= 'none';			# [string]	feed option for show childs feed
	public $page_items_per_page	= 10;				# [int]		show items on per page
	public $page_items_sorting	= "datepublication";		# [string]	type sorting for feed
	public $page_items		= 0;				# [int]		show amount items on feed
	public $page_thumb_img_width	= 0;				# [int]		in pixels
	public $page_thumb_img_height	= 0;				# [int]		in pixels



	/**
	* Запускаем класс
	*
	* @param boolean $ui - использовать true только в клиентском интерфейсе.
	*/
	public function __construct($ui=true) {

		global $db, $GET;

		# load site tree
		$this->sitetree = $this->load_tree();

		if(!empty($this->sitetree)) {
			$this->update_tree_parent();
		}

        	# user interface loaded
        	if($ui) {
			$this->load_ui();
		}
	}


	/**
	 * Собираем дерево "сайта" (шаг 1)
	 *
	 * @param int     $parent   - идентификатор родителя от которого расчитываем "дерево". Указываем его только если хотим не все дерево расчитать, а лишь его часть
	 * @param int     $maxlevel - указываем уровень глубины построения дерева, только если не хотим выводить все дерево.
	 * @param boolean $child    - укажите false если не хотите расчитывать подуровни.
	 *
	 * @return array|bool
	 */
	public function load_tree($parent=0, $maxlevel=0, $child=true) {

		global $db;
		static $use = false;

		# Делаем единичный запрос в БД собирая данные по структуре сайта.
		if(!$use) {
			$q = $db->query("SELECT 
						id, page_id, alias, parent_id,  
						title, meta_description, meta_keywords, noindex, rss,
						page_type, sort, childs, items, show_child_feeds, group_access, 
						items_per_page, items_sorting, thumb_img_width, thumb_img_height 
					FROM ".STRUCTURE_TABLE." ORDER BY sort ASC");
			while($row = $db->fetch_assoc($q)) {
				$row['level']	  = 0;
				$row['parent']	  = 0;

				$tree[$row['id']] = $row;

				$this->aliases[$row['alias']] = $row['id'];
			}

			$use = true;
		}
		else {
			$tree = $this->sitetree;
		}

		# construct tree
		if(isset($tree)) {
			$tree = $this->construct_tree($tree, $parent, $maxlevel, $child);
			return $tree;
		}
		else {
			return false;
		}
	}


	/**
	 * Собираем дерево "сайта" (шаг 2)
	 *
	 * @param array   $unit     - массив данных "дерева"
	 * @param int     $parent   - идентификатор родителя от которого расчитываем "дерево". Указываем его только если хотим не все дерево расчитать, а лишь его часть
	 * @param int     $maxlevel - указываем уровень глубины построения дерева, только если не хотим выводить все дерево.
	 * @param boolean $child    - укажите false если не хотите расчитывать подуровни.
	 * @param int     $level    - текущий обрабатываемый уровень (используется прирасчете дочерних страниц)
	 *
	 * @return array
	 */
	private function construct_tree(array $unit, $parent=0, $maxlevel=0, $child=true, $level=0) {

		# create array
		if($level == 0) {
			$tree = array();
		}

		foreach($unit AS $i=>$value) {
			if($unit[$i]['parent_id'] == $parent) {
				# update level
				$value['level'] = $level;

				# add branch(s)
				$tree[$value['id']] = $value;

				# check child
				if($child && ($maxlevel == 0 || $level+1 <= $maxlevel)) {

					$subtree = $this->construct_tree($unit, $unit[$i]['id'], $maxlevel, $child, $level + 1);

					if(is_array($subtree)) {
						$tree = $tree + $subtree;
					}
				}
			}
		}

		# be back
		if(!empty($tree)) {
			return $tree;
		}
	}


	/**
	 * Загружаем ui data
	 */
	private function load_ui() {

		global $db, $GET;

		# const for default structure id
		if(!defined('PAGEID')) {
			define('PAGEID', 1);
		}


		# init id for vars
		$lid = PAGEID;

		if(isset($GET->_page)) {
			if(isset($this->sitetree[$GET->_page])) {
				$lid = $GET->_page;
			}

			if(isset($this->aliases[$GET->_page])) {
				$lid = $this->aliases[$GET->_page];
			}
		}

		# set vars
		$this->set_page_vars($this->sitetree[$lid]);


		# mites
		if($this->page_parent != 0) {
			$this->construct_breadcumb($this->page_id);
			krsort($this->breadcumb);
		}
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
		$this->page_id 			= $data['id'];
		$this->page_pid 		= $data['page_id'];
		$this->page_parent 		= $data['parent_id'];
		$this->page_alias 		= $data['alias'];
		$this->page_title 		= $data['title'];
		if(isset($config->meta_description)) {
			$this->page_meta_desc 	= (trim($data['meta_description']) != "") ? $data['meta_description'] : $config->meta_description;
		}
		if(isset($config->meta_keywords)) {
			$this->page_meta_keys 	= (trim($data['meta_keywords']) != "") ? $data['meta_keywords'] : $config->meta_keywords;
		}
        	$this->page_noindex		= (bool) $data['noindex'];
		$this->page_type 		= $data['page_type'];
		$this->page_group_access 	= array_flip(explode(",", $data['group_access']));
		$this->page_rss 		= (bool) $data['rss'];
		$this->page_show_child_feeds  	= $data['show_child_feeds'];
		$this->page_items_per_page 	= $data['items_per_page'];
		$this->page_items_sorting 	= $data['items_sorting'];
		$this->page_items 		= $data['items'];
		$this->page_thumb_img_width 	= $data['thumb_img_width'];
		$this->page_thumb_img_height 	= $data['thumb_img_height'];

                # set smarty vars
                $smarty->assign("page_id",      $data['id']);
                $smarty->assign("page_alias",   $data['alias']);
                $smarty->assign("page_title",   $data['title']);
	}


	/**
	 * Собираем хлебные крошки
	 *
	 * @param int $sid - идентификатор текущей страницы от которой выстраиваются "крошки"
	 */
	private function construct_breadcumb($sid = 1) {
		if($sid != 1) {
			$v = $this->get_structure_info($sid);
			$this->breadcumb[] = array('id'		=> $v['id'],
						   'alias'	=> $v['alias'],
						   'act'	=> "",
						   'part'	=> "",
						   'title'	=> $v['title'],
						   'parent'	=> $v['parent']);

			if($v['parent_id'] != 0) {
				$this->construct_breadcumb($v['parent_id']);
			}
		}
	}


	/**
	 * Функция возвращает путь к структурному элементу.
	 *
	 * @param int $sid - идентификатор текущей страницы от которой выстраиваются "крошки"
	 */
	/*function get_mites($sid = 1) {

	}*/


	/**
	 * Функция собирает информация о родителе структурного элемента.
	 */
	private function update_tree_parent() {
		foreach($this->sitetree AS $k=>$v) {
			if($v['parent_id'] != 0) {
				$this->sitetree[$k]['parent'] = $this->get_structure_info($v['parent_id']);
			}
		}
	}


	/**
	 * Функция возвращает данные о структурной еденице ввиде массива.
	 *
	 * @param $sid - идентификатор структурной еденицы
	 *
	 * @return array - данные о структурной еденице
	 */
	public function get_structure_info($sid) {
		return $this->sitetree[$sid];
	}
}

?>