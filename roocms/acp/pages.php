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
|	Last Build: 		0:10 19.03.2011
|	Version file:		1.00 build 24
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


require_once _CMS."/functions_pages.php";


$pageacp = new Pageacp;
class Pageacp {

	# classes
	protected $engine;
	
	protected $create;
	protected $edit;
	protected $update;

	
	
	function __construct() {
		
		global $tpl;
		
		
		// run engine
		$this->engine = new PageEngine;
		
		
		// Load Template  ==============================
		$tpl->load_template("acp_pages");
		//==============================================
		
		
		// init action
		$this->init_action();
	}
	
	
	// определяем действие (проверка, инициализация)
	function init_action() {
		
		global $roocms, $db, $GET, $POST;
		
		# create
		if($roocms->part == "create" && isset($GET->_type) && $this->engine->check_type($GET->_type)) {
			// load action
			require_once _CMS."/acp/pages_create.php";
			$this->create = new PageacpCreate;
			
			// action
			$create = "\$this->create->create_".$GET->_type."_page();";
			eval($create);
		}
		# edit
		elseif($roocms->part == "edit" && $this->engine->page_id != 0 && $this->engine->check_type($this->engine->page_type)) {
			// load action
			require_once _CMS."/acp/pages_edit.php";
			$this->edit = new PageacpEdit;
			
			// action
			$edit = "\$this->edit->edit_".$this->engine->page_type."_page(".$this->engine->page_id.");";
			eval($edit);
		}
		# update
		elseif($roocms->part == "update" && $db->check_id($POST->page_id, PAGE_UNIT)) {
			$q = $db->query("SELECT page_type FROM ".PAGE_UNIT." WHERE id='".$POST->page_id."'");
			$t = $db->fetch_assoc($q);
			if($this->engine->check_type($t['page_type'])) {
				// load action
				require_once _CMS."/acp/pages_update.php";
				$this->update = new PageacpUpdate;
				
				// action
				$update = "\$this->update->update_".$t['page_type']."_page(".$POST->page_id.");";
				eval($update);
			}
		}
		# delete
		elseif($roocms->part == "delete" && $this->engine->page_id != 0) {
			 $this->delete_page($this->engine->page_id);
		}
		# default
		else {
			$this->view_list_pages();
		}
	}
	
	
	// по умолчанию список страниц
	function view_list_pages() {
		
		global $db, $tpl, $html, $parse;
		
		
		// запрашиваем список страниц
		$q = $db->query("SELECT id, def, alias, page_title, page_type, last_update FROM ".PAGE_UNIT." ORDER BY id ASC");
		while($page = $db->fetch_assoc($q)) {
			
			if($page['def'] == "true") 		$page['default'] = "<b title=\"страница по умолчанию\" style=\"cursor:help;\">x</b>";
			elseif($page['def'] == "false") $page['default'] = "";
			
			$page['last_update'] = $parse->date->unix_to_rus($page['last_update']);
			
			$html['page_brief'][] = $tpl->tpl->page_brief($page);
		}
		
		
		// init {html:content}
		$html['content'] = $tpl->tpl->view_list_pages();
	}
	
	
	// удаляем страницу из БД
	function delete_page($page_id) {
		
		global $db;
		
		// sql
		$db->query("DELETE FROM ".PAGE_UNIT." WHERE id='".$page_id."'");
		
		// notice
		$_SESSION['info'][] = "Страница удалена:";
		
		// move
		goback();
	}
}

?>