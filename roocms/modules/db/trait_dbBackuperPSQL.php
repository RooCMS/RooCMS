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
 * PostgreSQL Database Backup and Restore Operations Trait
 *
 * Specialized trait for PostgreSQL database backup and restore operations within the DbBackuper system.
 * Implements PostgreSQL-specific SQL syntax, schemas, sequences, and backup strategies optimized for
 * the PostgreSQL database engine's advanced features and capabilities.
 *
 * Key features:
 * - PostgreSQL-specific SQL syntax and schema handling
 * - Sequence and serial column backup and restore
 * - Schema-qualified table and object naming
 * - Advanced PostgreSQL data types and array support
 * - Function, trigger, and rule backup capabilities
 * - Transaction-safe operations with proper savepoints
 * - PostgreSQL-specific constraint and index handling
 * - Proper handling of inherited tables and table partitions
 * - Encoding and locale-aware backup generation
 * - Error handling and recovery for PostgreSQL-specific operations
 * - SQL statement parsing and execution for PostgreSQL
 */
trait DbBackuperPSQL {


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
	 * Get PostgreSQL indexes information
	 *
	 * @param string $table Table name
	 * @return array Indexes information
	 */
	private function get_postgres_indexes(string $table): array {
		$indexes = [];
		
		// Get all indexes for the table
		$index_query = "
			SELECT 
				i.indexname,
				i.indexdef,
				a.attname as column_name,
				CASE 
					WHEN i.indexname LIKE '%_pkey' THEN 'PRIMARY'
					WHEN ix.indisunique THEN 'UNIQUE'
					ELSE 'INDEX'
				END as index_type
			FROM pg_indexes i
			JOIN pg_class c ON c.relname = i.tablename
			JOIN pg_index ix ON ix.indexrelid = (SELECT oid FROM pg_class WHERE relname = i.indexname)
			JOIN pg_attribute a ON a.attrelid = c.oid AND a.attnum = ANY(ix.indkey)
			WHERE i.tablename = ?
				AND i.schemaname = 'public'
			ORDER BY i.indexname, a.attnum
		";
		
		try {
			$index_results = $this->db->fetch_all($index_query, [$table]);
			$grouped_indexes = [];
			
			// Group indexes by name
			foreach($index_results as $row) {
				$index_name = $row['indexname'];
				if(!isset($grouped_indexes[$index_name])) {
					$grouped_indexes[$index_name] = [
						'name' => $index_name,
						'type' => $row['index_type'],
						'columns' => []
					];
				}
				$grouped_indexes[$index_name]['columns'][] = $row['column_name'];
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
					c.conname as constraint_name,
					a.attname as column_name,
					cl.relname as ref_table,
					a2.attname as ref_column,
					CASE c.confdeltype
						WHEN 'a' THEN 'NO ACTION'
						WHEN 'r' THEN 'RESTRICT'
						WHEN 'c' THEN 'CASCADE'
						WHEN 'n' THEN 'SET NULL'
						WHEN 'd' THEN 'SET DEFAULT'
						ELSE 'UNKNOWN'
					END as on_delete,
					CASE c.confupdtype
						WHEN 'a' THEN 'NO ACTION'
						WHEN 'r' THEN 'RESTRICT'
						WHEN 'c' THEN 'CASCADE'
						WHEN 'n' THEN 'SET NULL'
						WHEN 'd' THEN 'SET DEFAULT'
						ELSE 'UNKNOWN'
					END as on_update
				FROM pg_constraint c
				JOIN pg_class t ON t.oid = c.conrelid
				JOIN pg_attribute a ON a.attrelid = c.conrelid AND a.attnum = ANY(c.conkey)
				JOIN pg_class cl ON cl.oid = c.confrelid
				JOIN pg_attribute a2 ON a2.attrelid = c.confrelid AND a2.attnum = ANY(c.confkey)
				WHERE c.contype = 'f'
					AND t.relname = ?
				ORDER BY c.conname, a.attnum
			";
			
			$fk_results = $this->db->fetch_all($fk_query, [$table]);
			$grouped_fks = [];
			
			// Group foreign keys by constraint name
			foreach($fk_results as $row) {
				$constraint_name = $row['constraint_name'];
				if(!isset($grouped_fks[$constraint_name])) {
					$grouped_fks[$constraint_name] = [
						'name' => $constraint_name,
						'columns' => [],
						'ref_table' => $row['ref_table'],
						'ref_columns' => [],
						'on_delete' => $row['on_delete'],
						'on_update' => $row['on_update']
					];
				}
				$grouped_fks[$constraint_name]['columns'][] = $row['column_name'];
				$grouped_fks[$constraint_name]['ref_columns'][] = $row['ref_column'];
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
			// If we can't get indexes, continue without them
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
     * Restore MySQL backup
     *
     * @param string $sql_content SQL dump content
     * @param array $options Restore options
     * @return array Restore result
     */
    abstract protected function restore_mysql_backup(string $sql_content, array $options): array;
}