<?php declare(strict_types=1);
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
//	Protect
//---------------------------------------------------------
if(!defined('RooCMS')) {roocms_protect();}
//#########################################################

/**
* DataBase prefix
*/
define('DB_PREFIX', $db_info['prefix']);


/**
* Config $DB Table
*/
const TABLE_MIGRATIONS          = DB_PREFIX.'migrations';
const TABLE_TOKENS              = DB_PREFIX.'tokens';
const TABLE_VERIFICATION_CODES  = DB_PREFIX.'verification_codes';
const TABLE_SETTINGS            = DB_PREFIX.'settings';

/**
* Users Tables
*/
const TABLE_USERS               = DB_PREFIX.'users';
const TABLE_USER_PROFILES       = DB_PREFIX.'user_profiles';
//const TABLE_USER_GROUPS         = DB_PREFIX.'user_groups';
//const TABLE_USER_GROUP_MEMBERS  = DB_PREFIX.'user_group_members';
//const TABLE_USER_ACTIVITY_LOG   = DB_PREFIX.'user_activity_log';

/**
* Structure Content Tables
*/
const TABLE_STRUCTURE           = DB_PREFIX.'structure';

/**
* Content Management Tables
*/
//const TABLE_CATEGORIES = DB_PREFIX.'categories';
//const TABLE_CONTENT = DB_PREFIX.'content';
//const TABLE_TAGS = DB_PREFIX.'tags';
//const TABLE_CONTENT_TAGS = DB_PREFIX.'content_tags';
//const TABLE_CONTENT_TRANSLATIONS = DB_PREFIX.'content_translations';

/**
* Media Tables
*/
const TABLE_MEDIA               = DB_PREFIX.'media';        // Table for media files (images, videos, documents, etc.)
const TABLE_MEDIA_VARS          = DB_PREFIX.'media_vars';   // Table for media variables (sizes, etc.)
const TABLE_MEDIA_RELS          = DB_PREFIX.'media_rels';   // Table for media relations (categories, tags, etc.)

/**
* Other Tables (commented - activate as needed)
*/
//const TABLE_PAGES = DB_PREFIX.'pages';
//const TABLE_TAGS_LINKED = DB_PREFIX.'tags_linked';
//const TABLE_BLOCKS = DB_PREFIX.'blocks';
//const TABLE_IMAGES = DB_PREFIX.'images';
//const TABLE_FILES = DB_PREFIX.'files';
//const TABLE_USERS_PM = DB_PREFIX.'users_pm';
//const TABLE_MAILING = DB_PREFIX.'mailing';
//const TABLE_MAILING_LINKED = DB_PREFIX.'mailing_links';
//const TABLE_LOG = DB_PREFIX.'log';