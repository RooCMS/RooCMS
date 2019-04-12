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
//	Anti Hack
//---------------------------------------------------------
define('RooCMS', true);
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
 * Smart function for check core files
 *
 * @param $file
 *
 * @return bool
 */
function check_file_core($file) {
	if(is_file($file)) {
		return true;
	}
	else {
		die("Запуск RooCMS невозможен. Нарушена целостность системы.");
	}
}


/**
* Sys & PHP Settings
*/
if(check_file_core(_SITEROOT."/roocms/config/set.cfg.php")) {
	require_once(_SITEROOT."/roocms/config/set.cfg.php");
}

/**
* Load Config
*/
if(check_file_core(_SITEROOT."/roocms/config/config.php")) {
	require_once(_SITEROOT."/roocms/config/config.php");
}

/**
* Load const
*/
if(check_file_core(_SITEROOT."/roocms/config/defines.php")) {
	require_once(_SITEROOT."/roocms/config/defines.php");
}

/**
 * Загружаем примитивные функции
 */
if(check_file_core(_ROOCMS."/functions.php")) {
	require_once(_ROOCMS."/functions.php");
}

/**
* Load Debug Class
*/
if(check_file_core(_CLASS."/class_debuger.php")) {
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
}

/**
* Init BD class
*/
if(check_file_core(_CLASS."/class_mysqlidb.php")) {
	if(check_file_core(_CLASS."/trait_mysqlidbExtends.php")) {
		require_once(_CLASS."/trait_mysqlidbExtends.php");
	}

	require_once(_CLASS."/class_mysqlidb.php");
	$db = new MySQLiDB;
}

/**
* Запускаем глобальный класс
*/
if(check_file_core(_CLASS."/class_global.php")) {
	require_once(_CLASS."/class_global.php");
	$roocms = new RooCMS_Global;
	$config =& $roocms->config;
}

/**
 * Запускаем класс логгера
 */
if(check_file_core(_CLASS."/class_logger.php")) {
	require_once(_CLASS."/class_logger.php");
	$logger	= new Logger;
}

/**
 * Class handler $_POST
 */
if(check_file_core(_CLASS."/class_post.php")) {
	require_once(_CLASS."/class_post.php");
	$post = new Post;
}

/**
* Init Parser Engine
*/
if(check_file_core(_CLASS."/class_parser.php")) {
	if(check_file_core(_CLASS."/trait_parserValidData.php")) {
		require_once(_CLASS."/trait_parserValidData.php");
	}

	require_once(_CLASS."/class_parser.php");
	$parse 	= new Parser;
	$get	=& $parse->get;
}

/**
 * Init Security class
 */
if(check_file_core(_CLASS."/class_security.php")) {
	if(check_file_core(_CLASS."/class_shteirlitz.php")) {
		require_once(_CLASS."/class_shteirlitz.php");
	}

	require_once(_CLASS."/class_security.php");
	$security = new Security;
}

/**
 * Mailing class
 */
if(check_file_core(_CLASS."/class_mailing.php")) {
	require_once(_CLASS."/class_mailing.php");
	$mailer = new Mailing;
}

/**
 * Init User class
 */
if(check_file_core(_CLASS."/class_users.php")) {
	if(check_file_core(_CLASS."/trait_userGroups.php")) {
		require_once(_CLASS."/trait_userGroups.php");
	}
	if(check_file_core(_CLASS."/trait_userAvatar.php")) {
		require_once(_CLASS."/trait_userAvatar.php");
	}

	require_once(_CLASS."/class_users.php");
	$users = new Users;
}

/**
 * Init Tags class
 */
if(check_file_core(_CLASS."/class_tags.php")) {
	require_once(_CLASS."/class_tags.php");
	$tags = new Tags;
}

/**
 * Запускаем класс работы с файлами
 */
if(check_file_core(_CLASS."/class_files.php")) {
	require_once(_CLASS."/class_files.php");
	$files = new Files;
}

/**
 * Graphic class load
 */
if(check_file_core(_CLASS."/class_gd.php")) {
	if(check_file_core(_CLASS."/trait_gdExtends.php")) {
		require_once(_CLASS."/trait_gdExtends.php");
	}

	require_once(_CLASS."/class_gd.php");
}

/**
 * Запускаем класс работы с изображениями
 */
if(check_file_core(_CLASS."/class_images.php")) {
	require_once(_CLASS."/class_images.php");
	$img = new Images;
}

/**
 * XML
 */
if(check_file_core(_CLASS."/class_xml.php")) {
	require_once(_CLASS."/class_xml.php");
	$xml = new XML;
}

/**
 * RSS
 */
if(check_file_core(_CLASS."/class_rss.php")) {
	require_once(_CLASS."/class_rss.php");
	$rss = new RSS;
}

/**
 * Запускаем библиотеку шаблонизации Smarty
 */
if(check_file_core(_LIB."/smarty.php")) {
	require_once(_LIB."/smarty.php");
}

/**
 * Template class
 */
if(check_file_core(_CLASS."/class_template.php")) {
	if(check_file_core(_CLASS."/trait_templateExtends.php")) {
		require_once(_CLASS."/trait_templateExtends.php");
	}

	require_once(_CLASS."/class_template.php");
	$tpl = new Template;
}


/**
 * UI
 */
if(!defined('ACP') && !defined('INSTALL')) {
	/**
	 * Инициализируем класс управления структурой сайта
	 */
	if(check_file_core(_CLASS."/class_structure.php")) {
		require_once(_CLASS."/class_structure.php");
		$structure = new Structure;
	}

	/**
	 * Инициализируем класс навигации по сайта
	 */
	if(check_file_core(_CLASS."/class_navigation.php")) {
		require_once(_CLASS."/class_navigation.php");
		$nav = new Navigation;
	}
}

// $PEAR_PATH_LOCAL = _SITEROOT.'/pear';

//set_include_path(
//    get_include_path() . PATH_SEPARATOR . $PEAR_PATH_LOCAL
//);
