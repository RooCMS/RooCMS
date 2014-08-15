<?php
/**
* @package      RooCMS
* @subpackage	Engine RooCMS classes
* @author       alex Roosso
* @copyright    2010-2015 (c) RooCMS
* @link         http://www.roocms.com
* @version      2.1
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
 * Class ParserDate
 */
class ParserDate {

	# date
	private $minute	= 60;		# [int] sec in one minute
	private $hour	= 3600;		# [int]	sec in one hour
	private $day	= 86400;	# [int]	sec in one day



	/**
	 * Функция генерирует выходной массив данных, для создания календаря на указанный месяц.
	 *
	 * @param int $year - год
	 * @param int $month - месяц
	 *
	 * @return array
	 */
	function makeCal($year, $month) {

		$wday = $this->get_num_day_of_week(1, $month, $year);
		if($wday == 0) $wday = 7;

		$n = - ($wday - 2);
		$cal = array();

		for($y=0;$y<=6;$y++) {
			$row = array();
			$notEmpty = false;

			for($x=0;$x<7;$x++,$n++) {
				if(checkdate($month, $n, $year)) {
					$row[] = $n;
					$notEmpty = true;
				}
				else {
					$row[] = "";
				}
			}

			if(!$notEmpty) break;
			$cal[] = $row;
		}

		return $cal;
	}


	/**
	 * Переводим дату иг Грегорианского формата в русское представление.
	 *
	 * @param      $gdate - дата в грегорианском формате
	 * @param bool $full - флаг указывает на вывод даты в полном или сокращенном формате
	 * @param bool $short - флаг указывает на использование сокращений в названии дней
	 * @param bool $time -  флаг указывает на вывод даты со временем и без
	 *
	 *	if $full == true and $short=false
	 *		date = Четверг, 22 апреля 2010г.
	 *	else if $full == true and $short=true
	 *		date = Чт, 22 апреля 2010г.
	 *	else if $full == false
	 *		date =  22 апреля 2010г.
	 *	* if $full == false to parametr $short automatically ingnored
	 *
	 * @return string
	 */
	public function gregorian_to_rus($gdate, $full=false, $short=true, $time=false) {

		$edate = explode("/", $gdate);

		# day
		$n_day	= round($edate[1]);
		# month
		$n_mon	= round($edate[0]);
		# year
		$n_year	= round($edate[2]);

		// FIXME: *checkdate

		# num day of week
		$nw_day = $this->get_num_day_of_week($n_day, $n_mon, $n_year);

		# title day
		$tday = ($full) ? $this->get_title_day($nw_day, $short).", " : "" ;

		# title month
		$tm = $this->get_title_month($n_mon);
			// SEE---> $tm = $this->get_title_month($n_mon, true, $short);

		# форматируем дату
		$date = $tday.$n_day." ".$tm." ".$n_year."г. ".$time;

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
	public function jd_to_rus($jddate, $full=false, $short=true) {

		$gregorian = JDToGregorian($jddate);

		$rus = $this->gregorian_to_rus($gregorian, $full, $short);

		return $rus;
	}

	//#####################################################
	//# unix timestamp to russian
	public function unix_to_rus($udate, $full=false, $short=true, $time=false) {

		$day 	= date("d", $udate);
		$month 	= date("m", $udate);
		$year 	= date("Y", $udate);

		# time
		if($time) {
			$hour 	= date("H", $udate);
			$minute	= date("i", $udate);

			$time = $hour.":".$minute;
		}

		$gregorian = $month."/".$day."/".$year;


		$rus = $this->gregorian_to_rus($gregorian, $full, $short, $time);

		return $rus;
	}


	/**
	 * Превращаем дату в формате unix timestamp в массив данных русскоязычной даты
	 *
	 * @param $udate
	 *
	 * @return array массив данных с ключами
	 *               day - число месяца
	 *               month - текущий месяц в числовом формате
	 *               year - год
	 *               hour - час
	 *               minute - минута
	 *               time - время в формате H:i
	 *               wday - день недели в числовом выражении (0-Вс,...,6-Сб)
	 *               stday - сокращенное названия дня недели
	 *               ftday - полное название дня недели
	 *               tmonth - название месяца
	 */
	public function unix_to_rus_array($udate) {

		$ar = array();

		$ar['day']	= date("d", $udate);
		$ar['month']	= date("m", $udate);
		$ar['year']	= date("Y", $udate);
		$ar['hour']	= date("H", $udate);
		$ar['minute']	= date("i", $udate);
		$ar['time']	= $ar['hour'].":".$ar['minute'];
		$ar['wday']	= $this->get_num_day_of_week($ar['day'], $ar['month'], $ar['year']);
		$ar['stday']	= $this->get_title_day($ar['wday']);
		$ar['ftday']	= $this->get_title_day($ar['wday'], false);
		$ar['tmonth']	= $this->get_title_month($ar['month']);

		return $ar;
	}


	/**
	 * Переводим unixtimestamp  в русское представление даты в формат dd.mm.YYYY
	 *
	 * @param $udate - дата в формате unixtimestamp
	 *
	 * @return string
	 */
	public function unix_to_rusint($udate) {

		$date = date("d.m.Y", $udate);

		return $date;
	}


	//#####################################################
	//# unix timestamp to gregorian
	public function unix_to_gregorian($udate) {

		$day 	= date("d", $udate);
		$month 	= date("m", $udate);
		$year 	= date("Y", $udate);

		$gregorian = $month."/".$day."/".$year;

		return $gregorian;
	}


	/**
	 * Convert gregorian to unix timestamp
	 *
	 * @param $gdate - date in gregorian format
	 *
	 * @return int - date formated in unix timestamp
	 */
	public function gregorian_to_unix($gdate) {

		$time = explode("/", $gdate);

		$day 	= round($time[1]);
		$month 	= round($time[0]);
		$year 	= round($time[2]);

		$unix 	= mktime(0,0,0,$month,$day,$year);

		// FIXME: *checkdate

		return $unix;
	}


	//#####################################################
	//# Russian integer format to Unix timestamp format
	public function rusint_to_unix($date) {

		$time = explode(".", $date);

		$day 	= round($time[0]);
		$month 	= round($time[1]);
		$year 	= round($time[2]);

		$unix 	= mktime(0,0,0,$month,$day,$year);

		// FIXME: *checkdate

		return $unix;
	}


	/**
	 * Функция вернет номер дня недели.
	 * 0 - Вс, 1 - Пн и т.д. и т.п.
	 *
	 * @param $day
	 * @param $month
	 * @param $year
	 *
	 * @return int
	 */
	function get_num_day_of_week($day, $month, $year) {

		// FIXME: *checkdate

		$jd = GregorianToJD($month, $day, $year);

		return JDDayOfWeek($jd);
	}


	/**
	 * Функция получения русского названия дня недели
	 *
	 * @param int  $nw    номер дня недели [0-Пн,...,6-Вс]
	 * @param bool $short флаг указывающий формат возврата названия
	 *                    true вернет сокращенное название [пример: Сб]
	 *                    false вернет полное название [пример: Суббота]
	 *
	 * @return string
	 */
	public function get_title_day($nw, $short=true) {

		$nw = round($nw);

		# full day			# short day
		$day	= array();		$sday	 = array();
		$day[0] = 'Воскресенье';	$sday[0] = 'Вс';
		$day[1] = 'Понедельник';	$sday[1] = 'Пн';
		$day[2] = 'Вторник';		$sday[2] = 'Вт';
		$day[3] = 'Среда';		$sday[3] = 'Ср';
		$day[4] = 'Четверг';		$sday[4] = 'Чт';
		$day[5] = 'Пятница';		$sday[5] = 'Пт';
		$day[6] = 'Суббота';		$sday[6] = 'Сб';

		$day = ($short) ? $sday[$nw] : $day[$nw] ;

		return $day;
	}


	/**
	 * Функция получения русского названия месяца
	 *
	 * @param int  $nm порядковый номер месяца [1-Янв,...,12-Дек]
	 * @param bool $ft флаг вывода формата месяцоы. Истина - будет показывать Февраля, Ложь - будет показывать Февраль
	 * @param bool $short флаг использования сокращений для названия месяцов.
	 *
	 * @return string
	 */
	public function get_title_month($nm, $ft = true, $short = false) {

		$nm = round($nm);

		# month
		if(!$short) {
			$month = array();
			$month[1]	= 'Январ';	$month[2]	= 'Феврал';	$month[3]	= 'Март';
			$month[4]	= 'Апрел';	$month[5]	= 'Ма';		$month[6]	= 'Июн';
			$month[7]	= 'Июл';	$month[8]	= 'Август';	$month[9]	= 'Сентябр';
			$month[10]	= 'Октябр';	$month[11]	= 'Ноябр';	$month[12]	= 'Декабр';
		}
		else {
			$month = array();
			$month[1]	= 'Янв';	$month[2]	= 'Фев';	$month[3]	= 'Мар';
			$month[4]	= 'Апр';	$month[5]	= 'Ма';		$month[6]	= 'Июн';
			$month[7]	= 'Июл';	$month[8]	= 'Авг';	$month[9]	= 'Сен';
			$month[10]	= 'Окт';	$month[11]	= 'Ноя';	$month[12]	= 'Дек';
		}

		# format title month
		$f = "";

		if($ft) {
			if(!$short) 	$f = ($nm == 3 || $nm == 8) ? "а" : "я" ;
			else 		$f = ($nm == 3) ? "я" : "" ;
		}
		else {
			switch($nm) {
				case 3:
					$f = "";
					break;
				case 5:
					$f = "й";
					break;
				case 8:
					$f = "";
					break;

				default:
					$f = (!$short) ? "ь" : "";
					break;
			}
		}



		return $month[$nm].$f;
	}
}

?>