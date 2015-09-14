<?php
/**
* @package      RooCMS
* @subpackage	Engine RooCMS classes
* @author       alex Roosso
* @copyright    2010-2015 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.1.8
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
*   Copyright (C) 2010-2016 alex Roosso aka alexandr Belov info@roocms.com
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
*   Copyright (C) 2010-2016 alex Roosso (александр Белов) info@roocms.com
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
 * Class Parser
 * $_POST / $_GET && other input data
 */
class Parsers {

	# included classes
	public 	$text;				# [obj]		for parsing texts
	public 	$date;				# [obj]		for parsing date format
	public	$xml;				# [obj]		for parsing xml data

	# objects
	public 	$Post;				# [obj]		$_POST data
	public 	$Get;				# [obj]		$_GET data

	# params
	public 	$uri		= "";		# [string]	URI
	public	$uri_chpu	= false;	# [bool]	on/off flag for use (как ЧПУ по аглицки будет?)
	public	$uri_separator	= "";		# [string]	URI seperator

	# уведомление
	public	$info		= "";		# [text]	information
	public	$error		= "";		# [text]	error message

	# arrays
	private $post 		= array();	# [array]
	private $get 		= array();	# [array]



	/**
	* Lets begin
	*
	*/
	public function __construct() {

		global $roocms;

		# обрабатываем глобальные массивы.
		$this->parse_global();

		# обрабатываем URI
		$this->parse_uri();

		# act & part
		if(isset($this->Get->_act)) 	$roocms->act 	=& $this->Get->_act;
		if(isset($this->Get->_part)) 	$roocms->part 	=& $this->Get->_part;
		# check query RSS Export
		if(isset($this->Get->_export)) 	$roocms->rss 	= true;
		# check ajax flag
		if(isset($this->Get->_ajax))	$roocms->ajax	= true;

		# обрабатываем URL
		$this->parse_url();

		# обрабатываем уведомления
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
	private function parse_global() {

		# $_GET
		settype($this->Get, 	"object");
		if(!empty($_GET)) 	$this->parse_Get();

		# $_POST
		settype($this->Post, 	"object");
		if(!empty($_POST)) 	$this->parse_Post();

		# init session data
		if(!empty($_SESSION))	$this->get_session();
	}


	/**
	* parse $_POST array
	*
	*/
	protected function parse_Post() {

		$empty = false;
		if(isset($_POST['empty']) && ($_POST['empty'] == "1" || $_POST['empty'] == "true")) $empty = true;
		unset($_POST['empty']);

		$this->post = $this->check_array($_POST, $empty);

		foreach ($this->post as $key=>$value) {


			if(is_string($value)) {
				$class_post = " \$this->Post->{$key} = \"{$value}\";\n";
			}
			else if(is_array($value)) {
				$class_post  = "\$this->Post->{$key} = ";
				$class_post .= print_array($value);
			}
			else {
				$class_post = "\$this->Post->{$key} = \"{$value}\";\n";
			}

			eval($class_post);
		}

		unset($_POST);
	}


	/**
	* parse $_GET array
	*
	*/
	protected function parse_Get() {

		$this->get = $this->check_array($_GET);

		foreach ($this->get as $key=>$value) {

			# чистим ключ объекта от фигни
			$key = "_".$key;

			if(is_string($value)) {
				$class_get = " \$this->Get->{$key} = \"{$value}\";\n";
			}
			else if(is_array($value)) {
				$class_get  = "\$this->Get->{$key} = ";
				$class_get .= print_array($value);
			}
			else {
				$class_get = "\$this->Get->{$key} = \"{$value}\";\n";
			}

			eval($class_get);
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

		//parse_str($_SERVER['QUERY_STRING'], $gets);
		//debug(parse_url());

		# Получаем uri
		$this->uri = str_replace($_SERVER['SCRIPT_NAME'], "", $_SERVER['REQUEST_URI']);
		if(isset($_SERVER['REDIRECT_URL']) && trim($_SERVER['REDIRECT_URL']) != "") $this->uri = str_replace($_SERVER['REDIRECT_URL'], "", $_SERVER['REQUEST_URI']);

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
					if($cs > 0) $this->uri_separator = "-";

					# проверка на присутсвие "="
					str_replace("=", "=", $gets[$el], $is);

					if($is == 1) {
						# устанавливаем разделитель, если распознали
						if(trim($this->uri_separator) == "") $this->uri_separator = "=";

						# Определяем элементы URI ключ и значение
						$str = explode("=",$gets[$el]);
						if(trim($str[0]) != "" && trim($str[1]) != "") {
							$str[0] = $this->clear_key($str[0]);
							$code = "\$this->Get->_".$str[0]." = \"{$str[1]}\";";
							eval($code);
						}
					}
					elseif($is == 0) {

						# устанавливаем разделитель, если распознали
						if(trim($this->uri_separator) == "") $this->uri_separator = "/";

						# Определяем элементы URI ключ и значение
						$elp = $el + 1;

						if(trim($gets[$el]) != "" && isset($gets[$elp]) && trim($gets[$elp]) != "") {
							$gets[$el] = $this->clear_key($gets[$el]);
							$code = "\$this->Get->_".$gets[$el]." = \"{$gets[$elp]}\";";
							eval($code);
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
			$url = strtr($url, array('?' => '/',
						 '&' => '/',
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
	 * @param string $key - имя ключа
	 *
	 * @return string clear $key
	 */
	public function clear_key($key) {

		$key = strtr($key, array(
			'?' => '', 	'!' => '',
			'@' => '', 	'#' => '',
			'$' => '', 	'%' => '',
			'^' => '', 	'&' => '',
			'*' => '', 	'(' => '',
			')' => '', 	'{' => '',
			'}' => '', 	'[' => '',
			']' => '', 	'|' => '',
			'<' => '', 	'>' => '',
			'/' => '', 	'"' => '',
			';' => '', 	',' => '',
			'`' => '', 	'~' => ''
		));

		return $key;
	}


	/**
	* add notice msg
	*
	* @param string $txt - Текст сообщения
	* @param boolean $info - flag true = info, false = error [default: true]
	*/
	public function msg($txt, $info=true) {

		($info) ? $type = "info" : $type="error" ;

		$_SESSION[$type][] = $txt;
	}


	//#####################################################
	// Escape special String
	public function escape_string($string, $key=true) {
		global $db;

		//$string = str_ireplace('"','',$string);
		if(!is_array($string)) {
			if($key)	$string = str_replace('\\','',$string);
			else		$string = addslashes($string);
			$string = $db->escape_string($string);
			$string = str_ireplace('\&','&',$string);
			$string = trim($string);
		}

		return $string;
	}


	//#####################################################
	// Функиции проверки массивов на всякую лажу.
	// Только её надо развить, а то она ещё маленькая.
	public function check_array($array, $empty = false) {

		$arr = array();

		foreach($array as $key=>$value)	{
			if(is_array($value)) {
				$subarr	= $this->check_array($value, $empty);
				$arr[$key] = $subarr;
			}
			else {
				# Чистим ключ
				$key	= str_replace("'","",$key);
				$key 	= $this->escape_string($key);

				# Чистим значение
				$value 	= $this->escape_string($value, false);

				if(trim($value) != "" || $empty) $arr[$key] = $value;
				//elseif(empty($value) && $empty) $arr[$key] = false;
			}
		}

		return $arr;
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

		$email = trim($email);
		if(preg_match($pattern, $email)) return true;
		else return false;
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

		$phone = trim($phone);
		if(preg_match($pattern, $phone)) return true;
		else return false;
	}


	/**
	* Parse NOTICE Massages
	*
	*/
	public function parse_notice() {

		global $roocms, $config, $debug;

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
	* В разработке
	*
	* @param mixed $url
	* @return mixed
	*/
	public function prep_url($url) {
		if($url=='' || $url=='http://' || $url=='https://')
			return '';
		if(mb_substr($url,0,7)!='http://' && mb_substr($url,0,8)!='https://')
			$url = 'http://'.$url;
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
			exit;
		}

		return array(	"r" => hexdec(mb_substr($hexcolor, 1, 2)),
				"g" => hexdec(mb_substr($hexcolor, 3, 2)),
				"b" => hexdec(mb_substr($hexcolor, 5, 2))
			    );
	}


	/**
	* Browser detect
	*
	* @param string $browser - [ie|opera|mozilla|firebird|firefox|konqueror|camino|safari|webtv|webkit|netscape|mac]
	* @param int $version
	*
	* @return int $version OR bool false
	*/
	public function browser($browser, $version = 0) {

		global $roocms;
		static $is;

		if (!is_array($is)) {

			$useragent = mb_strtolower($roocms->useragent);

			$is = array(
				'opera'     => 0,
				'ie'        => 0,
				'mozilla'   => 0,
				'firebird'  => 0,
				'firefox'   => 0,
				'camino'    => 0,
				'konqueror' => 0,
				'safari'    => 0,
				'webkit'    => 0,
				'webtv'     => 0,
				'netscape'  => 0,
				'mac'       => 0,
				'chrome'    => 0
			);

			# detect opera
			if (mb_strpos($useragent, 'opera', 0, 'utf8') !== false) {
				preg_match('#opera(/| )([0-9\.]+)#', $useragent, $regs);
				if($regs[2] == "9.80")	{
                                	preg_match('#version/([0-9\.]+)#', $useragent, $regs);
                                	$is['opera'] = $regs[1];
				}
				else $is['opera'] = $regs[2];
			}

			# detect internet explorer
			if (mb_strpos($useragent, 'msie ', 0, 'utf8') !== false AND !$is['opera']) {
				preg_match('#msie ([0-9\.]+)#', $useragent, $regs);
				$is['ie'] = $regs[1];
			}

			# detect macintosh
			if (mb_strpos($useragent, 'mac', 0, 'utf8') !== false) {
				$is['mac'] = 1;
			}

			# detect chrome
			if (mb_strpos($useragent, 'chrome', 0, 'utf8') !== false) {
				preg_match('#chrome/([0-9\.]+)#', $useragent, $regs);
				$is['chrome'] = $regs[1];
			}

			# detect safari
			if (mb_strpos($useragent, 'applewebkit', 0, 'utf8') !== false AND !$is['chrome']) {
				preg_match('#applewebkit/([0-9\.]+)#', $useragent, $regs);
				$is['webkit'] = $regs[1];

				if (mb_strpos($useragent, 'safari', 0, 'utf8') !== false) {
					preg_match('#safari/([0-9\.]+)#', $useragent, $regs);
					$is['safari'] = $regs[1];
				}
			}

			# detect konqueror
			if (mb_strpos($useragent, 'konqueror', 0, 'utf8') !== false) {
				preg_match('#konqueror/([0-9\.-]+)#', $useragent, $regs);
				$is['konqueror'] = $regs[1];
			}

			# detect mozilla
			if (mb_strpos($useragent, 'gecko', 0, 'utf8') !== false AND !$is['safari'] AND !$is['konqueror'] AND !$is['chrome']) {
				# detect mozilla
				$is['mozilla'] = 20090105;
				if (preg_match('#gecko/(\d+)#', $useragent, $regs)) {
					$is['mozilla'] = $regs[1];
				}

				# detect firebird / firefox
				if (mb_strpos($useragent, 'firefox', 0, 'utf8') !== false OR mb_strpos($useragent, 'firebird', 0, 'utf8') !== false OR mb_strpos($useragent, 'phoenix', 0, 'utf8') !== false) {
					preg_match('#(phoenix|firebird|firefox)( browser)?/([0-9\.]+)#', $useragent, $regs);
					$is['firebird'] = $regs[3];

					if ($regs[1] == 'firefox') {
						$is['firefox'] = $regs[3];
					}
				}

				# detect camino
				if (mb_strpos($useragent, 'chimera', 0, 'utf8') !== false OR mb_strpos($useragent, 'camino', 0, 'utf8') !== false) {
					preg_match('#(chimera|camino)/([0-9\.]+)#', $useragent, $regs);
					$is['camino'] = $regs[2];
				}
			}

			# detect web tv
			if (mb_strpos($useragent, 'webtv', 0, 'utf8') !== false) {
				preg_match('#webtv/([0-9\.]+)#', $useragent, $regs);
				$is['webtv'] = $regs[1];
			}

			# detect pre-gecko netscape
			if (preg_match('#mozilla/([1-4]{1})\.([0-9]{2}|[1-8]{1})#', $useragent, $regs)) {
				$is['netscape'] = "{$regs[1]}.{$regs[2]}";
			}
		}

		# return the version number of the detected browser if it is the same as $browser
		if ($is["{$browser}"]) {
			# $version was specified - only return version number if detected version is >= to specified $version
			if ($version) {
				if ($is["{$browser}"] <= $version) {
					return $is["{$browser}"];
				}
			}
			else {
				return $is["{$browser}"];
			}
		}

		# if we got this far, we are not the specified browser, or the version number is too low
		return false;
	}
}

?>