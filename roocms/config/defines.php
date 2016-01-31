<?php
/**
* @package	RooCMS
* @subpackage	Configuration
* @author	alex Roosso
* @copyright	2010-2016 (c) RooCMS
* @link		http://www.roocms.com
* @version	1.4.9
* @since	$date$
* @license	http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
*   Copyright (C) 2010-2016 alex Roosso aka alexandr Belov info@roocms.com
*
*   This program is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   This program is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with this program.  If not, see http://www.gnu.org/licenses/
*
*
*   RooCMS - Русская бесплатная система управления сайтом
*   Copyright (C) 2010-2016 alex Roosso (александр Белов) info@roocms.com
*
*   Это программа является свободным программным обеспечением. Вы можете
*   распространять и/или модифицировать её согласно условиям Стандартной
*   Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
*   Обеспечения, версии 3 или, по Вашему желанию, любой более поздней версии.
*
*   Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
*   ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
*   ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
*   Общественную Лицензию GNU для получения дополнительной информации.
*
*   Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
*   с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


/**
* DataBase prefix
*/
define('DB_PREFIX',		$db_info['prefix']);


/**
* Config $DB Table
*/
define('CONFIG_PARTS_TABLE', 	DB_PREFIX.'config__parts');
define('CONFIG_TABLE', 		DB_PREFIX.'config__settings');
define('STRUCTURE_TABLE', 	DB_PREFIX.'structure');
define('PAGES_HTML_TABLE', 	DB_PREFIX.'pages__html');
define('PAGES_PHP_TABLE', 	DB_PREFIX.'pages__php');
define('PAGES_FEED_TABLE', 	DB_PREFIX.'pages__feed');
define('BLOCKS_TABLE', 		DB_PREFIX.'blocks');
define('IMAGES_TABLE', 		DB_PREFIX.'images');
define('FILES_TABLE', 		DB_PREFIX.'files');
define('USERS_TABLE', 		DB_PREFIX.'users');
define('USERS_GROUP_TABLE', 	DB_PREFIX.'users_group');
define('USERS_PM_TABLE', 	DB_PREFIX.'users_pm');
define('HELP_TABLE', 		DB_PREFIX.'help');


/**
* RooCMS $Path
*/
if(!defined('_SITEROOT'))
define('_SITEROOT', 	str_ireplace(DIRECTORY_SEPARATOR."roocms".DIRECTORY_SEPARATOR."config", "", dirname(__FILE__)));
define('_ROOCMS',	_SITEROOT.'/roocms');
define('_CLASS', 	_ROOCMS.'/class');
define('_LIB', 		_ROOCMS.'/lib');
define('_MODULE', 	_ROOCMS.'/module');
define('_SMARTY', 	_LIB.'/smarty');
define('_SKIN',		_SITEROOT.'/skin');
define('_ACPSKIN',	_SKIN.'/acp');
define('_UPLOAD',	_SITEROOT.'/upload');
define('_UPLOADIMAGES',	_UPLOAD.'/images');
define('_UPLOADFILES',	_UPLOAD.'/files');
define('_CACHE',	_SITEROOT.'/cache');
define('_LOGS',		_CACHE.'/logs');
define('_CACHESKIN',	_CACHE.'/skin');
define('_CACHEIMAGE',	_CACHE.'/images');


/**
 * Web $Path
 */
define('_DOMAIN',	strtr($_SERVER['HTTP_HOST'], array('http://'=>'', 'www.'=>'')));


/**
* $Component
*/
define('_ACP',		_ROOCMS.'/acp.php');
define('_UCP',		_ROOCMS.'/ucp.php');
define('_SITE',		_ROOCMS.'/site.php');


/**
* $Param
*/
define('EMAIL_MESSAGE_PARAMETERS',	'content-Type: text/html; charset="utf-8"');
define('SCRIPT_NAME',			$_SERVER['SCRIPT_NAME']);
define('CHARSET',			'text/html; charset=utf-8');
define('CRITICAL_STYLESHEETS',		'<script type="text/javascript" src="/plugin/bootstrap.php?short"></script>');
define('ROOCMS_MAJOR_VERSION',		'1');
define('ROOCMS_MINOR_VERSION',		'2');
define('ROOCMS_RELEASE_VERSION',	'0 RC 1');
define('ROOCMS_BUILD_VERSION',		' beta 2');
define('ROOCMS_VERSION',		ROOCMS_MAJOR_VERSION.".".ROOCMS_MINOR_VERSION.".".ROOCMS_RELEASE_VERSION);
define('ROOCMS_FULL_VERSION',		ROOCMS_MAJOR_VERSION.".".ROOCMS_MINOR_VERSION.".".ROOCMS_RELEASE_VERSION.ROOCMS_BUILD_VERSION);
define('ROOCMS_VERSION_ID',		ROOCMS_MAJOR_VERSION.ROOCMS_MINOR_VERSION.ROOCMS_RELEASE_VERSION);
?>