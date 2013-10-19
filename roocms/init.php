<?php
/**
* @package      RooCMS
* @subpackage	RooCMS initialisation
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.5
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
*   Copyright (C) 2010-2014 alex Roosso aka alexandr Belov info@roocms.com
*
*   This program is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   This program is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with this program.  If not, see <http://www.gnu.org/licenses/
*
*
*   RooCMS - Русская бесплатная система управления сайтом
*   Copyright (C) 2010-2014 alex Roosso (александр Белов) info@roocms.com
*
*   Это программа является свободным программным обеспечением. Вы можете
*   распространять и/или модифицировать её согласно условиям Стандартной
*   Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
*   Обеспечения, версии 3 или, по Вашему желанию, любой более поздней версии.
*
*   Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
*   ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
*   ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
*   Общественную Лицензию GNU для получения дополнительной информации.
*
*   Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
*   с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
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


/**
*  Инициализируем константу указывающую путь к корню сайта
*/
if(!defined('_SITEROOT')) {
	define('_SITEROOT', str_ireplace(DIRECTORY_SEPARATOR."roocms", "", dirname(__FILE__)));
}


/**
* Настраиваем PHP и прочее
*/
if(file_exists(_SITEROOT."/roocms/config/set.cfg.php"))
	require_once(_SITEROOT."/roocms/config/set.cfg.php");
else die("Запуск RooCMS невозможен. Нарушена целостность системы.");

/**
* Загружаем конфигурацию
*/
if(file_exists(_SITEROOT."/roocms/config/config.php"))
	require_once(_SITEROOT."/roocms/config/config.php");
else die("Запуск RooCMS невозможен. Нарушена целостность системы.");

/**
* Заружаем основные константы
*/
if(file_exists(_SITEROOT."/roocms/config/defines.php"))
	require_once(_SITEROOT."/roocms/config/defines.php");
else die("Запуск RooCMS невозможен. Нарушена целостность системы.");

/**
* Запускаем класс отладки
*/
if(file_exists(_CLASS."/class_debug.php"))
	require_once(_CLASS."/class_debug.php");
else die("Запуск RooCMS невозможен. Нарушена целостность системы.");

/**
* Запускаем расширение класса БД MySQL
*/
if(file_exists(_CLASS."/class_mysql_ext.php"))
	require_once(_CLASS."/class_mysql_ext.php");
else die("Запуск RooCMS невозможен. Нарушена целостность системы.");

/**
* Запускаем класс БД MySQL
*/
if(file_exists(_CLASS."/class_mysql.php"))
	require_once(_CLASS."/class_mysql.php");
else die("Запуск RooCMS невозможен. Нарушена целостность системы.");

/**
* Загружаем примитивные функции
*/
if(file_exists(_ROOCMS."/functions.php"))
	require_once(_ROOCMS."/functions.php");
else die("Запуск RooCMS невозможен. Нарушена целостность системы.");

/**
* Запускаем глобальный класс
*/
if(file_exists(_CLASS."/class_global.php"))
	require_once(_CLASS."/class_global.php");
else die("Запуск RooCMS невозможен. Нарушена целостность системы.");

/**
* Запускаем класс парсинга
*/
if(file_exists(_CLASS."/class_parser.php"))
	require_once(_CLASS."/class_parser.php");
else die("Запуск RooCMS невозможен. Нарушена целостность системы.");

/**
* Запускаем класс работы с файлами
*/
if(file_exists(_CLASS."/class_files.php"))
	require_once(_CLASS."/class_files.php");
else die("Запуск RooCMS невозможен. Нарушена целостность системы.");

/**
* Загружаем класс графической обработки
*/
if(file_exists(_CLASS."/class_gd.php"))
	require_once(_CLASS."/class_gd.php");
else die("Запуск RooCMS невозможен. Нарушена целостность системы.");

/**
* Запускаем класс работы с изображениями
*/
if(file_exists(_CLASS."/class_images.php"))
	require_once(_CLASS."/class_images.php");
else die("Запуск RooCMS невозможен. Нарушена целостность системы.");

/**
* Запускаем класс RSS
*/
if(file_exists(_CLASS."/class_rss.php"))
	require_once(_CLASS."/class_rss.php");
else die("Запуск RooCMS невозможен. Нарушена целостность системы.");

/**
* Запускаем библиотеку шаблонизации Smarty
*/
if(file_exists(_LIB."/smarty.php"))
	require_once(_LIB."/smarty.php");
else die("Запуск RooCMS невозможен. Нарушена целостность системы.");

/**
* Запускаем класс шаблонизации RooCMS
*/
if(file_exists(_CLASS."/class_template.php"))
	require_once(_CLASS."/class_template.php");
else die("Запуск RooCMS невозможен. Нарушена целостность системы.");


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

?>