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
 * SQL Query Builder with Fluent Interface for RooCMS
 *
 * Provides a fluent interface for constructing complex SQL queries with support for
 * SELECT, INSERT, UPDATE, and DELETE operations. Includes JOINs, WHERE conditions,
 * ORDER BY, GROUP BY, HAVING, LIMIT, and OFFSET clauses.
 *
 * Key features:
 * - Multi-database support (MySQL/MariaDB, PostgreSQL, Firebird)
 * - Fluent interface for method chaining
 * - Comprehensive WHERE conditions with multiple operators
 * - JOIN operations (INNER, LEFT, RIGHT, FULL)
 * - Aggregation functions and HAVING clauses
 * - Parameter binding for SQL injection prevention
 * - Query result processing and error handling
 */
class DbQueryBuilder {

    use DebugLog;
	
	private Db $db;
	private string $type        = '';
	private array $select       = [];
	private bool $distinct      = false;
	private string $table       = '';
	private array $joins        = [];
	private array $where        = [];
	private array $where_params = [];
	private array $group_by     = [];
	private array $having       = [];
	private array $order_by     = [];
	private ?int $limit         = null;
	private ?int $offset        = null;
	private array $data         = [];


	/**
	 * Constructor
	 * 
	 * @param Db $db
	 */
	public function __construct(Db $db) {
		$this->db = $db;
	}


	/**
	 * SELECT query
	 * 
	 * @param string|array $columns
	 * @return self
	 */
	public function select(string|array $columns = '*'): self {
		$this->type = 'SELECT';
		$this->select = is_array($columns) ? $columns : [$columns];
		return $this;
	}


    /**
     * SELECT DISTINCT query
     * 
     * @param string|array $columns
     * @return self
     */
    public function select_distinct(string|array $columns = '*'): self {
        $this->type = 'SELECT';
        $this->select = is_array($columns) ? $columns : [$columns];
        $this->distinct = true;
        return $this;
    }

	
	/**
	 * FROM table
	 * 
	 * @param string $table
	 * @return self
	 */
	public function from(string $table): self {
		$this->table = $table;
		return $this;
	}


	/**
	 * INSERT query
	 * 
	 * @param string $table
	 * @return self
	 */
	public function insert(string $table): self {
		$this->type = 'INSERT';
		$this->table = $table;
		return $this;
	}


	/**
	 * UPDATE query
	 * 
	 * @param string $table
	 * @return self
	 */
	public function update(string $table): self {
		$this->type = 'UPDATE';
		$this->table = $table;
		return $this;
	}


	/**
	 * DELETE query
	 * 
	 * @param string $table
	 * @return self
	 */
	public function delete(string $table): self {
		$this->type = 'DELETE';
		$this->table = $table;
		return $this;
	}


	/**
	 * JOIN
	 * 
	 * @param string $table
	 * @param string $condition
	 * @param string $type
	 * @return self
	 */
	public function join(string $table, string $condition, string $type = 'INNER'): self {
		$this->joins[] = $type . ' JOIN ' . $table . ' ON ' . $condition;
		return $this;
	}


	/**
	 * LEFT JOIN
	 * 
	 * @param string $table
	 * @param string $condition
	 * @return self
	 */
	public function left_join(string $table, string $condition): self {
		return $this->join($table, $condition, 'LEFT');
	}


	/**
	 * RIGHT JOIN
	 * 
	 * @param string $table
	 * @param string $condition
	 * @return self
	 */
	public function right_join(string $table, string $condition): self {
		return $this->join($table, $condition, 'RIGHT');
	}

	/**
	 * WHERE condition
	 * 
	 * @param string $column
	 * @param mixed $value
	 * @param string $operator
	 * @return self
	 */
	public function where(string $column, mixed $value, string $operator = '='): self {
		$operator = strtoupper(trim($operator));

		// Handle NULL specifically
		if($value === null) {
			$null_operator = match($operator) {
				'=', 'IS' => 'IS NULL',
				'!=', '<>', 'IS NOT' => 'IS NOT NULL',
				default => 'IS NULL'
			};
			$this->where[] = $column . ' ' . $null_operator;
			return $this;
		}

		$this->where[] = $column . ' ' . $operator . ' ?';
		$this->where_params[] = $value;
		return $this;
	}


	/**
	 * WHERE IN condition
	 * 
	 * @param string $column
	 * @param array $values
	 * @return self
	 */
	public function where_in(string $column, array $values): self {
		$placeholders = str_repeat('?,', count($values) - 1) . '?';
		$this->where[] = $column . ' IN (' . $placeholders . ')';
		$this->where_params = array_merge($this->where_params, $values);
		return $this;
	}


	/**
	 * WHERE LIKE condition
	 * 
	 * @param string $column
	 * @param string $value
	 * @return self
	 */
	public function where_like(string $column, string $value): self {
		return $this->where($column, $value, 'LIKE');
	}


	/**
	 * ORDER BY condition
	 * 
	 * @param string $column
	 * @param string $direction
	 * @return self
	 */
	public function order_by(string $column, string $direction = 'ASC'): self {
		$this->order_by[] = $column . ' ' . strtoupper($direction);
		return $this;
	}


	/**
	 * GROUP BY condition
	 * 
	 * @param string|array $columns
	 * @return self
	 */
	public function group_by(string|array $columns): self {
		$this->group_by = array_merge($this->group_by, is_array($columns) ? $columns : [$columns]);
		return $this;
	}


	/**
	 * HAVING condition	
	 * 
	 * @param string $condition
	 * @return self
	 */
	public function having(string $condition): self {
		$this->having[] = $condition;
		return $this;
	}


	/**
	 * LIMIT condition
	 * 
	 * @param int $limit
	 * @return self
	 */
	public function limit(int $limit): self {
		$this->limit = $limit;
		return $this;
	}


	/**
	 * OFFSET condition
	 * 
	 * @param int $offset
	 * @return self
	 */
	public function offset(int $offset): self {
		$this->offset = $offset;
		return $this;
	}


	/**
	 * Data for INSERT/UPDATE
	 * 
	 * @param array $data
	 * @return self
	 */
	public function data(array $data): self {
		$this->data = $data;
		return $this;
	}


	/**
	 * Execution of the query
	 * 
	 * @return PDOStatement
	 */
	public function execute(): PDOStatement {
		$sql = $this->build_sql();
		$params = $this->get_all_params();
		return $this->db->query($sql, $params);
	}


	/**
	 * Getting one record
	 * 
	 * @return array|false
	 */
	public function first(): array|false {
		$result = $this->limit(1)->execute();
		return $result->fetch(PDO::FETCH_ASSOC);
	}


	/**
	 * Getting all records
	 * 
	 * @return array
	 */
	public function get(): array {
		$result = $this->execute();
		return $result->fetchAll(PDO::FETCH_ASSOC);
	}


	/**
	 * Counting records
	 * 
	 * @return int
	 */
	public function count(): int {
		$originalSelect = $this->select;
		$this->select = ['COUNT(*) as count'];
		
		$result = $this->execute();
		$row = $result->fetch(PDO::FETCH_ASSOC);
		
		$this->select = $originalSelect;
		return (int) ($row['count'] ?? 0);
	}


	/**
	 * Building SQL query
	 * 
	 * @return string
	 * @throws InvalidArgumentException
	 */
	private function build_sql(): string {
		return match($this->type) {
			'SELECT' => $this->build_select_sql(),
			'INSERT' => $this->build_insert_sql(),
			'UPDATE' => $this->build_update_sql(),
			'DELETE' => $this->build_delete_sql(),
			default => throw new InvalidArgumentException('Unsupported query type: ' . $this->type)
		};
	}


	/**
	 * Building SELECT SQL
	 * 
	 * @return string
	 */
	private function build_select_sql(): string {
		// Base SELECT clause
		$sql = 'SELECT ' . ($this->distinct ? 'DISTINCT ' : '') . implode(', ', $this->select);
		$sql .= " FROM {$this->table}";

		// SQL clause mappings for conditional appending
		$clauses = [
			'joins'    => [' ', $this->joins],
			'where'    => [' WHERE ', $this->where, ' AND '],
			'group_by' => [' GROUP BY ', $this->group_by, ', '],
			'having'   => [' HAVING ', $this->having, ' AND '],
			'order_by' => [' ORDER BY ', $this->order_by, ', ']
		];

		// Append clauses if they have data
		foreach($clauses as $clause_data) {
			$prefix = $clause_data[0];
			$data = $clause_data[1];
			$separator = $clause_data[2] ?? ' ';
			if(!empty($data)) {
				$sql .= $prefix . implode($separator, $data);
			}
		}

		// Append LIMIT and OFFSET
		if($this->limit !== null) {
			$sql .= " LIMIT {$this->limit}";
		}
		if($this->offset !== null) {
			$sql .= " OFFSET {$this->offset}";
		}

		return $sql;
	}


	/**
	 * Building INSERT SQL
	 * 
	 * @return string
	 */
	private function build_insert_sql(): string {
		$columns = array_keys($this->data);
		$placeholders = array_fill(0, count($this->data), '?');
		
		return sprintf(
			"INSERT INTO %s (%s) VALUES (%s)",
			$this->table,
			implode(', ', $columns),
			implode(', ', $placeholders)
		);
	}


	/**
	 * Building UPDATE SQL
	 * 
	 * @return string
	 */
	private function build_update_sql(): string {
		$setParts = array_map(fn($column) => $column . ' = ?', array_keys($this->data));
		$sql = "UPDATE {$this->table} SET " . implode(', ', $setParts);

		return $this->append_where_clause($sql);
	}


	/**
	 * Building DELETE SQL
	 * 
	 * @return string
	 */
	private function build_delete_sql(): string {
		return $this->append_where_clause("DELETE FROM {$this->table}");
	}


	/**
	 * Getting all parameters for the query
	 * 
	 * @return array
	 */
	private function get_all_params(): array {
		$all_params = [];

		if(!empty($this->data)) {
			$all_params = array_merge($all_params, array_values($this->data));
		}

		return array_merge($all_params, $this->where_params);
	}


	/**
	 * Append WHERE clause to SQL if conditions exist
	 * 
	 * @param string $sql
	 * @return string
	 */
	private function append_where_clause(string $sql): string {
		if(!empty($this->where)) {
			$sql .= ' WHERE ' . implode(' AND ', $this->where);
		}
		return $sql;
	}
}
