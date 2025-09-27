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
 * Universal classes for work with databases through PDO
 * Supports only free and open-source server databases: MySQL/MariaDB, PostgreSQL, Firebird
 * Includes Query Builder, Prepared Statements and transactions
 */
class Db {

	use DbExtends, DebugLog;

	private DbConnect|null $db_connect 	= null;
	private PDO|null $pdo 				= null;
	private string $driver 				= '';
	private array $query_log 			= [];
	public int $query_count 			= 0;
	private bool $is_connected 			= false;
	private array $transaction_stack 	= [];



	/**
	 * Constructor with dependency injection
	 *
	 * @param DbConnect|null $db_connect Database connection instance
	 * @param string|null $driver Database driver (fallback for backward compatibility)
	 * @param array|null $config Database config (fallback for backward compatibility)
	 */
	public function __construct(?DbConnect $db_connect = null, ?string $driver = null, ?array $config = null) {
		// Use injected DbConnect or create new one for backward compatibility
		$this->db_connect = $db_connect ?? new DbConnect($driver, $config);
		$this->pdo = $this->db_connect->get_pdo();
		$this->driver = $this->db_connect->get_driver();
		$this->is_connected = $this->db_connect->is_connected();
	}


	/**
	 * Execution SQL query
	 * 
	 * @param string $sql
	 * @param array $params
	 * 
	 * @return PDOStatement
	 */
	public function query(string $sql, array $params = []): PDOStatement {
		if(!$this->is_connected || $this->pdo === null) {
			throw new Exception('No connection to the database');
		}

		$start_time = microtime(true);

		try {
			if(!empty($params)) {
				$stmt = $this->pdo->prepare($sql);
				$this->bind_parameters($stmt, $params);
				$stmt->execute();
			} else {
				$stmt = $this->pdo->query($sql);
			}

			$this->log_query($sql, $params, microtime(true) - $start_time);
			return $stmt;

		} catch(PDOException $e) {
			$this->handle_error('Error executing the query: ' . $e->getMessage(), $sql, $params);
		}
	}


	/**
	 * Binding parameters to prepared statement
	 *
	 * @param PDOStatement $stmt Prepared statement object
	 * @param array $params Parameters to bind
	 *
	 * @return void
	 * @throws PDOException
	 */
	private function bind_parameters(PDOStatement $stmt, array $params): void {
		foreach($params as $key => $value) {
			$param_key = is_int($key) ? $key + 1 : $key;
			$param_type = $this->get_pdo_param_type($value);
			
			$stmt->bindValue($param_key, $value, $param_type);
		}
	}


	/**
	 * Determining the type of parameter for PDO
	 *
	 * @param mixed $value Parameter value
	 *
	 * @return int PDO parameter type constant
	 */
	private function get_pdo_param_type(mixed $value): int {
		return match(gettype($value)) {
			'boolean' => PDO::PARAM_BOOL,
			'integer' => PDO::PARAM_INT,
			'NULL' => PDO::PARAM_NULL,
			'resource' => PDO::PARAM_LOB,
			default => PDO::PARAM_STR
		};
	}


	/**
	 * Execution query with return all rows
	 * 
	 * @param string $sql
	 * @param array $params
	 * @param int $fetchMode
	 * 
	 * @return array
	 */
	public function fetch_all(string $sql, array $params = [], int $fetch_mode = PDO::FETCH_ASSOC): array {
		$stmt = $this->query($sql, $params);
		return $stmt->fetchAll($fetch_mode);
	}


	/**
	 * Execution query with return one value
	 * 
	 * @param string $sql
	 * @param array $params
	 * @param int $column_index
	 * 
	 * @return mixed
	 */
	public function fetch_column(string $sql, array $params = [], int $column_index = 0): mixed {
		$stmt = $this->query($sql, $params);
		return $stmt->fetchColumn($column_index);
	}


	/**
	 * Getting associative array from result
	 *
	 * @param string $sql
	 * @param array $params
	 *
	 * @return array|false
	 */
	public function fetch_assoc(string $sql, array $params = []): array|false {
		$stmt = $this->query($sql, $params);
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}


	/**
	 * Getting last inserted ID
	 * 
	 * @param string|null $sequence
	 * 
	 * @return string
	 */
	public function insert_id(?string $sequence = null): string {
		return $this->pdo->lastInsertId($sequence);
	}


	/**
	 * Counting the number of rows from SQL query
	 *
	 * @param string $sql
	 * @param array $params
	 *
	 * @return int
	 */
	public function count_rows(string $sql, array $params = []): int {
		// For counting rows we use a wrapper in a subquery
		$count_sql = 'SELECT COUNT(*) FROM (' . $sql . ') as count_query';
		return (int) $this->fetch_column($count_sql, $params);
	}


	/**
	 * Create new query builder instance
	 * 
	 * @return DbQueryBuilder
	 */
	protected function create_query_builder(): DbQueryBuilder {
		return new DbQueryBuilder($this);
	}


	/**
	 * Query Builder: SELECT
	 * 
	 * @param string|array $columns Columns
	 * 
	 * @return DbQueryBuilder
	 */
	public function select(string|array $columns = '*'): DbQueryBuilder {
		return $this->create_query_builder()->select($columns);
	}


	/**
	 * Query Builder: INSERT
	 * 
	 * @param string $table Table
	 * 
	 * @return DbQueryBuilder
	 */
	public function insert(string $table): DbQueryBuilder {
		return $this->create_query_builder()->insert($table);
	}


	/**
	 * Query Builder: UPDATE
	 * 
	 * @param string $table Table
	 * 
	 * @return DbQueryBuilder
	 */
	public function update(string $table): DbQueryBuilder {
		return $this->create_query_builder()->update($table);
	}


	/**
	 * Query Builder: DELETE
	 * 
	 * @param string $table Table
	 * 
	 * @return DbQueryBuilder
	 */
	public function delete(string $table): DbQueryBuilder {
		return $this->create_query_builder()->delete($table);
	}


	/**
	 * Insert from array
	 * 
	 * @param array $data
	 * @param string $table
	 * 
	 * @return bool
	 */
	public function insert_array(array $data, string $table): bool {
		try {
			$start_time = microtime(true);

			$columns = array_keys($data);
			$quoted_columns = $this->quote_identifiers($columns);
			$placeholders = ':' . implode(', :', $columns);
			
			$sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $quoted_columns) . ') VALUES (' . $placeholders . ')';
			
			$stmt = $this->pdo->prepare($sql);
			
			foreach($data as $key => $value) {
				$stmt->bindValue(":$key", $value, $this->get_pdo_param_type($value));
			}
			
			$result = $stmt->execute();
			$this->log_query($sql, $data, microtime(true) - $start_time);
			
			return $result;
		} catch(PDOException $e) {
			$this->handle_error('Error inserting data: ' . $e->getMessage());
			return false;
		}
	}


	/**
	 * Update from array
	 * 
	 * @param array $data
	 * @param string $table
	 * @param string $where
	 * @param array $where_params
	 * 
	 * @return bool
	 */
	public function update_array(array $data, string $table, string $where, array $where_params = []): bool {
		try {
			$start_time = microtime(true);

			$set_parts = [];
			foreach(array_keys($data) as $column) {
				$set_parts[] = $this->quote_identifier($column) . " = :$column";
			}

			// Convert positional placeholders in WHERE to named ones to avoid mixing
			$named_where = $where;
			$named_params = [];
			if(strpos($where, '?') !== false && !empty($where_params)) {
				$counter = 1;
				foreach($where_params as $param) {
					$placeholder = ":w{$counter}";
					$pos = strpos($named_where, '?');
					if($pos === false) { break; }
					$named_where = substr_replace($named_where, $placeholder, $pos, 1);
					$named_params[$placeholder] = $param;
					$counter++;
				}
			}

			$sql = "UPDATE {$table} SET " . implode(', ', $set_parts) . " WHERE {$named_where}";
			
			$stmt = $this->pdo->prepare($sql);
			
			// Bind data for SET
			foreach($data as $key => $value) {
				$stmt->bindValue(":$key", $value, $this->get_pdo_param_type($value));
			}
			
			// Bind named WHERE parameters (if converted)
			foreach($named_params as $ph => $param) {
				$stmt->bindValue($ph, $param, $this->get_pdo_param_type($param));
			}

			$result = $stmt->execute();
			$this->log_query($sql, array_merge($data, $where_params), microtime(true) - $start_time);
			
			return $result;
		} catch(PDOException $e) {
			$this->handle_error("Error updating data: " . $e->getMessage());
			return false;
		}
	}


	/**
	 * Checking the existence of ID
	 * 
	 * @param int|string $id
	 * @param string $table
	 * @param string $field
	 * @param string $additionalWhere
	 * @param array $params
	 * 
	 * @return bool
	 */
	public function check_id(int|string $id, string $table, string $field = 'id', string $additionalWhere = '', array $params = []): bool {
		$where = "{$field} = ?";
		$where_params = [$id];

		if($additionalWhere) {
			$where .= " AND {$additionalWhere}";
			$where_params = array_merge($where_params, $params);
		}

		$sql = "SELECT 1 FROM {$table} WHERE {$where} LIMIT 1";
		return $this->fetch_column($sql, $where_params) !== false;
	}


	/**
	 * Batch insert
	 * 
	 * @param array $data
	 * @param string $table
	 * 
	 * @return bool
	 */
	public function insert_batch(array $data, string $table): bool {
		if(empty($data)) return false;

		try {
			$start_time = microtime(true);

			$this->begin_transaction();
			
			$columns = array_keys($data[0]);
			$quoted_columns = $this->quote_identifiers($columns);
			$placeholders = ':' . implode(', :', $columns);
			$sql = "INSERT INTO {$table} (" . implode(', ', $quoted_columns) . ") VALUES ({$placeholders})";
			
			$stmt = $this->pdo->prepare($sql);
			
			foreach($data as $row) {
				foreach($row as $key => $value) {
					$stmt->bindValue(":$key", $value, $this->get_pdo_param_type($value));
				}
				$stmt->execute();
			}
			
			$this->commit();
			$this->log_query($sql, ['batch_count' => count($data)], microtime(true) - $start_time);
			
			return true;
		} catch(PDOException $e) {
			$this->rollback();
			$this->handle_error("Error batch insert: " . $e->getMessage());
			return false;
		}
	}


	/**
	 * Execution of several requests in a transaction
	 * 
	 * @param callable $callback
	 * 
	 * @return mixed
	 */
	public function transaction(callable $callback): mixed {
		try {
			$this->begin_transaction();
			$result = $callback($this);
			$this->commit();
			return $result;
		} catch(Exception $e) {
			$this->rollback();
			throw $e;
		}
	}


	/**
	 * Beginning of a transaction
	 * 
	 * @return bool
	 */
	public function begin_transaction(): bool {
		if(empty($this->transaction_stack)) {
			$result = $this->pdo->beginTransaction();
			if($result) {
				$this->transaction_stack[] = true;
			}
			return $result;
		} else {
			// Emulation of nested transactions through savepoints
			$savepoint_name = 'savepoint_' . count($this->transaction_stack);
			$this->pdo->exec("SAVEPOINT {$savepoint_name}");
			$this->transaction_stack[] = $savepoint_name;
			return true;
		}
	}


	/**
	 * Confirmation of a transaction
	 * 
	 * @return bool
	 */
	public function commit(): bool {
		if(empty($this->transaction_stack)) {
			return false;
		}

		// Handle drivers with implicit commits on DDL (e.g., MySQL)
		if(!$this->pdo->inTransaction()) {
			$this->transaction_stack = [];
			return true;
		}

		$last_transaction = array_pop($this->transaction_stack);
		
		if($last_transaction === true) {
			// Main transaction
			return $this->pdo->commit();
		} else {
			// Savepoint - just remove it from the stack
			$this->pdo->exec("RELEASE SAVEPOINT {$last_transaction}");
			return true;
		}
	}


	/**
	 * Rollback of a transaction
	 * 
	 * @return bool
	 */
	public function rollback(): bool {
		if(empty($this->transaction_stack)) {
			return false;
		}

		// Handle drivers with implicit commits on DDL (e.g., MySQL)
		if(!$this->pdo->inTransaction()) {
			$this->transaction_stack = [];
			return true;
		}

		$last_transaction = array_pop($this->transaction_stack);
		
		if($last_transaction === true) {
			// Main transaction
			return $this->pdo->rollBack();
		} else {
			// Rollback to savepoint
			$this->pdo->exec("ROLLBACK TO SAVEPOINT {$last_transaction}");
			return true;
		}
	}


	/**
	 * Getting the table schema
	 * 
	 * @param string $table
	 * 
	 * @return array
	 */
	public function get_table_schema(string $table): array {
		return match($this->driver) {
			'mysql', 'mysqli', 'mariadb' => $this->get_mysql_table_schema($table),
			'pgsql', 'postgres', 'postgresql' => $this->get_postgres_table_schema($table),
			'firebird' => $this->get_firebird_table_schema($table),
			default => []
		};
	}


	/**
	 * Checking the activity of a transaction
	 * 
	 * @return bool
	 */
	public function in_transaction(): bool {
		return $this->pdo->inTransaction();
	}


	/**
	 * MySQL table schema
	 * 
	 * @param string $table
	 * 
	 * @return array
	 */
	private function get_mysql_table_schema(string $table): array {
		return $this->fetch_all("DESCRIBE {$table}");
	}


	/**
	 * PostgreSQL table schema
	 * 
	 * @param string $table
	 * 
	 * @return array
	 */
	private function get_postgres_table_schema(string $table): array {
		return $this->fetch_all("
			SELECT column_name, data_type, is_nullable, column_default 
			FROM information_schema.columns 
			WHERE table_name = ?
		", [$table]);
	}


	/**
	 * Firebird table schema
	 * 
	 * @param string $table
	 * 
	 * @return array
	 */
	private function get_firebird_table_schema(string $table): array {
		return $this->fetch_all("
			SELECT 
				rf.rdb\$field_name as field_name,
				ft.rdb\$type_name as field_type,
				rf.rdb\$null_flag as is_nullable,
				rf.rdb\$default_source as field_default
			FROM rdb\$relation_fields rf
			JOIN rdb\$fields f ON f.rdb\$field_name = rf.rdb\$field_source
			JOIN rdb\$types ft ON ft.rdb\$type = f.rdb\$field_type
			WHERE rf.rdb\$relation_name = UPPER(?)
			ORDER BY rf.rdb\$field_position
		", [$table]);
	}


	/**
	 * Quote identifier (column/table name) per driver
	 * 
	 * @param string $identifier
	 * 
	 * @return string
	 */
	private function quote_identifier(string $identifier): string {
		return match($this->driver) {
			'pgsql', 'postgres', 'postgresql', 'firebird' => '"' . str_replace('"', '""', $identifier) . '"',
			default => '`' . str_replace('`', '``', $identifier) . '`'
		};
	}

	/**
	 * Quote list of identifiers
	 * 
	 * @param array $identifiers
	 * 
	 * @return array
	 */
	private function quote_identifiers(array $identifiers): array {
		return array_map(fn($id) => $this->quote_identifier((string)$id), $identifiers);
	}


	/**
	 * Logging requests
	 *
	 * @param string $sql SQL query string
	 * @param array $params Query parameters
	 * @param float $execution_time Query execution time in seconds
	 *
	 * @return void
	 */
	private function log_query(string $sql, array $params, float $execution_time): void {
		$this->query_count++;
		
		$this->query_log[] = [
			'sql' => $sql,
			'params' => $params,
			'time' => $execution_time,
			'number' => $this->query_count
		];

		if(defined('DEBUGMODE') && DEBUGMODE && function_exists('debugQuery')) {
			debugQuery($sql, $params, $execution_time);
		}
	}


	/**
	 * Error handling
	 *
	 * @param string $message Error message
	 * @param string $sql SQL query that caused error
	 * @param array $params Query parameters
	 *
	 * @return void
	 * @throws Exception
	 */
	private function handle_error(string $message, string $sql = '', array $params = []): never {
		http_response_code(500);

		$response = [
			'error' => true,
			'message' => $message,
			'status_code' => 500,
			'timestamp' => date('Y-m-d H:i:s')
		];

		// Add debug info in development mode
		if (defined('DEBUGMODE') && DEBUGMODE) {
			$error_info = $this->pdo->errorInfo();
			$response['debug'] = [
				'pdo_error' => $error_info[2] ?? null,
				'sql' => $sql,
				'params' => $params
			];
		}

		output_json($response);
	}


	/**
	 * Check current database connection health
	 *
	 * !But this parameter works not with all database drivers and may not give the expected effect.
	 * @param int $timeout Connection timeout in seconds (default 5 seconds) 
	 * @return bool Connection health status
	 */
	public function ping(int $timeout = 5): bool {

		if(!$this->is_connected) {
			return false;
		}

		try {
			$this->pdo->setAttribute(PDO::ATTR_TIMEOUT, $timeout);
			$stmt = $this->query('SELECT 1');
			return $stmt !== false;
		} catch(PDOException $e) {
			return false;
		}
	}


	/**
	 * Getting request statistics
	 *
	 * @return array
	 */
	public function get_query_stats(): array {
		return [
			'count_queries' => $this->query_count,
			'queries' => $this->query_log,
			'total_time' => array_sum(array_column($this->query_log, 'time')),
			'average_time' => $this->query_count > 0 ? array_sum(array_column($this->query_log, 'time')) / $this->query_count : 0,
			'memory_usage' => memory_get_usage(true),
			'peak_memory' => memory_get_peak_usage(true),
			'check_time' => time()
		];
	}

	
	/**
	 * Monitor database health
	 *
	 * @return array Health status information
	 */
	public function get_health_status(): array {
		$connection_alive = $this->ping(10);

		return [
			'status' => $connection_alive ? 'healthy' : 'unhealthy',
			'connection_alive' => $connection_alive
		];
	}


	/**
	 * Closing connection
	 */
	public function close(): void {
		if($this->pdo) {
			$this->pdo = null;
			$this->is_connected = false;
		}
	}


	/**
	 * Destructor - automatically closes the connection
	 */
	public function __destruct() {
		$this->close();
	}
}
