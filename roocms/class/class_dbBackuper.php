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
 * Database backup and restore system
 * Supports MySQL/MariaDB, PostgreSQL, and Firebird databases
 * Provides secure backup creation, compression, and restoration capabilities
 */
class DbBackuper {

	use DebugLog;

	private Db $db;
	private string $driver = '';
	private string $backup_path = '';
	private array $config = [];
	private array $backup_log = [];
	


	/**
	 * Constructor with dependency injection
	 *
	 * @param Db $db Database connection instance
	 * @param string|null $backup_path Custom backup storage path
	 */
	public function __construct(Db $db, ?string $backup_path = null) {
		global $db_info;

		$this->db = $db;
		$this->config = $db_info;
		$this->driver = strtolower($this->config['type'] ?? 'mysql');
		$this->backup_path = $backup_path ?? _BACKUPS;
		
		$this->ensure_backup_directory();
	}


	/**
	 * Create database backup
	 *
	 * @param array $options Backup options
	 * @return array Backup result information
	 * @throws Exception
	 */
	public function create_backup(array $options = []): array {
		$start_time = microtime(true);
		
		$backup_options = array_merge([
			'compress' => true,
			'include_data' => true,
			'include_structure' => true,
			'exclude_tables' => [],
			'filename' => null,
			'universal_format' => true // Universal SQL format for cross-database compatibility
		], $options);

		try {
			$filename = $this->generate_backup_filename($backup_options);
			$backup_file = $this->backup_path . DIRECTORY_SEPARATOR . $filename;
			
			$this->log_backup_operation('backup_start', $filename);

			// Create backup based on database driver
			$sql_content = $backup_options['universal_format'] 
				? $this->create_universal_backup($backup_options)
				: match($this->driver) {
					'mysql', 'mysqli', 'mariadb' => $this->create_mysql_backup($backup_options),
					'pgsql', 'postgres', 'postgresql' => $this->create_postgres_backup($backup_options),
					'firebird' => $this->create_firebird_backup($backup_options),
					default => throw new Exception('Unsupported database driver for backup: ' . $this->driver)
				};

			// Write backup to file
			$bytes_written = file_put_contents($backup_file, $sql_content);
			
			if($bytes_written === false) {
				throw new Exception('Failed to write backup file: ' . $backup_file);
			}

			// Compress backup if requested
			if($backup_options['compress']) {
				$compressed_file = $this->compress_backup($backup_file);
				if($compressed_file) {
					unlink($backup_file); // Remove uncompressed version
					$backup_file = $compressed_file;
					$filename = basename($compressed_file);
				}
			}

			$execution_time = microtime(true) - $start_time;
			$file_size = filesize($backup_file);

			$result = [
				'success' => true,
				'filename' => $filename,
				'filepath' => $backup_file,
				'size' => $file_size,
				'size_human' => $this->format_bytes($file_size),
				'execution_time' => round($execution_time, 3),
				'timestamp' => date('Y-m-d H:i:s'),
				'compressed' => $backup_options['compress']
			];

			$this->log_backup_operation('backup_complete', $filename, $result);
			return $result;

		} catch(Exception $e) {
			$this->log_backup_operation('backup_error', $filename ?? 'unknown', ['error' => $e->getMessage()]);
			throw new Exception('Backup creation failed: ' . $e->getMessage());
		}
	}


	/**
	 * Restore database from backup
	 *
	 * @param string $backup_filename Backup file name
	 * @param array $options Restore options
	 * @return array Restore result information
	 * @throws Exception
	 */
	public function restore_backup(string $backup_filename, array $options = []): array {
		$start_time = microtime(true);
		
		$restore_options = array_merge([
			'drop_existing' => false,
			'ignore_errors' => false,
			'batch_size' => 1000
		], $options);

		try {
			$backup_file = $this->backup_path . DIRECTORY_SEPARATOR . $backup_filename;
			
			if(!file_exists($backup_file)) {
				throw new Exception('Backup file not found: ' . $backup_filename);
			}

			$this->log_backup_operation('restore_start', $backup_filename);

			// Decompress if needed
			$sql_content = $this->read_backup_file($backup_file);
			
			if(empty($sql_content)) {
				throw new Exception('Backup file is empty or corrupted');
			}

			// Execute restore based on database driver
			$result = match($this->driver) {
				'mysql', 'mysqli', 'mariadb' => $this->restore_mysql_backup($sql_content, $restore_options),
				'pgsql', 'postgres', 'postgresql' => $this->restore_postgres_backup($sql_content, $restore_options),
				'firebird' => $this->restore_firebird_backup($sql_content, $restore_options),
				default => throw new Exception('Unsupported database driver for restore: ' . $this->driver)
			};

			$execution_time = microtime(true) - $start_time;
			
			$final_result = array_merge($result, [
				'filename' => $backup_filename,
				'execution_time' => round($execution_time, 3),
				'timestamp' => date('Y-m-d H:i:s')
			]);

			$this->log_backup_operation('restore_complete', $backup_filename, $final_result);
			return $final_result;

		} catch(Exception $e) {
			$this->log_backup_operation('restore_error', $backup_filename, ['error' => $e->getMessage()]);
			throw new Exception('Backup restoration failed: ' . $e->getMessage());
		}
	}


	/**
	 * List available backups
	 *
	 * @return array List of backup files with information
	 */
	public function list_backups(): array {
		$backups = [];
		$pattern = $this->backup_path . DIRECTORY_SEPARATOR . '*.{sql,sql.gz,sql.zip}';
		$files = glob($pattern, GLOB_BRACE);

		foreach($files as $file) {
			$filename = basename($file);
			$backups[] = [
				'filename' => $filename,
				'filepath' => $file,
				'size' => filesize($file),
				'size_human' => $this->format_bytes(filesize($file)),
				'created' => date('Y-m-d H:i:s', filemtime($file)),
				'compressed' => in_array(pathinfo($file, PATHINFO_EXTENSION), ['gz', 'zip'])
			];
		}

		// Sort by creation date (newest first)
		usort($backups, fn($a, $b) => strcmp($b['created'], $a['created']));

		return $backups;
	}


	/**
	 * Delete backup file
	 *
	 * @param string $backup_filename Backup file name to delete
	 * @return bool Success status
	 */
	public function delete_backup(string $backup_filename): bool {
		$backup_file = $this->backup_path . DIRECTORY_SEPARATOR . $backup_filename;
		
		if(!file_exists($backup_file)) {
			return false;
		}

		$deleted = unlink($backup_file);
		
		if($deleted) {
			$this->log_backup_operation('backup_deleted', $backup_filename);
		}

		return $deleted;
	}


	/**
	 * Get backup operation logs
	 *
	 * @return array Backup operation history
	 */
	public function get_backup_logs(): array {
		return $this->backup_log;
	}


	/**
	 * Create MySQL/MariaDB backup
	 *
	 * @param array $options Backup options
	 * @return string SQL dump content
	 */
	private function create_mysql_backup(array $options): string {
		$sql_dump = '';
		
		// Add header
		$sql_dump .= "-- RooCMS Database Backup\n";
		$sql_dump .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
		$sql_dump .= "-- Database: {$this->config['base']}\n";
		$sql_dump .= "-- Driver: MySQL/MariaDB\n\n";
		
		$sql_dump .= "SET FOREIGN_KEY_CHECKS = 0;\n";
		$sql_dump .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
		$sql_dump .= "SET AUTOCOMMIT = 0;\n";
		$sql_dump .= "START TRANSACTION;\n\n";

		// Get all tables
		$tables = $this->get_database_tables();
		
		foreach($tables as $table) {
			if(in_array($table, $options['exclude_tables'])) {
				continue;
			}

			// Add table structure
			if($options['include_structure']) {
				$sql_dump .= $this->get_mysql_table_structure($table);
			}

			// Add table data
			if($options['include_data']) {
				$sql_dump .= $this->get_mysql_table_data($table);
			}
		}

		$sql_dump .= "COMMIT;\n";
		$sql_dump .= "SET FOREIGN_KEY_CHECKS = 1;\n";

		return $sql_dump;
	}


	/**
	 * Create PostgreSQL backup
	 *
	 * @param array $options Backup options
	 * @return string SQL dump content
	 */
	private function create_postgres_backup(array $options): string {
		$sql_dump = '';
		
		// Add header
		$sql_dump .= "-- RooCMS Database Backup\n";
		$sql_dump .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
		$sql_dump .= "-- Database: {$this->config['base']}\n";
		$sql_dump .= "-- Driver: PostgreSQL\n\n";
		
		$sql_dump .= "BEGIN;\n\n";

		// Get all tables
		$tables = $this->get_database_tables();
		
		foreach($tables as $table) {
			if(in_array($table, $options['exclude_tables'])) {
				continue;
			}

			// Add table structure
			if($options['include_structure']) {
				$sql_dump .= $this->get_postgres_table_structure($table);
			}

			// Add table data
			if($options['include_data']) {
				$sql_dump .= $this->get_postgres_table_data($table);
			}
		}

		$sql_dump .= "COMMIT;\n";

		return $sql_dump;
	}


	/**
	 * Create Firebird backup
	 *
	 * @param array $options Backup options
	 * @return string SQL dump content
	 */
	private function create_firebird_backup(array $options): string {
		$sql_dump = '';
		
		// Add header
		$sql_dump .= "/* RooCMS Database Backup */\n";
		$sql_dump .= "/* Generated on: " . date('Y-m-d H:i:s') . " */\n";
		$sql_dump .= "/* Database: {$this->config['base']} */\n";
		$sql_dump .= "/* Driver: Firebird */\n\n";

		// Get all tables
		$tables = $this->get_database_tables();
		
		foreach($tables as $table) {
			if(in_array($table, $options['exclude_tables'])) {
				continue;
			}

			// Add table structure
			if($options['include_structure']) {
				$sql_dump .= $this->get_firebird_table_structure($table);
			}

			// Add table data
			if($options['include_data']) {
				$sql_dump .= $this->get_firebird_table_data($table);
			}
		}

		return $sql_dump;
	}


	/**
	 * Create universal database backup (compatible with multiple databases)
	 *
	 * @param array $options Backup options
	 * @return string Universal SQL dump content
	 */
	private function create_universal_backup(array $options): string {
		$sql_dump = '';
		
		// Add universal header
		$sql_dump .= "-- RooCMS Universal Database Backup\n";
		$sql_dump .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
		$sql_dump .= "-- Database: {$this->config['base']}\n";
		$sql_dump .= "-- Original Driver: {$this->driver}\n";
		$sql_dump .= "-- Universal format for cross-database compatibility\n\n";
		
		// Universal settings
		$sql_dump .= "-- Disable foreign key checks and other DB-specific settings\n";
		$sql_dump .= "-- SET FOREIGN_KEY_CHECKS = 0; -- MySQL\n";
		$sql_dump .= "-- SET session_replication_role = replica; -- PostgreSQL\n\n";
		
		$sql_dump .= "-- Start transaction\n";
		$sql_dump .= "-- BEGIN; -- Uncomment for transactional restore\n\n";

		// Get all tables
		$tables = $this->get_database_tables();
		
		foreach($tables as $table) {
			if(in_array($table, $options['exclude_tables'])) {
				continue;
			}

			// Add table structure in universal format
			if($options['include_structure']) {
				$sql_dump .= $this->get_universal_table_structure($table);
			}

			// Add table data in universal format
			if($options['include_data']) {
				$sql_dump .= $this->get_universal_table_data($table);
			}
		}

		$sql_dump .= "-- End transaction\n";
		$sql_dump .= "-- COMMIT; -- Uncomment for transactional restore\n\n";
		
		$sql_dump .= "-- Re-enable foreign key checks\n";
		$sql_dump .= "-- SET FOREIGN_KEY_CHECKS = 1; -- MySQL\n";
		$sql_dump .= "-- SET session_replication_role = DEFAULT; -- PostgreSQL\n";

		return $sql_dump;
	}


	/**
	 * Get MySQL table structure with full information (keys, indexes, constraints)
	 *
	 * @param string $table Table name
	 * @return string CREATE TABLE statement with all constraints
	 */
	private function get_mysql_table_structure(string $table): string {
		$result = $this->db->fetch_assoc("SHOW CREATE TABLE `{$table}`");
		
		$sql = "\n-- Table structure for `{$table}`\n";
		$sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
		$sql .= $result['Create Table'] . ";\n\n";
		
		return $sql;
	}


	/**
	 * Get MySQL table data
	 *
	 * @param string $table Table name
	 * @return string INSERT statements
	 */
	private function get_mysql_table_data(string $table): string {
		$sql = "-- Data for table `{$table}`\n";
		
		$rows = $this->db->fetch_all("SELECT * FROM `{$table}`");
		
		if(empty($rows)) {
			return $sql . "\n";
		}

		$columns = array_keys($rows[0]);
		$sql .= "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES\n";
		
		$values = [];
		foreach($rows as $row) {
			$row_values = [];
			foreach($row as $value) {
				if($value === null) {
					$row_values[] = 'NULL';
				} elseif(is_numeric($value)) {
					$row_values[] = $value;
				} else {
					$row_values[] = "'" . addslashes($value) . "'";
				}
			}
			$values[] = '(' . implode(', ', $row_values) . ')';
		}
		
		$sql .= implode(",\n", $values) . ";\n\n";
		
		return $sql;
	}


	/**
	 * Get PostgreSQL table structure
	 *
	 * @param string $table Table name
	 * @return string CREATE TABLE statement
	 */
	private function get_postgres_table_structure(string $table): string {
		// This is a simplified version - in production you might want to use pg_dump
		$sql = "\n-- Table structure for \"{$table}\"\n";
		$sql .= "DROP TABLE IF EXISTS \"{$table}\" CASCADE;\n";
		
		$columns = $this->db->fetch_all("
			SELECT column_name, data_type, is_nullable, column_default 
			FROM information_schema.columns 
			WHERE table_name = ? 
			ORDER BY ordinal_position
		", [$table]);
		
		$sql .= "CREATE TABLE \"{$table}\" (\n";
		$column_definitions = [];
		
		foreach($columns as $column) {
			$definition = "\"{$column['column_name']}\" {$column['data_type']}";
			if($column['is_nullable'] === 'NO') {
				$definition .= ' NOT NULL';
			}
			if($column['column_default']) {
				$definition .= " DEFAULT {$column['column_default']}";
			}
			$column_definitions[] = $definition;
		}
		
		$sql .= implode(",\n", $column_definitions) . "\n);\n\n";
		
		return $sql;
	}


	/**
	 * Get PostgreSQL table data
	 *
	 * @param string $table Table name
	 * @return string INSERT statements
	 */
	private function get_postgres_table_data(string $table): string {
		$sql = "-- Data for table \"{$table}\"\n";
		
		$rows = $this->db->fetch_all("SELECT * FROM \"{$table}\"");
		
		if(empty($rows)) {
			return $sql . "\n";
		}

		$columns = array_keys($rows[0]);
		$sql .= "INSERT INTO \"{$table}\" (\"" . implode('", "', $columns) . "\") VALUES\n";
		
		$values = [];
		foreach($rows as $row) {
			$row_values = [];
			foreach($row as $value) {
				if($value === null) {
					$row_values[] = 'NULL';
				} elseif(is_numeric($value)) {
					$row_values[] = $value;
				} else {
					$row_values[] = "'" . str_replace("'", "''", $value) . "'";
				}
			}
			$values[] = '(' . implode(', ', $row_values) . ')';
		}
		
		$sql .= implode(",\n", $values) . ";\n\n";
		
		return $sql;
	}


	/**
	 * Get Firebird table structure
	 *
	 * @param string $table Table name
	 * @return string CREATE TABLE statement
	 */
	private function get_firebird_table_structure(string $table): string {
		$sql = "\n/* Table structure for {$table} */\n";
		
		$columns = $this->db->fetch_all("
			SELECT 
				TRIM(rf.rdb\$field_name) as field_name,
				TRIM(ft.rdb\$type_name) as field_type,
				rf.rdb\$null_flag as is_nullable,
				TRIM(rf.rdb\$default_source) as field_default
			FROM rdb\$relation_fields rf
			JOIN rdb\$fields f ON f.rdb\$field_name = rf.rdb\$field_source
			JOIN rdb\$types ft ON ft.rdb\$type = f.rdb\$field_type
			WHERE rf.rdb\$relation_name = UPPER(?)
			ORDER BY rf.rdb\$field_position
		", [$table]);
		
		$sql .= "CREATE TABLE {$table} (\n";
		$column_definitions = [];
		
		foreach($columns as $column) {
			$definition = "{$column['field_name']} {$column['field_type']}";
			if($column['is_nullable'] === 1) {
				$definition .= ' NOT NULL';
			}
			if($column['field_default']) {
				$definition .= " DEFAULT {$column['field_default']}";
			}
			$column_definitions[] = $definition;
		}
		
		$sql .= implode(",\n", $column_definitions) . "\n);\n\n";
		
		return $sql;
	}


	/**
	 * Get Firebird table data
	 *
	 * @param string $table Table name
	 * @return string INSERT statements
	 */
	private function get_firebird_table_data(string $table): string {
		$sql = "/* Data for table {$table} */\n";
		
		$rows = $this->db->fetch_all("SELECT * FROM {$table}");
		
		if(empty($rows)) {
			return $sql . "\n";
		}

		foreach($rows as $row) {
			$columns = array_keys($row);
			$values = [];
			
			foreach($row as $value) {
				if($value === null) {
					$values[] = 'NULL';
				} elseif(is_numeric($value)) {
					$values[] = $value;
				} else {
					$values[] = "'" . str_replace("'", "''", $value) . "'";
				}
			}
			
			$sql .= "INSERT INTO {$table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
		}
		
		$sql .= "\n";
		
		return $sql;
	}


	/**
	 * Restore MySQL backup
	 *
	 * @param string $sql_content SQL dump content
	 * @param array $options Restore options
	 * @return array Restore result
	 */
	private function restore_mysql_backup(string $sql_content, array $options): array {
		$statements = $this->split_sql_statements($sql_content);
		$executed = 0;
		$errors = [];

		$this->db->begin_transaction();

		try {
			foreach($statements as $statement) {
				$statement = trim($statement);
				if(empty($statement) || str_starts_with($statement, '--')) {
					continue;
				}

				try {
					$this->db->query($statement);
					$executed++;
				} catch(Exception $e) {
					if(!$options['ignore_errors']) {
						throw $e;
					}
					$errors[] = $e->getMessage();
				}
			}

			$this->db->commit();

			return [
				'success' => true,
				'statements_executed' => $executed,
				'errors' => $errors
			];

		} catch(Exception $e) {
			$this->db->rollback();
			throw $e;
		}
	}


	/**
	 * Restore PostgreSQL backup
	 *
	 * @param string $sql_content SQL dump content
	 * @param array $options Restore options
	 * @return array Restore result
	 */
	private function restore_postgres_backup(string $sql_content, array $options): array {
		return $this->restore_mysql_backup($sql_content, $options); // Similar logic
	}


	/**
	 * Restore Firebird backup
	 *
	 * @param string $sql_content SQL dump content
	 * @param array $options Restore options
	 * @return array Restore result
	 */
	private function restore_firebird_backup(string $sql_content, array $options): array {
		$statements = $this->split_sql_statements($sql_content, ';');
		$executed = 0;
		$errors = [];

		try {
			foreach($statements as $statement) {
				$statement = trim($statement);
				if(empty($statement) || str_starts_with($statement, '/*')) {
					continue;
				}

				try {
					$this->db->query($statement);
					$executed++;
				} catch(Exception $e) {
					if(!$options['ignore_errors']) {
						throw $e;
					}
					$errors[] = $e->getMessage();
				}
			}

			return [
				'success' => true,
				'statements_executed' => $executed,
				'errors' => $errors
			];

		} catch(Exception $e) {
			throw $e;
		}
	}


	/**
	 * Get universal table structure with full constraints (compatible with multiple databases)
	 *
	 * @param string $table Table name
	 * @return string Universal CREATE TABLE statement with constraints
	 */
	private function get_universal_table_structure(string $table): string {
		$sql = "\n-- Table structure for {$table} (Universal format)\n";
		$sql .= "-- DROP TABLE IF EXISTS {$table}; -- Uncomment if needed\n";
		
		// Get column information using driver-specific queries
		$columns = match($this->driver) {
			'mysql', 'mysqli', 'mariadb' => $this->get_mysql_column_info($table),
			'pgsql', 'postgres', 'postgresql' => $this->get_postgres_column_info($table),
			'firebird' => $this->get_firebird_column_info($table),
			default => []
		};
		
		if(empty($columns)) {
			return $sql . "-- Unable to get column information for {$table}\n\n";
		}
		
		// Get indexes and constraints
		$indexes = match($this->driver) {
			'mysql', 'mysqli', 'mariadb' => $this->get_mysql_indexes($table),
			'pgsql', 'postgres', 'postgresql' => $this->get_postgres_indexes($table),
			'firebird' => $this->get_firebird_indexes($table),
			default => []
		};
		
		$sql .= "CREATE TABLE {$table} (\n";
		$column_definitions = [];
		$constraints = [];
		
		// Process columns
		foreach($columns as $column) {
			$definition = "  {$column['name']} {$this->map_to_universal_type($column['type'])}";
			
			// Add AUTO_INCREMENT if applicable
			if(isset($column['extra']) && str_contains(strtolower($column['extra']), 'auto_increment')) {
				$definition .= ' AUTO_INCREMENT';
			}
			
			if($column['nullable'] === false) {
				$definition .= ' NOT NULL';
			}
			
			if(!empty($column['default']) && $column['default'] !== 'NULL') {
				$default = $column['default'];
				// Handle special defaults
				if(str_contains(strtolower($default), 'current_timestamp')) {
					$definition .= " DEFAULT current_timestamp()";
				} elseif(is_string($default) && !is_numeric($default)) {
					$definition .= " DEFAULT '{$default}'";
				} else {
					$definition .= " DEFAULT {$default}";
				}
			}
			
			$column_definitions[] = $definition;
		}
		
		// Process indexes and constraints
		foreach($indexes as $index) {
			if($index['type'] === 'PRIMARY') {
				$constraints[] = "  PRIMARY KEY ({$index['columns']})";
			} elseif($index['type'] === 'UNIQUE') {
				$constraints[] = "  UNIQUE KEY {$index['name']} ({$index['columns']})";
			} elseif($index['type'] === 'INDEX') {
				$constraints[] = "  INDEX {$index['name']} ({$index['columns']})";
			} elseif($index['type'] === 'FOREIGN') {
				$constraints[] = "  FOREIGN KEY {$index['name']} ({$index['columns']}) REFERENCES {$index['ref_table']}({$index['ref_columns']})";
				if(!empty($index['on_delete'])) {
					$constraints[count($constraints)-1] .= " ON DELETE {$index['on_delete']}";
				}
				if(!empty($index['on_update'])) {
					$constraints[count($constraints)-1] .= " ON UPDATE {$index['on_update']}";
				}
			}
		}
		
		// Combine columns and constraints
		$all_definitions = array_merge($column_definitions, $constraints);
		$sql .= implode(",\n", $all_definitions) . "\n);\n\n";
		
		return $sql;
	}


	/**
	 * Get universal table data (compatible with multiple databases)
	 *
	 * @param string $table Table name
	 * @return string Universal INSERT statements
	 */
	private function get_universal_table_data(string $table): string {
		$sql = "-- Data for table {$table} (Universal format)\n";
		
		// Use driver-agnostic query
		$rows = $this->db->fetch_all("SELECT * FROM {$table}");
		
		if(empty($rows)) {
			return $sql . "\n";
		}

		$columns = array_keys($rows[0]);
		$sql .= "INSERT INTO {$table} (" . implode(', ', $columns) . ") VALUES\n";
		
		$values = [];
		foreach($rows as $row) {
			$row_values = [];
			foreach($row as $value) {
				if($value === null) {
					$row_values[] = 'NULL';
				} elseif(is_numeric($value)) {
					$row_values[] = $value;
				} else {
					// Universal string escaping
					$escaped = str_replace(["'", '\\'], ["''", '\\\\'], $value);
					$row_values[] = "'" . $escaped . "'";
				}
			}
			$values[] = '(' . implode(', ', $row_values) . ')';
		}
		
		$sql .= implode(",\n", $values) . ";\n\n";
		
		return $sql;
	}


	/**
	 * Map database-specific types to universal types
	 *
	 * @param string $type Original database type
	 * @return string Universal type
	 */
	private function map_to_universal_type(string $type): string {
		$type = strtolower($type);
		
		// Map to universal SQL types
		return match(true) {
			str_contains($type, 'int') => 'INTEGER',
			str_contains($type, 'varchar') || str_contains($type, 'char') => 
				preg_match('/\((\d+)\)/', $type, $matches) ? "VARCHAR({$matches[1]})" : 'VARCHAR(255)',
			str_contains($type, 'text') => 'TEXT',
			str_contains($type, 'decimal') || str_contains($type, 'numeric') => 'DECIMAL',
			str_contains($type, 'float') || str_contains($type, 'double') => 'FLOAT',
			str_contains($type, 'date') => 'DATE',
			str_contains($type, 'time') => str_contains($type, 'datetime') ? 'DATETIME' : 'TIME',
			str_contains($type, 'bool') => 'BOOLEAN',
			default => strtoupper($type)
		};
	}


	/**
	 * Get MySQL column information with detailed attributes
	 *
	 * @param string $table Table name
	 * @return array Column information with all attributes
	 */
	private function get_mysql_column_info(string $table): array {
		$columns = $this->db->fetch_all("SHOW COLUMNS FROM `{$table}`");
		$result = [];
		
		foreach($columns as $column) {
			$result[] = [
				'name' => $column['Field'],
				'type' => $column['Type'],
				'nullable' => $column['Null'] === 'YES',
				'default' => $column['Default'],
				'extra' => $column['Extra'] ?? '' // Contains AUTO_INCREMENT and other extras
			];
		}
		
		return $result;
	}


	/**
	 * Get PostgreSQL column information
	 *
	 * @param string $table Table name
	 * @return array Column information
	 */
	private function get_postgres_column_info(string $table): array {
		$columns = $this->db->fetch_all("
			SELECT column_name, data_type, is_nullable, column_default 
			FROM information_schema.columns 
			WHERE table_name = ? 
			ORDER BY ordinal_position
		", [$table]);
		
		$result = [];
		foreach($columns as $column) {
			$result[] = [
				'name' => $column['column_name'],
				'type' => $column['data_type'],
				'nullable' => $column['is_nullable'] === 'YES',
				'default' => $column['column_default']
			];
		}
		
		return $result;
	}


	/**
	 * Get Firebird column information
	 *
	 * @param string $table Table name
	 * @return array Column information
	 */
	private function get_firebird_column_info(string $table): array {
		// TODO: Implement Firebird column information extraction
		// This would require detailed Firebird system table queries
		return [];
	}


	/**
	 * Get MySQL indexes and constraints information
	 *
	 * @param string $table Table name
	 * @return array Indexes and constraints information
	 */
	private function get_mysql_indexes(string $table): array {
		$indexes = [];
		
		// Get all indexes for the table
		$index_results = $this->db->fetch_all("SHOW INDEX FROM `{$table}`");
		$grouped_indexes = [];
		
		// Group indexes by name
		foreach($index_results as $row) {
			$key_name = $row['Key_name'];
			if(!isset($grouped_indexes[$key_name])) {
				$grouped_indexes[$key_name] = [
					'name' => $key_name,
					'unique' => $row['Non_unique'] == 0,
					'columns' => [],
					'type' => $key_name === 'PRIMARY' ? 'PRIMARY' : ($row['Non_unique'] == 0 ? 'UNIQUE' : 'INDEX')
				];
			}
			$grouped_indexes[$key_name]['columns'][] = $row['Column_name'];
		}
		
		// Convert to final format
		foreach($grouped_indexes as $index) {
			$indexes[] = [
				'name' => $index['name'],
				'type' => $index['type'],
				'columns' => implode(', ', $index['columns'])
			];
		}
		
		// Get foreign key constraints
		$fk_query = "
			SELECT 
				kcu.CONSTRAINT_NAME,
				kcu.COLUMN_NAME,
				kcu.REFERENCED_TABLE_NAME,
				kcu.REFERENCED_COLUMN_NAME,
				rc.DELETE_RULE,
				rc.UPDATE_RULE
			FROM information_schema.KEY_COLUMN_USAGE kcu
			JOIN information_schema.REFERENTIAL_CONSTRAINTS rc 
				ON kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME 
				AND kcu.CONSTRAINT_SCHEMA = rc.CONSTRAINT_SCHEMA
			WHERE kcu.TABLE_NAME = ? 
				AND kcu.TABLE_SCHEMA = DATABASE()
				AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
			ORDER BY kcu.CONSTRAINT_NAME, kcu.ORDINAL_POSITION
		";
		
		try {
			$fk_results = $this->db->fetch_all($fk_query, [$table]);
			$grouped_fks = [];
			
			// Group foreign keys by constraint name
			foreach($fk_results as $row) {
				$constraint_name = $row['CONSTRAINT_NAME'];
				if(!isset($grouped_fks[$constraint_name])) {
					$grouped_fks[$constraint_name] = [
						'name' => $constraint_name,
						'columns' => [],
						'ref_table' => $row['REFERENCED_TABLE_NAME'],
						'ref_columns' => [],
						'on_delete' => $row['DELETE_RULE'],
						'on_update' => $row['UPDATE_RULE']
					];
				}
				$grouped_fks[$constraint_name]['columns'][] = $row['COLUMN_NAME'];
				$grouped_fks[$constraint_name]['ref_columns'][] = $row['REFERENCED_COLUMN_NAME'];
			}
			
			// Add foreign keys to indexes array
			foreach($grouped_fks as $fk) {
				$indexes[] = [
					'name' => $fk['name'],
					'type' => 'FOREIGN',
					'columns' => implode(', ', $fk['columns']),
					'ref_table' => $fk['ref_table'],
					'ref_columns' => implode(', ', $fk['ref_columns']),
					'on_delete' => $fk['on_delete'],
					'on_update' => $fk['on_update']
				];
			}
		} catch(Exception $e) {
			// If we can't get foreign keys, continue without them
		}
		
		return $indexes;
	}

	/**
	 * Get PostgreSQL indexes information
	 *
	 * @param string $table Table name
	 * @return array Indexes information
	 */
	private function get_postgres_indexes(string $table): array {
		// TODO: Implement PostgreSQL indexes extraction
		// Query: SELECT * FROM pg_indexes WHERE tablename = ?
		return [];
	}

	/**
	 * Get Firebird indexes information
	 *
	 * @param string $table Table name
	 * @return array Indexes information
	 */
	private function get_firebird_indexes(string $table): array {
		// TODO: Implement Firebird indexes extraction
		// Query: SELECT * FROM rdb$indices WHERE rdb$relation_name = UPPER(?)
		return [];
	}


	/**
	 * Get database tables list
	 *
	 * @return array List of table names
	 */
	private function get_database_tables(): array {
		$tables = match($this->driver) {
			'mysql', 'mysqli', 'mariadb' => (
				function() {
					$result = $this->db->fetch_all('SHOW TABLES');
					// MySQL returns results with column name like 'Tables_in_database_name'
					$table_names = [];
					foreach($result as $row) {
						$table_names[] = array_values($row)[0];
					}
					return $table_names;
				}
			)(),
			'pgsql', 'postgres', 'postgresql' => array_column($this->db->fetch_all("
				SELECT tablename FROM pg_tables WHERE schemaname = 'public'
			"), 'tablename'),
			'firebird' => array_column($this->db->fetch_all("
				SELECT TRIM(rdb\$relation_name) as table_name 
				FROM rdb\$relations 
				WHERE rdb\$relation_type = 0 AND rdb\$system_flag = 0
			"), 'table_name'),
			default => []
		};
		
		return $tables;
	}


	/**
	 * Split SQL content into individual statements
	 *
	 * @param string $sql_content SQL content
	 * @param string $delimiter Statement delimiter
	 * @return array Array of SQL statements
	 */
	private function split_sql_statements(string $sql_content, string $delimiter = ';'): array {
		$statements = [];
		$current_statement = '';
		$in_string = false;
		$string_char = null;
		
		for($i = 0; $i < strlen($sql_content); $i++) {
			$char = $sql_content[$i];
			
			if(!$in_string && ($char === '"' || $char === "'")) {
				$in_string = true;
				$string_char = $char;
			} elseif($in_string && $char === $string_char) {
				// Check for escaped quotes
				if($i > 0 && $sql_content[$i-1] !== '\\') {
					$in_string = false;
					$string_char = null;
				}
			}
			
			if(!$in_string && $char === $delimiter) {
				$statements[] = trim($current_statement);
				$current_statement = '';
				continue;
			}
			
			$current_statement .= $char;
		}
		
		// Add the last statement if not empty
		if(!empty(trim($current_statement))) {
			$statements[] = trim($current_statement);
		}
		
		return array_filter($statements);
	}


	/**
	 * Compress backup file
	 *
	 * @param string $backup_file Path to backup file
	 * @return string|null Path to compressed file or null on failure
	 */
	private function compress_backup(string $backup_file): ?string {
		if(function_exists('gzencode')) {
			$content = file_read($backup_file);
			$compressed_content = gzencode($content, 9);
			$compressed_file = $backup_file . '.gz';
			
			if(file_put_contents($compressed_file, $compressed_content) !== false) {
				return $compressed_file;
			}
		}
		
		return null;
	}


	/**
	 * Read backup file (with decompression if needed)
	 *
	 * @param string $backup_file Path to backup file
	 * @return string File content
	 */
	private function read_backup_file(string $backup_file): string {
		$extension = pathinfo($backup_file, PATHINFO_EXTENSION);
		
		if($extension === 'gz' && function_exists('gzdecode')) {
			$compressed_content = file_read($backup_file);
			return gzdecode($compressed_content);
		}
		
		return file_read($backup_file);
	}


	/**
	 * Generate backup filename
	 *
	 * @param array $options Backup options
	 * @return string Generated filename
	 */
	private function generate_backup_filename(array $options): string {
		$filename = $options['filename'] ?? '';
		
		if(empty($filename)) {
			// Use current date and time as default filename
			$filename = 'backup_' . date('Y-m-d_H-i-s');
		}
		
		$filename .= '.sql';
		
		return $filename;
	}


	/**
	 * Log backup operations
	 *
	 * @param string $operation Operation type
	 * @param string $filename Backup filename
	 * @param array $data Additional data
	 * @return void
	 */
	private function log_backup_operation(string $operation, string $filename, array $data = []): void {
		$log_entry = [
			'operation' => $operation,
			'filename' => $filename,
			'timestamp' => date('Y-m-d H:i:s'),
			'data' => $data
		];
		
		$this->backup_log[] = $log_entry;
	}


	/**
	 * Ensure backup directory exists and is properly secured
	 *
	 * @return void
	 */
	private function ensure_backup_directory(): void {
		if(!is_dir($this->backup_path)) {
			if(!mkdir($this->backup_path, 0755, true)) {
				throw new Exception('Cannot create backup directory: ' . $this->backup_path);
			}
		}
		
		// Create index.php for security
		$index_file = $this->backup_path . DIRECTORY_SEPARATOR . 'index.php';
		if(!file_exists($index_file)) {
			file_put_contents($index_file, '<?php http_response_code(403); exit(\'403:Access denied\'); ?>');
		}
		
		// Create .htaccess for additional security
		$htaccess_file = $this->backup_path . DIRECTORY_SEPARATOR . '.htaccess';
		if(!file_exists($htaccess_file)) {
			$htaccess_content = $this->generate_htaccess_content();
			file_put_contents($htaccess_file, $htaccess_content);
		}
	}


	/**
	 * Generate .htaccess content for backup directory security
	 *
	 * @return string .htaccess content
	 */
	private function generate_htaccess_content(): string {
		$domain = env('HTTP_HOST') ?? 'localhost';
		
		return <<<HTACCESS
# RooCMS Database Backups Security
# Deny all direct access to backup files

# Deny access to all files in this directory
<Files "*">
    Order Allow,Deny
    Deny from all
</Files>

# Specifically deny backup file extensions
<FilesMatch "\.(sql|sql\.gz|sql\.bz2|dump|backup)$">
    Order Allow,Deny 
    Deny from all
</FilesMatch>

# Deny directory browsing
Options -Indexes

# Prevent access to hidden files (like .htaccess itself)
<Files ".*">
    Order Allow,Deny
    Deny from all
</Files>

# Custom error page for unauthorized access
ErrorDocument 403 /err.php

# Additional security headers
<IfModule mod_headers.c>
    Header always set X-Robots-Tag "noindex, nofollow, nosnippet, noarchive"
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
</IfModule>

# Disable execution of any scripts
<IfModule mod_php8.c>
    php_flag engine off
</IfModule>

# Prevent hotlinking
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTP_REFERER} !^$
    RewriteCond %{HTTP_REFERER} !^https?://(www\.)?{$domain} [NC]
    RewriteRule \.(sql|sql\.gz|sql\.bz2|dump|backup)$ - [F,L]
</IfModule>
HTACCESS;
	}


	/**
	 * Format bytes to human readable format
	 *
	 * @param int $bytes Number of bytes
	 * @return string Formatted string
	 */
	public function format_bytes(int $bytes): string {
		$units = ['B', 'KB', 'MB', 'GB', 'TB'];
		$factor = floor((strlen($bytes) - 1) / 3);
		
		return sprintf('%.2f %s', $bytes / (1024 ** $factor), $units[$factor]);
	}
}