<?php
/**
* @package      RooCMS
* @subpackage	Engine RooCMS classes
* @subpackage	Structure Class
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.1.1
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
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


class Structure {

	# vars
	public $page_types	= array('html'	=> array('enable'	=> true, 'title'	=> 'HTML страница'),
								'php'	=> array('enable'	=> true, 'title'	=> 'PHP страница'),
								'feed'	=> array('enable'	=> true, 'title'	=> 'Лента'));
	public $mites		= array();

	# page vars
	public $page_id				= 1;						# [int]		page id
	public $page_pid			= 1;						# [int]		id content
	public $page_alias			= "index";					# [string]	alias name
	public $page_title			= "Hello World [RooCMS]";	# [string]	title page
	public $page_meta_desc		= "";						# [string]	Meta description
	public $page_meta_keys		= "";						# [string]	Meta keywords
	public $page_noindex		= 0;						# [bool?]		Meta noindex
	public $page_type			= "html";					# [string]	page type
	public $page_rss			= 0;						# [bool?]		on/off RSS feed
	public $page_items_per_page	= 10;						# [int]		show items on per page
	public $page_items			= 0;						# [int]		show amount items on feed



	/**
	* Let's go
	*
	*/
	function Structure() {

		global $db, $GET;

		# const for default structure id
		if(!defined('PAGEID')) define('PAGEID', '1');

		# init page vars
		if(isset($GET->_page)) {
			$q = $db->query("SELECT id, page_id, alias, title, meta_description, meta_keywords, noindex, type, rss, items_per_page, items FROM ".STRUCTURE_TABLE." WHERE id='".$GET->_page."' OR alias='".$GET->_page."'");
			$row = $db->fetch_assoc($q);
			if(!empty($row)) $this->set_page_vars($row);
			# load index page
			else {
				$q = $db->query("SELECT id, page_id, alias, title, meta_description, meta_keywords, noindex, type, rss, items_per_page, items FROM ".STRUCTURE_TABLE." WHERE id='".PAGEID."'");
				$row = $db->fetch_assoc($q);
				$this->set_page_vars($row);
			}
		}
		# deafult load index
		else {
			$q = $db->query("SELECT id, page_id, alias, title, meta_description, meta_keywords, noindex, type, rss, items_per_page, items FROM ".STRUCTURE_TABLE." WHERE id='".PAGEID."'");
			$row = $db->fetch_assoc($q);
			$this->set_page_vars($row);
		}
	}


	/* ####################################################
	 * Pages Tree construction step 1: select
	 */
	public function load_tree($parent=0, $maxlevel=0, $child=true) {

		global $db;

		$q = $db->query("SELECT id, alias, parent_id, sort, title, noindex, type, childs, page_id, rss, items_per_page, items FROM ".STRUCTURE_TABLE." ORDER BY sort ASC");
		while($row = $db->fetch_assoc($q)) {
			$row['level']	= 0;
			$tree[] = $row;
		}


		# construct tree
		if(isset($tree)) {
			$tree = $this->construct_tree($tree, $parent, $maxlevel, $child);

			# be back
			return $tree;
		}
		else return false;
	}


	/* ####################################################
	 * Pages Tree construction step 2: construct
	 */
	public function construct_tree($unit, $parent=0, $maxlevel=0, $child=true, $level=0) {

		# create array
		if($level == 0) $tree = array();

		foreach($unit AS $i=>$value) {
			if($unit[$i]['parent_id'] == $parent) {
				# update indention
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
	* @param array $data параметры вызванной страницы
	*/
	private function set_page_vars($data) {

		global $config, $smarty;

        # set vars
		$this->page_id 				= $data['id'];
		$this->page_pid 			= $data['page_id'];
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


	/* ####################################################
	 *	Load attached image
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