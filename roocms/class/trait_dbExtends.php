<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * © 2010-2025 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      https://www.roocms.com
 * @license   https://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program. If not, see https://www.gnu.org/licenses/
 */

//#########################################################
//	Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	http_response_code(403);
	header('Content-Type: text/plain; charset=utf-8');
	exit('403:Access denied');
}
//#########################################################


trait DbExtends {

	// Pagination parameters
	public int $pages   = 0;
	public int $page    = 1;
	public int $limit   = 15;
	public int $from    = 0;



    /**
	 * Checking connection to the database
     *
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $base
     * @param int|null $port
     * @param bool $detailed Return detailed connection info
     *
     * @return bool|array Connection status or detailed info
	 */
	public function check_connect(string $host, string $user, string $pass, string $base, ?int $port = null, ?string $driver = null, bool $detailed = false): bool|array {
		try {
			$driver = $driver ?? $this->driver;
			
			$config = [
				'host' => $host,
				'user' => $user,
				'pass' => $pass,
				'base' => $base,
				'port' => $port,
				'type' => $driver
			];

			$testDb = new DbConnect($driver, $config);
			$connected = $testDb->is_connected();

			if(!$detailed) {
				return $connected;
			}

			if($connected) {
				return [
					'connected' => true,
					'database_info' => $testDb->get_database_info(),
					'table_count' => $this->get_table_count(),
					'test_time' => time()
				];
			}

			return [
				'connected' => false,
				'test_time' => time()
			];

		} catch(Exception $e) {
			if($detailed) {
				return [
					'connected' => false,
					'error' => $e->getMessage(),
					'test_time' => time()
				];
			}
			return false;
		}
	}


    /**
	 * Getting PDO object (for extended operations)
	 * 
	 * @return PDO
	 */
	public function get_pdo(): PDO {
		return $this->pdo;
	}


	/**
	 * Getting driver type
	 * 
	 * @return string
	 */
	public function get_driver(): string {
		return $this->driver;
	}

	
	/**
	 * Checking connection
	 * 
	 * @return bool
	 */
	public function is_connected(): bool {
		return $this->is_connected;
	}


    /**
	 * Pagination for DB
     * Similar page_in_db() method from 1.x version
     * 
     * @param string $table
     * @param string $where
     * @param array $params
	 */
	public function paginate_from_db(string $table, string $where = '1=1', array $params = []): void {
		$count = $this->count_rows($table, $where, $params);
		$this->calculate_pagination($count);
	}


	/**
	 * Pagination for array
     * Similar page_non_db() method from 1.x version
     * 
     * @param int $totalItems
	 */
	public function paginate_from_array(int $totalItems): void {
		$this->calculate_pagination($totalItems);
	}


	/**
	 * Calculation of pagination
     * 
     * @param int $totalItems
	 */
	private function calculate_pagination(int $totalItems): void {
		if($totalItems > $this->limit) {
			$this->pages = (int) ceil($totalItems / $this->limit);
		}

		if($this->pages > 1 && $this->page > 0) {
			$this->page = min($this->page, $this->pages);
			$this->from = ($this->page - 1) * $this->limit;
		}
	}


	/**
	 * Getting an array of pages for pagination
     * Similar construct_pagination() method from 1.x version
     * 
     * @return array
	 */
	public function get_pagination_array(): array {
		$pages = [];
		for($i = 1; $i <= $this->pages; $i++) {
			$pages[] = ['n' => $i];
		}
		return $pages;
	}


    /**
     * Handling the query condition method
     * 
     * @param string $method AND or OR
     * @param array $arguments
     * @return string
     */
    public function query_condition(string $method, array $arguments): string {
		$cond = $arguments[0] ?? '';
		
		if(trim($cond) != "") {
			return $method === 'AND' ? $cond . " AND " : $cond . " OR "; // AND or OR
		}
		
		return $cond;
	}

	
	/**
     * Getting the number of tables in the database
     *
     * @return int
     */
    public function get_table_count(): int {
        try {
            $sql = match($this->driver) {
                'mysql', 'mysqli', 'mariadb' => "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()",
                'pgsql', 'postgres', 'postgresql' => "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = current_schema()",
                'firebird' => "SELECT COUNT(*) FROM rdb\$relations WHERE rdb\$system_flag = 0",
                default => "SELECT 0"
            };

            return (int) $this->fetch_column($sql);
        } catch(Exception $e) {
            return 0;
        }
    }


    /**
     * Counting the number of rows in the table
     *
     * @param string $table
     * @param string $where
     * @param array $params
     * @return int
     */
    abstract protected function count_rows(string $table, string $where = '1=1', array $params = []): int;
}