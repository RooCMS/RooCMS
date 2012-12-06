<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS mod Image Gallery
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
|	Build date: 		7:28 07.03.2011
|	Build date: 		21:37 14.10.2011
|	Version file:		1.00 build 2
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


//*********************************************************
// Init Mod
//---------------------------------------------------------
if(!defined('THIS_MOD')) define('THIS_MOD', 'GALLERY');
//*********************************************************


// mysql tables ==========================================================
if(!defined('GALLERY_CATEGORY'))		define('GALLERY_CATEGORY', 	DB_PREFIX.'gallery__category');
if(!defined('GALLERY_ITEMS'))			define('GALLERY_ITEMS', 	DB_PREFIX.'gallery__items');
//========================================================================


class GalleryEngine {

	# settings
	var $settings 	= array();
	
	# image
	var $image_id		= 0;
	
	# category
	var $category_id	= 0;
	var $category_type	= "category";
	var $category_name	= "";
	var $category_info	= array();
	
	
	
	function __construct() {
		
		global $db, $parse, $GET;

		
		// Получаем настройки галереи
		$q = $db->query("SELECT  setting_name, value FROM ".CONFIG_TABLE." WHERE part='Gallery'");
		while($row = $db->fetch_assoc($q)) {
			$this->settings[$row['setting_name']] = $row['value'];
		}

		
		// init image
		if(isset($GET->_image)) {
			settype($GET->_image, "integer");
			if($db->check_id($GET->_image, GALLERY_ITEMS) == 1) {
				$this->image_id =& $GET->_image;
			}
		}
		

		// init category
		if(isset($GET->_category) && !isset($GET->_image)) {
			settype($GET->_category, "integer");
			$q = $db->query("SELECT id, parent_id, type, name FROM ".GALLERY_CATEGORY." WHERE id='".$GET->_category."'");
			if($db->num_rows($q) == 1) {
				$row = $db->fetch_assoc($q);
				$this->category_id 		= $row['id'];	// id
				$this->category_type 	= $row['type'];	// type
				$this->category_name 	= $row['name'];	// name
				
				$this->category_info	= $row;			// all info
			}
		}
	}

	
	//*******************************************
	// Category Tree construction step 1: select
	public function category_tree($parent=0, $subcat=true) {
		
		global $db;
		
		// sql query
		$q = $db->query("SELECT id, parent_id, name, images, type, sort
							FROM ".GALLERY_CATEGORY."	
							ORDER BY sort, name ASC");
							
		while($row = $db->fetch_assoc($q)) {
			$category[] = array('cat_id' 	=> $row['id'],
								'cat_name' 	=> $row['name'],
								'padding'	=> 0,
								'images'	=> $row['images'],
								'type'		=> $row['type'],
								'parent'	=> $row['parent_id'],
								'sort'		=> $row['sort']);
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
	// Check category type
	public function check_type($category_id) {
		
		global $db;
	
		$q = $db->query("SELECT type FROM ".GALLERY_CATEGORY." WHERE id='".$category_id."'");
		$c = $db->fetch_assoc($q);
		
		return $c['type'];
	}
	
}

?>