<?php
/**
* @package      RooCMS
* @subpackage	Library
* @subpackage	List protected files
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.0
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
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
* Folders
*
* @var array
*/
$protectfolder = array();
$protectfolder[]	= array('path'	=> _ROOCMS,				'chmod'	=> '0755');
$protectfolder[]	= array('path'	=> _ROOCMS.'/config',	'chmod'	=> '0755');
$protectfolder[]	= array('path'	=> _CLASS,				'chmod'	=> '0755');
$protectfolder[]	= array('path'	=> _ROOCMS.'/acp',		'chmod'	=> '0755');
$protectfolder[]	= array('path'	=> _LIB,				'chmod'	=> '0755');
$protectfolder[]	= array('path'	=> _SKIN,				'chmod'	=> '0755');
$protectfolder[]	= array('path'	=> _ACPSKIN,			'chmod'	=> '0755');
$protectfolder[]	= array('path'	=> _UPLOAD,				'chmod'	=> '0755');
$protectfolder[]	= array('path'	=> _UPLOADIMAGES,		'chmod'	=> '0755');
$protectfolder[]	= array('path'	=> _UPLOADFILES,		'chmod'	=> '0755');
$protectfolder[]	= array('path'	=> _CACHE,				'chmod'	=> '0755');
$protectfolder[]	= array('path'	=> _CACHESKIN,			'chmod'	=> '0755');
$protectfolder[]	= array('path'	=> _ROOT.'/plugin',		'chmod'	=> '0755');
$protectfolder[]	= array('path'	=> _ROOT.'/inc',		'chmod'	=> '0755');


/**
* Files
*
* @var array
*/
$protectfiles = array();
$protectfiles[]	= array('path'	=> _ROOCMS.'/config/config.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/config/defines.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/config/set.cfg.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_debug.php',			'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_files.php',			'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_gd.php',				'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_global.php',			'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_mysql.php',			'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_parser.php',			'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_parserDate.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_parserText.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_parserXML.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_rss.php',				'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_structure.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _CLASS.'/class_template.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/ajax.php',				'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/blocks.php',			'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/blocks_html.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/blocks_php.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/config.php',			'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/feeds.php',			'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/feeds_feed.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/index.php',			'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/login.php',			'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/logout.php',			'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/menu.php',				'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/pages.php',			'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/pages_html.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/pages_php.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/security_check.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp/structure.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/acp.php',					'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/functions.php',			'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/functions_blocks.php',		'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/functions_page_feed.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/functions_page_html.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/functions_page_php.php',	'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/init.php',					'chmod'	=> '0644',	'hash'	=> '');
$protectfiles[]	= array('path'	=> _ROOCMS.'/site.php',					'chmod'	=> '0644',	'hash'	=> '');

?>