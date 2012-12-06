<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS Debug Class
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
|	Build: 				21:04 07.11.2010
|	Last Build: 		15:12 28.10.2011
|	Version file:		1.00 build 8
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


$Debug = new Debug;

//#########################################################
//# 	Debug Class
//#########################################################
class Debug {
	
	# param
	public $debug 		= 0;
	public $show_debug 	= 0;
	public $xdebug		= 0;
	public $debug_info 	= "";
	
	# developer mode on/off
	public $dev_mode	= false;
	

	function __construct() {
	
		// try show error
		if($this->debug == 1 || $this->dev_mode) 
			error_reporting  (E_ALL);
		else 
			error_reporting(0);
	}
	
	
	//*****************************************************
	// Start timer for count work scripts
    public function startTimer() {
        
		global $starttime;
		
        $mtime = microtime ();
        $mtime = explode (' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $starttime = $mtime;
    }
	
	//*****************************************************
	// Stop timer for count work scripts
    public function endTimer() {
        
		global $starttime;
		
        $mtime = microtime ();
        $mtime = explode (' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $endtime = $mtime;
        $totaltime = round (($endtime - $starttime), 5);
        
		return $totaltime;
    }
	
}
//#	End Class


//#########################################################
//# 	Function for Debuggins
//#########################################################
// For dumping 
function debug($str, $expand=false) {
	// output
	echo "<pre>\n";
	echo "<b>Дебаг:</b>\n";
	print_r($str);
	
	if($expand == true) {
		echo "<hr>\n";
		var_dump($str);
	}
	
	echo "</pre>\n";
}

//*********************************************************
// For error handler
function debug_error($errno, $msg, $file, $line) {

	if(error_reporting() == 0) return;

	echo "<div id=\"system_error\">
	<font color=#990000>Ошибка: </font>
	<br />&nbsp;&nbsp;&nbsp;код:<b>{$errno}</b>
	<br />&nbsp;&nbsp;&nbsp;<font color=#990000>файл:</font> <b>{$file}</b> &nbsp;&nbsp;&nbsp;<font color=#990000>строка:</font> <b>{$line}</b>
	<br />&nbsp;&nbsp;&nbsp;сообщение:<b>{$msg}</b>
	</div>\n";
}

?>