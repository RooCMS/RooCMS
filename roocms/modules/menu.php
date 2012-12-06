<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS module Menu
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
|	Build date: 		21:31 10.09.2010
|	Last build: 		10:45 18.02.2011
|	Version file:		1.00 build 2
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


// Load template =================
$module['menu'] = $tpl->load_template("module_menu", true);


$menu  = array();
// menu item =====================
$menu[] = array('link' 	=> 'index',
				'title' => 'Главная');
$menu[] = array('link' 	=> 'news',
				'title' => 'Новости');
$menu[] = array('link' 	=> 'portfolio',
				'title' => 'Портфолио');
$menu[] = array('link' 	=> 'gallery',
				'title' => 'Галерея');
$menu[] = array('link' 	=> 'pages',
				'title' => 'Страницы');
$menu[] = array('link' 	=> 'avatar',
				'title' => 'Аватары');

// $menu[] = array('link' 	=> 'test',
				// 'title' => 'Тест');
				
				
foreach($menu as $key => $value) {
	$html['menu_item'][] = $tpl->tpl->menu_item($menu[$key]);
}


?>