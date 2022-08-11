<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2023 alexandr Belov aka alex Roosso. All rights reserved.
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
 * Class MySqlExtends
 */
trait MySQLiDBExtends {

	# pages param
	public  $pages		= 0;	# [int]	Number of pages
	public  $page		= 1;	# [int] Current page
	public 	$limit		= 15;	# [int] Number of rows for query
	public  $from		= 0;	# [int] Starting position for request



	/**
	 * Check connection pattern
	 *
	 * @return bool
	 */
	protected function connecting() {

		if($this->db_connect || DEBUGMODE) {
			$res = true;
		}
		else {
			$res = false;
		}

		return $res;
	}


	/**
	* Функция подсчета страниц
	*
	* @param mixed $from
	* @param mixed $cond
	* @param mixed $query
	*/
	public function pages_mysql($from, $cond="id!=0", $query="") {

		# Count
		$count = $this->count($from, "{$cond} {$query}");

		# Если товаров больше чем на одну страницу...
		if($count > $this->limit) {
			# Get number of pages
			$this->pages = $count / $this->limit;
			# Проверяем полученное число на "целое" или "десятичное"
			if(mb_strpos($this->pages,".", 0, "utf8") !== false || mb_strpos($this->pages,",", 0,"utf8") !== false) {
				$this->pages++;
			}
			# Round off
			$this->pages = (int) floor($this->pages);
		}

		# Calculate page vars
		$this->claculate_page_vars();
	}


	/**
	* Функция для расчета страниц, на случай когда не используется mySql
	*
	* @param int $items - общее число элементов
	*/
	public function pages_non_mysql(int $items) {

		# Если товаров больше чем на одну страницу...
		if($items > $this->limit) {
			# Получаем кол-во страниц
			$this->pages = $items / $this->limit;
			# Проверяем полученное число на "целое" или "десятичное"
			if(mb_strpos($this->pages,".", 0, "utf8") !== false || mb_strpos($this->pages,",", 0, "utf8") !== false) {
				$this->pages++;
			}
			# Round off
			$this->pages = (int) floor($this->pages);
		}

		# Calculate page vars
		$this->claculate_page_vars();
	}


	/**
	 * Вспомогательная функция расчитывает переменные для управления постраничного листинга
	 */
	private function claculate_page_vars() {

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
		if($this->page > $this->pages) {
			$this->page = $this->pages;
		}
	}


	/**
	 * Array pagination
	 *
	 * @return array
	 */
	public function construct_pagination() {

		global $db, $site;

		$pages = [];

		# pages
		for($p=1;$p<=$this->pages;$p++) {
			$pages[]['n'] = $p;
		}

		return $pages;
	}


	/**
	 * Check for adding AND rules for condition
	 *
	 * @param string $cond - condition
	 *
	 * @return string
	 */
	public function qcond_and(string $cond) {
		if(trim($cond) != "") {
			$cond .= " AND ";
		}

		return $cond;
	}


	/**
	 * Check for adding OR rules for condition
	 *
	 * @param string $cond - condition
	 *
	 * @return string
	 */
	public function qcond_or(string $cond) {
		if(trim($cond) != "") {
			$cond .= " OR ";
		}

		return $cond;
	}


	/**
	 * Abstract
	 *
	 * @param $from
	 * @param $proviso
	 *
	 * @return mixed
	 */
	abstract protected function count($from, $proviso);
}
