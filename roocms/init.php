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
//	Anti Hack
//---------------------------------------------------------
const RooCMS = true;
//#########################################################

/**
* SEO Rederict
*/
if($_SERVER['REQUEST_URI'] == "/index.php") {
	$http = isset($_SERVER["HTTPS"]) ? 'https' : 'http';
	header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');
	header('Location: '.$http.'://'.$_SERVER['HTTP_HOST'].'');
	exit;
}


/**
 *  init root cms path
 */
defined('_SITEROOT') or define('_SITEROOT', str_ireplace(DIRECTORY_SEPARATOR."roocms", "", dirname(__FILE__)));


/**
* Sys & PHP Settings
*/
require_once(_SITEROOT."/roocms/config/set.cfg.php");

/**
* Load Config
*/
require_once(_SITEROOT."/roocms/config/config.php");

/**
* Load const
*/
require_once(_SITEROOT."/roocms/config/defines.php");

/**
 * Загружаем примитивные функции
 */
require_once(_ROOCMS."/functions.php");

/**
* Load Debug Class
*/
require_once(_CLASS."/trait_debugLog.php");
require_once(_CLASS."/class_debuger.php");
$debug = new Debuger;

/**
 * init debug function
 *
 * @param mixed $obj
 *
 * @example debug($var);
 */
function debug($obj) {
	global $debug;
	$debug->rundebug($obj);
}

/**
* Init BD class
*/
require_once(_CLASS."/trait_mysqlidbExtends.php");
require_once(_CLASS."/class_mysqlidb.php");
$db = new MySQLiDB;

/**
* Load global class
*/
require_once(_CLASS."/class_global.php");
$roocms = new RooCMS_Global;
$config =& $roocms->config;

/**
 * Load logger class
 */
require_once(_CLASS."/class_logger.php");
$logger	= new Logger;

/**
 * Class handler $_POST
 */
require_once(_CLASS."/class_post.php");
$post = new Post;

/**
* Init Parser Engine
*/
require_once(_CLASS."/trait_parserValidData.php");
require_once(_CLASS."/class_parser.php");
$parse 	= new Parser;
$get	=& $parse->get;

/**
 * Init Security class
 */
require_once(_CLASS."/class_shteirlitz.php");
require_once(_CLASS."/class_security.php");
$security = new Security;

/**
 * Mailing class
 */
require_once(_CLASS."/class_mailing.php");
$mailer = new Mailing;

/**
 * Init User class
 */
require_once(_CLASS."/trait_userGroups.php");
require_once(_CLASS."/trait_userAvatar.php");
require_once(_CLASS."/class_users.php");
$users = new Users;

/**
 * Init Tags class
 */
require_once(_CLASS."/class_tags.php");
$tags = new Tags;

/**
 * Load handler Files
 */
require_once(_CLASS."/class_files.php");
$files = new Files;

/**
 * Graphic class load
 */
require_once(_CLASS."/trait_gdExtends.php");
require_once(_CLASS."/class_gd.php");

/**
 * Load handler Images
 */
require_once(_CLASS."/class_images.php");
$img = new Images;

/**
 * XML
 */
require_once(_CLASS."/class_xml.php");
$xml = new XML;

/**
 * RSS
 */
require_once(_CLASS."/class_rss.php");
$rss = new RSS;

/**
 * Load Smarty
 */
require_once(_LIB."/smarty.php");

/**
 * Template class
 */
require_once(_CLASS."/trait_templateExtends.php");
require_once(_CLASS."/class_template.php");
$tpl = new Template;


/**
 * UI
 */
if(!defined('ACP') && !defined('INSTALL')) {
	/**
	 * Init site structure
	 */
	require_once(_CLASS."/class_structure.php");
	$structure = new Structure;

	/**
	 * init navigation
	 */
	require_once(_CLASS."/class_navigation.php");
	$nav = new Navigation;
}

// $PEAR_PATH_LOCAL = _SITEROOT.'/pear';

//set_include_path(
//    get_include_path() . PATH_SEPARATOR . $PEAR_PATH_LOCAL
//);
