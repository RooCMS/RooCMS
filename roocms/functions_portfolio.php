<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS mod Portfolio
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
|	Build date: 		11:14 28.11.2010
|	Build date: 		9:20 15.10.2011
|	Version file:		1.00 build 19
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


//*********************************************************
// Init Mod
//---------------------------------------------------------
if(!defined('THIS_MOD')) define('THIS_MOD', 'PORTFOLIO');
//*********************************************************


// mysql tables ===========================================
if(!defined('PORTFOLIO_CATEGORY'))		define('PORTFOLIO_CATEGORY', 		DB_PREFIX.'portfolio__category');
if(!defined('PORTFOLIO_PROJECT'))		define('PORTFOLIO_PROJECT', 		DB_PREFIX.'portfolio__projects');
if(!defined('PORTFOLIO_PROJECT_STEPS'))	define('PORTFOLIO_PROJECT_STEPS', 	DB_PREFIX.'portfolio__projects_steps');
if(!defined('PORTFOLIO_WORKS_PHOTO'))	define('PORTFOLIO_WORKS_PHOTO', 	DB_PREFIX.'portfolio__works_poster');
//=========================================================


class PortfolioEngine {

	# settings
	var $settings 	= array();
	
	# category
	var $category_id	= 0;
	var $category_type	= "category";
	var $category_name	= "";
	var $category_info	= array();
	
	# project
	var $project_id		= 0;
	var $project_title	= "";
	
	
	
	function __construct() {
		
		global $db, $parse, $GET;

		
		// Получаем настройки портфолио
		$q = $db->query("SELECT  setting_name, value FROM ".CONFIG_TABLE." WHERE part='Portfolio'");
		while($row = $db->fetch_assoc($q)) {
			$this->settings[$row['setting_name']] = $row['value'];
		}

		// init project
		if(isset($GET->_project)) {
			settype($GET->_project, "integer");
			if($db->check_id($GET->_project, PORTFOLIO_PROJECT) == 1) {
				
				$this->project_id =& $GET->_project;
				
				$q = $db->query("SELECT category_id, title FROM ".PORTFOLIO_PROJECT." WHERE id='".$this->project_id."'");
				$row = $db->fetch_assoc($q);
				
				$this->category_id 		= $row['category_id'];
				$this->project_title	= $row['title'];
			}
		}
		
		// init category
		if(isset($GET->_category) && $this->project_id == 0) {
			settype($GET->_category, "integer");
			$q = $db->query("SELECT id, parent_id, type, name, projects FROM ".PORTFOLIO_CATEGORY." WHERE id='".$GET->_category."'");
			if($db->num_rows($q) == 1) {
				$row = $db->fetch_assoc($q);
				$this->category_id 		= $row['id'];	// id
				$this->category_type 	= $row['type'];	// type
				$this->category_name 	= $row['name'];	// name
				
				$this->category_info	= $row;			// all info
			}
		}
		
		
		// for birth day
		$birth_date = explode("/", $this->settings['birth_date']);
		$this->settings['birth_day'] 		= $parse->field_select_day($birth_date[1]);
		$this->settings['birth_month'] 		= $parse->field_select_month($birth_date[0]);
		$this->settings['birth_year'] 		= $parse->field_select_year($birth_date[2]);
	}

	
	//*******************************************
	// Category Tree construction step 1: select
	public function category_tree($parent=0, $subcat=true) {
		
		global $db;
		
		// sql query
		$q = $db->query("SELECT id, parent_id, name, projects, type, sort
							FROM ".PORTFOLIO_CATEGORY."	
							ORDER BY sort, name ASC");
							
		while($row = $db->fetch_assoc($q)) {
			$category[] = array('cat_id' 	=> $row['id'],
								'cat_name' 	=> $row['name'],
								'padding'	=> 0,
								'projects'	=> $row['projects'],
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
	
		$q = $db->query("SELECT type FROM ".PORTFOLIO_CATEGORY." WHERE id='".$category_id."'");
		$c = $db->fetch_assoc($q);
		
		return $c['type'];
	}
	
	//*******************************************
	// tags
	public function tags() {
		
		global $roocms, $db;
		
		// category
		// if($this->engine->category_id == 0) {
			// $where = "category_id!='0'";
			// $incat = "";
		// }
		// else {
			// $where = "category_id='".$this->engine->category_id."'";
			
			// # subcat
			// $cs = count($this->subcat);
			// if($cs != 0) {
				// for($i=0;$i<=$cs-1;$i++) {
					// $where .= " OR category_id='".$this->subcat[$i]."'";
				// }
			// }
			
			// $incat = "&category=".$this->engine->category_id;
		// }
		
		
		$min = 0;
		$max = 0;
		
		$q = $db->query("SELECT tags FROM ".PORTFOLIO_PROJECT." ORDER BY id DESC");
		while($row = $db->fetch_assoc($q)) {
			$x = explode(",",$row['tags']);
			foreach($x AS $key=>$value) {
				$value = trim(mb_strtolower($value, 'utf8'));
				if($value != "") {
					if(isset($this->tags[$value])) $this->tags[$value] = $this->tags[$value] + 1;
					else $this->tags[$value] = 1;
					// max
					if($this->tags[$value] > $max) $max = $this->tags[$value];
				}
			}
		}
		
		// min
		if(isset($this->tags)) {
			foreach($this->tags AS $key=>$value) {
				if($value < $max) $min = $this->tags[$key];
			}
			
			$minsize = 90;
			$maxsize = 175;
			
			foreach ($this->tags AS $key=>$value) {
				if($min == $max) {
					$fontsize = round(($maxsize - $minsize)/2+$minsize);
				}
				else {
					settype($max, "integer");
					settype($min, "integer");
					settype($value, "integer");
					$fontsize = round(((($maxsize-$minsize)/$max)*$value)+$minsize);
				}
				
				$ukey = urlencode($key);
				$tags[] = array('fontsize'=>$fontsize, 'key'=>$key, 'ukey'=>$ukey, 'value'=>$value);
			}
			
			return $tags;
		}
	}
	
}

?>