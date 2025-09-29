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
 * Trait for Firebird backup and restore operations
 */
trait DbBackuperFB {


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
	 * Get Firebird column information
	 *
	 * @param string $table Table name
	 * @return array Column information
	 */
	private function get_firebird_column_info(string $table): array {
		$columns_query = "
			SELECT 
				TRIM(rf.rdb\$field_name) as field_name,
				CASE f.rdb\$field_type
					WHEN 261 THEN 'BLOB'
					WHEN 14 THEN 'CHAR('
					WHEN 40 THEN 'CSTRING('
					WHEN 11 THEN 'D_FLOAT'
					WHEN 27 THEN 'DOUBLE PRECISION'
					WHEN 10 THEN 'FLOAT'
					WHEN 16 THEN 'BIGINT'
					WHEN 8 THEN 'INTEGER'
					WHEN 9 THEN 'QUAD'
					WHEN 7 THEN 'SMALLINT'
					WHEN 12 THEN 'DATE'
					WHEN 13 THEN 'TIME'
					WHEN 35 THEN 'TIMESTAMP'
					WHEN 37 THEN 'VARCHAR('
					WHEN 23 THEN 'BOOLEAN'
					ELSE 'UNKNOWN'
				END ||
				CASE f.rdb\$field_type
					WHEN 14 THEN f.rdb\$field_length || ')'
					WHEN 37 THEN f.rdb\$field_length || ')'
					WHEN 40 THEN f.rdb\$field_length || ')'
					ELSE ''
				END as field_type,
				f.rdb\$field_length,
				f.rdb\$field_scale,
				rf.rdb\$null_flag as is_nullable,
				COALESCE(TRIM(rf.rdb\$default_source), '') as field_default,
				rf.rdb\$field_position
			FROM rdb\$relation_fields rf
			JOIN rdb\$fields f ON f.rdb\$field_name = rf.rdb\$field_source
			WHERE rf.rdb\$relation_name = UPPER(?)
			ORDER BY rf.rdb\$field_position
		";
		
		try {
			$columns = $this->db->fetch_all($columns_query, [$table]);
			$result = [];
			
			foreach($columns as $column) {
				// Clean up the field type
				$field_type = trim($column['field_type']);
				
				// Handle numeric types with precision
				if($column['field_scale'] !== null && $column['field_scale'] < 0) {
					$precision = $column['field_length'];
					$scale = abs($column['field_scale']);
					
					if(str_contains($field_type, 'INTEGER') || str_contains($field_type, 'BIGINT')) {
						$field_type = "DECIMAL({$precision},{$scale})";
					}
				}
				
				// Parse default value
				$default_value = trim($column['field_default']);
				if(!empty($default_value)) {
					// Remove DEFAULT keyword if present
					$default_value = preg_replace('/^DEFAULT\s+/i', '', $default_value);
				}
				
				$result[] = [
					'name' => trim($column['field_name']),
					'type' => $field_type,
					'nullable' => $column['is_nullable'] !== 1,
					'default' => !empty($default_value) ? $default_value : null
				];
			}
			
			return $result;
			
		} catch(Exception $e) {
			// If we can't get column information, return empty array
			return [];
		}
	}


	/**
	 * Get Firebird indexes information
	 *
	 * @param string $table Table name
	 * @return array Indexes information
	 */
	private function get_firebird_indexes(string $table): array {
		$indexes = [];
		
		// Get all indexes for the table
		$index_query = "
			SELECT 
				TRIM(i.rdb\$index_name) as index_name,
				TRIM(s.rdb\$field_name) as column_name,
				i.rdb\$unique_flag as is_unique,
				CASE 
					WHEN rc.rdb\$constraint_type = 'PRIMARY KEY' THEN 'PRIMARY'
					WHEN i.rdb\$unique_flag = 1 THEN 'UNIQUE'
					ELSE 'INDEX'
				END as index_type,
				s.rdb\$field_position
			FROM rdb\$indices i
			JOIN rdb\$index_segments s ON s.rdb\$index_name = i.rdb\$index_name
			LEFT JOIN rdb\$relation_constraints rc ON rc.rdb\$index_name = i.rdb\$index_name
			WHERE i.rdb\$relation_name = UPPER(?)
				AND i.rdb\$system_flag = 0
			ORDER BY i.rdb\$index_name, s.rdb\$field_position
		";
		
		try {
			$index_results = $this->db->fetch_all($index_query, [$table]);
			$grouped_indexes = [];
			
			// Group indexes by name
			foreach($index_results as $row) {
				$index_name = trim($row['index_name']);
				if(!isset($grouped_indexes[$index_name])) {
					$grouped_indexes[$index_name] = [
						'name' => $index_name,
						'type' => $row['index_type'],
						'columns' => []
					];
				}
				$grouped_indexes[$index_name]['columns'][] = trim($row['column_name']);
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
				SELECT DISTINCT
					TRIM(rc.rdb\$constraint_name) as constraint_name,
					TRIM(d1.rdb\$field_name) as column_name,
					TRIM(d2.rdb\$depended_on_name) as ref_table,
					TRIM(d3.rdb\$field_name) as ref_column,
					CASE TRIM(rc.rdb\$delete_rule)
						WHEN 'CASCADE' THEN 'CASCADE'
						WHEN 'SET NULL' THEN 'SET NULL'
						WHEN 'SET DEFAULT' THEN 'SET DEFAULT'
						ELSE 'RESTRICT'
					END as on_delete,
					CASE TRIM(rc.rdb\$update_rule)
						WHEN 'CASCADE' THEN 'CASCADE'
						WHEN 'SET NULL' THEN 'SET NULL'
						WHEN 'SET DEFAULT' THEN 'SET DEFAULT'
						ELSE 'RESTRICT'
					END as on_update
				FROM rdb\$relation_constraints rc
				JOIN rdb\$dependencies d1 ON d1.rdb\$dependent_name = rc.rdb\$constraint_name
				JOIN rdb\$dependencies d2 ON d2.rdb\$dependent_name = rc.rdb\$constraint_name
				JOIN rdb\$dependencies d3 ON d3.rdb\$dependent_name = rc.rdb\$constraint_name
				WHERE rc.rdb\$constraint_type = 'FOREIGN KEY'
					AND rc.rdb\$relation_name = UPPER(?)
					AND d1.rdb\$dependent_type = 3
					AND d2.rdb\$dependent_type = 0
					AND d3.rdb\$dependent_type = 3
				ORDER BY rc.rdb\$constraint_name
			";
			
			$fk_results = $this->db->fetch_all($fk_query, [$table]);
			$grouped_fks = [];
			
			// Group foreign keys by constraint name
			foreach($fk_results as $row) {
				$constraint_name = trim($row['constraint_name']);
				if(!isset($grouped_fks[$constraint_name])) {
					$grouped_fks[$constraint_name] = [
						'name' => $constraint_name,
						'columns' => [],
						'ref_table' => trim($row['ref_table']),
						'ref_columns' => [],
						'on_delete' => $row['on_delete'],
						'on_update' => $row['on_update']
					];
				}
				$grouped_fks[$constraint_name]['columns'][] = trim($row['column_name']);
				$grouped_fks[$constraint_name]['ref_columns'][] = trim($row['ref_column']);
			}
			
			// Add foreign keys to indexes array
			foreach($grouped_fks as $fk) {
				$indexes[] = [
					'name' => $fk['name'],
					'type' => 'FOREIGN',
					'columns' => implode(', ', array_unique($fk['columns'])),
					'ref_table' => $fk['ref_table'],
					'ref_columns' => implode(', ', array_unique($fk['ref_columns'])),
					'on_delete' => $fk['on_delete'],
					'on_update' => $fk['on_update']
				];
			}
			
		} catch(Exception $e) {
			// If we can't get indexes, continue without them
		}
		
		return $indexes;
	}
}