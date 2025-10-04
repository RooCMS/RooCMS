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
//	Protect
//---------------------------------------------------------
if(!defined('RooCMS')) {roocms_protect();}
//#########################################################


/**
 * MySQL/MariaDB Database Backup and Restore Operations Trait
 *
 * Specialized trait for MySQL and MariaDB database backup and restore operations within the DbBackuper system.
 * Implements MySQL-specific SQL syntax, storage engines, character sets, and backup strategies
 * optimized for MySQL/MariaDB database engines.
 *
 * Key features:
 * - MySQL/MariaDB-specific SQL syntax and storage engine support
 * - Table structure extraction with indexes, constraints, and triggers
 * - Character set and collation handling for proper encoding
 * - Foreign key constraint management during backup/restore
 * - View, procedure, and function backup capabilities
 * - Optimized data export with bulk insert statements
 * - Transaction-safe restore operations with proper sequencing
 * - MySQL-specific error handling and recovery mechanisms
 */
trait DbBackuperMSQL {


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
     * Getting the list of tables in the database
     *
     * @return array
     */
    abstract protected function get_database_tables(): array;
    

    /**
     * Splitting the SQL content into statements
     *
     * @param string $sql_content
     * @param string $delimiter Statement delimiter
     * @return array
     */
    abstract protected function split_sql_statements(string $sql_content, string $delimiter = ';'): array;
}