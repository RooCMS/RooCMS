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
 * Class Parser
 * $_POST / $_GET && other input data
 */
class Parser {

	# included classes
	public 	$text;				# [obj]		for parsing texts
	public 	$date;				# [obj]		for parsing date format
	public	$xml;				# [obj]		for parsing xml data

	# objects
	public 	$post;				# [obj]		$_POST data
	public 	$get;				# [obj]		$_GET data

	# uri params
	public 	$uri		= "";		# [string]	URI
	public	$uri_chpu	= false;	# [bool]	on/off flag for use (как ЧПУ по аглицки будет?)
	public	$uri_separator	= "/";		# [string]	URI seperator

	# уведомление
	public	$info		= "";		# [text]	information
	public	$error		= "";		# [text]	error message



	/**
	* Lets begin
	*
	*/
	public function __construct() {

		# обрабатываем глобальные массивы.
		$this->parse_global_arrays();

		# обрабатываем URI
		$this->get_uri();
		$this->parse_uri();

		# обрабатываем URL
		$this->set_url_vars();

		# обрабатываем уведомления для вывода пользователю
		$this->parse_notice();

		# расширяем класс
		require_once "class_parserText.php";
		$this->text = new ParserText;

		require_once "class_parserDate.php";
		$this->date = new ParserDate;
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

			if(is_array($value)) {
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

			if(is_array($value)) {
				$value = $this->check_array($value);
				$this->get->{$key} = (array) $value;
			}
			else {
				$this->get->{$key} = (trim($value) != "") ? (string) $this->escape_string($value) : NULL ;
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
	 * Get URI and clear garbage
	 */
	private function get_uri() {

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
	}


	/**
	* Parser URI
	*
	*/
	private function parse_uri() {

		# разбиваем
		$gets = explode("/",$this->uri);

		# считаем
		$c = count($gets);
		# если у нас чистый ури без левых примесей.
		if($c > 2 && trim($gets[0]) == "") {

			# Подтверждаем что используем ЧПУ
			$this->uri_chpu = true;

			# перебираем
			for($el=1;$el<=$c-1;$el++) {

				# Если элемент строки не пустой
				if(trim($gets[$el]) != "") {

					$elp = $el + 1;

					if(trim($gets[$el]) != "" && isset($gets[$elp]) && trim($gets[$elp]) != "") {
						$gets[$el] = "_".$this->clear_string($gets[$el]);
						$this->get->{$gets[$el]} = (string) $this->escape_string($gets[$elp]);
						$el++;
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
	* Set global vars from url
	*
	*/
	protected function set_url_vars() {

		global $roocms, $db;

		# Страницы
		if(isset($this->get->_pg)) {
			$db->page = floor($this->get->_pg);
		}

		# act(ion) & part(ition) & move
		if(isset($this->get->_act)) {
			$roocms->act = $this->clear_string($this->get->_act);
		}

		if(isset($this->get->_part)) {
			$roocms->part = $this->clear_string($this->get->_part);
		}

		if(isset($this->get->_move)) {
			$roocms->move = $this->clear_string($this->get->_move);
		}

		# check query RSS Export
		if(isset($this->get->_export)) {
			$roocms->rss = true;
		}

		# check ajax flag
		if(isset($this->get->_ajax)) {
			$roocms->ajax = true;
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
	 * @return string|array
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

		$arr = [];

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
				$this->info .= "<i class='fa fa-info-circle fa-fw'></i> {$value}<br />";
			}

			# уничтожаем
			unset($_SESSION['info']);
		}

		# Ошибки
		if(isset($roocms->sess['error'])) {
			foreach($roocms->sess['error'] AS $value) {
				$this->error .= "<i class='fa fa-exclamation-triangle fa-fw'></i> {$value}<br />";
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

		return (bool) preg_match($pattern, trim($email));

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

		return (bool) preg_match($pattern, trim($phone));

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
	 * @return array|false
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