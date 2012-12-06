<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS Function
|	Author:	alex Roosso
|	Copyright: 2010-2011 (c) RooCMS. 
|	Web: http://www.roocms.com
|	All rights reserved.
|----------------------------------------------------------
|	This program is free software; you can redistribute it and/or modify
|	it under the terms of the GNU General Public License as published by
|	the Free Software Foundation; either version 2 of the License, or
|	(at your option) any later version.
|	
|	Данное программное обеспечение является свободным и распространяется
|	по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
|	При любом использовании данного ПО вы должны соблюдать все условия
|	лицензии.
|----------------------------------------------------------
|	Build date:		20:26 28.11.2010
|	Last Build:		3:02 17.10.2011
|	Version file: 	1.00 build 13
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


//#########################################################
// Generator Code
function RndCode($ns, $symbols="ABCEFHKLMNPRSTVXYZ123456789") {
	$Code = "";
	$i = 0;
	mt_srand((double)microtime() * 1000000);
	while ($i < $ns) {
		$Code .= $symbols[mt_rand(0, mb_strlen($symbols, 'utf8') - 1)];
		$i++;
	}
	return $Code;
}


//#########################################################
//# 		Check Valid Mail
//#			$email = string
//#########################################################
function valid_email($email) {
	if(preg_match('/^[\.\-_A-Za-z0-9]+?@[\.\-A-Za-z0-9]+?\.[A-Za-z0-9]{2,6}$/',$email)==1) 
		return true;
	else return false; 
}


//#########################################################
// Send Mail
function sendmail($mail,$theme,$text, $from="robot") {

	global $var;
	
	$to			=	"".$mail."";
	
	$subject	=	"{$theme}";
	
	$text = str_replace("\\r", "", $text);
	$text = str_replace("\\n", "\n", $text);
	
	if($from == "robot") $from = "robot@".$var['domain'];
	
	$message 	=	"{$text}";
	
	$headers	=	"From: $from\n".EMAIL_MESSAGE_PARAMETERS;
	$headers	.=	"X-Sender: <no-reply@".$var['domain'].">\n";
	$headers	.=	"X-Mailer: PHP ".$var['domain']."\n";
	$headers	.=	"Return-Path: <no-replay@".$var['domain'].">";
	
	mail($to,$subject,$message,$headers);
}


//#########################################################
// Функция вывода массива для печати. 
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


//#########################################################
// HTTP Moved 
function go($str, $code=301) {

	if($code == 301)		header('HTTP/1.1 301 Moved Permanently');	// перемещен навсегда
	elseif($code == 302)	header('HTTP/1.1 302 Found');				// перемещен временно
	elseif($code == 303)	header('HTTP/1.1 303 See Other');			// GET на другой адрес
	elseif($code == 307)	header('HTTP/1.1 307 Temporary Redirect');	// перемещен временно
	else 					header('HTTP/1.1 301 Moved Permanently');
	
	header("Location: $str");
	exit;
}


//#########################################################
//	Move back
function goback() {
	go(getenv("HTTP_REFERER"));
	exit;
}


//#########################################################
// NoCache
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