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
|	Last Build: 		2:34 11.10.2011
|	Version file:		1.00 build 7
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


require_once _CMS."/functions_pages.php";


$pageuser = new PageUser;
class PageUser {
	
	# classes
	protected $engine;


	
	function __construct() {
	
		global $db, $tpl, $GET, $PageAlias, $PageID;
		
		
		// Передаем параметры если заданы вручную
		//	Алиас
		if(isset($PageAlias) && $PageAlias != "" && !isset($GET->_subpage) && !isset($GET->_page)) $GET->_alias = $PageAlias;
		else if(isset($GET->_subpage)) $GET->_alias = $GET->_subpage;
		//	ИД
		if(isset($PageID) && $PageID > 0 && !isset($GET->_page)) $GET->_page = $PageID;

		
		// run engine
		$this->engine = new PageEngine;

		
		// Если есть параметры subpage или page но они неверные
		// А так же мы знаем что данная страница вложенаая то надо вернуть к дефолту вложенные параметры
		// Немного замудренный получился алгоритм, но мы его поправим =)
		if($this->engine->alias == "" && $this->engine->page_id == 0 
			&& (isset($GET->_subpage) || isset($GET->_page)) 
			&& ((isset($PageAlias) && $PageAlias != "") || (isset($PageID) && $PageID > 0))) {
			
			if(isset($PageAlias) && $PageAlias != "") $GET->_alias = $PageAlias;
			if(isset($PageID) && $PageID > 0) $GET->_page = $PageID;
			
			// переопределяем
			$this->engine->check_param();
		}

		
		// Load Template  ==============================
		$tpl->load_template("user_pages");
		//==============================================
		
		
		// определяем дефолтную страницу
		if($this->engine->alias == "" && $this->engine->page_id == 0) {
			$q = $db->query("SELECT id, alias FROM ".PAGE_UNIT." WHERE def='true'");
			$default = $db->fetch_assoc($q);
			
			$this->engine->alias 	= $default['alias'];
			$this->engine->page_id	= $default['id'];
		}
		
		
		$this->viewpage($this->engine->page_id);
	}
	
	
	// выводим страницу
	function viewpage($page_id) {
	
		global $db, $config, $tpl, $html, $parse, $var;

		
		// Выбираем страницу из БД
		$q = $db->query("SELECT id, alias, page_title, page_content, page_type, meta_description, meta_keywords FROM ".PAGE_UNIT." WHERE id='".$page_id."'");
		$page = $db->fetch_assoc($q);
		
		
		// SEO
		$var['title'] = $page['page_title']." :: ".$var['title'];
		$config->meta_description 	.= ". ".$page['meta_description'];
		$config->meta_keywords 		.= ", ".$page['meta_keywords'];
		
		
		// init {html:title}
		$html['title'] = $tpl->tpl->page_title($page['page_title']);
		
		
		// parse content & init {html:content}
		/*if($page['page_type'] == "text") {
			$page['page_content'] = $parse->text->br($page['page_content']);
			$html['content'] = $tpl->tpl->page_type_text($page['page_content']);
		}
		elseif($page['page_type'] == "bbcode") {
			$page['page_content'] = $parse->text->bbcode($page['page_content']);
			$html['content'] = $tpl->tpl->page_type_bbcode($page['page_content']);
		}
		else*/
		
		if($page['page_type'] == "html") {
			$page['page_content'] = $parse->text->html($page['page_content']);
			$html['content'] = $tpl->tpl->page_type_html($page['page_content']);
		}
		elseif($page['page_type'] == "php") {
			ob_start();
				eval($parse->text->html($page['page_content']));
				$page['page_content'] = ob_get_contents();
			ob_end_clean();
			$html['content'] = $tpl->tpl->page_type_php($page['page_content']);
		}
	}
}


?>