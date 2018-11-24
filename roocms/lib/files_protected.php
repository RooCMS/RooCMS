<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
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
$protect = [];
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
$protect[] = array('path' => _LOGS,			'chmod'	=> '0755');
$protect[] = array('path' => _SITEROOT.'/plugin',	'chmod'	=> '0755');


/**
 * Files
 */

$protect[] = array('path' => _ROOCMS.'/config/config.php', 		'chmod' => '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/config/defines.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/config/set.cfg.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_debuger.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_files.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_gd.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_gdExtends.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_global.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_images.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_logger.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_mysqlidb.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_parser.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_parserDate.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_parserText.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_rss.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_security.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_shteirlitz.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_structure.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_tags.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_template.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_users.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/class_xml.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/trait_feedExtends.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _CLASS.'/trait_mysqlidbExtends.php',	'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/ajax.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/blocks.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/blocks_html.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/blocks_php.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/config.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/config_action.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/feeds.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/feeds_feed.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/help.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/index.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/login.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/logout.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/logs.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/mailing.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/menu.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/pages.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/pages_html.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/pages_php.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/security_check.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/structure.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp/users.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _MODULE.'/auth.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _MODULE.'/express_reg.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _MODULE.'/search.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _MODULE.'/tagcloud.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/ucp/login.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/ucp/logout.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/ucp/pm.php',				'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/ucp/security_check.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/ucp/ucp.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/fl152.php',				'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/reg.php',				'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/repass.php',				'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/search.php',				'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/tags.php',				'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _UI.'/ucp.php',				'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/acp.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/functions.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/init.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/site.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/site_blocks.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/site_modules.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/site_pageFeed.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/site_pageHTML.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/site_pagePHP.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _ROOCMS.'/ui.php',				'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _LIB.'/files_protected.php',		'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _LIB.'/mimetype.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _LIB.'/mysql_schema.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _LIB.'/smarty.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _LIB.'/phpqrcode.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _LIB.'/spiders.php',			'chmod'	=> '0644',	'hash'	=> '');
$protect[] = array('path' => _LIB.'/license.php',			'chmod'	=> '0644',	'hash'	=> '');
if(is_file(_LOGS.'/errors.log')) {
	$protect[] = array('path' => _LOGS.'/errors.log',		'chmod'	=> '0644',	'hash'	=> '');
}
if(is_file(_LOGS.'/php_error.log'))	{
	$protect[] = array('path' => _LOGS.'/php_error.log',		'chmod'	=> '0644',	'hash'	=> '');
}