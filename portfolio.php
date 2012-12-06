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
|	Build date: 		7:31 25.02.2011
|	Last Build: 		7:31 25.02.2011
|	Version file:		1.00.00 
=========================================================*/

if(!defined('THIS_SCRIPT')) {
	define('THIS_SCRIPT',	'portfolio');
	require_once $_SERVER['DOCUMENT_ROOT']."/roocms/functions_header.php";
}


// meta
$config->meta_keywords  .= "";


// init cms ===================================
require_once _PORTFOLIO;
//===============================================


if(THIS_SCRIPT == 'portfolio') {
	require_once _CMS."/functions_footer.php";
}

?>