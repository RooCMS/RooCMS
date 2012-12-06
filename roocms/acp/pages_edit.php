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
|	Build date: 		22:45 09.12.2010
|	Last Build: 		13:06 10.10.2011
|	Version file:		1.00 build 4
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


class PageacpEdit {



	// форма редактирования HTML страниц
	function edit_html_page($page_id) {
		
		global $db, $tpl, $html;
		
		
		// sql data
		$q = $db->query("SELECT id, alias, page_title, page_content, meta_description, meta_keywords 
							FROM ".PAGE_UNIT." 
							WHERE id='".$page_id."'");
		$page = $db->fetch_assoc($q);
		
		
		// init {html:content}
		$html['content'] = $tpl->tpl->edit_html_page($page);
	}
	
	
	// форма редактирования PHP страниц
	function edit_php_page($page_id) {
		
		global $db, $tpl, $html;
		
		
		// sql data
		$q = $db->query("SELECT id, alias, page_title, page_content, meta_description, meta_keywords 
							FROM ".PAGE_UNIT." 
							WHERE id='".$page_id."'");
		$page = $db->fetch_assoc($q);
		
		
		// init {html:content}
		$html['content'] = $tpl->tpl->edit_php_page($page);
	}
}

?>