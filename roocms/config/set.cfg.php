<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS Settings Config File
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
|	Build: 			22:35 05.11.2010
|	Last Build: 	14:57 28.10.2011
|	Version file:	1.00 build 4
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


###########################################################
ob_start("ob_gzhandler", 9);
###########################################################


//=========================================================
// Session
ini_set("session.use_trand_sid", 	true); 	// 	session activated
//ini_set("session.save_path", 		"tmp");	//	session save path
//session_save_path("tmp");
session_start();
//=========================================================


//=========================================================
// Cookie
ini_set("session.use_cookie", 		true);	//	cookie activated
ini_set("session.cookie_domain", 	"");	//	cookie domain
ini_set("session.cookie_path", 		"/");	//	cookie path
ini_set("session.cookie_secure", 	"");	//	cookie secure
setcookie("",	"",	time()+3600);
//=========================================================


//=========================================================
// Cache
ini_set("session.cache_limiter", 	"nocache");		//	no-cache
//=========================================================


//=========================================================
//	Encoding
header("Content-type: text/html; charset=utf-8");
//=========================================================


@set_magic_quotes_runtime(0);


//setlocale(LC_ALL, 'ru_RU');


ini_set("max_execution_time", 	30);
ini_set("memory_limit", 		"64M");

ini_set("html_errors", "Off");

?>