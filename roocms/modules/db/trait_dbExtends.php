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
 * Extended Database Operations Trait
 *
 * Provides additional utility methods for database operations, including
 * pagination management, connection testing, data manipulation helpers,
 * and advanced query execution features.
 * 
 * Key features:
 * - Getting the table schema
 */
trait DbExtends {


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
     * Fetch all rows from the database
     * 
     * @param string $sql
     * @param array $params
     * 
     * @return array
     */
    abstract protected function fetch_all(string $sql, array $params = []): array;
}