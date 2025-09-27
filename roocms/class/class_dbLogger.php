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

/**
 * Database Query Logger
 * Handles logging and statistics for database queries
 */
class DbLogger {

	private array $query_log = [];
	private int $query_count = 0;

	/**
	 * Log a database query
	 *
	 * @param string $sql SQL query string
	 * @param array $params Query parameters
	 * @param float $execution_time Query execution time in seconds
	 * @return void
	 */
	public function log_query(string $sql, array $params, float $execution_time): void {
		$this->query_count++;
		
		$this->query_log[] = [
			'sql' => $sql,
			'params' => $params,
			'time' => $execution_time,
			'number' => $this->query_count
		];

		// External debug logging if available
		if(defined('DEBUGMODE') && DEBUGMODE && function_exists('debugQuery')) {
			debugQuery($sql, $params, $execution_time);
		}
	}

	/**
	 * Get query statistics
	 *
	 * @return array
	 */
	public function get_query_stats(): array {
		$total_time = array_sum(array_column($this->query_log, 'time'));
		
		return [
			'count_queries' => $this->query_count,
			'queries' => $this->query_log,
			'total_time' => $total_time,
			'average_time' => $this->query_count > 0 ? $total_time / $this->query_count : 0,
			'memory_usage' => memory_get_usage(true),
			'peak_memory' => memory_get_peak_usage(true),
			'check_time' => time()
		];
	}

	/**
	 * Get query count
	 *
	 * @return int
	 */
	public function get_query_count(): int {
		return $this->query_count;
	}

	/**
	 * Get query log
	 *
	 * @return array
	 */
	public function get_query_log(): array {
		return $this->query_log;
	}

	/**
	 * Clear query log
	 *
	 * @return void
	 */
	public function clear_log(): void {
		$this->query_log = [];
		$this->query_count = 0;
	}
}
