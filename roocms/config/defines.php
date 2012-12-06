<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS Defines
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
|	Build: 				2:58 12.04.2010
|	Last Build: 		5:42 28.10.2011
|	Version file:		1.00 build 12
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


//#########################################################
//	DataBase prefix
//---------------------------------------------------------
define('DB_PREFIX',		'roocms_');
//#########################################################


//#########################################################
//	Config $DB Table
//---------------------------------------------------------
define('CONFIG_PARTS', DB_PREFIX.'config__parts');
define('CONFIG_TABLE', DB_PREFIX.'config__settings');
//#########################################################


//#########################################################
//	RooCMS $Path
//---------------------------------------------------------
define('_CMS',			$_SERVER['DOCUMENT_ROOT'].'/roocms');
define('_CLASS', 		_CMS.'/class');
define('_LIB', 			_CMS.'/lib');
define('_TEMPLATES',	_CMS.'/templates');
define('_UPLOAD',		_CMS.'/../upload');
define('_UPLOADFILES',	_UPLOAD.'/files');
//#########################################################


//#########################################################
//	Component
//---------------------------------------------------------
define('_ACP',			_CMS.'/acp.php');
define('_PAGES',		_CMS.'/pages.php');
define('_NEWS',			_CMS.'/news.php');
define('_PORTFOLIO',	_CMS.'/portfolio.php');
define('_GALLERY',		_CMS.'/gallery.php');
//#########################################################


//#########################################################
// 	$Param
//---------------------------------------------------------
define('EMAIL_MESSAGE_PARAMETERS',	'content-Type: text/plain; charset="utf-8"');
define('VERSION',					'1.00 Nightly Build 9 dev');
define('CHARSET',					'text/html; charset=utf-8');
define('BUILD',						'10009');
//#########################################################
?>