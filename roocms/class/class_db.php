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

	private DbConnect $db_connect;
	private PDO $pdo;
	private string $driver 				= '';
	private DbLogger $logger;
	private bool $is_connected 			= false;
	private array $transaction_stack 	= [];



	/**
	 * Constructor with dependency injection
	 *
	 * @param DbConnect|null $db_connect Database connection instance
	 * @param string|null $driver Database driver (fallback for backward compatibility)
	 * @param array|null $config Database config (fallback for backward compatibility)
	 * @param DbLogger|null $logger Database logger instance
	 */
	public function __construct(?DbConnect $db_connect = null, ?string $driver = null, ?array $config = null, ?DbLogger $logger = null) {
		// Use injected DbConnect or create new one for backward compatibility
		$this->db_connect = $db_connect ?? new DbConnect($driver, $config);
		$this->pdo = $this->db_connect->get_pdo();
		$this->driver = $this->db_connect->get_driver();
		$this->is_connected = $this->db_connect->is_connected();
		$this->logger = $logger ?? new DbLogger();
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
		if(!$this->is_connected) {
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

			$this->logger->log_query($sql, $params, microtime(true) - $start_time);
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
			$this->logger->log_query($sql, $data, microtime(true) - $start_time);
			
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

			// Build SET clause
			$set_parts = array_map(
				fn($column) => $this->quote_identifier($column) . " = :set_{$column}",
				array_keys($data)
			);

			// Convert positional WHERE placeholders to named to avoid parameter conflicts
			[$processed_where, $named_params] = $this->convert_positional_to_named($where, $where_params);

			$sql = "UPDATE {$table} SET " . implode(', ', $set_parts) . " WHERE {$processed_where}";
			$stmt = $this->pdo->prepare($sql);
			
			// Bind SET parameters with prefix
			foreach($data as $key => $value) {
				$stmt->bindValue(":set_{$key}", $value, $this->get_pdo_param_type($value));
			}
			
			// Bind WHERE parameters
			foreach($named_params as $placeholder => $param) {
				$stmt->bindValue($placeholder, $param, $this->get_pdo_param_type($param));
			}

			$result = $stmt->execute();
			$this->logger->log_query($sql, array_merge($data, $where_params), microtime(true) - $start_time);
			
			return $result;
		} catch(PDOException $e) {
			$this->handle_error("Error updating data: " . $e->getMessage());
			return false;
		}
	}

	
	/**
	 * Convert positional placeholders to named ones
	 * 
	 * @param string $where WHERE clause
	 * @param array $params Parameters
	 * @return array [processed_where, named_params]
	 */
	private function convert_positional_to_named(string $where, array $params): array {
		if(empty($params) || !str_contains($where, '?')) {
			return [$where, []];
		}

		$named_params = [];
		$counter = 1;
		$processed_where = preg_replace_callback('/\?/', function() use (&$counter, $params, &$named_params) {
			$placeholder = ":where_{$counter}";
			$named_params[$placeholder] = $params[$counter - 1] ?? null;
			$counter++;
			return $placeholder;
		}, $where);

		return [$processed_where, $named_params];
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

		return $this->transaction(function() use ($data, $table) {
			$start_time = microtime(true);
			
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
			
			$this->logger->log_query($sql, ['batch_count' => count($data)], microtime(true) - $start_time);
			return true;
		});
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
			$result && $this->transaction_stack[] = true;
			return $result;
		}
		
		// Nested transaction via savepoint
		$savepoint_name = 'savepoint_' . count($this->transaction_stack);
		$this->pdo->exec("SAVEPOINT {$savepoint_name}");
		$this->transaction_stack[] = $savepoint_name;
		return true;
	}


	/**
	 * Confirmation of a transaction
	 * 
	 * @return bool
	 */
	public function commit(): bool {
		if(empty($this->transaction_stack) || !$this->pdo->inTransaction()) {
			$this->transaction_stack = [];
			return !empty($this->transaction_stack);
		}

		$last_transaction = array_pop($this->transaction_stack);
		
		return $last_transaction === true 
			? $this->pdo->commit()
			: (bool)$this->pdo->exec("RELEASE SAVEPOINT {$last_transaction}");
	}


	/**
	 * Rollback of a transaction
	 * 
	 * @return bool
	 */
	public function rollback(): bool {
		if(empty($this->transaction_stack) || !$this->pdo->inTransaction()) {
			$this->transaction_stack = [];
			return !empty($this->transaction_stack);
		}

		$last_transaction = array_pop($this->transaction_stack);
		
		return $last_transaction === true 
			? $this->pdo->rollBack()
			: (bool)$this->pdo->exec("ROLLBACK TO SAVEPOINT {$last_transaction}");
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
		// This line will never execute, but helps static analysis
		throw new RuntimeException('Response sent'); // TODO: Maybe it will break the analyzer?
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
		return $this->logger->get_query_stats();
	}

	/**
	 * Get query count (for backward compatibility)
	 *
	 * @return int
	 */
	public function get_query_count(): int {
		return $this->logger->get_query_count();
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
		// PDO connections are closed automatically when the object is destroyed
		// Just mark as disconnected
		$this->is_connected = false;
	}


	/**
	 * Destructor - automatically closes the connection
	 */
	public function __destruct() {
		$this->close();
	}
}
