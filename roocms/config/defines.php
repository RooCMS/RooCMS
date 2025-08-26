<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2025 alexandr Belov aka alex Roosso. All rights reserved.
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
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Access denied');
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
const PAGES_TABLE        = DB_PREFIX.'pages';
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
const LOG_TABLE          = DB_PREFIX.'log';


/**
* RooCMS $Path
*/

const _SITEROOT     = str_ireplace(DIRECTORY_SEPARATOR."roocms".DIRECTORY_SEPARATOR."config", "", dirname(__FILE__));
const _ROOCMS       = _SITEROOT.'/roocms';
const _CLASS        = _ROOCMS.'/class';
const _API          = _SITEROOT.'/api';
const _UPLOAD       = _SITEROOT.'/upload';
const _UPLOADIMAGES = _UPLOAD.'/images';
const _UPLOADFILES  = _UPLOAD.'/files';
const _CACHE        = _SITEROOT.'/cache';
const _LOGS         = _CACHE.'/logs';


/**
 * Web $Path
 */
define('_DOMAIN',	str_ireplace(array('http://','www.'), '', $site['domain']));


/**
 * Logs
 */
const ERRORSLOG = _LOGS."/lowerrors.log";
const SYSERRLOG = _LOGS."/syserrors.log";


/**
 * $Param
 */
const EMAIL_MESSAGE_PARAMETERS = 'content-Type: text/html; charset="utf-8"';
const CHARSET                  = 'text/html; charset=utf-8';

const ROOCMS_MAJOR_VERSION   = '2';
const ROOCMS_MINOR_VERSION   = '0';
const ROOCMS_RELEASE_VERSION = '0';
const ROOCMS_BUILD_VERSION   = 'alpha';
const ROOCMS_VERSION         = ROOCMS_MAJOR_VERSION.".".ROOCMS_MINOR_VERSION.".".ROOCMS_RELEASE_VERSION;
const ROOCMS_FULL_VERSION    = ROOCMS_MAJOR_VERSION.".".ROOCMS_MINOR_VERSION.".".ROOCMS_RELEASE_VERSION." ".ROOCMS_BUILD_VERSION;
const ROOCMS_VERSION_ID      = ROOCMS_MAJOR_VERSION.ROOCMS_MINOR_VERSION.ROOCMS_RELEASE_VERSION;