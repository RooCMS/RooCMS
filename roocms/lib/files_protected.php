<?php
/**
 *   RooCMS - Russian Open Source Free Content Managment System
 *   Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 *   Contacts: <info@roocms.com>
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
 *   RooCMS - Бесплатная система управления контентом с открытым исходным кодом
 *   Copyright © 2010-2018 александр Белов (alex Roosso). Все права защищены.
 *   Для связи: <info@roocms.com>
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

/**
 * @package      RooCMS
 * @subpackage	 Library
 * @author       alex Roosso
 * @copyright    2010-2018 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.3
 * @since        $date$
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################

/**
 * Folders
 *
 * @var array
 */
$protect = array();
$protect[] = array('path' => _ROOCMS,			'chmod'	=> '0755');
$protect[] = array('path' => _ROOCMS.'/config',		'chmod'	=> '0755');
$protect[] = array('path' => _CLASS,			'chmod'	=> '0755');
$protect[] = array('path' => _ROOCMS.'/acp',		'chmod'	=> '0755');
$protect[] = array('path' => _LIB,			'chmod'	=> '0755');
$protect[] = array('path' => _MODULE,			'chmod'	=> '0755');
$protect[] = array('path' => _UI,			'chmod'	=> '0755');
$protect[] = array('path' => _SKIN,			'chmod'	=> '0755');
$protect[] = array('path' => _ACPSKIN,			'chmod'	=> '0755');
$protect[] = array('path' => _UPLOAD,			'chmod'	=> '0755');
$protect[] = array('path' => _UPLOADIMAGES,		'chmod'	=> '0755');
$protect[] = array('path' => _UPLOADFILES,		'chmod'	=> '0755');
$protect[] = array('path' => _CACHE,			'chmod'	=> '0755');
$protect[] = array('path' => _CACHESKIN,		'chmod'	=> '0755');
$protect[] = array('path' => _CACHEIMAGE,		'chmod'	=> '0755');
$protect[] = array('path' => _LOGS,			'chmod'	=> '0755');
$protect[] = array('path' => _SITEROOT.'/plugin',	'chmod'	=> '0755');


/**
 * Files
 */
if(defined('INSTALL')) {
	$protect[] = array('path' => _ROOCMS.'/config/config.php', 'chmod' => '0755',	'hash'	=> '');
}
else {
	$protect[] = array('path' => _ROOCMS.'/config/config.php', 'chmod' => '0644',	'hash'	=> '');
}
$protect[] = array('path' => _ROOCMS.'/config/defines.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/config/set.cfg.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_debug.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_files.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_gd.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_global.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_images.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_logger.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_mysqli_ext.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_mysqli.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_parser.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_parserDate.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_parserText.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_parserXML.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_rss.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_security.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_shteirlitz.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_structure.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_tags.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_template.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_users.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/ajax.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/blocks.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/blocks_html.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/blocks_php.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/config.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/feeds.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/feeds_feed.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/help.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/index.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/login.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/logout.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/menu.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/pages.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/pages_html.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/pages_php.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/security_check.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/structure.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/users.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/ucp/login.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/ucp/logout.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/ucp/pm.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/ucp/security_check.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/ucp/ucp.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/reg.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/repass.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/search.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/tags.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/ucp.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _MODULE.'/auth.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _MODULE.'/express_reg.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _MODULE.'/search.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _MODULE.'/tagcloud.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/functions.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/init.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/site.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/site_blocks.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/site_module.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/site_page_feed.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/site_page_html.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/site_page_php.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/ui.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _LIB.'/files_protected.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _LIB.'/mimetype.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _LIB.'/mysql_schema.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _LIB.'/smarty.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _LIB.'/phpqrcode.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _LIB.'/spiders.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _LIB.'/license.php',		'chmod'	=> '0644',	'hash'	=> '');
if(file_exists(_LOGS.'/errors.log')) {
	$protect[] = array('path' => _LOGS.'/errors.log',	'chmod'	=> '0755',	'hash'	=> '');
}
if(file_exists(_LOGS.'/php_error.log'))	{
	$protect[] = array('path' => _LOGS.'/php_error.log',	'chmod'	=> '0755',	'hash'	=> '');
}