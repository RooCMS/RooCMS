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
|	Build date: 		22:31 09.12.2010
|	Last Build: 		13:06 10.10.2011
|	Version file:		1.00 build 3
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


class PageacpCreate {



	// форма добавления html страниц
	function create_html_page() {
		
		global $db, $tpl, $html, $POST;
		
		
		// create
		if(@$_REQUEST['create_page']) {
			if(isset($POST->page_title) && isset($POST->page_alias) && isset($POST->page_content)) {
			
				// проверка уникального алиаса
				if($db->check_id($POST->page_alias, PAGE_UNIT, "alias") == 0) {
				
					if(!isset($POST->meta_description)) $POST->meta_description = "";
					if(!isset($POST->meta_keywords)) 	$POST->meta_keywords = "";
				
					// заносим в БД страницу
					$db->query("INSERT INTO ".PAGE_UNIT." (alias, page_title, page_content, page_type, meta_description, meta_keywords, last_update)
												   VALUES ('".$POST->page_alias."', '".$POST->page_title."', '".$POST->page_content."', 'html', '".$POST->meta_description."', '".$POST->meta_keywords."', '".time()."')");
												   
					// notice
					$_SESSION['info'][] = "Страница создана";
				}
				else {
					# error
					$_SESSION['error'][] = "Не удалось добавить страницу:";
					$_SESSION['error'][] = "Такой алиас уже существует!";
				}
			}
			else {
				# error
				$_SESSION['error'][] = "Не удалось добавить страницу:";
				if(!isset($POST->page_title))	$_SESSION['error'][] = "Не указан заголовок для страницы!";
				if(!isset($POST->page_content))	$_SESSION['error'][] = "Страница пустая!";
				if(!isset($POST->page_alias))	$_SESSION['error'][] = "Не указан алиас страницы!";
			}
			
			// move
			go(THIS_SCRIPT.".php?act=pages");
		}
		
		
		// init {html:content}
		$html['content'] = $tpl->tpl->create_html_page();
	}
	
	
	// Форма добавления php страниц
	function create_php_page() {
	
		global $db, $tpl, $html, $POST;
		
		
		// create
		if(@$_REQUEST['create_page']) {
			if(isset($POST->page_title) && isset($POST->page_alias) && isset($POST->page_content)) {
			
				// проверка уникального алиаса
				if($db->check_id($POST->page_alias, PAGE_UNIT, "alias") == 0) {
				
					if(!isset($POST->meta_description)) $POST->meta_description = "";
					if(!isset($POST->meta_keywords)) 	$POST->meta_keywords = "";
				
					// заносим в БД страницу
					$db->query("INSERT INTO ".PAGE_UNIT." (alias, page_title, page_content, page_type, meta_description, meta_keywords, last_update)
												   VALUES ('".$POST->page_alias."', '".$POST->page_title."', '".$POST->page_content."', 'php', '".$POST->meta_description."', '".$POST->meta_keywords."', '".time()."')");
												   
					// notice
					$_SESSION['info'][] = "Страница создана";
				}
				else {
					# error
					$_SESSION['error'][] = "Не удалось добавить страницу:";
					$_SESSION['error'][] = "Такой алиас уже существует!";
				}
			}
			else {
				# error
				$_SESSION['error'][] = "Не удалось добавить страницу:";
				if(!isset($POST->page_title))	$_SESSION['error'][] = "Не указан заголовок для страницы!";
				if(!isset($POST->page_content))	$_SESSION['error'][] = "Страница пустая!";
				if(!isset($POST->page_alias))	$_SESSION['error'][] = "Не указан алиас страницы!";
			}
			
			// move
			go(THIS_SCRIPT.".php?act=pages");
		}
		
		
		// init {html:content}
		$html['content'] = $tpl->tpl->create_php_page();
	}
}

?>