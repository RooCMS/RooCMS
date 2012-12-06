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
|	Build: 			11:35 29.11.2010
|	Last Build: 	5:42 28.10.2011
|	Version file:	1.00 build 6
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


// load footer template ===================================
if(THIS_SCRIPT == "acp") $tpl->load_template("acp_footer");
else $tpl->load_template("user_footer");
//=========================================================


// Output =================================================
echo $tpl->out();
//=========================================================


// Close connection to DB (recommended) ===================
$db->close();
//=========================================================


// Debug output ===========================================
if($Debug->debug == 1) {
	echo  $Debug->debug_info."\n<div id=\"debug_info\">Время работы скрипта: ".$Debug->endTimer();
	echo  "\n<br /> Версия: ".VERSION."";
	echo  "\n<br /> Запросов БД: ".$db->cnt_querys."";
	
	echo  "\n<br /> \$GET: 		".count($_GET)." ";
	if(count($GET) != 0) {
		foreach($GET as $key=>$value) {
			echo "[ {$key} ]";
		}
	}
	
	echo  "\n<br /> \$html: 	".count($html)." ";
	if(count($html) != 0) {
		foreach($html as $key=>$value) {
			echo "[ {$key} ]";
		}
	}
	
	echo  "\n<br /> \$module: 	".count($module)."";
	if(count($module) != 0) {
		foreach($module as $key=>$value) {
			echo "[ {$key} ]";
		}
	}
	echo "</div>";
}
//=========================================================

?>