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
|	Build date: 		20:13 29.11.2010
|	Last build: 		8:42 26.10.2011
|	Version file:		1.00 build 15
=========================================================*/

//#########################################################
//	Anti Hack
//---------------------------------------------------------
define('RooCMS', true);
//=========================================================


// SEO Rederict ===========================================
if($_SERVER['REQUEST_URI'] == "/index.php") {
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: http://'.$_SERVER['HTTP_HOST'].'');
	exit;
} 
//=========================================================


// Init Engine ============================================
require_once $_SERVER['DOCUMENT_ROOT']."/roocms/class/class_debug.php";		// init Debuger class
	set_error_handler("debug_error", E_ALL);			// set error handler
	if($Debug->debug == 1) $Debug->startTimer();		// start Debug timer
require_once $_SERVER['DOCUMENT_ROOT']."/roocms/config/config.php";			// init Config (cfg)
require_once _LIB."/mimetype.php";						// inc Mimetypes
require_once _CLASS."/class_mysql.php";					// init MySql class
require_once _CMS."/functions.php";						// init Functions
require_once _CLASS."/class_global.php";				// init Global class
require_once _CLASS."/class_parser.php";				// init Parser class
require_once _CLASS."/class_files.php";					// init File class
require_once _CLASS."/class_gd.php";					// init GD class
require_once _CLASS."/class_rss.php";					// init RSS class
require_once _CLASS."/class_template.php";				// init Templater
//=========================================================
// function __autoload($class_name) {
     // include_once($class_name . "php");
// } 


// Load template header ===================================
if(THIS_SCRIPT == "acp") {
	$tpl->load_template("acp_header");
	nocache();
}
else {
	$tpl->load_template("user_header");
}
//=========================================================


?>