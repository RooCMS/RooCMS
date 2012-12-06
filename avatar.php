<?php
/*==================================================================================
|	This script was developed by alex Roosso .
|	Title:  		miniGallery for Avatar
|	Description:	Скрипт для создания минигаллереи из картинок небольших размеров
|				находящихся в указанной папке.
|	Author:	alex Roosso
|	Copyright: 2010-2011 (c) RooCMS. 
|	Web: http://www.roocms.com
|	All rights reserved.
|-----------------------------------------------------------------------------------
|	This program is free software; you can redistribute it and/or modify
|	it under the terms of the GNU General Public License as published by
|	the Free Software Foundation; either version 2 of the License, or
|	(at your option) any later version.
|	
|	Данное программное обеспечение является свободным и распространяется
|	по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
|	При любом использовании данного ПО вы должны соблюдать все условия
|	лицензии.
|-----------------------------------------------------------------------------------
|	Build date: 	23:36 13.09.2010
|	Last Build: 	3:01 17.10.2011
|	Version file: 	1.00 build 9
==================================================================================*/

//##################################################################################
//	Settings / Настройка
//----------------------------------------------------------------------------------
//
// Укажите путь к папке с аватарами без слеша на конце
$folder = "upload/images/avatar2";

// Укажите количество аватаров отображаемых в одном ряду
$line	= 11;

// Укажите количество аватаров на отображаемых на странице
$limit	= 44;

//	* 	количество рядов будет высчитано автоматически, из 
//	  	параметров $line и $limit
//----------------------------------------------------------------------------------
//	Далее ничего не меняйте, если Вы не владете
//	языком программирование PHP 5
//==================================================================================

if(!defined('THIS_SCRIPT')) {
	define('THIS_SCRIPT',	'avatar');
	require_once $_SERVER['DOCUMENT_ROOT']."/roocms/functions_header.php";
}

// meta
$var['title']		= "Разные аватары из интернета :: ".$var['title'];
$config->meta_keywords 	.= "аватар, userpic,";

// Считаем аватары
$av  = array();
$av  = glob($folder."/*.{gif,jpg,png}", GLOB_BRACE);

$c = count($av)-1;

// Присваиваем лимиты
$db->limit = $limit;
$db->pages_non_mysql($c+1);

// Инициализируем шаблон аватаров.
$tpl->load_template("user_avatar");

// номер страницы к заголовку
if($db->page > 1) $var['title'] .= " : Страница ".$db->page;

// Отрисовка аватаров
$to = $db->from + $db->limit - 1;
if($to >= $c) $to = $c;
$hr = 0;
for($i=$db->from;$i<=$to;$i++) {
	$hr++;
	$html['avatars'][] = $tpl->tpl->avatars($av[$i]);

	if($hr == $line) {
		$hr = 0;
		$html['avatars'][] = $tpl->tpl->avatars_line($av[$i]);
	}
}

// отрисовка страниц
for($p=1;$p<=$db->pages;$p++) {
	$html['pages'][] = $tpl->tpl->pages($_SERVER['SCRIPT_NAME']."?page={$p}",$p);
}


if(THIS_SCRIPT == 'avatar') {
	require_once _CMS."/functions_footer.php";
}
?>