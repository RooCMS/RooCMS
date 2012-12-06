<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS acp menu
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
|	Build date: 		18:51 14.09.2010
|	Last Bould: 		3:16 08.09.2011
|	Version file:		1.00 build 7
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


// Load template ======================
$tpl->load_template("acp_menu");
//=====================================


// news
if(file_exists(_CMS."/acp/news.php") && file_exists(_CMS."/functions_news.php")) {
	$html['menu_items_left'][] = $tpl->tpl->menu_items("acp.php?act=news", "img/acp/news_16.png", "Новости");
}

// cms pages
if(file_exists(_CMS."/acp/pages.php") && file_exists(_CMS."/functions_pages.php")) {
	$html['menu_items_left'][] = $tpl->tpl->menu_items("acp.php?act=pages", "img/acp/page_16.png", "Страницы");
}

// gallery
if(file_exists(_CMS."/acp/gallery.php") && file_exists(_CMS."/functions_gallery.php")) {
	$html['menu_items_left'][] = $tpl->tpl->menu_items("acp.php?act=gallery", "img/acp/gallery_16.png", "Галерея");
}

// portfolio
if(file_exists(_CMS."/acp/portfolio.php") && file_exists(_CMS."/functions_portfolio.php")) {
	$html['menu_items_left'][] = $tpl->tpl->menu_items("acp.php?act=portfolio", "img/acp/portfolio_16.png", "Портфолио");
}

// config page
if(file_exists(_CMS."/acp/config.php")) {
	$html['menu_items_left'][] = $tpl->tpl->menu_items("acp.php?act=config", "img/acp/config_16.png", "Настройки");
}


$html['menu_items_left'][] = $tpl->tpl->menu_items("/", "img/acp/index_16.png", "На сайт", "_blank");


// logout
if(file_exists(_CMS."/acp/logout.php")) {
	$html['menu_items_right'][] = $tpl->tpl->menu_items("acp.php?act=logout", "img/acp/logout_16.png", "Выход");
}

?>