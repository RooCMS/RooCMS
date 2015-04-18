<?php
/**
* @package      RooCMS
* @subpackage	Function
* @author       alex Roosso
* @copyright    2010-2015 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.24
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
 * Генератор псевдослучайного кода.
 * Сколько не мучайся, это скотина все равно на случайность не смахивает.
 *
 * @param int $ns        - количество символов в коде
 * @param mixed $symbols - Символы из которых будет сгенерирован код
 *
 * @return string $Code  - Возвращает сгенерированный код
 */
function randcode($ns, $symbols="ABCEFHKLMNPRSTVXYZ123456789") {

	if(trim($symbols) == "")
		$symbols = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890";

	settype($symbols, "string");

	$Code = ""; $i = 0;

	while ($i < $ns) {
		$a = rand(0,1);
		mt_srand(); srand();
		$Code .= ($a == 1)
			? $symbols[mt_rand(0, mb_strlen($symbols) - 1)]
			: $symbols[rand(0, mb_strlen($symbols) - 1)]	;
		$i++;
	}

	return $Code;
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

	if($from == "robot" || !preg_match($pattern, $from)) $from = "robot@".$domain;

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
 * Функция вывода массива для печати.
 *
 * @param array $array       - Массив для печати
 * @param boolean $subarray  - флаг проверки на вложенность массивов
 *
 * @return text $buffer      - Возвращает массив в текстовом представлении.
 */
function print_array(array $array, $subarray=false) {

	$c = count($array) - 1;
	$t = 0;

	$buffer = "array(";

	foreach($array as $key=>$value) {

		if(is_array($value)) {
			$buffer .= "'".$key."' => ".print_array($value,true);
		}
		else {
			$buffer .= "'".$key."' => '".$value."'";
			if($t < $c) $buffer .= ",\n";
		}

		$t++;
	}

	$buffer .= ")";
	if(!$subarray) $buffer .= ";\n";
	else $buffer .= ",\n";

	return $buffer;
}

/**
 * Переадресация
 *
 * @param     $address - URL назначения
 * @param int $code - Код переадресации
 */
function go($address, $code=301) {

	if($code == 300)	header($_SERVER['SERVER_PROTOCOL'].' 300 Multiple Choices');	# множественный выбор
	elseif($code == 301)	header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');	# перемещен навсегда
	elseif($code == 302)	header($_SERVER['SERVER_PROTOCOL'].' 302 Found');		# перемещен временно
	elseif($code == 303)	header($_SERVER['SERVER_PROTOCOL'].' 303 See Other');		# GET на другой адрес
	elseif($code == 304)	header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');	# не изменялось
	elseif($code == 307)	header($_SERVER['SERVER_PROTOCOL'].' 307 Temporary Redirect');	# перемещен временно
	else			header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');

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

	$expires = time() + 60;

	Header("Expires: ".gmdate("D, d M Y H:i:s", $expires)." GMT");
	Header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	Header("Cache-Control: no-cache, must-revalidate");
	Header("Cache-Control: post-check=0,pre-check=0");
	Header("Cache-Control: max-age=0");
	Header("Pragma: no-cache");
}


/**
 * Debug функция
 * Синоним $Debug->debug();
 *
 * @param mixed $obj
 * @param mixed $expand
 *
 * @example debug($var);
 */
function debug($obj, $expand=false) {
	global $debug;
	$debug->debug($obj, $expand);
}
?>