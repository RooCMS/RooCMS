<?php
/**
* @package      RooCMS
* @subpackage	Engine RooCMS classes
* @subpackage	Parser Class [extends: Date]
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.5
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*	RooCMS - Russian free content managment system
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
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


class ParserDate {

	# date
	public $minute	= 60;		# [int] sec in one minute
	public $hour	= 3600;		# [int]	sec in one hour
	public $day	= 86400;	# [int]	sec in one day



	/* ####################################################
	 * Translate date from one format to rus standart
	 * 	$full 	= boolean;
	 * 	$short	= boolean;
	 * ----------------------------------------------------
	 *	if $full == true and $short=false
	 *		date = Четверг, 22 апреля 2010г.
	 *	else if $full == true and $short=true
	 *		date = Чт, 22 апреля 2010г.
	 *	else if $full == false
	 *		date =  22 апреля 2010г.
	 *	* if $full == false to parametr $short automatically ingnored
	*/
	# gregorian to russian
	function gregorian_to_rus($gdate, $full=false, $short=true, $time=false) {

		# month
		$month 		= array();
		$month[1] 	= 'январ';	$month[2]	= 'феврал';	$month[3]	= 'март';
		$month[4] 	= 'апрел';	$month[5] 	= 'ма';		$month[6]	= 'июн';
		$month[7] 	= 'июл';	$month[8] 	= 'август';	$month[9] 	= 'сентябр';
		$month[10] 	= 'октябр';	$month[11] 	= 'ноябр';	$month[12] 	= 'декабр';

		# full day			# short day
		$day	= array();		$sday	 = array();
		$day[0] = 'Воскресенье';	$sday[0] = 'Вс';
		$day[1] = 'Понедельник';	$sday[1] = 'Пн';
		$day[2] = 'Вторник';		$sday[2] = 'Вт';
		$day[3] = 'Среда';		$sday[3] = 'Ср';
		$day[4] = 'Четверг';		$sday[4] = 'Чт';
		$day[5] = 'Пятница';		$sday[5] = 'Пт';
		$day[6] = 'Суббота';		$sday[6] = 'Сб';


		$edate = explode("/", $gdate);

		# day
		$n_day	= round($edate[1]);
		# month
		$n_mon	= round($edate[0]);
		# year
		$n_year	= round($edate[2]);

/*
--------> */
		// *checkdate

		# day of week
		$jd = GregorianToJD($n_mon, $n_day, $n_year); // convert for julian
		$w_day	= JDDayOfWeek($jd);


		$tday = "";


		if($full == true) {
			if($short==true) 	$tday = $sday[$w_day].", ";
			else			$tday = $day[$w_day].", ";
		}


		if($n_mon == 3 || $n_mon == 8) 	$f = "а";
		else $f = "я";


		$date = $tday.$n_day." ".$month[$n_mon].$f." ".$n_year."г. ".$time;

		return $date;
	}

	/**
	 * Функция преобразования даты из Юлианского Календаря в русский формат
	 *
	 * @param      $jddate - дата в формате юлианского календаря
	 * @param bool $full - флаг формата вывода даты
	 * @param bool $short - флаг формата вывода даты
	 *                    	if $full == true and $short=false
	 *				date = Четверг, 22 апреля 2010г.
	 *			else if $full == true and $short=true
	 *				date = Чт, 22 апреля 2010г.
	 *			else if $full == false
	 *				date =  22 апреля 2010г.
	 *			* if $full == false to parametr $short automatically ingnored
	 *
	 * @return string - вовзвращает дату в заданном формате.
	 */
	function jd_to_rus($jddate, $full=false, $short=true) {

		$gregorian = JDToGregorian($jddate);

		$rus = $this->gregorian_to_rus($gregorian, $full, $short);

		return $rus;
	}

	//#####################################################
	//# unix timestamp to russian
	function unix_to_rus($udate, $full=false, $short=true, $time=false) {

		$day 	= date("d", $udate);
		$month 	= date("m", $udate);
		$year 	= date("Y", $udate);

		if($time) {
			$hour 	= date("H", $udate);
			$minute	= date("i", $udate);

			$time = $hour.":".$minute;
		}

		$gregorian = $month."/".$day."/".$year;

		$rus = $this->gregorian_to_rus($gregorian, $full, $short, $time);

		return $rus;
	}


	//#####################################################
	//# unix timestamp to russian integer format dd.mm.YYYY
	function unix_to_rusint($udate) {

		$date = date("d.m.Y", $udate);

		return $date;
	}


	//#####################################################
	//# unix timestamp to gregorian
	function unix_to_gregorian($udate) {

		$day 	= date("d", $udate);
		$month 	= date("m", $udate);
		$year 	= date("Y", $udate);

		$gregorian = $month."/".$day."/".$year;

		return $gregorian;
	}

	//#####################################################
	//# gregorian to unix timestamp
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


	//#####################################################
	//# Russian integer format to Unix timestamp format
	function rusint_to_unix($date) {

		$time = explode(".", $date);

		$day 	= round($time[0]);
		$month 	= round($time[1]);
		$year 	= round($time[2]);

		$unix 	= mktime(0,0,0,$month,$day,$year);

/*
--------> */
		// *checkdate

		return $unix;
	}

}

?>