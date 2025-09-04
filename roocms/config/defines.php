<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * © 2010-2025 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      https://www.roocms.com
 * @license   https://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program. If not, see https://www.gnu.org/licenses/
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('403:Access denied');
}
//#########################################################


/**
* DataBase prefix
*/
define('DB_PREFIX', $db_info['prefix']);


/**
* Config $DB Table
*/
const TABLE_CONFIG_PARTS    = DB_PREFIX.'config__parts';
const TABLE_CONFIG_SETTINGS = DB_PREFIX.'config__settings';
const TABLE_MIGRATIONS      = DB_PREFIX.'migrations';
const TABLE_TOKENS          = DB_PREFIX.'tokens';

/**
* Users Tables
*/
const TABLE_USERS = DB_PREFIX.'users';
const TABLE_USER_PROFILES = DB_PREFIX.'user_profiles';
const TABLE_USER_ACTIVITY_LOG = DB_PREFIX.'user_activity_log';

/**
* Content Management Tables
*/
//const TABLE_CATEGORIES = DB_PREFIX.'categories';
//const TABLE_CONTENT = DB_PREFIX.'content';
//const TABLE_TAGS = DB_PREFIX.'tags';
//const TABLE_CONTENT_TAGS = DB_PREFIX.'content_tags';
//const TABLE_CONTENT_TRANSLATIONS = DB_PREFIX.'content_translations';

/**
* Other Tables (commented - activate as needed)
*/
//const TABLE_STRUCTURE = DB_PREFIX.'structure';
//const TABLE_PAGES = DB_PREFIX.'pages';
//const TABLE_TAGS = DB_PREFIX.'tags';
//const TABLE_TAGS_LINKED = DB_PREFIX.'tags_linked';
//const TABLE_BLOCKS = DB_PREFIX.'blocks';
//const TABLE_IMAGES = DB_PREFIX.'images';
//const TABLE_FILES = DB_PREFIX.'files';
//const TABLE_USERS = DB_PREFIX.'users';
//const TABLE_USERS_GROUP = DB_PREFIX.'users_group';
//const TABLE_USERS_PM = DB_PREFIX.'users_pm';
//const TABLE_MAILING = DB_PREFIX.'mailing';
//const TABLE_MAILING_LINKED = DB_PREFIX.'mailing_links';
//const TABLE_LOG = DB_PREFIX.'log';


/**
* RooCMS $Path
*/
const _ROOCMS       = _SITEROOT.'/roocms';
const _CLASS        = _ROOCMS.'/class';
const _API          = _SITEROOT.'/api';
const _UPLOAD       = _SITEROOT.'/upload';
const _UPLOADIMAGES = _UPLOAD.'/images';
const _UPLOADFILES  = _UPLOAD.'/files';
const _MIGRATIONS   = _ROOCMS.'/database/migrations';
const _STORAGE      = _SITEROOT.'/storage';
const _ASSETS       = _STORAGE.'/assets';
const _LOGS         = _STORAGE.'/logs';


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