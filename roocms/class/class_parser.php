<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS Parser Class
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
|	Build: 			19:41 02.12.2010
|	Last Build: 	4:48 27.10.2011
|	Version file:	1.00 build 13
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


$parse 	= new Parser;
$GET	=& $parse->Get;
$POST	=& $parse->Post;

class Parser {

	# classes
	public 	$text;			// inc object
	public 	$date;			// inc object
	
	# objects
	public 	$Post;			// Object $_POST
	public 	$Get;			// Object $_GET
	
	# params
	public 	$uri			= "";
	public	$uri_chpu		= false;
	public	$uri_separator	= "";
	
	# notice
	public	$info			= "";
	public	$error			= "";
	
	# arrays
	private $post 			= array();
	private $get 			= array();
	
	

	function __construct() {
		
		global $roocms;
		
		// обрабатываем глобальные массивы.
		$this->parse_global();

		// обрабатываем URI
		$this->parse_uri();
		
			// act & part
			if(isset($this->Get->_act)) 	$roocms->act 	=& $this->Get->_act;
			if(isset($this->Get->_part)) 	$roocms->part 	=& $this->Get->_part;
			// check query RSS Export
			if(isset($this->Get->_export)) 	$roocms->rss 	= true;

		// обрабатываем URL
		$this->parse_url();
		
		// обрабатываем уведомления
		$this->parse_notice();
		

		// расширяем класс ======================
		require_once "class_parserText.php";
		$this->text = new ParserText;
		
		require_once "class_parserDate.php";
		$this->date = new ParserDate;
		//=======================================
	}
	
	
	//*****************************************************
	//	Parse global array
	function parse_global() {
		
		if(!empty($_GET)) 		$this->parse_Get();
		if(!empty($_POST)) 		$this->parse_Post();
		
		// init session data
		if(!empty($_SESSION))	$this->get_session();
	}
	
	
	//*****************************************************
	//  parse $_POST array
	protected function parse_Post() {
		
		$this->post = $this->check_array($_POST);
		
		settype($this->Post, "object");
		
		foreach ($this->post as $key=>$value) {
			
			// чистим ключ объекта от фигни
			//$key = "_".$key;
			
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
	}
	
	
	//*****************************************************
	//  parse $_GET array
	protected function parse_Get() {
		
		$this->get = $this->check_array($_GET);
		
		settype($this->Get, "object");
		
		foreach ($this->get as $key=>$value) {
			
			// чистим ключ объекта от фигни
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
	
	
	//*****************************************************
	//	Get session data
	private function get_session() {
		
		global $roocms;
		
		$roocms->sess = $this->check_array($_SESSION);
	}
	

	//*****************************************************
	// Parser URI 
	function parse_uri() {
	
		//parse_str($_SERVER['QUERY_STRING'], $gets);
		
		// Получаем uri
		$this->uri = str_replace($_SERVER['SCRIPT_NAME'], "", $_SERVER['REQUEST_URI']);
				
		// разбиваем
		$gets = explode("/",$this->uri);
		
		// считаем
		$c = count($gets);
		// если у нас чистый ури без левых примесей.
		if($c > 1 && trim($gets[0]) == "") {

			// Подтверждаем что используем ЧПУ
			$this->uri_chpu = true;
		
			// gogo
			for($el=1;$el<=$c-1;$el++) {
				
				// Если элемент строки не пустой
				if(trim($gets[$el]) != "") {
				
					// тире и равно одинаковы для ЧПУ, и заодно узнаем разделитель
					$gets[$el] = str_replace("-","=",$gets[$el],$cs);

					// устанавливаем разделитель, если распознали
					if($cs > 0) $this->uri_separator = "-"; 
					
					// проверка на присутсвие "="
					str_replace("=", "=", $gets[$el], $is);
					
					if($is == 1) {
						// устанавливаем разделитель, если распознали
						if($this->uri_separator == "") $this->uri_separator = "=";
						
						// Определяем элементы URI ключ и значение
						$str = explode("=",$gets[$el]);
						if(trim($str[0])!="" && trim($str[1]) != "") {
							$str[0] = $this->clear_key($str[0]);
							$code = "\$this->Get->_".$str[0]." = \"{$str[1]}\";";
							eval($code);
						}
					}
					// elseif($is > 1) {
					// }
					elseif($is == 0) {
						
						// устанавливаем разделитель, если распознали
						if($this->uri_separator == "") $this->uri_separator = "/";
						
						// Определяем элементы URI ключ и значение
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
	
	
	//*****************************************************
	// transform uri if CHPU
	public function transform_uri($url) {
		
		if($this->uri_chpu) {
			$url = strtr($url, array('?' => '/',
									 '&' => '/',
									 '=' => $this->uri_separator));
		}
		
		return $url;
	}
	
	
	//*****************************************************
	// parse URL
	protected function parse_url() {
	
		global $db;
		
		//	Страницы
		if(isset($this->Get->_page)) {
			$db->page = floor($this->Get->_page);
		}
	}
	
	
	//*****************************************************
	// функция чистит ключи глобальных переменных
	function clear_key($key) {
		
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
	
	
	//*****************************************************
	// Escape special String
	public function escape_string($string) {
		global $db, $Debug;

		//$string = str_ireplace('"','',$string);
		if(!is_array($string) OR $Debug->debug == 1) {
			$string = str_replace('\\','',$string);
			$string = trim($string);
			$string = $db->escape_string($string);
		}
		
		return $string;
	}
	
	
	//*********************************************************
	// Функиции проверки массивов на всякую лажу.
	// Только её надо развить, а то она ещё маленькая.
	public function check_array($array) {
		
		$arr = array();
		
		foreach($array as $key=>$value)	{
			if(is_array($value)) {
				$subarr	= $this->check_array($value);
				$arr[$key] = $subarr;
			}
			else {
				// clear key
				$key	= str_ireplace("'","",$key);
				$key 	= $this->escape_string($key);
				// clear value
				$value 	= $this->escape_string($value);
				if($value != "") $arr[$key] = $value;
			}
		}
		
		return $arr;
	}


	//*****************************************************
	// Parse NOTICE 
	function parse_notice() {

		global $roocms, $config;
		
		if(isset($roocms->sess['info'])) {
			foreach($roocms->sess['info'] AS $key=>$value) {
				$this->info .= "{$value}<br />";
			}
			
			// уничтожаем
			unset($_SESSION['info']);
		}
		
		if(isset($roocms->sess['error'])) {
			foreach($roocms->sess['error'] AS $key=>$value) {
				$this->error .= "{$value}<br />";
			}
			
			// уничтожаем
			unset($_SESSION['error']);
		}
	}
	
	
/* 	function txt2uri($txt,$sep='_') {
		$rus = Array('А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К',
		'Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ',
		'Ь','Ы','Ъ','Э','Ю','Я','а','б','в','г','д','е','ё','ж','з',
		'и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц',
		'ч','ш','щ','ь','ы','ъ','э','ю','я',' ');
		$eng = Array('a','b','v','g','d','e','yo','j','z','i','y','k',
		'l','m','n','o','p','r','s','t','u','f','h','c','ch','sh','csh',
		'','i','','e','yu','ya','a','b','v','g','d','e','yo','j','z',
		'i','y','k','l','m','n','o','p','r','s','t','u','f','h','c',
		'ch','sh','csh','','i','','e','yu','ya',$sep);
		$txt = str_replace($rus,$eng,trim($txt));
		$txt = mb_strtolower($txt, 'utf8');
		$txt = preg_replace("/[^(a-z0-9\-_ )]+/","",$txt);
		return $txt;
	}

	function prep_url($url) {
		if($url=='' || $url=='http://' || $url=='https://') 
			return '';
		if(substr($url,0,7)!='http://' && substr($url,0,8)!='https://')
			$url = 'http://'.$url;
		return $url;
	} */
	
/* 	function percent($n,$from) {
		
		$procent = ($n / $from) * 100;
		$procent = round($procent);
		$result = "{$procent}%";
		
		return $result;
	} */
	
	
	
	//#####################################################
	//#		Ниже функции для работы с полями дат в формах
	//#		Функции:
	//#			field_day, field_month и field_year
	//#####################################################
	# Day 
	function field_select_day($day) {
		$buffer = "";
		
		for($d=1;$d<=31;$d++) {
			$buffer .= "<option value=\"{$d}\"";
			if($d == $day) $buffer .= " selected";
			$buffer .= ">{$d}</option>";
		}
		
		return $buffer;
	}
	# Month
	function field_select_month($mon) {
		$buffer = "";
		
		# month
		$month 		= array();
		$month[1] 	= 'января';		$month[2]	= 'февраля';	$month[3]	= 'марта';
		$month[4] 	= 'апреля';		$month[5] 	= 'мая';		$month[6]	= 'июня';
		$month[7] 	= 'июля';		$month[8] 	= 'августа';	$month[9] 	= 'сентября';
		$month[10] 	= 'октября';	$month[11] 	= 'ноября';		$month[12] 	= 'декабря';
		
		for($m=1;$m<=12;$m++) {
			$buffer .= "<option value=\"{$m}\"";
			if($m == $mon) $buffer .= " selected";
			$buffer .= ">{$month[$m]}</option>";
		}
		
		return $buffer;
	}
	# Year
	function field_select_year($year) {
		$buffer = "";
		
		$now = date("Y");
		
		for($y=$now;$y>=$now-100;$y--) {
			$buffer .= "<option value=\"{$y}\"";
			if($y == $year) $buffer .= " selected";
			$buffer .= ">{$y}</option>";
		}
		
		return $buffer;
	}
	//#####################################################
	

	//#########################################################
	// one two one two check my microphone
	public function browser($browser, $version = 0) {
		static $is;
		if (!is_array($is))	{
		
			$useragent = mb_strtolower($_SERVER['HTTP_USER_AGENT'], 'utf8');
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
				'mac'       => 0
			);

			// detect opera
			if (mb_strpos($useragent, 'opera', 0, 'utf8') !== false) {
				preg_match('#opera(/| )([0-9\.]+)#', $useragent, $regs);
				$is['opera'] = $regs[2];
			}

			// detect internet explorer
			if (mb_strpos($useragent, 'msie ', 0, 'utf8') !== false AND !$is['opera']) {
				preg_match('#msie ([0-9\.]+)#', $useragent, $regs);
				$is['ie'] = $regs[1];
			}

			// detect macintosh
			if (mb_strpos($useragent, 'mac', 0, 'utf8') !== false) {
				$is['mac'] = 1;
			}

			// detect safari
			if (mb_strpos($useragent, 'applewebkit', 0, 'utf8') !== false) {
				preg_match('#applewebkit/([0-9\.]+)#', $useragent, $regs);
				$is['webkit'] = $regs[1];

				if (mb_strpos($useragent, 'safari', 0, 'utf8') !== false) {
					preg_match('#safari/([0-9\.]+)#', $useragent, $regs);
					$is['safari'] = $regs[1];
				}
			}

			// detect konqueror
			if (mb_strpos($useragent, 'konqueror', 0, 'utf8') !== false) {
				preg_match('#konqueror/([0-9\.-]+)#', $useragent, $regs);
				$is['konqueror'] = $regs[1];
			}

			// detect mozilla
			if (mb_strpos($useragent, 'gecko', 0, 'utf8') !== false AND !$is['safari'] AND !$is['konqueror']) {
				// See bug #26926, this is for Gecko based products without a build
				$is['mozilla'] = 20090105;
				if (preg_match('#gecko/(\d+)#', $useragent, $regs)) {
					$is['mozilla'] = $regs[1];
				}

				// detect firebird / firefox
				if (mb_strpos($useragent, 'firefox', 0, 'utf8') !== false OR mb_strpos($useragent, 'firebird', 0, 'utf8') !== false OR mb_strpos($useragent, 'phoenix', 0, 'utf8') !== false) {
					preg_match('#(phoenix|firebird|firefox)( browser)?/([0-9\.]+)#', $useragent, $regs);
					$is['firebird'] = $regs[3];

					if ($regs[1] == 'firefox') {
						$is['firefox'] = $regs[3];
					}
				}

				// detect camino
				if (mb_strpos($useragent, 'chimera', 0, 'utf8') !== false OR mb_strpos($useragent, 'camino', 0, 'utf8') !== false) {
					preg_match('#(chimera|camino)/([0-9\.]+)#', $useragent, $regs);
					$is['camino'] = $regs[2];
				}
			}

			// detect web tv
			if (mb_strpos($useragent, 'webtv', 0, 'utf8') !== false) {
				preg_match('#webtv/([0-9\.]+)#', $useragent, $regs);
				$is['webtv'] = $regs[1];
			}

			// detect pre-gecko netscape
			if (preg_match('#mozilla/([1-4]{1})\.([0-9]{2}|[1-8]{1})#', $useragent, $regs)) {
				$is['netscape'] = "$regs[1].$regs[2]";
			}
		}

		// sanitize the incoming browser name
		$browser = mb_strtolower($browser, 'utf8');
		if (substr($browser, 0, 3) == 'is_') {
			$browser = substr($browser, 3);
		}

		// return the version number of the detected browser if it is the same as $browser
		if ($is["$browser"]) {
			// $version was specified - only return version number if detected version is >= to specified $version
			if ($version) {
				if ($is["$browser"] <= $version) {
					return $is["$browser"];
				}
			}
			else {
				return $is["$browser"];
			}
		}

		// if we got this far, we are not the specified browser, or the version number is too low
		return 0;
	}
}

?>