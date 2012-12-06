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
|	Build date: 		22:03 07.11.2010
|	Last Build: 		3:24 28.10.2011
|	Version file:		1.00 build 4
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


$tpl->load_template("acp_index");

// Версия mySql ===========================================
$q = $db->query("SHOW VARIABLES LIKE 'version'");
$mysql = $db->fetch_row($q);
$version['mysql']	= $mysql[1];
//=========================================================


$version['php'] 	= PHP_VERSION;						// Версия php
$version['apache'] 	= $_SERVER['SERVER_SOFTWARE'];		// Версия сервера
$version['os']		= PHP_OS; 							// ОС

$version['ml']		= ini_get('memory_limit');			// Memory limit
$version['mfs']		= ini_get('upload_max_filesize');	// Maximum file size
$version['mps']		= ini_get('post_max_size');			// Maximum post size
$version['met']		= ini_get('max_execution_time');	// Max execution time


$extimages = "";
foreach($imagetype AS $key=>$value) {$extimages .= $tpl->tpl->ext($value);}
$extfiles = "";
foreach($filetype AS $key=>$value) {$extfiles .= $tpl->tpl->ext($value);}


$filetypes['mfs']		= $version['mfs'];	// Maximum file size
$filetypes['images']	= $extimages;		// Allow image types
$filetypes['files']		= $extfiles;		// Allow file types


//debug($GLOBALS);

//DIRECTORY_SEPARATOR;

// output
$html['version'] 	= $tpl->tpl->version($version);
$html['filetypes'] 	= $tpl->tpl->filetypes($filetypes);

?>