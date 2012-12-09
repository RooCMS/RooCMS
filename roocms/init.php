<?php
/**
* @package      RooCMS
* @subpackage	RooCMS initialisation
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.1.4
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
*   Данное программное обеспечение является свободным и распространяется
*   по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
*   При любом использовании данного ПО вы должны соблюдать все условия
*   лицензии.
*/

//#########################################################
//	Anti Hack
//---------------------------------------------------------
define('RooCMS', true);
//=========================================================


/**
* SEO Rederict
*/
if($_SERVER['REQUEST_URI'] == "/index.php") {
	header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');
	header('Location: http://'.$_SERVER['HTTP_HOST'].'');
	exit;
}
//=========================================================


/**
*  Инициализируем RooCMS
*/
define('ROOT', $_SERVER['DOCUMENT_ROOT']);
if(file_exists(ROOT."/roocms/config/set.cfg.php"))		require_once ROOT."/roocms/config/set.cfg.php";			// Настраиваем PHP
if(file_exists(ROOT."/roocms/config/config.php"))		require_once ROOT."/roocms/config/config.php";			// конфигурация
if(file_exists(ROOT."/roocms/config/defines.php"))		require_once ROOT."/roocms/config/defines.php";			// заружаем основные константы
if(file_exists(ROOT."/roocms/class/class_debug.php"))	require_once ROOT."/roocms/class/class_debug.php";		// класс отладки
if(file_exists(_CLASS."/class_mysql.php"))				require_once _CLASS."/class_mysql.php";					// класс БД MySQL
if(file_exists(_ROOCMS."/functions.php"))				require_once _ROOCMS."/functions.php";					// функции
if(file_exists(_CLASS."/class_global.php"))				require_once _CLASS."/class_global.php";				// глобальный класс
if(file_exists(_CLASS."/class_parser.php"))				require_once _CLASS."/class_parser.php";				// класс парсинга
if(file_exists(_CLASS."/class_files.php"))				require_once _CLASS."/class_files.php";					// класс файлов
if(file_exists(_CLASS."/class_gd.php"))					require_once _CLASS."/class_gd.php";					// графический класс
if(file_exists(_CLASS."/class_rss.php"))				require_once _CLASS."/class_rss.php";					// класс RSS
if(file_exists(_LIB."/smarty.php"))						require_once _LIB."/smarty.php";						// библиотека шаблонизации Smarty
if(file_exists(_CLASS."/class_template.php"))			require_once _CLASS."/class_template.php";				// класс шаблонизации RooCMS


/**
* Инициализируем класс управления структурой сайта
*/
if(!defined('ACP')) {
	require_once _CLASS."/class_structure.php";
	$structure = new Structure;
}


// $PEAR_PATH_LOCAL = $_SERVER['DOCUMENT_ROOT'].'/pear';

//set_include_path(
//    get_include_path() . PATH_SEPARATOR . $PEAR_PATH_LOCAL
//);

// function __autoload($class_name) {
     // include_once($class_name . "php");
// }

?>