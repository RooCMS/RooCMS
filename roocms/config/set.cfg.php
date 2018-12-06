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
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################

/**
* Системные настройки отладки
*/
define('DEBUGMODE',	false);				# Режим отладки


/**
 * Получаем количество выделенной памяти в самом начале работы
 * В дальнейшем будет вычеслять продуктивность
 */
define('MEMORYUSAGE', 	memory_get_usage());


/**
 * Проверяем работает ли Apache
 */
if(stristr(strtolower($_SERVER['SERVER_SOFTWARE']), 'apache') === FALSE) {
	define('APACHE', false);
}
else {
	define('APACHE', true);
}


/**
* Start GZip
*/
ob_start("ob_gzhandler", 8);



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
if(session_status() == 1) {
	session_start();
}


/**
* Настройка "печенек"
*
* "- Будешь на кухне, захвати печеньки.
*  - Печеньки захвачены, мой генерал!"
* 				(с) Шутник
*/
ini_set("session.use_cookie",			true);	#	Активируем "печеньки"
if(APACHE) {
	ini_set("session.cookie_domain",	"");	#	Устанавливаем домен для "печенек"
	ini_set("session.cookie_path",		"/");	#	Устанавливаем путь к "печенькам"
	ini_set("session.cookie_secure",	"");	#	Секрет хороших "печенек"
	ini_set("session.cookie_httponly",	true);	#	Секрет хороших "печенек"
}
//setcookie("", "", time()+3600);


/**
* Настройки PHP
*/
//setlocale(LC_ALL, 'ru_RU.UTF8', 'ru.UTF8', 'ru_RU.UTF-8', 'ru.UTF-8', 'ru_RU', 'ru');
ini_set("max_execution_time",		30);
ini_set("memory_limit", 		"512M");
#ini_set("upload_tmp_dir", 		"/tmp");	# временная директория для загружаемых файлов. (разкоментируйте, если испытываете трудности с настройками PHP по-умолчанию)

ini_set("serialize_precision", 		"-1");

ini_set("date.timezone",		"Europe/Moscow");
ini_set("default_charset",		"utf-8");
ini_set("default_mimetype",		"text/html");
ini_set("default_socket_timeout",	60);

ini_set("error_prepend_string",		"<script type='text/javascript' src='/plugin/bootstrap.php?short'></script>
						<div class='alert alert-danger t12 text-left in fade col-md-10 col-md-offset-1' role='alert'>");
ini_set("error_append_string",		"</div>");


/**
* Настройки Multibyte String
*/
ini_set("mbstring.internal_encoding",		"UTF-8");
ini_set("mbstring.http_input",			"auto");
ini_set("mbstring.http_output",			"UTF-8");
ini_set("mbstring.substitute_character",	"none");


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