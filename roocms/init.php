<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 * Contacts: <info@roocms.com>
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */

/**
 * @package      RooCMS
 * @subpackage   Initialisation
 * @author       alex Roosso
 * @copyright    2010-2019 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.7.8
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
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
 * Текст сообщения о невозможности запуска RooCMS
 */
define('ROOCMS_NOT_RUNNING', 'Запуск RooCMS невозможен. Нарушена целостность системы.');

/**
* Настраиваем PHP и прочее
*/
if(file_exists(_SITEROOT."/roocms/config/set.cfg.php")) {
	require_once(_SITEROOT."/roocms/config/set.cfg.php");
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
* Загружаем конфигурацию
*/
if(file_exists(_SITEROOT."/roocms/config/config.php")) {
	require_once(_SITEROOT."/roocms/config/config.php");
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
* Заружаем основные константы
*/
if(file_exists(_SITEROOT."/roocms/config/defines.php")) {
	require_once(_SITEROOT."/roocms/config/defines.php");
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
 * Загружаем примитивные функции
 */
if(file_exists(_ROOCMS."/functions.php")) {
	require_once(_ROOCMS."/functions.php");
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
* Запускаем класс отладки
*/
if(file_exists(_CLASS."/class_debug.php")) {
	require_once(_CLASS."/class_debug.php");

	/**
	 * Инициализируем класс
	 */
	$debug = new Debug;

	/**
	 * Debug функция
	 * Синоним $Debug->debug();
	 *
	 * @param mixed $obj
	 *
	 * @example debug($var);
	 */
	function debug($obj) {
		global $debug;
		$debug->godebug($obj);
	}
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
* Запускаем расширение класса БД MySQL
*/
if(file_exists(_CLASS."/class_mysqli_ext.php")) {
	require_once(_CLASS."/class_mysqli_ext.php");
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
* Запускаем класс БД MySQL
*/
if(file_exists(_CLASS."/class_mysqli.php")) {
	require_once(_CLASS."/class_mysqli.php");
	/**
	 * Инициализируем класс
	 */
	$db = new MySQLiDatabase;
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
* Запускаем глобальный класс
*/
if(file_exists(_CLASS."/class_global.php")) {
	require_once(_CLASS."/class_global.php");
	/**
	 * Инициализируем класс
	 */
	$roocms = new Globals;
	$config =& $roocms->config;
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
 * Запускаем класс логгера
 */
if(file_exists(_CLASS."/class_logger.php")) {
	require_once(_CLASS."/class_logger.php");
	/**
	 * Инициализируем класс
	 */
	$logger	= new Logger;
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
* Запускаем класс парсинга
*/
if(file_exists(_CLASS."/class_parser.php")) {
	require_once(_CLASS."/class_parser.php");
	/**
	 * Инициализируем класс
	 */
	$parse 	= new Parser;
	$get	=& $parse->get;
	$post	=& $parse->post;
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
 * Запускаем расширение класса Security
 */
if(file_exists(_CLASS."/class_shteirlitz.php")) {
	require_once(_CLASS."/class_shteirlitz.php");
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
 * Запускаем класс безопастности
 */
if(file_exists(_CLASS."/class_security.php")) {
	require_once(_CLASS."/class_security.php");
	/**
	 * Инициализируем класс
	 */
	$security = new Security;
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
 * Запускаем класс определения пользователей
 */
if(file_exists(_CLASS."/class_users.php")) {
	require_once(_CLASS."/class_users.php");
	/**
	 * Инициализируем класс
	 */
	$users = new Users;
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
* Запускаем класс работы с файлами
*/
if(file_exists(_CLASS."/class_files.php")) {
	require_once(_CLASS."/class_files.php");
	/**
	 * Инициализируем класс
	 */
	$files = new Files;
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
 * Загружаем класс функций расширений графической обработки
 */
if(file_exists(_CLASS."/class_gd_ext.php")) {
	require_once(_CLASS."/class_gd_ext.php");
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
* Загружаем класс графической обработки
*/
if(file_exists(_CLASS."/class_gd.php")) {
	require_once(_CLASS."/class_gd.php");
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
* Запускаем класс работы с изображениями
*/
if(file_exists(_CLASS."/class_images.php")) {
	require_once(_CLASS."/class_images.php");
	/**
	 * Инициализируем класс
	 */
	$img = new Images;
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
* Запускаем класс RSS
*/
if(file_exists(_CLASS."/class_rss.php")) {
	require_once(_CLASS."/class_rss.php");
	/**
	 * Инициализируем класс
	 */
	$rss = new RSS;
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
* Запускаем библиотеку шаблонизации Smarty
*/
if(file_exists(_LIB."/smarty.php")) {
	require_once(_LIB."/smarty.php");
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
* Запускаем класс шаблонизации RooCMS
*/
if(file_exists(_CLASS."/class_template.php")) {
	require_once(_CLASS."/class_template.php");
	/**
	 * Запускаем шаблонизатор
	 */
	$tpl = new Template;
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
 * Запускаем класс для работы с Тегами
 */
if(file_exists(_CLASS."/class_tags.php")) {
	require_once(_CLASS."/class_tags.php");
	/**
	 * Инициализируем класс
	 */
	$tags = new Tags;
}
else {
	die(ROOCMS_NOT_RUNNING);
}

/**
* Инициализируем класс управления структурой сайта
*/
if(!defined('ACP') && file_exists(_CLASS."/class_structure.php")) {
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