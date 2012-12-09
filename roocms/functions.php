<?php
/**
* @package      RooCMS
* @subpackage	Function
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.17
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
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


/**
* Generator Random Code
*
* @param int $ns        - количество символов в коде
* @param mixed $symbols - Список символов для генерации кода
* @return string $Code  - Возвращает сгенерированный код
*/
function randcode($ns, $symbols="ABCEFHKLMNPRSTVXYZ123456789") {
	$Code = "";
	$i = 0;
	mt_srand((double)microtime() * 1000000);
	while ($i < $ns) {
		$Code .= $symbols[mt_rand(0, mb_strlen($symbols, 'utf8') - 1)];
		$i++;
	}
	return $Code;
}


/**
* Send mail
*
* @param string $mail   - Адрес направления
* @param string $theme  - Заголовок письма
* @param text $text     - Тело письма
* @param string $from   - Обратный адрес
*/
function sendmail($mail,$theme,$text, $from="robot") {

	global $site;

	$to			=	"".$mail."";

	$subject	=	"{$theme}";

	$text = str_replace("\\r", "", $text);
	$text = str_replace("\\n", "\n", $text);

	$site['domain'] = strtr($site['domain'], array('http://'=>'', 'www.'=>''));

	if($from == "robot") $from = "robot@".$site['domain'];

	$message 	=	"{$text}";

	$headers	=	"From: $from\n".EMAIL_MESSAGE_PARAMETERS;
	$headers	.=	"X-Sender: <no-reply@".$site['domain'].">\n";
	$headers	.=	"X-Mailer: PHP ".$site['domain']."\n";
	$headers	.=	"Return-Path: <no-replay@".$site['domain'].">";

	mb_send_mail($to,$subject,$message,$headers);
}


/**
* Функция вывода массива для печати.
*
* @param array $array       - Массив для печати
* @param boolean $subarray  - флаг проверки на вложенность массивов
* @return text $buffer      - Возвращает массив в текстовом представлении.
*/
function print_array($array,$subarray=false) {

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
* @param url $str   - URL назначения
* @param int $code  - Код переадресации
*/
function go($str, $code=301) {

	if($code == 301)		header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');	// перемещен навсегда
	elseif($code == 302)	header($_SERVER['SERVER_PROTOCOL'].' 302 Found');				// перемещен временно
	elseif($code == 303)	header($_SERVER['SERVER_PROTOCOL'].' 303 See Other');			// GET на другой адрес
	elseif($code == 307)	header($_SERVER['SERVER_PROTOCOL'].' 307 Temporary Redirect');	// перемещен временно
	else 					header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');

	header("Location: $str");
	exit;
}


/**
* Вернуться назад
*
*/
function goback() {
	go(getenv("HTTP_REFERER"));
	exit;
}


/**
* Заголовки некеширования
*
*/
function nocache() {

	$minute	= 60;
	$hour	= $minute * 60;
	$day	= $hour * 24;

	$expires = time() + $minute;

	Header("Expires: ".gmdate("D, d M Y H:i:s", $expires)." GMT");
	Header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	Header("Cache-Control: no-cache, must-revalidate");
	Header("Cache-Control: post-check=0,pre-check=0");
	Header("Cache-Control: max-age=0");
	Header("Pragma: no-cache");
}



?>