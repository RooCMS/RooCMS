<?php
/**
* @package		RooCMS
* @subpackage	Configuration
* @subpackage	Apache and PHP config
* @author		alex Roosso
* @copyright	2010-2014 (c) RooCMS
* @link			http://www.roocms.com
* @version		1.3
* @since		$date$
* @license		http://www.gnu.org/licenses/gpl-2.0.html
*
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
*   Данное программное обеспечение является свободным и распространяется
*   по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
*   При любом использовании данного ПО вы должны соблюдать все условия
*   лицензии.
*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


/**
* Start GZip
*/
ob_start("ob_gzhandler", 9);



/**
* Initialisation session settings
*/
ini_set("session.use_trand_sid",	true); 	# 	session activated
ini_set("session.gc_maxlifetime",	1440); 	# 	session max life time
//ini_set("session.save_path",		"tmp");	#	session save path
//session_save_path("tmp");
session_start();



/**
* Initialisation cookie settings
*/
ini_set("session.use_cookie",		true);	#	cookie activated
ini_set("session.cookie_domain",	"");	#	cookie domain
ini_set("session.cookie_path",		"/");	#	cookie path
ini_set("session.cookie_secure",	"");	#	cookie secure
setcookie("", "", time()+3600);



/**
* Initialisation cache settings
*/
ini_set("session.cache_limiter", 	"nocache");	#	no-cache



/**
* Set PHP settings
*/
@set_magic_quotes_runtime(0);
//setlocale(LC_ALL, 'ru_RU');
ini_set("max_execution_time",	30);
ini_set("memory_limit", 		"96M");

ini_set("date.timezone",		"Europe/Moscow");
ini_set("default_charset",		"utf-8");


/**
* Multibyte settings
*/
@ini_set("mbstring.internal_encoding",		"UTF-8");
@ini_set("mbstring.http_input",				"auto");
@ini_set("mbstring.http_output",			"UTF-8");
@ini_set("mbstring.substitute_character",	"none");


/**
* Set encoding header
*/
header("Content-type: text/html; charset=utf-8");


/**
* Set signature header
*/
header("X-Engine: Roocms");
header("X-Engine-Copyright: 2010-2014 (c) RooCMS");
header("X-Engine-Site: http://www.roocms.com");
?>