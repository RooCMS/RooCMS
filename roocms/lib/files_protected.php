<?php
/**
* @package      RooCMS
* @subpackage	Library
* @author       alex Roosso
* @copyright    2010-2015 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.1.7
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
*   Copyright (C) 2010-2014 alex Roosso aka alexandr Belov info@roocms.com
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
*   Copyright (C) 2010-2014 alex Roosso (александр Белов) info@roocms.com
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
* Folders
*
* @var array
*/
$protectfolder = array();
$protectfolder[] = array('path'	=> _ROOCMS,		'chmod'	=> '0755');
$protectfolder[] = array('path'	=> _ROOCMS.'/config',	'chmod'	=> '0755');
$protectfolder[] = array('path'	=> _CLASS,		'chmod'	=> '0755');
$protectfolder[] = array('path'	=> _ROOCMS.'/acp',	'chmod'	=> '0755');
$protectfolder[] = array('path'	=> _LIB,		'chmod'	=> '0755');
$protectfolder[] = array('path'	=> _SKIN,		'chmod'	=> '0755');
$protectfolder[] = array('path'	=> _ACPSKIN,		'chmod'	=> '0755');
$protectfolder[] = array('path'	=> _UPLOAD,		'chmod'	=> '0755');
$protectfolder[] = array('path'	=> _UPLOADIMAGES,	'chmod'	=> '0755');
$protectfolder[] = array('path'	=> _UPLOADFILES,	'chmod'	=> '0755');
$protectfolder[] = array('path'	=> _CACHE,		'chmod'	=> '0755');
$protectfolder[] = array('path'	=> _CACHESKIN,		'chmod'	=> '0755');
$protectfolder[] = array('path'	=> _CACHEIMAGE,		'chmod'	=> '0755');
$protectfolder[] = array('path'	=> _LOGS,		'chmod'	=> '0755');
$protectfolder[] = array('path'	=> _SITEROOT.'/plugin',	'chmod'	=> '0755');


/**
* Files
*
* @var array
*/
$protectfiles = array();
if(defined('INSTALL')) 	$protectfiles[]	= array('path'	=> _ROOCMS.'/config/config.php',	'chmod'	=> '0755',	'hash'	=> '');
else			$protectfiles[]	= array('path'	=> _ROOCMS.'/config/config.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/config/defines.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/config/set.cfg.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_debug.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_files.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_images.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_gd.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_global.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_mysql_ext.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_mysql.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_parser.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_parserDate.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_parserText.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_parserXML.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_rss.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_security.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_structure.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_template.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/ajax.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/blocks.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/blocks_html.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/blocks_php.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/config.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/feeds.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/feeds_feed.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/help.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/index.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/login.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/logout.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/menu.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/pages.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/pages_html.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/pages_php.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/security_check.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/structure.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/users.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp.php',			'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/functions.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/functions_blocks.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/functions_page_feed.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/functions_page_html.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/functions_page_php.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/init.php',			'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/site.php',			'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _LIB.'/files_protected.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _LIB.'/mimetype.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _LIB.'/mysql_schema.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _LIB.'/smarty.php',			'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _LIB.'/spiders.php',			'chmod'	=> '0644',	'hash'	=> '');
if(file_exists(_LOGS.'/errors.log'))	$protectfiles[]	= array('path'	=> _LOGS.'/errors.log',		'chmod'	=> '0755',	'hash'	=> '');
if(file_exists(_LOGS.'/php_error.log'))	$protectfiles[]	= array('path'	=> _LOGS.'/php_error.log',	'chmod'	=> '0755',	'hash'	=> '');

?>