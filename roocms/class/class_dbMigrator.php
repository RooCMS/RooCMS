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
 * Universal Database Migration Manager
 * Supports MySQL, PostgreSQL, Firebird
 */
class DbMigrator {

	private Db $db;
	private string $driver;
	private array $migrations_table_schema;
	private string $migrations_dir;



	/**
	 * Constructor
	 * @param Db $db - Database connection object
	 */
	public function __construct(Db $db) {
		$this->db = $db;
		$this->driver = $this->detect_driver();
		$this->migrations_dir = _MIGRATIONS . '/';
		
		$this->migrations_table_schema = [
			'mysql' => 'CREATE TABLE IF NOT EXISTS `' . TABLE_MIGRATIONS . '` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`migration` VARCHAR(255) NOT NULL,
				`executed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`),
				UNIQUE KEY `migration` (`migration`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
			
			'postgresql' => 'CREATE TABLE IF NOT EXISTS ' . TABLE_MIGRATIONS . ' (
				id SERIAL PRIMARY KEY,
				migration VARCHAR(255) NOT NULL UNIQUE,
				executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
			)',
			
			'firebird' => 'CREATE TABLE ' . TABLE_MIGRATIONS . ' (
				id INTEGER NOT NULL,
				migration VARCHAR(255) NOT NULL,
				executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				CONSTRAINT PK_migrations PRIMARY KEY (id),
				CONSTRAINT UK_migration UNIQUE (migration)
			)'
		];

		$this->ensure_migrations_table();
	}


	/**
	 * Determining the type of database driver
	 * @return string
	 */
	private function detect_driver(): string {
		$info = $this->db->get_database_info();
		$driver = strtolower($info['driver'] ?? 'mysql');
		
		return match($driver) {
			'mysql', 'mysqli' => 'mysql',
			'pgsql', 'postgres' => 'postgresql',
			'firebird' => 'firebird',
			default => 'mysql'
		};
	}


	/**
	 * Creating a migrations table if it doesn't exist
	 */
	private function ensure_migrations_table(): void {
		try {
			$sql = $this->migrations_table_schema[$this->driver];
			$this->db->query($sql);
		} catch (Exception $e) {
			throw new Exception("Error creating migrations table: " . $e->getMessage());
		}
	}


	/**
	 * Performing all new migrations
	 * @return array
	 */
	public function migrate(): array {
		$migration_files = $this->get_pending_migrations();
		$executed = [];

		foreach ($migration_files as $file) {
			try {
				$this->execute_migration($file, 'up');
				$executed[] = $file;
				echo "✓ Migration " . sanitize_log($file) . " completed successfully\n";
			} catch (Exception $e) {
				echo "✗ Error executing migration " . sanitize_log($file) . ": " . $e->getMessage() . "\n";
				break;
			}
		}

		return $executed;
	}


	/**
	 * Rolling back migrations
	 * @param int $steps - Number of steps to roll back
	 * @return array
	 */
	public function rollback(int $steps = 1): array {
		$executed_migrations = $this->get_executed_migrations();
		$to_rollback = array_slice($executed_migrations, -$steps);
		$rolled_back = [];

		foreach (array_reverse($to_rollback) as $migration) {
			try {
				$this->execute_migration($migration, 'down');
				$this->remove_migration_record($migration);
				$rolled_back[] = $migration;
				echo "✓ Migration {$migration} rolled back successfully\n";
			} catch (Exception $e) {
				echo "✗ Error rolling back migration {$migration}: " . $e->getMessage() . "\n";
				break;
			}
		}

		return $rolled_back;
	}


	/**
	 * Getting the list of pending migrations to be executed
	 * @return array
	 */
	private function get_pending_migrations(): array {
		$all_migrations = $this->get_all_migration_files();
		$executed_migrations = $this->get_executed_migrations();
		
		return array_diff($all_migrations, $executed_migrations);
	}


	/**
	 * Getting all migration files
	 * @return array
	 */
	private function get_all_migration_files(): array {
		$files = glob($this->migrations_dir . 'migrate_*.php');
		$migrations = [];
		
		foreach ($files as $file) {
			$migrations[] = basename($file, '.php');
		}
		
		sort($migrations);
		return $migrations;
	}


	/**
	 * Getting executed migrations
	 * @return array
	 */
	private function get_executed_migrations(): array {
		$result = $this->db->fetch_all('SELECT migration FROM ' . TABLE_MIGRATIONS . ' ORDER BY id');
		return array_column($result, 'migration');
	}


	/**
	 * Performing a specific migration
	 * @param string $migration_name
	 * @param string $direction - 'up' or 'down'
	 */
	private function execute_migration(string $migration_name, string $direction): void {
		$migration_file = $this->migrations_dir . $migration_name . '.php';
		
		if (!file_exists($migration_file)) {
			throw new Exception('Migration file not found: ' . $migration_file);
		}

		// Loading the migration
		$migration = require $migration_file;
		
		if (!isset($migration[$direction])) {
			throw new Exception('Direction ' . $direction . ' not found in migration ' . $migration_name);
		}

		$this->db->begin_transaction();
		
		try {
			$this->process_migration($migration[$direction]);
			
			if ($direction === 'up') {
				$this->add_migration_record($migration_name);
			}
			
			$this->db->commit();
		} catch (Exception $e) {
			$this->db->rollback();
			throw $e;
		}
	}


	/**
	 * Processing the migration
	 * @param array $migration_data
	 */
	private function process_migration(array $migration_data): void {
		// Creating tables
		if (isset($migration_data['tables'])) {
			foreach ($migration_data['tables'] as $table_constant => $table_config) {
				$table_name = constant($table_constant);
				$this->create_table($table_name, $table_config);
			}
		}

		// Changing tables
		if (isset($migration_data['alter_tables'])) {
			foreach ($migration_data['alter_tables'] as $table_constant => $alter_config) {
				$table_name = constant($table_constant);
				$this->alter_table($table_name, $alter_config);
			}
		}

		// Inserting data
		if (isset($migration_data['data'])) {
			foreach ($migration_data['data'] as $table_constant => $records) {
				$table_name = constant($table_constant);
				$this->insert_data($table_name, $records);
			}
		}

		// Deleting data
		if (isset($migration_data['delete_data'])) {
			foreach ($migration_data['delete_data'] as $table_constant => $conditions) {
				$table_name = constant($table_constant);
				$this->delete_data($table_name, $conditions);
			}
		}

		// Deleting tables
		if (isset($migration_data['drop_tables'])) {
			foreach ($migration_data['drop_tables'] as $table_constant) {
				$table_name = constant($table_constant);
				$this->drop_table($table_name);
			}
		}

		// Performing arbitrary SQL queries
		if (isset($migration_data['raw_sql'])) {
			foreach ($migration_data['raw_sql'] as $sql) {
				$this->db->query($sql);
			}
		}
	}


	/**
	 * Creating a table
	 * @param string $table_name
	 * @param array $config
	 */
	private function create_table(string $table_name, array $config): void {
		$sql = $this->build_create_table_sql($table_name, $config);
		$this->db->query($sql);
	}


	/**
	 * Building SQL for creating a table
	 * @param string $table_name
	 * @param array $config
	 * @return string
	 */
	private function build_create_table_sql(string $table_name, array $config): string {
		$columns = [];
		$indexes = [];
		
		// Building columns
		foreach ($config['columns'] as $column_name => $column_config) {
			$columns[] = $this->build_column_definition($column_name, $column_config);
		}

		// Building indexes
		if (isset($config['indexes'])) {
			foreach ($config['indexes'] as $index) {
				$indexes[] = $this->build_index_definition($index);
			}
		}

		$table_definition = implode(', ', array_merge($columns, $indexes));
		
		$sql = match($this->driver) {
			'mysql' => $this->build_mysql_create_table($table_name, $table_definition, $config),
			'postgresql' => $this->build_postgres_create_table($table_name, $table_definition, $config),
			'firebird' => $this->build_firebird_create_table($table_name, $table_definition, $config),
			default => throw new Exception('Unsupported driver: ' . $this->driver)
		};

		return $sql;
	}

    
	/**
	 * Building the column definition
	 * @param string $name
	 * @param array $config
	 * @return string
	 */
	private function build_column_definition(string $name, array $config): string {
		$type = $this->convert_column_type($config['type'], $config);
		$definition = '`' . $name . '` ' . $type;

		// NOT NULL
		if (isset($config['null']) && !$config['null']) {
			$definition .= ' NOT NULL';
		}

		// DEFAULT value
		if (isset($config['default'])) {
			if (is_string($config['default']) && !in_array(strtoupper($config['default']), ['CURRENT_TIMESTAMP', 'NULL'])) {
				$definition .= ' DEFAULT \'' . $config['default'] . '\'';
			} else {
				$definition .= ' DEFAULT ' . $config['default'];
			}
		}

		// AUTO_INCREMENT
		if (isset($config['auto_increment']) && $config['auto_increment']) {
			$definition .= match($this->driver) {
				'mysql' => ' AUTO_INCREMENT',
				'postgresql' => '', // In PostgreSQL SERIAL is used
				'firebird' => ' GENERATED BY DEFAULT AS IDENTITY', // Firebird 3.0+
				default => ''
			};
		}

		return $definition;
	}


	/**
	 * Converting column types for different databases
	 * @param string $type
	 * @param array $config
	 * @return string
	 */
	private function convert_column_type(string $type, array $config): string {
		$length = $config['length'] ?? null;
		$precision = $config['precision'] ?? null;
		$scale = $config['scale'] ?? null;

		return match($this->driver) {
			'mysql' => $this->convert_mysql_type($type, $length, $precision, $scale, $config),
			'postgresql' => $this->convert_postgres_type($type, $length, $precision, $scale, $config),
			'firebird' => $this->convert_firebird_type($type, $length, $precision, $scale, $config),
			default => $type
		};
	}


	/**
	 * Converting types for MySQL
	 */
	private function convert_mysql_type(string $type, ?int $length, ?int $precision, ?int $scale, array $config): string {
		return match(strtolower($type)) {
			'integer', 'int' => $length ? 'INT(' . $length . ')' : 'INT(11)',
			'bigint' => 'BIGINT(20)',
			'string', 'varchar' => $length ? 'VARCHAR(' . $length . ')' : 'VARCHAR(255)',
			'text' => 'TEXT',
			'longtext' => 'LONGTEXT',
			'boolean', 'bool' => 'TINYINT(1)',
			'timestamp' => 'TIMESTAMP',
			'datetime' => 'DATETIME',
			'date' => 'DATE',
			'time' => 'TIME',
			'decimal' => $precision && $scale ? 'DECIMAL(' . $precision . ', ' . $scale . ')' : 'DECIMAL(10, 2)',
			'float' => 'FLOAT',
			'double' => 'DOUBLE',
			'enum' => isset($config['values']) ? 'ENUM(\'' . implode('\',\'', $config['values']) . '\')' : 'ENUM()',
			default => strtoupper($type)
		};
	}


	/**
	 * Converting types for PostgreSQL
	 */
	private function convert_postgres_type(string $type, ?int $length, ?int $precision, ?int $scale, array $config): string {
		return match(strtolower($type)) {
			'integer', 'int' => isset($config['auto_increment']) && $config['auto_increment'] ? 'SERIAL' : 'INTEGER',
			'bigint' => isset($config['auto_increment']) && $config['auto_increment'] ? 'BIGSERIAL' : 'BIGINT',
			'string', 'varchar' => $length ? 'VARCHAR(' . $length . ')' : 'VARCHAR(255)',
			'text', 'longtext' => 'TEXT',
			'boolean', 'bool' => 'BOOLEAN',
			'timestamp' => 'TIMESTAMP',
			'datetime' => 'TIMESTAMP',
			'date' => 'DATE',
			'time' => 'TIME',
			'decimal' => $precision && $scale ? 'DECIMAL(' . $precision . ', ' . $scale . ')' : 'DECIMAL(10, 2)',
			'float' => 'REAL',
			'double' => 'DOUBLE PRECISION',
			'enum' => isset($config['values']) ? 'VARCHAR(50) CHECK(' . $config['column'] . ' IN (\'' . implode('\',\'', $config['values']) . '\'))' : 'VARCHAR(50)',
			default => strtoupper($type)
		};
	}


	/**
	 * Converting types for Firebird
	 */
	private function convert_firebird_type(string $type, ?int $length, ?int $precision, ?int $scale, array $config): string {
		return match(strtolower($type)) {
			'integer', 'int' => $length && $length <= 32767 ? 'SMALLINT' : 'INTEGER',
			'bigint' => 'BIGINT',
			'string', 'varchar' => $length ? 'VARCHAR(' . $length . ')' : 'VARCHAR(255)',
			'text', 'longtext' => 'BLOB SUB_TYPE TEXT',
			'boolean', 'bool' => 'BOOLEAN',
			'timestamp' => 'TIMESTAMP',
			'datetime' => 'TIMESTAMP',
			'date' => 'DATE',
			'time' => 'TIME',
			'decimal' => ($precision && $scale) ? 'DECIMAL(' . $precision . ',' . $scale . ')' : 'DECIMAL(18,2)',
			'float' => 'FLOAT',
			'double' => 'DOUBLE PRECISION',
			'enum' => $length ? 'VARCHAR(' . $length . ')' : 'VARCHAR(50)',
			default => 'VARCHAR(255)'
		};
	}


	/**
	 * Building the index definition
	 */
	private function build_index_definition(array $index): string {
		$type = $index['type'] ?? 'KEY';
		$name = $index['name'] ?? '';
		$columns = is_array($index['columns']) ? implode('`, `', $index['columns']) : $index['columns'];

		return match(strtoupper($type)) {
			'PRIMARY' => 'PRIMARY KEY (`' . $columns . '`)',
			'UNIQUE' => $name ? 'UNIQUE KEY `' . $name . '` (`' . $columns . '`)' : 'UNIQUE (`' . $columns . '`)',
			'KEY', 'INDEX' => $name ? 'KEY `' . $name . '` (`' . $columns . '`)' : 'KEY (`' . $columns . '`)',
			default => 'KEY (`' . $columns . '`)'
		};
	}


	/**
	 * Building CREATE TABLE for MySQL
	 */
	private function build_mysql_create_table(string $table_name, string $definition, array $config): string {
		$sql = 'CREATE TABLE IF NOT EXISTS `' . $table_name . '` (' . $definition . ')';
		
		// ENGINE
		$engine = $config['options']['engine'] ?? 'InnoDB';
		$sql .= ' ENGINE=' . $engine;
		
		// CHARSET
		$charset = $config['options']['charset'] ?? 'utf8mb4';
		$collate = $config['options']['collate'] ?? 'utf8mb4_unicode_ci';
		$sql .= ' DEFAULT CHARSET=' . $charset . ' COLLATE=' . $collate;

		// AUTO_INCREMENT
		if (isset($config['options']['auto_increment'])) {
			$sql .= ' AUTO_INCREMENT=' . $config['options']['auto_increment'];
		}

		return $sql;
	}


	/**
	 * Building CREATE TABLE for PostgreSQL
	 */
	private function build_postgres_create_table(string $table_name, string $definition, array $config): string {
		return 'CREATE TABLE IF NOT EXISTS ' . $table_name . ' (' . $definition . ')';
	}


	/**
	 * Building CREATE TABLE for Firebird
	 */
	private function build_firebird_create_table(string $table_name, string $definition, array $config): string {
		return 'CREATE TABLE ' . $table_name . ' (' . $definition . ')';
	}


	/**
	 * Changing the table
	 */
	private function alter_table(string $table_name, array $config): void {
		if (isset($config['add_columns'])) {
			foreach ($config['add_columns'] as $column_name => $column_config) {
				$column_def = $this->build_column_definition($column_name, $column_config);
				$sql = 'ALTER TABLE ' . $table_name . ' ADD COLUMN ' . $column_def;
				$this->db->query($sql);
			}
		}

		if (isset($config['drop_columns'])) {
			foreach ($config['drop_columns'] as $column_name) {
				$sql = match($this->driver) {
					'mysql' => 'ALTER TABLE ' . $table_name . ' DROP COLUMN `' . $column_name . '`',
					'postgresql' => 'ALTER TABLE ' . $table_name . ' DROP COLUMN ' . $column_name,
					'firebird' => 'ALTER TABLE ' . $table_name . ' DROP COLUMN ' . $column_name
				};
				$this->db->query($sql);
			}
		}

		if (isset($config['add_indexes'])) {
			foreach ($config['add_indexes'] as $index) {
				$this->add_index($table_name, $index);
			}
		}
	}


	/**
	 * Adding an index
	 */
	private function add_index(string $table_name, array $index): void {
		$type = strtoupper($index['type'] ?? 'INDEX');
		$name = $index['name'] ?? $table_name . '_' . implode('_', (array)$index['columns']) . '_idx';
		$columns = is_array($index['columns']) ? implode(', ', $index['columns']) : $index['columns'];

		$sql = match($type) {
			'UNIQUE' => 'CREATE UNIQUE INDEX ' . $name . ' ON ' . $table_name . ' (' . $columns . ')',
			'INDEX', 'KEY' => 'CREATE INDEX ' . $name . ' ON ' . $table_name . ' (' . $columns . ')',
			default => 'CREATE INDEX ' . $name . ' ON ' . $table_name . ' (' . $columns . ')'
		};

		$this->db->query($sql);
	}


	/**
	 * Inserting data
	 */
	private function insert_data(string $table_name, array $records): void {
		foreach ($records as $record) {
			$this->db->insert_array($record, $table_name);
		}
	}


	/**
	 * Deleting data
	 */
	private function delete_data(string $table_name, array $conditions): void {
		if (isset($conditions['where'])) {
			$where = $conditions['where'];
			$params = $conditions['params'] ?? [];
			$sql = 'DELETE FROM ' . $table_name . ' WHERE ' . $where;
			$this->db->query($sql, $params);
		} else {
			$sql = 'DELETE FROM ' . $table_name;
			$this->db->query($sql);
		}
	}


	/**
	 * Deleting a table
	 */
	private function drop_table(string $table_name): void {
		$sql = 'DROP TABLE IF EXISTS ' . $table_name;
		$this->db->query($sql);
	}


	/**
	 * Adding a record about the executed migration
	 */
	private function add_migration_record(string $migration_name): void {
		$this->db->insert_array([
			'migration' => $migration_name,
			'executed_at' => date('Y-m-d H:i:s')
		], TABLE_MIGRATIONS);
	}


	/**
	 * Deleting a record about the migration
	 */
	private function remove_migration_record(string $migration_name): void {
		$this->db->query('DELETE FROM ' . TABLE_MIGRATIONS . ' WHERE migration = ?', [$migration_name]);
	}


	/**
	 * Getting the status of migrations
	 */
	public function status(): array {
		$all_migrations = $this->get_all_migration_files();
		$executed_migrations = $this->get_executed_migrations();
		$pending_migrations = array_diff($all_migrations, $executed_migrations);

		return [
			'total' => count($all_migrations),
			'executed' => count($executed_migrations),
			'pending' => count($pending_migrations),
			'executed_list' => $executed_migrations,
			'pending_list' => $pending_migrations
		];
	}
}
