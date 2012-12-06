<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS extends Parser Class 
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
|	Build: 			5:29 06.12.2010
|	Last Build: 	21:14 10.12.2010
|	Version file:	1.00 build 3
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


class ParserDate {

	# date
	public $minute	= 60;
	public $hour	= 3600;
	public $day		= 86400;
	
	

	//###############################################################
	//	Translate date from one format to rus standart
	//		$full 	= boolean;
	//		$short	= boolean;
	//---------------------------------------------------------------
	//	if $full == true and $short=false
	//		date = Четверг, 22 апреля 2010г.
	//	else if $full == true and $short=true
	//		date = Чт, 22 апреля 2010г.
	//	else if $full == false
	//		date =  22 апреля 2010г.
	//	* if $full == false to parametr $short automatically ingnored
	//===============================================================
	# gregorian to russian
	function gregorian_to_rus($gdate, $full=false, $short=true) {

		# month
		$month 		= array();
		$month[1] 	= 'январ';	$month[2]	= 'феврал';	$month[3]	= 'март';
		$month[4] 	= 'апрел';	$month[5] 	= 'ма';		$month[6]	= 'июн';
		$month[7] 	= 'июл';	$month[8] 	= 'август';	$month[9] 	= 'сентябр';
		$month[10] 	= 'октябр';	$month[11] 	= 'ноябр';	$month[12] 	= 'декабр';
		
		# full day					# short day
		$day	= array();			$sday	 = array();
		$day[0] = 'Воскресенье';	$sday[0] = 'Вс';
		$day[1] = 'Понедельник';	$sday[1] = 'Пн';
		$day[2] = 'Вторник';		$sday[2] = 'Вт';
		$day[3] = 'Среда';			$sday[3] = 'Ср';
		$day[4] = 'Четверг';		$sday[4] = 'Чт';
		$day[5] = 'Пятница';		$sday[5] = 'Пт';
		$day[6] = 'Суббота';		$sday[6] = 'Сб';
		
		
		$time = explode("/", $gdate);
		
		# day
		$n_day	= round($time[1]);
		# month
		$n_mon	= round($time[0]);
		# year
		$n_year	= round($time[2]);
		
/* 
--------> */
		// *checkdate
		
		# day of week
		$jd = GregorianToJD($n_mon, $n_day, $n_year); // convert for julian
		$w_day	= JDDayOfWeek($jd);
		
		
		$tday = "";
		
		
		if($full == true) {
			if($short==true) 	$tday = $sday[$w_day].", ";
			else				$tday = $day[$w_day].", ";
		}

		
		if($n_mon == 3 || $n_mon == 8) 	$f = "а";
		else $f = "я";
		
		
		$date = $tday.$n_day." ".$month[$n_mon].$f." ".$n_year."г.";
		
		return $date;
	}
	
	//************************************************
	# julian to russian
	function jd_to_rus($jddate, $full=false, $short=true) {

		$gregorian = JDToGregorian($jddate);
		
		$rus = $this->gregorian_to_rus($gregorian, $full, $short);
		
		return $rus;
	}
	
	//************************************************
	# unix timestamp to russian
	function unix_to_rus($udate, $full=false, $short=true) {
		
		$day 	= date("d", $udate);
		$month 	= date("m", $udate);
		$year 	= date("Y", $udate);
		
		$gregorian = $month."/".$day."/".$year;
		
		$rus = $this->gregorian_to_rus($gregorian, $full, $short);
		
		return $rus;
	}
	
	//************************************************
	# unix timestamp to gregorian
	function unix_to_gregorian($udate) {
		
		$day 	= date("d", $udate);
		$month 	= date("m", $udate);
		$year 	= date("Y", $udate);
		
		$gregorian = $month."/".$day."/".$year;
		
		return $gregorian;
	}
	
	//************************************************
	# gregorian to unix timestamp
	function gregorian_to_unix($gdate) {
		
		$time = explode("/", $gdate);
		
		$day 	= round($time[1]);
		$month 	= round($time[0]);
		$year 	= round($time[2]);
		
		$unix 	= mktime(0,0,0,$month,$day,$year);

/* 
--------> */
		// *checkdate
		
		return $unix;
	}
	//###############################################################

}

?>