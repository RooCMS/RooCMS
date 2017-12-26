<?php
/**
 *   RooCMS - Russian Open Source Free Content Managment System
 *   Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 *   Contacts: <info@roocms.com>
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
 *   RooCMS - Бесплатная система управления контентом с открытым исходным кодом
 *   Copyright © 2010-2018 александр Белов (alex Roosso). Все права защищены.
 *   Для связи: <info@roocms.com>
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

/**
* @package      RooCMS
* @subpackage	Engine RooCMS classes
* @author       alex Roosso
* @copyright    2010-2019 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.4.4
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
 * Class Parser
 * $_POST / $_GET && other input data
 */
class Parsers {

	# included classes
	public 	$text;				# [obj]		for parsing texts
	public 	$date;				# [obj]		for parsing date format
	public	$xml;				# [obj]		for parsing xml data

	# objects
	public 	$post;				# [obj]		$_POST data
	public 	$get;				# [obj]		$_GET data

	# params
	public 	$uri		= "";		# [string]	URI
	public	$uri_chpu	= false;	# [bool]	on/off flag for use (как ЧПУ по аглицки будет?)
	public	$uri_separator	= "";		# [string]	URI seperator

	# уведомление
	public	$info		= "";		# [text]	information
	public	$error		= "";		# [text]	error message



	/**
	* Lets begin
	*
	*/
	public function __construct() {

		global $roocms;

		# обрабатываем глобальные массивы.
		$this->parse_global_arrays();

		# обрабатываем URI
		$this->parse_uri();

		# act(ion) & part(ition) & move
		if(isset($this->get->_act)) {
			$roocms->act 	=& $this->get->_act;
		}

		if(isset($this->get->_part)) {
			$roocms->part 	=& $this->get->_part;
		}

		if(isset($this->get->_move)) {
			$roocms->move 	=& $this->get->_move;
		}

		# check query RSS Export
		if(isset($this->get->_export)) {
			$roocms->rss 	= true;
		}

		# check ajax flag
		if(isset($this->get->_ajax)) {
			$roocms->ajax	= true;
		}

		# обрабатываем URL
		$this->parse_url();

		# обрабатываем уведомления для вывода пользователю
		$this->parse_notice();

		# расширяем класс
		require_once "class_parserText.php";
		$this->text = new ParserText;

		require_once "class_parserDate.php";
		$this->date = new ParserDate;

		require_once "class_parserXML.php";
		$this->xml = new ParserXML;
	}


	/**
	* Parse global array
	*
	*/
	private function parse_global_arrays() {

		# $_GET
		settype($this->get, "object");
		if(!empty($_GET)) {
			$this->parse_get();
		}

		# $_POST
		settype($this->post, "object");
		if(!empty($_POST)) {
			$this->parse_post();
		}

		# init session data
		if(!empty($_SESSION)) {
			$this->get_session();
		}
	}


	/**
	* parse $_POST array
	*
	*/
	protected function parse_post() {

		$post = $this->check_array($_POST);

		foreach ($post as $key=>$value) {

			if(is_string($value)) {
				$this->post->{$key} = (trim($value) != "") ? $value : NULL ;
			}
			else if(is_array($value)) {
				$value = $this->check_array($value);
				$this->post->{$key} = (array) $value;
			}
			else {
				$this->post->{$key} = (trim($value) != "") ? (string) $value : NULL ;
			}
		}

		unset($_POST);
	}


	/**
	* parse $_GET array
	*
	*/
	protected function parse_get() {

		$get = $this->check_array($_GET);

		foreach ($get as $key=>$value) {

			# чистим ключ объекта от фигни
			$key = "_".$key;

			if(is_string($value)) {
				$this->get->{$key} = (trim($value) != "") ? $value : NULL ;
			}
			else if(is_array($value)) {
				$value = $this->check_array($value);
				$this->get->{$key} = (array) $value;
			}
			else {
				$this->get->{$key} = (trim($value) != "") ? (string) $value : NULL ;
			}
		}
	}


	/**
	* Get session data
	*
	*/
	private function get_session() {

		global $roocms;

		$roocms->sess = $this->check_array($_SESSION);
	}


	/**
	* Parser URI
	*
	*/
	private function parse_uri() {

		# Получаем uri
		$this->uri = str_replace($_SERVER['SCRIPT_NAME'], "", $_SERVER['REQUEST_URI']);
		if(isset($_SERVER['REDIRECT_URL']) && trim($_SERVER['REDIRECT_URL']) != "") {
			$this->uri = str_replace($_SERVER['REDIRECT_URL'], "", $_SERVER['REQUEST_URI']);
		}

		/**
		 * Ex: ЧПУ fix
		 */
		if($this->uri == "" && isset($_SERVER['REDIRECT_QUERY_STRING']) && trim($_SERVER['REDIRECT_QUERY_STRING']) != "") {
			$this->uri = "?".str_replace($_SERVER['SCRIPT_NAME'], "", $_SERVER['REDIRECT_QUERY_STRING']);
		}

		$this->uri = str_ireplace("\\","", $this->uri);

		# разбиваем
		$gets = explode("/",$this->uri);

		# считаем
		$c = count($gets);
		# если у нас чистый ури без левых примесей.
		if($c > 1 && trim($gets[0]) == "") {

			# Подтверждаем что используем ЧПУ
			$this->uri_chpu = true;

			# перебираем
			for($el=1;$el<=$c-1;$el++) {

				# Если элемент строки не пустой
				if(trim($gets[$el]) != "") {

					# тире и равно одинаковы для ЧПУ, и заодно узнаем разделитель
					$gets[$el] = str_replace("-","=",$gets[$el],$cs);

					# устанавливаем разделитель, если распознали
					if($cs > 0) {
						$this->uri_separator = "-";
					}

					# проверка на присутсвие "="
					str_replace("=", "=", $gets[$el], $is);

					if($is == 1) {
						# устанавливаем разделитель, если распознали
						if(trim($this->uri_separator) == "") {
							$this->uri_separator = "=";
						}

						# Определяем элементы URI ключ и значение
						$str = explode("=",$gets[$el]);
						if(trim($str[0]) != "" && trim($str[1]) != "") {
							$str[0] = "_".$this->clear_string($str[0]);
							$this->Get->{$str[0]} = $str[1];

						}
					}
					elseif($is == 0) {

						# устанавливаем разделитель, если распознали
						if(trim($this->uri_separator) == "") {
							$this->uri_separator = "/";
						}

						# Определяем элементы URI ключ и значение
						$elp = $el + 1;

						if(trim($gets[$el]) != "" && isset($gets[$elp]) && trim($gets[$elp]) != "") {
							$gets[$el] = "_".$this->clear_string($gets[$el]);
							$this->Get->{$gets[$el]} = $gets[$elp];
							$el++;
						}
					}
				}
			}
		}
	}


	/**
	 * transform uri if CHPU
	 *
	 * @param string $url - URI строка
	 *
	 * @return string $uri
	 */
	public function transform_uri($url) {

		if($this->uri_chpu) {
			$url = strtr($url, array('?' => $this->uri_separator,
						 '&' => $this->uri_separator,
						 '=' => $this->uri_separator));
		}

		return $url;
	}


	/**
	* parse URL
	*
	*/
	protected function parse_url() {

		global $db;

		# Страницы
		if(isset($this->Get->_pg)) {
			$db->page = floor($this->Get->_pg);
		}
	}


	/**
	 * функция чистит ключи глобальных переменных
	 *
	 * @param string $string - имя ключа
	 *
	 * @return string clear $key
	 */
	public function clear_string($string) {

		$string = trim(strtr($string, array(
			'?' => '', 	'!' => '',
			'@' => '', 	'#' => '',
			'$' => '', 	'%' => '',
			'^' => '', 	'&' => '',
			'*' => '', 	'(' => '',
			')' => '', 	'{' => '',
			'}' => '', 	'[' => '',
			']' => '', 	'|' => '',
			'<' => '', 	'>' => '',
			'/' => '', 	'\\' => '',
			'"' => '',	'`' => '',
			'.' => '', 	',' => '',
			'~' => '',	'=' => '',
			';' => ''
		)));

		return $string;
	}


	/**
	 * Escape special String
	 *
	 * @param      $string
	 * @param bool $key
	 *
	 * @return mixed|string
	 */
	public function escape_string($string, $key=true) {
		global $db;

		if(!is_array($string)) {

			if($key) {
				$string = str_replace('\\','',$string);
			}
			else {
				$string = addslashes($string);
			}

			$string = $db->escape_string($string);
			$string = str_ireplace('\&','&',$string);
			$string = trim($string);
		}

		return $string;
	}


	/**
	 * Функиции проверки массивов на всякую лажу.
	 * Только её надо развить, а то она ещё маленькая.
	 *
	 * @param $array
	 *
	 * @return array
	 */
	public function check_array($array) {

		$arr = array();

		foreach($array as $key=>$value)	{
			if(is_array($value)) {
				$subarr	= $this->check_array($value);
				$arr[$key] = $subarr;
			}
			else {
				# Чистим ключ
				$key	= str_replace("'","",$key);
				$key 	= $this->escape_string($key);

				# Чистим значение
				$value 	= $this->escape_string($value, false);

				//$arr[$key] = $value;
				$arr[$key] = (trim($value) != "") ? $value : NULL ;
			}
		}

		return $arr;
	}


	/**
	 * Parse NOTICE Massages
	 *
	 */
	public function parse_notice() {

		global $roocms, $debug;

		# Уведомления
		if(isset($roocms->sess['info'])) {
			foreach($roocms->sess['info'] AS $value) {
				$this->info .= "<span class='fa fa-info-circle fa-fw'></span> {$value}<br />";
			}

			# уничтожаем
			unset($_SESSION['info']);
		}

		# Ошибки
		if(isset($roocms->sess['error'])) {
			foreach($roocms->sess['error'] AS $value) {
				$this->error .= "<span class='fa fa-exclamation-triangle fa-fw'></span> {$value}<br />";
			}

			# уничтожаем
			unset($_SESSION['error']);
		}

		# Критические ошибки в PHP
		if(!empty($debug->nophpextensions)) {
			foreach($debug->nophpextensions AS $value) {
				$this->error .= "<b><span class='fa fa-exclamation-triangle fa-fw'></span> КРИТИЧЕСКАЯ ОШИБКА:</b> Отсутсвует PHP расширение - {$value}. Работа RooCMS нестабильна!";
			}
		}
	}


	/**
	 * Проверка на email на валидность
	 *
	 * @param string $email - email
	 *
	 * @return bool
	 */
	public function valid_email($email) {

		$pattern = '/^[\.\-_A-Za-z0-9]+?@[\.\-A-Za-z0-9]+?\.[A-Za-z0-9]{2,6}$/';

		return preg_match($pattern, trim($email));

	}


	/**
	 * Проверка телефонного номера на валидность
	 * Валидацию пройдут номера:
	 *        Код страны с плюсом и без, без кода страны
	 *        Код города от 3 до 5 символов в скобках и без скобок
	 *        Номер телефона от 5 до 7 цифр
	 *        Дефисы и пробелы учитываются, но не обязательны
	 *
	 * @param mixed $phone - номер введеного телефона
	 *
	 * @return bool
	 */
	public function valid_phone($phone){

		$pattern = "/^[\+]?[0-9]?(\s)?(\-)?(\s)?(\()?[0-9]{3,5}(\))?(\s)?(\-)?(\s)?[0-9]{1,3}(\s)?(\-)?(\s)?[0-9]{2}(\s)?(\-)?(\s)?[0-9]{2}\Z/";

		return preg_match($pattern, trim($phone));

	}


	/**
	* В разработке
	*
	* @param mixed $url
	* @return mixed
	*/
	public function prep_url($url) {

		if($url=='' || $url=='http://' || $url=='https://') {
			return '';
		}

		if(mb_substr($url,0,7)!='http://' && mb_substr($url,0,8)!='https://') {
			$url = 'http://'.$url;
		}

		return $url;
	}


	/**
	 * Вычисляем процент от числа
	 *
	 * @param int $n    - %
	 * @param int $from - Число из которого вычесляем %
	 *
	 * @return float
	 */
 	public function percent($n,$from) {

		$percent = ($n / $from) * 100;
		$percent = round($percent);

		return $percent;
	}


	/**
	 * Конвертируем hex color в decimal color
	 *
	 * @param string $hexcolor - Значение цвета в HEX. Example: #A9B7D3
	 *
	 * @return array|bool
	 */
	public function cvrt_color_h2d($hexcolor) {
		if(mb_strlen($hexcolor) != 7 || mb_strpos($hexcolor, "#") === false) {
			return false;
		}

		return array(	"r" => hexdec(mb_substr($hexcolor, 1, 2)),
				"g" => hexdec(mb_substr($hexcolor, 3, 2)),
				"b" => hexdec(mb_substr($hexcolor, 5, 2))
			    );
	}
}