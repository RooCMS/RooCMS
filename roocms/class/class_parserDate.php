<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
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
	public function makeCal($year, $month) {

		$wday = $this->get_num_day_of_week(1, $month, $year);
		if($wday == 0) {
			$wday = 7;
		}

		$n = - ($wday - 2);
		$cal = [];

		for($y=0;$y<=6;$y++) {
			$row = [];
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

			if(!$notEmpty) {
				break;
			}

			$cal[] = $row;
		}

		return $cal;
	}


	/**
	 * Переводим дату иг Грегорианского формата в русское представление.
	 *
	 * @param string $gdate  - дата в грегорианском формате
	 * @param bool   $full   - флаг указывает на вывод даты в полном или сокращенном формате
	 * @param bool   $short  - флаг указывает на использование сокращений в названии дней
	 * @param string $time   - переменная содержит часы и минуты.
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
	public function gregorian_to_rus($gdate, $full=false, $short=true, $time="") {

		$edate = explode("/", $gdate);

		$day	= (int) round($edate[1]);	# day
		$mon	= (int) round($edate[0]);	# month
		$year	= (int) round($edate[2]);	# year

		if(checkdate($mon, $day, $year)) {
			# num day of week
			$nw_day = $this->get_num_day_of_week($day, $mon, $year);

			# title day
			$tday = ($full) ? $this->get_title_day($nw_day, $short).", " : "" ;

			# title month
			$tm = $this->get_title_month($mon);
			// SEE---> $tm = $this->get_title_month($n_mon, true, $short);

			# форматируем дату
			$date = $tday.$day." ".$tm." ".$year."г. ".$time;
		}
		else {
			$date = "Некорректная дата";
		}

		return $date;
	}

	/**
	 * Функция преобразования даты из Юлианского Календаря в русский формат
	 *
	 * @param string $jddate - дата в формате юлианского календаря
	 * @param bool   $full   - флаг формата вывода даты
	 * @param bool   $short  - флаг формата вывода даты
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

	/**
	 * Преобразование даты из unix формата в русский формат
	 *
	 * @param int        $udate    - дата в формате unixtimestamp
	 * @param bool|false $full     - флаг указывает на вывод даты в полном или сокращенном формате
	 * @param bool|true  $short    - флаг указывает на использование сокращений в названии дней
	 * @param bool|false $showtime - флаг указывает на вывод даты со временем и без
	 *
	 *	if $full == true and $short=false
	 *		date = Четверг, 22 апреля 2010г.
	 *	else if $full == true and $short=true
	 *		date = Чт, 22 апреля 2010г.
	 *	else if $full == false
	 *		date =  22 апреля 2010г.
	 *	if $full == false to parametr $short automatically ingnored
	 * 	if $time == true
	 * 		date 22 апреля 2010г 22:30
	 *
	 * @return string
	 */
	public function unix_to_rus($udate, $full=false, $short=true, $showtime=false) {

		$day 	= date("d", $udate);
		$month 	= date("m", $udate);
		$year 	= date("Y", $udate);
		$time	= "";

		# time
		if($showtime) {
			$time = date("H:i", $udate);
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

		$ar = [];

		$ar['day']	= (int) date("d", $udate);
		$ar['month']	= (int) date("m", $udate);
		$ar['year']	= (int) date("Y", $udate);
		$ar['hour']	= (int) date("H", $udate);
		$ar['minute']	= (int) date("i", $udate);
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
	 * @param int $udate - дата в формате unixtimestamp
	 *
	 * @return string
	 */
	public function unix_to_rusint($udate) {

		$date = date("d.m.Y", $udate);

		return $date;
	}


	/**
	 * unix timestamp преобразовываем в грегорианскую дату
	 *
	 * @param int $udate - unixtimestamp
	 *
	 * @return string - дата в грегорианском представлении
	 */
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
	 * @param string $gdate - date in gregorian format
	 *
	 * @return int - date formated in unix timestamp
	 */
	public function gregorian_to_unix($gdate) {

		$time = explode("/", $gdate);

		$day 	= (int) round($time[1]);
		$month 	= (int) round($time[0]);
		$year 	= (int) round($time[2]);

		if(checkdate($month, $day, $year)) {
			$unix 	= mktime(0,0,0,$month,$day,$year);
		}
		else {
			$unix	= 1;
		}

		return $unix;
	}


	/**
	 * Преобразовываем представление даты из цифрового российского в unixtimestamp
	 *
	 * @param string $date - Дата в форме дд.мм.гггг
	 *
	 * @return int - unixtimestamp
	 */
	public function rusint_to_unix($date) {

		$time = explode(".", $date);

		$day 	= (int) round(mb_substr($time[0],0,2));
		$month 	= (int) round(mb_substr($time[1],0,2));
		$year 	= (int) round(mb_substr($time[2],0,4));

		if(checkdate($month, $day, $year)) {
			$unix 	= mktime(0,0,0,$month,$day,$year);
		}
		else {
			$unix	= 1;
		}

		return $unix;
	}


	/**
	 * Функция вернет номер дня недели.
	 * 0 - Вс, 1 - Пн и т.д. и т.п.
	 *
	 * @param int $day
	 * @param int $month
	 * @param int $year
	 *
	 * @return int
	 */
	public function get_num_day_of_week($day, $month, $year) {

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

		# long day			# short day
		$lday	= [];			$sday	 = [];
		$lday[0] = 'Воскресенье';	$sday[0] = 'Вс';
		$lday[1] = 'Понедельник';	$sday[1] = 'Пн';
		$lday[2] = 'Вторник';		$sday[2] = 'Вт';
		$lday[3] = 'Среда';		$sday[3] = 'Ср';
		$lday[4] = 'Четверг';		$sday[4] = 'Чт';
		$lday[5] = 'Пятница';		$sday[5] = 'Пт';
		$lday[6] = 'Суббота';		$sday[6] = 'Сб';

		$day = ($short) ? $sday[$nw] : $lday[$nw] ;

		return $day;
	}


	/**
	 * Функция получения русского названия месяца
	 *
	 * @param int  $nm    - порядковый номер месяца [1-Янв,...,12-Дек]
	 * @param bool $ft    - флаг вывода формата месяцоы. Истина - будет показывать Февраля, Ложь - будет показывать Февраль
	 * @param bool $short - флаг использования сокращений для названия месяцов.
	 *
	 * @return string
	 */
	public function get_title_month($nm, $ft = true, $short = false) {

		$nm = round($nm);

		# month
		$month = [];
		if(!$short) {
			$month[1]	= 'Январ';	$month[2]	= 'Феврал';	$month[3]	= 'Март';
			$month[4]	= 'Апрел';	$month[5]	= 'Ма';		$month[6]	= 'Июн';
			$month[7]	= 'Июл';	$month[8]	= 'Август';	$month[9]	= 'Сентябр';
			$month[10]	= 'Октябр';	$month[11]	= 'Ноябр';	$month[12]	= 'Декабр';
		}
		else {
			$month[1]	= 'Янв';	$month[2]	= 'Фев';	$month[3]	= 'Мар';
			$month[4]	= 'Апр';	$month[5]	= 'Ма';		$month[6]	= 'Июн';
			$month[7]	= 'Июл';	$month[8]	= 'Авг';	$month[9]	= 'Сен';
			$month[10]	= 'Окт';	$month[11]	= 'Ноя';	$month[12]	= 'Дек';
		}

		# format title month
		$f = "";

		if($ft) {
			if(!$short) {
				$f = ($nm == 3 || $nm == 8) ? "а" : "я" ;
			}
			else {
				$f = ($nm == 3) ? "я" : "" ;
			}
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