<?php
/*=========================================================
|	This script was developed by alex Roosso .
|	Title: RooCMS MySQL Class
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
|	Build: 				20:03 29.11.2010
|	Last Build: 		15:59 28.10.2011
|	Version file:		2.00 build 25
=========================================================*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


//	database class :: MySQL

$db = new DataBase;

class DataBase {

	private $id 		= false;
	public  $q 			= "";
	public  $cnt_querys = 0;

	
	# pages
	public  $pages		= 0;
	public  $page		= 1;
	public	$prev_page	= 0;
	public	$next_page	= 0;
	public 	$limit		= 15;
	public  $from		= 0;
	
	

	//=====================================================
	// start
	function __construct() {
	
		global $db_info;
		
		if($db_info['host'] != "" && $db_info['database'] != "")
			$this->connect($db_info['host'], $db_info['user'], $db_info['pass'], $db_info['database']);
		
		// set mysql charset
		$this->charset();
	}
	
	
	//=====================================================
	// Connect to DataBase 
	private function connect($host,$user,$pass, $base) {
	
		global $debug;
		
		mysql_connect($host,$user,$pass) or die ($this->error());
		mysql_select_db($base) or die ($this->error());
	}
	
	
	//=====================================================
	// Set query charset
	private function charset() {
	
		mysql_query ("set character_set_client = 'utf8'"); 
		mysql_query ("set character_set_results = 'utf8'"); 
		mysql_query ("set collation_connection = 'utf8_general_ci'"); 
		//mysql_query ("set names 'utf8'");		
	}
	
	
	//=====================================================
	// Parse error
	private function error($q = "") {
	
		global $Debug;
	
		if($Debug->debug == "1") {
			$query = "<div style=\"padding: 5px;text-align: left;\"><font style=\"font-family: Tahoma; font-size: 12px;text-align: left;\">
			MySQL Error: <b>".mysql_errno()."</b>
			<br />".mysql_error()."
			<br />
			<br /><table width=\"100%\" style=\"border: 1px solid #ffdd00; background-color: #ffffee;text-align: left;\">
			 <tr>
			  <td align=\"left\" style=\"text-align: left;\"><font style=\"font-family: Tahoma; font-size: 10px;text-align: left;\">
			  <font color=\"#990000\"><b>SQL Query:</b><pre> $q</pre></font>
			  </font></td>
			 </tr>
			</table></font></div>";
		}
		else {
			$f = file(_TEMPLATES."/db_error.html");
			foreach($f AS $k=>$v) echo $v;
		}
		return $query;
	}

	
	//=====================================================
	// Query
	public function query($q = "") {
	
		global $Debug;
	
		$query = mysql_query($q) or die ($this->error($q));
		
		// Считаем запросы
		if($Debug->debug == 1) {
			$this->cnt_querys++;
		}

		// Выводим информацию по всем запросам
		if($Debug->show_debug == 1)
		{
			// parse debug
			$q = strtr($q, array(
				'SELECT' 	=> '<b>SELECT</b>', 
				'FROM' 		=> '<b>FROM</b>', 
				'WHERE' 	=> '<b>WHERE</b>',
				'ORDER' 	=> '<b>ORDER</b>',
				'BY' 		=> '<b>BY</b>',
				'LIMIT' 	=> '<b>LIMIT</b>' 
			));
			
			// debug info querys
			$Debug->debug_info .= "<table width=\"100%\" style=\"border: 1px solid #ffdd00; background-color: #ffffee;text-align: left;\">
			 <tr>
			  <td align=\"left\" style=\"text-align: left;\"><font style=\"font-family: Tahoma; font-size: 10px;text-align: left;\">
			  <font color=\"#990000\"><pre><i style=\"color: blue;\"><u>SQL Query ".$this->cnt_querys.":</u></i><br /> $q</pre></font>
			  </font></td>
			 </tr>
			</table>";
		}
		
		return $query;
	}
	
	
	//=====================================================
	// Fetch result to array
	public function fetch_row($q) {
		$arr = mysql_fetch_row($q);
		return $arr;
	}
	
	
	//=====================================================
	// Fetch result to assoc array
	public function fetch_assoc($q) {
		$arr = mysql_fetch_assoc($q);
		return $arr;
	}
	
	
	//=====================================================
	// Fetch result to object
	public function fetch_object($q) {
		$obj = mysql_fetch_object($q);
		return $obj;
	}
	
	
	//=====================================================
	// Have a index query
	public function insert_id() {
		$id = mysql_insert_id();
		return $id;
	}
	
	
	//=====================================================
	// Функция проверяет имеется ли запрашиваймый id.
	public function check_id($id, $table, $field="id") {

		$q = $this->query("SELECT count(*) FROM ".$table." WHERE {$field}='".$id."'");
		$c = $this->fetch_row($q);
		
		return $c[0];
	}
	
	
	public function num_rows($q) {
		return mysql_num_rows($q);
	}

	
	//=====================================================
	// Clear system symbols for query
	public function escape_string($q) {
	
		$q = htmlspecialchars($q);
		$q = strtr($q, array(
			'{' => '&#123;', 
			'}' => '&#125;', 
			'$' => '&#36;' 
		));
		$q = str_replace("'","&#39;",	$q);
		return mysql_real_escape_string($q);
	}
	
	
	//=====================================================
	// Close connection to DataaBase
	public function close() {	
		@mysql_close();
	}
	
	
	//*****************************************************
	//	jaga jaga
	public function pages_mysql($from, $where="id!=0", $query="") {
	
		//Считаем
		$count = array();
		$c = $this->query("SELECT count(*) FROM ".$from." WHERE ".$where." ".$query);
		$count = $this->fetch_row($c);

		//Если товаров больше чем на одну страницу...
		if($count[0] > $this->limit) {	
			//Получаем кол-во страниц
			$this->pages = $count[0] / $this->limit;
			//Проверяем полученное число на "целое" или "десятичное"
			if(mb_strpos($this->pages,".", 0, "utf8") !== false OR mb_strpos($this->pages,",", 0,"utf8") !== false) $this->pages++;
			//Округляем
			$this->pages = floor($this->pages);
		}
		
		//Если у нас используется переменная страницы в строке запроса, неравная первой странице...
		if($this->pages > "1" && $this->page != 0) {
			//Округляем до целых, что бы не вызвать ошибки в скрипте.
			$this->page = floor($this->page);
			
			//Если запрос не к нулевой странице и такая страница имеет право быть...
			if($this->page != "0" && $this->page <= $this->pages) {
				//$this->page--;
				$this->from = $this->limit * ($this->page - 1);
			}
		}

		//Если у нас в строке запроса указана страница, больше максимальной...
		if($this->page > $this->pages) {
			$this->page = $this->pages;
		}
		
		// Предыдущая и следующая страница
		if($this->page > 1) 			$this->prev_page = $this->page - 1;
		if($this->page < $this->pages) 	$this->next_page = $this->page + 1;
	}
	
	
	//*****************************************************
	// Функция для расчета страниц, на случай когда не используется mySql
	public function pages_non_mysql($items) {
	
		//Если товаров больше чем на одну страницу...
		if($items > $this->limit) {	
			//Получаем кол-во страниц
			$this->pages = $items / $this->limit;
			//Проверяем полученное число на "целое" или "десятичное"
			if(mb_strpos($this->pages,".", 0, "utf8") !== false OR mb_strpos($this->pages,",", 0, "utf8") !== false) $this->pages++;
			//Округляем
			$this->pages = floor($this->pages);
		}
		
		//Если у нас используется переменная страницы в строке запроса, неравная первой странице...
		if($this->pages > "1" && $this->page != 0) {
			//Округляем до целых, что бы не вызвать ошибки в скрипте.
			$this->page = floor($this->page);
			
			//Если запрос не к нулевой странице и такая страница имеет право быть...
			if($this->page != "0" && $this->page <= $this->pages) {
				//$this->page--;
				$this->from = $this->limit * ($this->page - 1);
			}
		}

		//Если у нас в строке запроса указана страница, больше максимальной...
		if($this->page > $this->pages) {
			$this->page = $this->pages;
		}
		
		// Предыдущая и следующая страница
		if($this->page > 1) 			$this->prev_page = $this->page - 1;
		if($this->page < $this->pages) 	$this->next_page = $this->page + 1;
	}
}

?>