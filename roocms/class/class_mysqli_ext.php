<?php
/**
* @package      RooCMS
* @subpackage	Engine RooCMS classes
* @author       alex Roosso
* @copyright    2010-2016 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.1.1
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
*   Copyright (C) 2010-2017 alex Roosso aka alexandr Belov info@roocms.com
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
*   Copyright (C) 2010-2017 alex Roosso (александр Белов) info@roocms.com
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
 * Class MySqlExtends
 */
class MySQLiExtends {

	# pages param
	public  $pages		= 0;	# [int]	Всего страниц
	public  $page		= 1;	# [int] Текущая страница
	public	$prev_page	= 0;	# [int] Предыдущая страница
	public	$next_page	= 0;	# [int] Следующая страница
	public 	$limit		= 15;	# [int] Число строк для запроса
	public  $from		= 0;	# [int] Стартовая позиция для запроса


	/**
	* Функция подсчета страниц
	*
	* @param mixed $from
	* @param mixed $where
	* @param mixed $query
	*/
	public function pages_mysql($from, $where="id!=0", $query="") {

		# Считаем
		$c = $this->query("SELECT count(*) FROM {$from} WHERE {$where} {$query}");
		$count = $this->fetch_row($c);

		# Если товаров больше чем на одну страницу...
		if($count[0] > $this->limit) {
			# Получаем кол-во страниц
			$this->pages = $count[0] / $this->limit;
			# Проверяем полученное число на "целое" или "десятичное"
			if(mb_strpos($this->pages,".", 0, "utf8") !== false || mb_strpos($this->pages,",", 0,"utf8") !== false) $this->pages++;
			# Округляем
			$this->pages = (int) floor($this->pages);
		}

		# Если у нас используется переменная страницы в строке запроса, неравная первой странице...
		if($this->pages > "1" && $this->page != 0) {
			# Округляем до целых, что бы не вызвать ошибки в скрипте.
			$this->page = (int) floor($this->page);

			# Если запрос не к нулевой странице и такая страница имеет право быть...
			if($this->page != "0" && $this->page <= $this->pages) {
				$this->from = (int) $this->limit * ($this->page - 1);
			}
		}

		# Если у нас в строке запроса указана страница, больше максимальной...
		if($this->page > $this->pages) $this->page = $this->pages;

		# Предыдущая и следующая страница
		if($this->page > 1) 		$this->prev_page = $this->page - 1;
		if($this->page < $this->pages) 	$this->next_page = $this->page + 1;
	}


	/**
	* Функция для расчета страниц, на случай когда не используется mySql
	*
	* @param int $items - общее число элементов
	*/
	public function pages_non_mysql($items) {

		# Если товаров больше чем на одну страницу...
		if($items > $this->limit) {
			# Получаем кол-во страниц
			$this->pages = $items / $this->limit;
			# Проверяем полученное число на "целое" или "десятичное"
			if(mb_strpos($this->pages,".", 0, "utf8") !== false || mb_strpos($this->pages,",", 0, "utf8") !== false) $this->pages++;
			# Округляем
			$this->pages = (int) floor($this->pages);
		}

		# Если у нас используется переменная страницы в строке запроса, неравная первой странице...
		if($this->pages > "1" && $this->page != 0) {
			# Округляем до целых, что бы не вызвать ошибки в скрипте.
			$this->page = (int) floor($this->page);

			# Если запрос не к нулевой странице и такая страница имеет право быть...
			if($this->page != "0" && $this->page <= $this->pages) {
				$this->from = (int) $this->limit * ($this->page - 1);
			}
		}

		# Если у нас в строке запроса указана страница, больше максимальной...
		if($this->page > $this->pages) $this->page = $this->pages;

		# Предыдущая и следующая страница
		if($this->page > 1) 		$this->prev_page = $this->page - 1;
		if($this->page < $this->pages) 	$this->next_page = $this->page + 1;
	}
}

?>