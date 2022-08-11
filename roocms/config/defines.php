<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2023 alexandr Belov aka alex Roosso. All rights reserved.
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
define('DB_PREFIX', $db_info['prefix']);


/**
* Config $DB Table
*/
const CONFIG_PARTS_TABLE = DB_PREFIX.'config__parts';
const CONFIG_TABLE       = DB_PREFIX.'config__settings';
const STRUCTURE_TABLE    = DB_PREFIX.'structure';
const PAGES_HTML_TABLE   = DB_PREFIX.'pages__html';
const PAGES_STORY_TABLE  = DB_PREFIX.'pages__story';
const PAGES_PHP_TABLE    = DB_PREFIX.'pages__php';
const PAGES_FEED_TABLE   = DB_PREFIX.'pages__feed';
const TAGS_TABLE         = DB_PREFIX.'tags';
const TAGS_LINK_TABLE    = DB_PREFIX.'tags_linked';
const BLOCKS_TABLE       = DB_PREFIX.'blocks';
const IMAGES_TABLE       = DB_PREFIX.'images';
const FILES_TABLE        = DB_PREFIX.'files';
const USERS_TABLE        = DB_PREFIX.'users';
const USERS_GROUP_TABLE  = DB_PREFIX.'users_group';
const USERS_PM_TABLE     = DB_PREFIX.'users_pm';
const MAILING_TABLE      = DB_PREFIX.'mailing';
const MAILING_LINK_TABLE = DB_PREFIX.'mailing_links';
const HELP_TABLE         = DB_PREFIX.'help';
const LOG_TABLE          = DB_PREFIX.'log';


/**
* RooCMS $Path
*/

defined('_SITEROOT') or define('_SITEROOT', str_ireplace(DIRECTORY_SEPARATOR."roocms".DIRECTORY_SEPARATOR."config", "", dirname(__FILE__)));

const _ROOCMS       = _SITEROOT.'/roocms';
const _CLASS        = _ROOCMS.'/class';
const _LIB          = _ROOCMS.'/lib';
const _MODULE       = _ROOCMS.'/module';
const _UI           = _ROOCMS.'/ui';
const _SMARTY       = _LIB.'/smarty';
const _SKIN         = _SITEROOT.'/skin';
const _ACPSKIN      = _SKIN.'/acp';
const _UPLOAD       = _SITEROOT.'/upload';
const _UPLOADIMAGES = _UPLOAD.'/images';
const _UPLOADFILES  = _UPLOAD.'/files';
const _CACHE        = _SITEROOT.'/cache';
const _LOGS         = _CACHE.'/logs';
const _CACHESKIN    = _CACHE.'/skin';
const _CACHEIMAGE   = _CACHE.'/images';


/**
 * Web $Path
 */
define('_DOMAIN',	str_ireplace(array('http://','www.'), '', $_SERVER['HTTP_HOST']));


/**
* $Component
*/
const INIT_ACP  = _ROOCMS.'/acp.php';
const INIT_UI   = _ROOCMS.'/ui.php';
const INIT_UCP  = _UI.'/ucp.php';
const INIT_SITE = _ROOCMS.'/site.php';

/**
 * Logs
 */
const ERRORSLOG = _LOGS."/lowerrors.log";
const SYSERRLOG = _LOGS."/syserrors.log";


/**
* $Param
*/
define("SCRIPT_NAME", $_SERVER['SCRIPT_NAME']);

const EMAIL_MESSAGE_PARAMETERS = 'content-Type: text/html; charset="utf-8"';
const CHARSET                  = 'text/html; charset=utf-8';

const ROOCMS_MAJOR_VERSION   = '1';
const ROOCMS_MINOR_VERSION   = '4';
const ROOCMS_RELEASE_VERSION = '0';
const ROOCMS_BUILD_VERSION   = '';
const ROOCMS_VERSION         = ROOCMS_MAJOR_VERSION.".".ROOCMS_MINOR_VERSION.".".ROOCMS_RELEASE_VERSION;
const ROOCMS_FULL_VERSION    = ROOCMS_MAJOR_VERSION.".".ROOCMS_MINOR_VERSION.".".ROOCMS_RELEASE_VERSION." ".ROOCMS_BUILD_VERSION;
const ROOCMS_VERSION_ID      = ROOCMS_MAJOR_VERSION.ROOCMS_MINOR_VERSION.ROOCMS_RELEASE_VERSION;