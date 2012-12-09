<?php
/**
* @package      RooCMS
* @subpackage	Engine RooCMS classes
* @subpackage	MySQL Class
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      2.4.5
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
*   Данное программное обеспечение является свободным и распространяется
*   по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
*   При любом использовании данного ПО вы должны соблюдать все условия
*   лицензии.
*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


//	database class :: MySQL

$db = new DataBase;

class DataBase {

	public	$db_connect = false;	# [bool]	Флаг состояния подключения к БД
	public  $cnt_querys = 0;		# [int] 	Счетчик запросов в БД


	# pages param
	public  $pages		= 0;		# [int]	Всего страниц
	public  $page		= 1;		# [int] Текущая страница
	public	$prev_page	= 0;		# [int] Предыдущая страница
	public	$next_page	= 0;		# [int] Следующая страница
	public 	$limit		= 15;		# [int] Число строк для запроса
	public  $from		= 0;		# [int] Стартовая позиция для запроса



	/**
	* Let's begin
	*
	*/
	function __construct() {

		global $db_info;

		if(trim($db_info['host']) != "" && trim($db_info['base']) != "")
			$this->connect($db_info['host'], $db_info['user'], $db_info['pass'], $db_info['base']);

		# set mysql charset
		if($this->db_connect) $this->charset();
	}


	/**
	* Коннектимся к БД
	*
	* @param string $host - Хост
	* @param string $user - Пользователь БД
	* @param string $pass - Пароль для доступа к БД
	* @param string $base - Название БД
	*/
	private function connect($host, $user, $pass, $base) {

		mysql_connect($host,$user,$pass) or die ($this->error());
		mysql_select_db($base) or die ($this->error());

		if(mysql_errno() == 0) $this->db_connect = true;
	}


	/**
	* Проверяем подключение к БД сайта
	*
	* @param string $host - Хост
	* @param string $user - Пользователь БД
	* @param string $pass - Пароль для доступа к БД
	* @param string $base - Название БД
	*
	* @return boolean
	*/
	public function check_connect($host, $user, $pass, $base) {

		error_reporting(0);
		mysql_connect($host, $user, $pass);
		mysql_select_db($base);
		error_reporting(E_ALL);

		if(mysql_errno() != 0) return false;
		else return true;
	}


	/**
	* Установка кодировок для соединения с БД
	* Некоторые версии MySQL неправильно работают с кодировками.
	* Эта функция помогает устранить ошибки в работе.
	* Стоит помнить, что это все таки 3 запроса к БД на каждую страницу и вызов.
	* Поэтому если БД работает стабильно, лучше выключить данную функцию.
	*/
	private function charset() {

        $this->query("set character_set_client = 'utf8'");
        $this->query("set character_set_results = 'utf8'");
        $this->query("set collation_connection = 'utf8_general_ci'");
		//mysql_query ("set names 'utf8'");
	}


	/**
	* Парсер ошибок запросов к БД
	* Функция используется для отладки запросов
	*
	* @param string $q - Запрос осуществленный к БД
	* @return mixed при влключенном режиме отладки вернет ошибку, иначе выведет общее сообщение на экран об ошибке
	*/
	private function error($q = "") {

		global $debug;

		# режим отладки
		if($debug->debug) {
			$query = "<div style=\"padding: 5px;text-align: left;\"><font style=\"font-family: Tahoma; font-size: 12px;text-align: left;\">
			Ошибка БД / MySQL Error: <b>".mysql_errno()."</b>
			<br /> -- ".mysql_error()."
			<br />
			<br /><table width=\"100%\" style=\"border: 1px solid #ffdd00; background-color: #ffffee;text-align: left;\">
			 <tr>
			  <td align=\"left\" style=\"text-align: left;\"><font style=\"font-family: Tahoma; font-size: 10px;text-align: left;\">
			  <font color=\"#990000\"><b>SQL Query:</b><pre> $q</pre></font>
			  </font>
			 </tr>
			</table>
			</td>
			</font></div>";

			return $query;
		}
		# рабочий режим
		else {
			$f = file(_SKIN."/db_error.tpl");
			foreach($f AS $k=>$v) echo $v;
		}
	}


	/**
	* Запрос в БД
	*
	* @param string $q - Строка запроса в БД
	* @return resource - результат запроса.
	*/
	public function query($q = "") {

		global $debug;

		if($this->db_connect || $debug->debug) {
			# Выполняем запрос
			$query = mysql_query($q) or die ($this->error($q));

			# Считаем запросы
			if($debug->debug || defined('ACP')) $this->cnt_querys++;

			# Выводим информацию по всем запросам
			if($debug->show_debug) {

				# parse debug
				$q = strtr($q, array(
					'SELECT' 	=> '<b>SELECT</b>',
					'INSERT' 	=> '<b>INSERT</b>',
					'UPDATE' 	=> '<b>UPDATE</b>',
					'DELETE' 	=> '<b>DELETE</b>',
					'INTO' 		=> '<b>INTO</b>',
					'FROM' 		=> '<b>FROM</b>',
					'LEFT' 		=> '<b>LEFT</b>',
					'JOIN' 		=> '<b>JOIN</b>',
					'AS' 		=> '<b>AS</b>',
					'ON' 		=> '<b>ON</b>',
					'WHERE' 	=> '<b>WHERE</b>',
					'ORDER' 	=> '<b>ORDER</b>',
					'BY' 		=> '<b>BY</b>',
					'LIMIT' 	=> '<b>LIMIT</b>'
				));

				# debug info querys
				$debug->debug_info .= "<table width=\"100%\" style=\"border: 1px solid #ffdd00; background-color: #ffffee;text-align: left;\">
				 <tr>
				  <td align=\"left\" style=\"text-align: left;\"><font style=\"font-family: Tahoma; font-size: 10px;text-align: left;\">
				  <font color=\"#990000\"><pre><i style=\"color: blue;\"><u>SQL Query ".$this->cnt_querys.":</u></i><br /> {$q}</pre></font>
				  </font></td>
				 </tr>
				</table>";
			}

			return $query;
		}
	}


	/**
	* Функция вставляет данные из массива в указанную таблицу.
	* Не рекомендуется использовать данную функцию в пользовательской части CMS
	*
	* @param array $array  - Массива данных, где ключ это имя поля в таблице а значение данные этого поля.
	* @param string $table - Название целевой таблицы.
	*/
	public function insert_array($array, $table) {

		$fields	= "";
		$values	= "";

		foreach($array AS $key=>$value) {
			if(trim($fields) == "") 	$fields .= $key;
			else						$fields .= ", ".$key;

			if(trim($values) == "") 	$values .= "'".$value."'";
			else						$values .= ", '".$value."'";
		}

		$q = "INSERT INTO ".$table." (".$fields.") VALUES (".$values.")";

		$this->query($q);
	}


	/**
	* Функция обновляет данные из массива в указанную таблицу.
	* Не рекомендуется использовать данную функцию в пользовательской части CMS
	*
	* @param array $array  - Массив данных, где ключ это имя поля в таблице а значение данные этого поля.
	* @param string $table - Название целевой таблицы.
	* @param string $where - Условие (фильтр) для отборо целевых строк таблицы
	*/
	public function update_array($array, $table, $where) {

		$update = "";
		foreach($array AS $key=>$value) {
			if(trim($update) == "") 	$update .= $key."='".$value."'";
			else						$update .= ", ".$key."='".$value."'";
		}

		$q = "UPDATE ".$table." SET ".$update." WHERE ".$where;

		$this->query($q);
	}


	/**
	* Преобразует результаты запроса в простой массив
	*
	* @param data $q - Результат произведенного в БД запроса.
	* @return array  - Возвращает данные из БД в ввиде нумерованного массива
	*/
	public function fetch_row($q) {

		global $debug;

		if($this->db_connect || $debug->debug) {
			$result = mysql_fetch_row($q);
			return $result;
		}
	}


	/**
	* Преобразует результаты запроса в ассоциативный массив
	*
	* @param data $q - Результат произведенного в БД запроса.
	* @return array  - Возвращает данные из БД в ввиде ассоциативного массива
	*/
	public function fetch_assoc($q) {

		global $debug;

		if($this->db_connect || $debug->debug) {
			$result = mysql_fetch_assoc($q);
			return $result;
		}
	}


	/**
	* Преобразует результаты запроса в объект
	*
	* @param data $q - Результат произведенного в БД запроса.
	* @return object - Возвращает данные из БД в ввиде объекта
	*/
	public function fetch_object($q) {

		global $debug;

		if($this->db_connect || $debug->debug) {
			$obj = mysql_fetch_object($q);
			return $obj;
		}
	}


	/**
	* Have a index query
	* @return int Возвращает идентификатор
	*/
	public function insert_id() {

		global $debug;

		if($this->db_connect || $debug->debug) {
			$id = mysql_insert_id();
			return $id;
		}
	}


	/**
	* Функция проверяет имеется ли запрашиваймый id.
	*
	* @param uniq int $id  - проверямый идентификатор
	* @param string $table - таблица в которой проводится проверка
	* @param string $field - название поля таблицы содержащий идентификатор
	* @param string $where - Дополнительное условие (фильтр) для проверки
	* @return int|boolean - Возвращает количество найденных строк, соответсвующих критериям или false в случае неудачи
	*/
	public function check_id($id, $table, $field="id", $where="") {

		if(trim($where) != "") $where = " AND ".$where;

		$q = $this->query("SELECT count(*) FROM ".$table." WHERE {$field}='".$id."' ".$where."");
		$c = $this->fetch_row($q);

        if($c[0] == 0) return false;
        else return $c[0];
	}


	public function num_rows($q) {
		return mysql_num_rows($q);
	}


	/**
	* Clear system symbols for query
	*
	* @param string $q - запрос
	* @return string - возвращает строку запроса в бд вычещенной
	*/
	public function escape_string($q) {

		$q = htmlspecialchars($q);
		$q = strtr($q, array(
			'{' 		=> '&#123;',
			'}' 		=> '&#125;',
			'$' 		=> '&#36;',
			'&amp;gt;' 	=> '&gt;',
			"'"			=> "&#39;"
		));

		if($this->db_connect) return mysql_real_escape_string($q);
		else return $q;

	}


	/**
	* Закрываем подключение к БД сайта
	*
	*/
	public function close() {
		@mysql_close();
	}


	//#####################################################
	//	jaga jaga
	public function pages_mysql($from, $where="id!=0", $query="") {

		# Считаем
		$count = array();
		$c = $this->query("SELECT count(*) FROM ".$from." WHERE ".$where." ".$query);
		$count = $this->fetch_row($c);

		# Если товаров больше чем на одну страницу...
		if($count[0] > $this->limit) {
			# Получаем кол-во страниц
			$this->pages = $count[0] / $this->limit;
			# Проверяем полученное число на "целое" или "десятичное"
			if(mb_strpos($this->pages,".", 0, "utf8") !== false OR mb_strpos($this->pages,",", 0,"utf8") !== false) $this->pages++;
			# Округляем
			$this->pages = floor($this->pages);
		}

		# Если у нас используется переменная страницы в строке запроса, неравная первой странице...
		if($this->pages > "1" && $this->page != 0) {
			# Округляем до целых, что бы не вызвать ошибки в скрипте.
			$this->page = floor($this->page);

			# Если запрос не к нулевой странице и такая страница имеет право быть...
			if($this->page != "0" && $this->page <= $this->pages) {
				# $this->page--;
				$this->from = $this->limit * ($this->page - 1);
			}
		}

		# Если у нас в строке запроса указана страница, больше максимальной...
		if($this->page > $this->pages) {
			$this->page = $this->pages;
		}

		# Предыдущая и следующая страница
		if($this->page > 1) 			$this->prev_page = $this->page - 1;
		if($this->page < $this->pages) 	$this->next_page = $this->page + 1;
	}


	//#####################################################
	// Функция для расчета страниц, на случай когда не используется mySql
	public function pages_non_mysql($items) {

		# Если товаров больше чем на одну страницу...
		if($items > $this->limit) {
			# Получаем кол-во страниц
			$this->pages = $items / $this->limit;
			# Проверяем полученное число на "целое" или "десятичное"
			if(mb_strpos($this->pages,".", 0, "utf8") !== false OR mb_strpos($this->pages,",", 0, "utf8") !== false) $this->pages++;
			# Округляем
			$this->pages = floor($this->pages);
		}

		# Если у нас используется переменная страницы в строке запроса, неравная первой странице...
		if($this->pages > "1" && $this->page != 0) {
			# Округляем до целых, что бы не вызвать ошибки в скрипте.
			$this->page = floor($this->page);

			# Если запрос не к нулевой странице и такая страница имеет право быть...
			if($this->page != "0" && $this->page <= $this->pages) {
				# $this->page--;
				$this->from = $this->limit * ($this->page - 1);
			}
		}

		# Если у нас в строке запроса указана страница, больше максимальной...
		if($this->page > $this->pages) {
			$this->page = $this->pages;
		}

		# Предыдущая и следующая страница
		if($this->page > 1) 			$this->prev_page = $this->page - 1;
		if($this->page < $this->pages) 	$this->next_page = $this->page + 1;
	}
}

?>