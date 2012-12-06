<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS mod News
|	Author:	alex Roosso
|	Copyright: 2010-2011 (c) RooCMS. 
|	Web: http://www.roocms.com
|	All rights reserved.
|----------------------------------------------------------
|	This program is free software; you can redistribute it and/or modify
|	it under the terms of the GNU General Public License as published by
|	the Free Software Foundation; either version 2 of the License, or
|	(at your option) any later version.
|	
|	Данное программное обеспечение является свободным и распространяется
|	по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
|	При любом использовании данного ПО вы должны соблюдать все условия
|	лицензии.
|----------------------------------------------------------
|	Build date: 		7:24 30.11.2010
|	Last build: 		21:39 14.10.2011
|	Version file:		1.00 build 10
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


//*********************************************************
// Init Mod
//---------------------------------------------------------
if(!defined('THIS_MOD')) define('THIS_MOD', 'NEWS');
//*********************************************************


// mysql tables ===========================================
if(!defined('NEWS_ITEM'))		define('NEWS_ITEM', 	DB_PREFIX.'news__item');
if(!defined('NEWS_IMAGE'))		define('NEWS_IMAGE', 	DB_PREFIX.'news__image');
if(!defined('NEWS_FILES'))		define('NEWS_FILES', 	DB_PREFIX.'news__files');
if(!defined('NEWS_CATEGORY'))	define('NEWS_CATEGORY', DB_PREFIX.'news__category');
//=========================================================


class NewsEngine {

	# settings
	public 	$settings	= array();

	# current time
	public	$udate		= "";
	
	# category
	var $category_info	= array();
	var $category_id	= 0;
	var $category_pid	= 0;
	var $category_name	= "";
	
	#stat
	var $total_cats		= 0;
	var $total_news		= 0;
	var $total_vnews	= 0;
	
	# other
	public 	$news_id	= 0;

	

	function __construct() {
	
		global $db, $GET;
	
	
		// Получаем настройки новостей
		$q = $db->query("SELECT  setting_name, value FROM ".CONFIG_TABLE." WHERE part='News'");
		while($row = $db->fetch_assoc($q)) {
			$this->settings[$row['setting_name']] = $row['value'];
		}

		// init category
		if(isset($GET->_category)) {
			settype($GET->_category, "integer");
			$q = $db->query("SELECT id, parent_id, name, meta_description, meta_keywords, items FROM ".NEWS_CATEGORY." WHERE id='".$GET->_category."'");
			$row = $db->fetch_assoc($q);
			
			$this->category_id 		= $row['id'];			// id
			$this->category_pid 	= $row['parent_id'];	// parent category id
			$this->category_name 	= $row['name'];			// name
			
			$this->category_info	= $row;	// all
		}
	
		//init news id
		if(isset($GET->_news)) {
			settype($GET->_news, "integer");
			if($db->check_id($GET->_news, NEWS_ITEM) == 1) {
				$this->news_id =& $GET->_news;
			}
		}
	
	
		// set current time
		$this->udate = time();
	}
	
	
	//*******************************************
	// Category Tree construction step 1: select
	public function category_tree($parent=0, $subcat=true) {
		
		global $db;
		
		// sql query
		$q = $db->query("SELECT c.id, c.parent_id, c.name, c.items, c.sort, (SELECT count(*) FROM ".NEWS_ITEM." WHERE date <= '".$this->udate."' AND category_id = c.id) AS viewitems
							FROM ".NEWS_CATEGORY." AS c	
							ORDER BY c.sort ASC, c.name ASC");
							
		while($row = $db->fetch_assoc($q)) {
			$category[] = array('cat_id' 	=> $row['id'],
								'cat_name' 	=> $row['name'],
								'padding'	=> 0,
								'parent'	=> $row['parent_id'],
								'items'		=> $row['items'],
								'sort'		=> $row['sort'],
								'viewitems'	=> $row['viewitems']);
								
			// stats
			$this->total_cats++;
			$this->total_news 	+= $row['items'];
			$this->total_vnews 	+= $row['viewitems'];
		}

		
		// construct tree
		if(isset($category)) {
			$tree = $this->construct_tree($category, $parent, $subcat);
		
			// be back
			return $tree;
		}
	}
	
	
	//*******************************************
	// Category Tree construction step 2: construct
	public function construct_tree($category, $parent=0, $subcat=true, $level=0) {
		
		// counter
		$c = count($category) - 1;
		
		// create array
		if($level == 0) $tree = array();
		
		for($i=0;$i<=$c;$i++) {
			if($category[$i]['parent'] == $parent) {
				// update indention
				$category[$i]['padding'] = $level;
				
				// add branch(s)
				$tree[] = $category[$i];
				
				// check subcat
				if($subcat) {
					$subtree = $this->construct_tree($category, $category[$i]['cat_id'], $subcat, $level + $this->settings['indention']);
					if(is_array($subtree)) $tree = array_merge($tree, $subtree);
				}
			}
		}
		
		// be back
		if(!empty($tree)) 
			return $tree;
	}
	

	//*******************************************
	//	Check cat mites
	public function check_subcat($thisid, $toid) {
		
		if($thisid != $toid) {
			$category = $this->category_tree($thisid);

			$answer = true;
			for($i=0;$i<=count($category)-1;$i++) {
				if($category[$i]['cat_id'] == $toid) {
					$answer = false;
				}
			}
		}
		else $answer = false;
		
		
		// return check
		return $answer;
	}
	
	
	//*******************************************
	// load news image
	public function load_news_image($news_id) {
		
		global $db;
		
		$image = array();
		
		$q = $db->query("SELECT id, news_id, description, original_img, thumb_img FROM ".NEWS_IMAGE." WHERE news_id='".$news_id."'");
		while($row = $db->fetch_assoc($q)) {
			$image[] = $row;
		}
		
		return $image;
	}
	
	//*******************************************
	// load news image
	public function load_news_file($news_id) {
		
		global $db, $files, $filetype;

		
		$newsfiles = array();
		
		$q = $db->query("SELECT id, news_id, description, filename, ext FROM ".NEWS_FILES." WHERE news_id='".$news_id."'");
		while($row = $db->fetch_assoc($q)) {
			for($i=0;$i<=count($filetype)-1;$i++) {
				if($filetype[$i]['ext'] == $row['ext']) $row['icon'] = $filetype[$i]['ico'];
			}
			
			$row['size'] = $files->file_size(_UPLOADFILES."/".$row['filename']);
			
			$newsfiles[] = $row;
		}
		
		return $newsfiles;
	}
}

?>