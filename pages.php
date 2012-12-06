<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS 
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
|	Build date: 		1:06 10.10.2011
|	Last Build: 		1:07 10.10.2011
|	Version file:		1.00
=========================================================*/
if(!defined('THIS_SCRIPT')) {
	define('THIS_SCRIPT',	'pages');
	require_once $_SERVER['DOCUMENT_ROOT']."/roocms/functions_header.php";
}


// init cms =====================================
$PageAlias = "easypage";
require_once _PAGES;
//===============================================


if(THIS_SCRIPT == 'pages') {
	require_once _CMS."/functions_footer.php";
}

?>