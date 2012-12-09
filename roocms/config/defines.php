<?php
/**
* @package		RooCMS
* @subpackage	Configuration
* @subpackage	Defines
* @author		alex Roosso
* @copyright	2010-2014 (c) RooCMS
* @link			http://www.roocms.com
* @version		1.3.1
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
* DataBase prefix
*/
define('DB_PREFIX',			$db_info['prefix']);


/**
* Config $DB Table
*/
define('CONFIG_PARTS', 		DB_PREFIX.'config__parts');
define('CONFIG_TABLE', 		DB_PREFIX.'config__settings');
define('HELP_TABLE', 		DB_PREFIX.'help');
define('STRUCTURE_TABLE', 	DB_PREFIX.'structure');
define('PAGES_HTML_TABLE', 	DB_PREFIX.'pages__html');
define('PAGES_PHP_TABLE', 	DB_PREFIX.'pages__php');
define('PAGES_FEED_TABLE', 	DB_PREFIX.'pages__feed');
define('BLOCKS_TABLE', 		DB_PREFIX.'blocks');
define('IMAGES_TABLE', 		DB_PREFIX.'images');


/**
* RooCMS $Path
*/
define('_ROOT',				$_SERVER['DOCUMENT_ROOT']);
define('_ROOCMS',			_ROOT.'/roocms');
define('_CLASS', 			_ROOCMS.'/class');
define('_LIB', 				_ROOCMS.'/lib');
define('_SMARTY', 			_LIB.'/smarty');
define('_SKIN',				_ROOT.'/skin');
define('_ACPSKIN',			_SKIN.'/acp');
define('_UPLOAD',			_ROOT.'/upload');
define('_UPLOADIMAGES',		_UPLOAD.'/images');
define('_UPLOADFILES',		_UPLOAD.'/files');
define('_CACHE',			_ROOT.'/cache');
define('_LOGS',				_CACHE.'/logs');
define('_CACHESKIN',		_CACHE.'/skin');


/**
* Component
*/
define('_ACP',				_ROOCMS.'/acp.php');
define('_SITE',				_ROOCMS.'/site.php');


/**
* $Param
*/
define('EMAIL_MESSAGE_PARAMETERS',	'content-Type: text/plain; charset="utf-8"');
define('SCRIPT_NAME',				$_SERVER['SCRIPT_NAME']);
define('CHARSET',					'text/html; charset=utf-8');
define('ROOCMS_VERSION',			'1.0.10');
define('VERSION',					'1.0 Nightly Build 10');
define('BUILD',						'1010');

?>