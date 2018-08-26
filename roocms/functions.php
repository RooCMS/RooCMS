<?php
/**
 *   RooCMS - Open Source Free Content Managment System
 *   Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 *   Contacts: <info@roocms.com>
 *
 *   You should have received a copy of the GNU General Public License v3
 *   along with this program.  If not, see http://www.gnu.org/licenses/
 */

/**
* @package      RooCMS
* @subpackage   Function
* @author       alex Roosso
* @copyright    2010-2019 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.3.2
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


/**
 * Генератор псевдослучайного кода.
 * Сколько не мучайся, это скотина все равно на случайность не смахивает.
 *
 * @param int   $ns      - количество символов в коде
 * @param mixed $symbols - Символы из которых будет сгенерирован код
 *
 * @return string $Code  - Возвращает сгенерированный код
 */
function randcode($ns, $symbols="ABCEFHKLMNPRSTVXYZ123456789") {

	if(trim($symbols) == "") {
		$symbols = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	}

	settype($symbols, "string");

	$code = ""; $i = 0;

	while ($i < $ns) {
		$a = rand(0,1);
		mt_srand(); srand();
		$code .= ($a == 1)
			? $symbols[mt_rand(0, mb_strlen($symbols) - 1)]
			: $symbols[rand(0, mb_strlen($symbols) - 1)]	;
		$i++;
	}

	return $code;
}

/**
 * Send mail
 *
 * @param string $mail		- Адрес направления
 * @param string $theme		- Заголовок письма
 * @param string $message	- Тело письма
 * @param string $from		- Обратный адрес
 */
function sendmail($mail, $theme, $message, $from="robot") {

	global $site;

	settype($mail,    "string");
	settype($theme,   "string");
	settype($message, "string");

	$message = strtr($message, array('\\r'=>'', '\\n'=>'\n'));

	$domain = strtr($site['domain'], array('http://'=>'', 'https://'=>'', 'www.'=>''));

	$from = trim($from);
	$pattern = '/^[\.\-_A-Za-z0-9]+?@[\.\-A-Za-z0-9]+?\.[A-Za-z0-9]{2,6}$/';

	if($from == "robot" || !preg_match($pattern, $from)) {
		$from = "robot@".$domain;
	}

	# заголовки
	$headers  = "MIME-Version: 1.0\n";
	$headers .= "From: '{$from}' <'Mail {$domain}'>\n".EMAIL_MESSAGE_PARAMETERS."\n";
	$headers .= "X-Sender: <no-reply@".$domain.">\n";
	$headers .= "X-Mailer: PHP ".$domain."\n";
	$headers .= "Return-Path: <no-replay@".$domain.">";

	# отправляем письмо
	mb_send_mail($mail,$theme,$message,$headers);
}

/**
 * мультибайтовая функция преобразования первого символа строки
 *
 * @param string $string
 *
 * @return string
 */
function mb_ucfirst($string) {
	return mb_strtoupper(mb_substr($string, 0, 1)).mb_strtolower(mb_substr($string, 1));
}

/**
 * Переадресация
 *
 * @param string $address - URL назначения
 * @param int    $code    - Код переадресации
 */
function go($address, $code=301) {

	switch($code) {
		# множественный выбор
		case 300:
			header($_SERVER['SERVER_PROTOCOL'].' 300 Multiple Choices');
			break;

		# перемещен временно
		case 302:
			header($_SERVER['SERVER_PROTOCOL'].' 302 Found');
			break;

		# GET на другой адрес
		case 303:
			header($_SERVER['SERVER_PROTOCOL'].' 303 See Other');
			break;

		# не изменялось
		case 304:
			header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
			break;

		# перемещен временно
		case 307:
			header($_SERVER['SERVER_PROTOCOL'].' 307 Temporary Redirect');
			break;

		# по умолчанию 301: перемещен навсегда
		default:
			header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');
			break;
	}

	header("Location: ".$address);
	exit;
}


/**
 * Вернуться назад
 */
function goback() {
	go(getenv("HTTP_REFERER"));
	exit;
}


/**
 * Заголовки некеширования
 */
function nocache() {

	$expires = time() + (60*60*24);

	header("Expires: ".gmdate("D, d M Y H:i:s", $expires)." GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Cache-Control: post-check=0,pre-check=0");
	header("Cache-Control: max-age=0");
	header("Pragma: no-cache");
}

/**
 * Функция получает код ответа от удаленного адреса
 *
 * @param string $url -  удаленный адрес
 *
 * @return string - код ответа
 */
function get_http_response_code($url) {
	$headers = get_headers($url);
	return substr($headers[0], 9, 3);
}

/**
 * Считываем файл
 *
 * @param string $file - полный пукть к файлу
 *
 * @return string - данные файла
 */
function file_read($file) {
	$buffer = "";

	if(file_exists($file)) {
		$buffer .= file_get_contents($file);
	}

	return $buffer;
}