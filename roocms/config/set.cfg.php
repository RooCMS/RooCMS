<?php
/**
* @package	RooCMS
* @subpackage	Configuration
* @author	alex Roosso
* @copyright	2010-2015 (c) RooCMS
* @link		http://www.roocms.com
* @version	1.5.1
* @since	$date$
* @license	http://www.gnu.org/licenses/gpl-3.0.html
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
*   along with this program.  If not, see http://www.gnu.org/licenses/
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
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################

/**
* Системные настройки отладки
*/
define('DEBUGMODE',	false);				# Режим отладки
define('DEVMODE',	false);				# Режим разработчики. Если используете этот режим, рекомендуется так же включить режим отладки.

define('STARTTIME',	microtime());
define('MEMORYUSAGE', 	memory_get_usage());



/**
* Start GZip
*/
ob_start("ob_gzhandler", 9);



/**
* Initialisation session settings
*/
ini_set("session.use_trand_sid",	true);		#	Активируем сессию
ini_set("session.gc_maxlifetime",	1440);		#	Устанавливаем время жизни сессии
ini_set("session.cache_limiter", 	"nocache");	#	нет кешу в сессии
ini_set("session.cache_expire", 	180);		#	Установим срок годности для сессии
ini_set("session.name", 		"PHPSESSID");	#	Имя параметра с сессией
//ini_set("session.save_handler",	"files");	#	Хранить значение сессиий в файлах (разкоментерийте, если испытываете трудности с настройками PHP по-умолчанию)
//ini_set("session.save_path",		"tmp");		#	Путь сохранения файла сессии (разкоментируйте, если испытываете трудности с настройками PHP по-умолчанию)
//session_save_path("tmp");
session_start();



/**
* Настройка "печенек"
*
* "- Будешь на кухне, захвати печеньки.
*  - Печеньки захвачены, мой генерал!"
* 				(с) Шутник
*/
ini_set("session.use_cookie",		true);	#	Активируем "печеньки"
ini_set("session.cookie_domain",	"");	#	Устанавливаем домен для "печенек"
ini_set("session.cookie_path",		"/");	#	Устанавливаем путь к "печенькам"
ini_set("session.cookie_secure",	"");	#	Секрет хороших "печенек"
ini_set("session.cookie_httponly",	true);	#	Секрет хороших "печенек"
setcookie("", "", time()+3600);


/**
* Настройки PHP
*/
@set_magic_quotes_runtime(0);
//setlocale(LC_ALL, 'ru_RU');
ini_set("max_execution_time",		30);
ini_set("memory_limit", 		"96M");
#ini_set("upload_tmp_dir", 		"/tmp");	# временная директория для загружаемых файлов. (разкоментируйте, если испытываете трудности с настройками PHP по-умолчанию)

ini_set("date.timezone",		"Europe/Moscow");
ini_set("default_charset",		"utf-8");
ini_set("default_mimetype",		"text/html");
ini_set("default_socket_timeout",	60);

ini_set("error_prepend_string",		"<script type='text/javascript' src='/plugin/bootstrap.php?short'></script>
						<div class='alert alert-danger t12 text-left in fade col-md-10 col-md-offset-1' role='alert'>");
ini_set("error_append_string",		"</div>");


/**
* Настройки Multibyte
*/
@ini_set("mbstring.internal_encoding",		"UTF-8");
@ini_set("mbstring.http_input",			"auto");
@ini_set("mbstring.http_output",		"UTF-8");
@ini_set("mbstring.substitute_character",	"none");


/**
* Устанавливаем заголовок кодировки
*/
header("Content-type: text/html; charset=utf-8");


/**
* Устанавливаем подпись в заголовке
*/
header("X-Engine: RooCMS");
header("X-Engine-Copyright: 2010-".date("Y")." (c) RooCMS");
header("X-Engine-Site: http://www.roocms.com");

?>