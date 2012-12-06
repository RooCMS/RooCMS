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
|	Build date: 		0:39 10.12.2010
|	Last Build: 		13:06 10.10.2011
|	Version file:		1.00 build 3
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


class PageacpUpdate {



	// форма обновления html страниц
	function update_html_page($page_id) {
		
		global $db, $POST;
		
		if(@$_REQUEST['update_page']) {
			if(isset($POST->page_title) && isset($POST->page_content) && isset($POST->page_alias)) {

				if(!isset($POST->meta_description)) $POST->meta_description = "";
				if(!isset($POST->meta_keywords)) 	$POST->meta_keywords = "";
			
				// обновляем запись в БД
				$db->query("UPDATE ".PAGE_UNIT." SET
									alias='".$POST->page_alias."',
									page_title='".$POST->page_title."',
									page_content='".$POST->page_content."',
									meta_description='".$POST->meta_description."',
									meta_keywords='".$POST->meta_keywords."',
									last_update='".time()."'
								WHERE id='".$POST->page_id."'");
								
				#info
				$_SESSION['info'][] = "Страница успешно обновлена";
			}
			else {
				# error
				$_SESSION['error'][] = "Не удалось обновить страницу:";
				if(!isset($POST->page_title))	$_SESSION['error'][] = "Не указан заголовок для страницы!";
				if(!isset($POST->page_content))	$_SESSION['error'][] = "Страница пустая!";
				if(!isset($POST->page_alias))	$_SESSION['error'][] = "Не указан алиас страницы!";
			}
		}
		
		// move
		goback();
	}
	
	
	// форма обновления html страниц
	function update_php_page($page_id) {
		
		global $db, $POST;
		
		if(@$_REQUEST['update_page']) {
			if(isset($POST->page_title) && isset($POST->page_content) && isset($POST->page_alias)) {

				if(!isset($POST->meta_description)) $POST->meta_description = "";
				if(!isset($POST->meta_keywords)) 	$POST->meta_keywords = "";
			
				// обновляем запись в БД
				$db->query("UPDATE ".PAGE_UNIT." SET
									alias='".$POST->page_alias."',
									page_title='".$POST->page_title."',
									page_content='".$POST->page_content."',
									meta_description='".$POST->meta_description."',
									meta_keywords='".$POST->meta_keywords."',
									last_update='".time()."'
								WHERE id='".$POST->page_id."'");
								
				#info
				$_SESSION['info'][] = "Страница успешно обновлена";
			}
			else {
				# error
				$_SESSION['error'][] = "Не удалось обновить страницу:";
				if(!isset($POST->page_title))	$_SESSION['error'][] = "Не указан заголовок для страницы!";
				if(!isset($POST->page_content))	$_SESSION['error'][] = "Страница пустая!";
				if(!isset($POST->page_alias))	$_SESSION['error'][] = "Не указан алиас страницы!";
			}
		}
		
		// move
		goback();
	}
}

?>