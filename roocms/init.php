<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2019 alexandr Belov aka alex Roosso. All rights reserved.
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
if(!defined('_SITEROOT')) {
	define('_SITEROOT', str_ireplace(DIRECTORY_SEPARATOR."roocms", "", dirname(__FILE__)));
}


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
* Запускаем класс отладки
*/
if(check_file_core(_CLASS."/class_debuger.php")) {

	require_once(_CLASS."/class_debuger.php");

	/**
	 * Инициализируем класс
	 */
	$debug = new Debuger;

	/**
	 * Debug функция
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
* Запускаем расширение класса БД MySQL
*/
if(check_file_core(_CLASS."/trait_mysqlidbExtends.php")) {
	require_once(_CLASS."/trait_mysqlidbExtends.php");
}

/**
* Запускаем класс БД MySQL
*/
if(check_file_core(_CLASS."/class_mysqlidb.php")) {
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
 * Запускаем класс обработчик $_POST
 */
if(check_file_core(_CLASS."/class_post.php")) {
	require_once(_CLASS."/class_post.php");
	$post = new Post;
}

/**
* Запускаем класс парсинга
*/
if(check_file_core(_CLASS."/class_parser.php")) {
	require_once(_CLASS."/class_parser.php");
	$parse 	= new Parser;
	$get	=& $parse->get;
}

/**
 * Запускаем расширение класса Security
 */
if(check_file_core(_CLASS."/class_shteirlitz.php")) {
	require_once(_CLASS."/class_shteirlitz.php");
}

/**
 * Запускаем класс безопастности
 */
if(check_file_core(_CLASS."/class_security.php")) {
	require_once(_CLASS."/class_security.php");
	$security = new Security;
}

/**
 * User groups trait
 */
if(check_file_core(_CLASS."/trait_usergroups.php")) {
	require_once(_CLASS."/trait_usergroups.php");
}

/**
 * Запускаем класс определения пользователей
 */
if(check_file_core(_CLASS."/class_users.php")) {
	require_once(_CLASS."/class_users.php");
	$users = new Users;
}

/**
 * Запускаем класс для работы с Тегами
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
 * Загружаем класс функций расширений графической обработки
 */
if(check_file_core(_CLASS."/trait_gdExtends.php")) {
	require_once(_CLASS."/trait_gdExtends.php");
}

/**
* Загружаем класс графической обработки
*/
if(check_file_core(_CLASS."/class_gd.php")) {
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
* Запускаем класс шаблонизации RooCMS
*/
if(check_file_core(_CLASS."/class_template.php")) {
	require_once(_CLASS."/class_template.php");
	$tpl = new Template;
}


/**
 * UI
 */
if(!defined('ACP')) {
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

// function __autoload($class_name) {
     // include_once($class_name . "php");
// }