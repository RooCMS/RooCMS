<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS component Pages
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
|	Build date: 		6:57 30.11.2010
|	Last build: 		21:39 14.10.2011
|	Version file:		1.00 build 7
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


//*********************************************************
// Init Mod
//---------------------------------------------------------
if(!defined('THIS_MOD')) define('THIS_MOD', 'PAGES');
//*********************************************************


// mysql tables ===========================================
if(!defined('PAGE_UNIT'))	define('PAGE_UNIT', 		DB_PREFIX.'page__unit');
if(!defined('PAGE_TYPE'))	define('PAGE_TYPE', 		DB_PREFIX.'page__type');
//=========================================================


class PageEngine {

	# types
	private $types	= array('html' 	=> true,
							'php' 	=> true);

	# init page
	public	$alias		= "";
	public 	$page_id	= 0;
	public	$page_type	= "";
	
	
	
	function __construct() {
		
		$this->check_param();
	}
	
	
	//	Определяем параметры цели
	public function check_param() {
		
		global $db, $GET;
	
		// инициализируем страницу
		// если указан входящий алиас
		if(isset($GET->_alias) && $db->check_id($GET->_alias, PAGE_UNIT, "alias") == 1) {
			$this->alias =& $GET->_alias;
			$q = $db->query("SELECT id, page_type FROM ".PAGE_UNIT." WHERE alias='".$this->alias."'");
			$id = $db->fetch_assoc($q);
			$this->page_id = $id['id'];
			$this->page_type = $id['page_type'];
		}

		// если входящего алиаса нет, но есть идентификатор
		if(isset($GET->_page) && $this->alias == "") {
			settype($GET->_page, "integer");
			if($db->check_id($GET->_page, PAGE_UNIT) == 1)  {
				$this->page_id	=& $GET->_page;
				$q = $db->query("SELECT alias, page_type FROM ".PAGE_UNIT." WHERE id='".$this->page_id."'");
				$t = $db->fetch_assoc($q);
				$this->alias		= $t['alias'];
				$this->page_type 	= $t['page_type'];
			}
		}
	}
	
	
	// Проверка допустимости типа страницы
	function check_type($type) {

		if(isset($this->types[$type])) return $this->types[$type];
	}
}


?>