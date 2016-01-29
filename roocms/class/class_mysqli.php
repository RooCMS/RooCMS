<?php
/**
 * @package      RooCMS
 * @subpackage	 Engine RooCMS classes
 * @author       alex Roosso
 * @copyright    2010-2016 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      3.2.3
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
 * Class MySQLDatabase
 */
class MySQLiDatabase extends MySQLiExtends {

	private $sql;

	public	$db_connect 	= false;	# [bool]	Флаг состояния подключения к БД
	public	$cnt_querys 	= 0;		# [int] 	Счетчик запросов в БД


	/**
	* Let's begin
	*
	*/
	public function MySQLiDatabase() {

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

		$this->sql = new mysqli($host,$user,$pass, $base);

		if($this->sql->connect_errno == 0) $this->db_connect = true;
		else exit($this->error());
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
		$this->sql = new mysqli($host,$user,$pass, $base);
		error_reporting(E_ALL);

		if($this->sql->connect_errno != 0) return false;
		else {
			if(defined('INSTALL')) $this->db_connect = true;
			return true;
		}
	}


	/**
	* Установка кодировок для соединения с БД
	* Некоторые версии MySQL неправильно работают с кодировками.
	* Эта функция помогает устранить ошибки в работе.
	* Стоит помнить, что это все таки 3 запроса к БД на каждую страницу и вызов.
	* Поэтому если БД работает стабильно, лучше выключить данную функцию.
	*/
	private function charset() {
		$this->sql->set_charset("utf8");
	}


	/**
	* Парсер ошибок запросов к БД
	* Функция используется для отладки запросов
	*
	* @param string $q - Запрос осуществленный к БД
	* @return mixed при влключенном режиме отладки вернет ошибку, иначе выведет общее сообщение на экран об ошибке
	*/
	private function error($q = "") {

		# режим отладки
		if(DEBUGMODE && $q!="") {
			$query = "<div style='padding: 5px;text-align: left;'><font style='font-family: Verdana, Tahoma; font-size: 12px;text-align: left;'>
			Ошибка БД [MySQL Error]: <b>".$this->sql->errno."</b>
			<br /> &bull; ".$this->sql->error."
			<br />
			<br /><table width='100%' style='border: 1px solid #ffdd00; background-color: #ffffee;text-align: left;'>
			 <tr>
			  <td align='left' style='text-align: left;'><font style='font-family: Tahoma; font-size: 11px;text-align: left;'>
			  <font color='#990000'><b>SQL Запрос:</b> <pre>{$q}</pre></font>
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
			foreach($f AS $k=>$v) {
				echo $v;
			}
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

		if($this->db_connect || DEBUGMODE) {
			# Выполняем запрос
			$query = $this->sql->query($q) or die ($this->error($q));

			# Считаем запросы
			if(DEBUGMODE || defined('ACP')) $this->cnt_querys++;

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
				if($this->cnt_querys != 1) $debug->debug_info .= "<hr>";
				$debug->debug_info .= "<blockquote class='col-xs-12'>
							    <small>Запрос <b>#".$this->cnt_querys."</b></small>
							    <span class='text-danger'>{$q}</span>
						       </blockquote>";
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
	public function insert_array(array $array, $table) {

		$fields	= "";
		$values	= "";

		foreach($array AS $key=>$value) {
			if(trim($fields) == "") 	$fields .= $key;
			else				$fields .= ", ".$key;

			if(trim($values) == "") 	$values .= "'".$value."'";
			else				$values .= ", '".$value."'";
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
	public function update_array(array $array, $table, $where) {

		$update = "";
		foreach($array AS $key=>$value) {
			if(trim($update) == "") 	$update .= $key."='".$value."'";
			else				$update .= ", ".$key."='".$value."'";
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

		if($this->db_connect || DEBUGMODE) {
			$result = mysqli_fetch_row($q);
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

		if($this->db_connect || DEBUGMODE) {
			$result = mysqli_fetch_assoc($q);
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

		if($this->db_connect || DEBUGMODE) {
			$obj = mysqli_fetch_object($q);
			return $obj;
		}
	}


	/**
	* Have a index query
	* @return int Возвращает идентификатор
	*/
	public function insert_id() {

		if($this->db_connect || DEBUGMODE) {
			$id = $this->sql->insert_id;
			return $id;
		}
	}


	/**
	 * Функция проверяет имеется ли запрашиваймый id.
	 *
	 * @param        $id
	 * @param string $table - таблица в которой проводится проверка
	 * @param string $field - название поля таблицы содержащий идентификатор
	 * @param string $where - Дополнительное условие (фильтр) для проверки
	 *
	 * @internal param int $uniq $id  - проверямый идентификатор
	 * @return int|boolean - Возвращает количество найденных строк, соответсвующих критериям или false в случае неудачи
	 */
	public function check_id($id, $table, $field="id", $where="") {

		if($field == "id") $id = round($id);

		if(trim($where) != "") $where = " AND ".$where;

		$q = $this->query("SELECT count(*) FROM ".$table." WHERE {$field}='".$id."' ".$where."");
		$c = $this->fetch_row($q);

                if($c[0] == 0) return false;
                else return $c[0];
	}


	/**
	 * Функция указывает какое кол-во строк вернул запрос
	 * Работает только с Select и Show
	 *
	 * @param string $q - запрос
	 *
	 * @return int
	 */
	public function num_rows($q) {
		return mysqli_num_rows($q);
	}


	/**
	 * Функция указывает какое кол-во строк были затронуты последним запросом
	 * Работает только с Insert и Update
	 *
	 * @return int
	 */
	public function affected_rows() {
		return $this->sql->affected_rows;
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
		        "'"		=> "&#39;"
		));

		if($this->db_connect) return $this->sql->real_escape_string($q);
		else return $q;
	}


	/**
	 * ERRNO
	 *
	 * @param bool $error
	 *
	 * @return mixed
	 */
	public function errno($error=false) {
		if(defined('INSTALL') || defined('UPDATE')) {
			if($error) return $this->sql->error;
			else return $this->sql->errno;
		}

	}


	/**
	* Закрываем подключение к БД сайта
	*
	*/
	public function close() {
		$this->sql->close();
	}
}

?>