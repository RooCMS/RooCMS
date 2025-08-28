<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * © 2010-2025 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program. If not, see http://www.gnu.org/licenses/
 */

//#########################################################
//	Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('403:Access denied');
}
//#########################################################

/**
 * define root roocms path
 */
if(!defined('_SITEROOT')) {
    define('_SITEROOT', str_ireplace(DIRECTORY_SEPARATOR."roocms", "", dirname(__FILE__)));
}

/**
 * Include sys & php settings
 */
require_once _SITEROOT."/roocms/config/set.cfg.php";

/**
 * Include config
 */
require_once _SITEROOT."/roocms/config/config.php";

/**
 * Include const
 */
require_once _SITEROOT."/roocms/config/defines.php";

