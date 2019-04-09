<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2020 alexandr Belov aka alex Roosso. All rights reserved.
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
* DataBase prefix
*/
define('DB_PREFIX',		$db_info['prefix']);


/**
* Config $DB Table
*/
define('CONFIG_PARTS_TABLE',    DB_PREFIX.'config__parts');
define('CONFIG_TABLE',          DB_PREFIX.'config__settings');
define('STRUCTURE_TABLE',       DB_PREFIX.'structure');
define('PAGES_HTML_TABLE',      DB_PREFIX.'pages__html');
define('PAGES_PHP_TABLE',       DB_PREFIX.'pages__php');
define('PAGES_FEED_TABLE',      DB_PREFIX.'pages__feed');
define('TAGS_TABLE',            DB_PREFIX.'tags');
define('TAGS_LINK_TABLE',       DB_PREFIX.'tags_linked');
define('BLOCKS_TABLE',          DB_PREFIX.'blocks');
define('IMAGES_TABLE',          DB_PREFIX.'images');
define('FILES_TABLE',           DB_PREFIX.'files');
define('USERS_TABLE',           DB_PREFIX.'users');
define('USERS_GROUP_TABLE',     DB_PREFIX.'users_group');
define('USERS_PM_TABLE',        DB_PREFIX.'users_pm');
define('HELP_TABLE',            DB_PREFIX.'help');
define('LOG_TABLE',             DB_PREFIX.'log');


/**
* RooCMS $Path
*/

defined('_SITEROOT') or define('_SITEROOT', str_ireplace(DIRECTORY_SEPARATOR."roocms".DIRECTORY_SEPARATOR."config", "", dirname(__FILE__)));

define('_ROOCMS',       _SITEROOT.'/roocms');
define('_CLASS',        _ROOCMS.'/class');
define('_LIB',          _ROOCMS.'/lib');
define('_MODULE',       _ROOCMS.'/module');
define('_UI',           _ROOCMS.'/ui');
define('_SMARTY',       _LIB.'/smarty');
define('_SKIN',         _SITEROOT.'/skin');
define('_ACPSKIN',      _SKIN.'/acp');
define('_UPLOAD',       _SITEROOT.'/upload');
define('_UPLOADIMAGES', _UPLOAD.'/images');
define('_UPLOADFILES',  _UPLOAD.'/files');
define('_CACHE',        _SITEROOT.'/cache');
define('_LOGS',         _CACHE.'/logs');
define('_CACHESKIN',    _CACHE.'/skin');
define('_CACHEIMAGE',   _CACHE.'/images');


/**
 * Web $Path
 */
define('_DOMAIN',	str_ireplace(array('http://','www.'), '', $_SERVER['HTTP_HOST']));


/**
* $Component
*/
define('INIT_ACP',     _ROOCMS.'/acp.php');
define('INIT_UI',      _ROOCMS.'/ui.php');
define('INIT_UCP',     _UI.'/ucp.php');
define('INIT_SITE',    _ROOCMS.'/site.php');

/**
 * Logs
 */
define('ERRORSLOG', 	_LOGS."/lowerrors.log");
define('SYSERRLOG', 	_LOGS."/syserrors.log");


/**
* $Param
*/
define('EMAIL_MESSAGE_PARAMETERS',	'content-Type: text/html; charset="utf-8"');
define('SCRIPT_NAME',			$_SERVER['SCRIPT_NAME']);
define('CHARSET',			'text/html; charset=utf-8');

define('ROOCMS_MAJOR_VERSION',		'1');
define('ROOCMS_MINOR_VERSION',		'3');
define('ROOCMS_RELEASE_VERSION',	'3');
define('ROOCMS_BUILD_VERSION',		'alpha');
define('ROOCMS_VERSION',		ROOCMS_MAJOR_VERSION.".".ROOCMS_MINOR_VERSION.".".ROOCMS_RELEASE_VERSION);
define('ROOCMS_FULL_VERSION',		ROOCMS_MAJOR_VERSION.".".ROOCMS_MINOR_VERSION.".".ROOCMS_RELEASE_VERSION." ".ROOCMS_BUILD_VERSION);
define('ROOCMS_VERSION_ID',		ROOCMS_MAJOR_VERSION.ROOCMS_MINOR_VERSION.ROOCMS_RELEASE_VERSION);