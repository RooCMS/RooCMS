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
//	Anti Hack
//---------------------------------------------------------
define('RooCMS', true);
//#########################################################


/**
* SEO Rederict
*/
if($_SERVER['REQUEST_URI'] == "/index.php") {
	header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');
	header('Location: http://'.$_SERVER['HTTP_HOST'].'');
	exit;
}

/**
 *  Инициализируем константу указывающую путь к корню сайта
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
* Настраиваем PHP и прочее
*/
if(check_file_core(_SITEROOT."/roocms/config/set.cfg.php")) {
	require_once(_SITEROOT."/roocms/config/set.cfg.php");
}

/**
* Загружаем конфигурацию RooCMS
*/
if(check_file_core(_SITEROOT."/roocms/config/config.php")) {
	require_once(_SITEROOT."/roocms/config/config.php");
}

/**
* Заружаем основные константы RooCMS
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
	/**
	 * Инициализируем класс
	 */
	$db = new MySQLiDB;
}

/**
* Запускаем глобальный класс
*/
if(check_file_core(_CLASS."/class_global.php")) {
	require_once(_CLASS."/class_global.php");
	/**
	 * Инициализируем класс
	 */
	$roocms = new RooCMS_Global;
	$config =& $roocms->config;
}

/**
 * Запускаем класс логгера
 */
if(check_file_core(_CLASS."/class_logger.php")) {
	require_once(_CLASS."/class_logger.php");
	/**
	 * Инициализируем класс
	 */
	$logger	= new Logger;
}

/**
* Запускаем класс парсинга
*/
if(check_file_core(_CLASS."/class_parser.php")) {
	require_once(_CLASS."/class_parser.php");
	/**
	 * Инициализируем класс
	 */
	$parse 	= new Parser;
	$get	=& $parse->get;
	$post	=& $parse->post;
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
	/**
	 * Инициализируем класс
	 */
	$security = new Security;
}

/**
 * Запускаем класс определения пользователей
 */
if(check_file_core(_CLASS."/class_users.php")) {
	require_once(_CLASS."/class_users.php");
	/**
	 * Инициализируем класс
	 */
	$users = new Users;
}

/**
 * Запускаем класс для работы с Тегами
 */
if(check_file_core(_CLASS."/class_tags.php")) {
	require_once(_CLASS."/class_tags.php");
	/**
	 * Инициализируем класс
	 */
	$tags = new Tags;
}

/**
* Запускаем класс работы с файлами
*/
if(check_file_core(_CLASS."/class_files.php")) {
	require_once(_CLASS."/class_files.php");
	/**
	 * Инициализируем класс
	 */
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
	/**
	 * Инициализируем класс
	 */
	$img = new Images;
}

/**
 * Запускаем класс XML
 */
if(check_file_core(_CLASS."/class_xml.php")) {
	require_once(_CLASS."/class_xml.php");
	$xml = new XML;
}

/**
* Запускаем класс RSS
*/
if(check_file_core(_CLASS."/class_rss.php")) {
	require_once(_CLASS."/class_rss.php");
	/**
	 * Инициализируем класс
	 */
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
	/**
	 * Запускаем шаблонизатор
	 */
	$tpl = new Template;
}

/**
* Инициализируем класс управления структурой сайта
*/
if(!defined('ACP') && check_file_core(_CLASS."/class_structure.php")) {
	require_once(_CLASS."/class_structure.php");
	$structure = new Structure;
}

// $PEAR_PATH_LOCAL = _SITEROOT.'/pear';

//set_include_path(
//    get_include_path() . PATH_SEPARATOR . $PEAR_PATH_LOCAL
//);

// function __autoload($class_name) {
     // include_once($class_name . "php");
// }