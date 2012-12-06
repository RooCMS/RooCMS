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
|	Build date: 		3:08 13.09.2010
|	Last Build: 		6:19 11.10.2011
|	Version file:		1.00 build 5
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


require_once _CMS."/acp/security_check.php";


// check security
if($security == true) {
	// запускаем меню админа
	require _CMS."/acp/menu.php";
	
	
	if(!empty($roocms->act) && file_exists(_CMS."/acp/".$roocms->act.".php")) {
		require_once _CMS."/acp/".$roocms->act.".php";
	}
	else {
		require_once _CMS."/acp/index.php";
	}
}
else {
	require_once _CMS."/acp/login.php";
}


?>