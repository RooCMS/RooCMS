<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2023 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class MySQLDatabase
 */
class MySQLiDB {

	use MySQLiDBExtends, DebugLog;

	# obj
	private $sql;

	private	$querys = [];

	public	$db_connect 	= false; # [bool]	connecting db status
	public	$cnt_querys 	= 0;	 # [int] 	query db counter


	/**
	* Let's begin
	*
	*/
	public function __construct() {

		global $db_info;

		if(trim($db_info['host']) != "" && trim($db_info['base']) != "") {
			$this->connect($db_info['host'], $db_info['user'], $db_info['pass'], $db_info['base']);
		}
	}


	/**
	* Connect to DB
	*
	* @param string $host - db host
	* @param string $user - user login
	* @param string $pass - user password
	* @param string $base - db name
	*/
	private function connect(string $host, string $user, string $pass, string $base) {

		$this->sql = new mysqli($host,$user,$pass, $base);

		if(!$this->sql->connect_errno) {
			$this->db_connect = true;

			# set mysql charset
			$this->charset();
		}
		else {
			exit($this->error());
		}
	}


	/**
	 * Check connect to DB
	 *
	 * @param string $host - db host
	 * @param string $user - user login
	 * @param string $pass - user password
	 * @param string $base - db name
	 *
	 * @return boolean
	 */
	public function check_connect(string $host, string $user, string $pass, string $base) {

		error_reporting(0);
		$this->sql = new mysqli($host,$user,$pass, $base);
		error_reporting(E_ALL);

		if($this->sql->connect_errno) {
			return false;
		}
		else {
			if(defined('INSTALL')) {
				$this->db_connect = true;
			}
			return true;
		}
	}


	/**
	* Set character encoding for DB connect
	* Some versions of MySQL do not work correctly with encodings. This feature helps to eliminate errors in the work.
	* It is worth remembering that these are all 3 queries to database. Therefore, if the database is stable, it is better to turn off this function.
	*/
	private function charset() {
		if($this->sql->character_set_name() != "utf8") {
			$this->sql->set_charset("utf8");

			# alternative
			if(!$this->sql->set_charset("utf8")) {
				$this->sql->query('set names utf8');
			}
		}
	}


	/**
	* Parser error queries to database
	* This function use for debugging
	*
	* @param string $q - query to db
	*
	* @return string|null when debug mode is on, will return an error, otherwise it will display a general error message on screen
	*/
	private function error(string $q = "") {

		# debug mode
		if(DEBUGMODE && $q != "") {
			return "<div style='padding: 5px;text-align: left;'><span style='font-family: Verdana, Tahoma; font-size: 12px;text-align: left;'>
			Ошибка БД [MySQL Error]: <b>".$this->sql->errno."</b>
			<br /> &bull; ".$this->sql->error."
			<br />
			<br />
			<table width='100%' style='border: 1px solid #ffdd00; background-color: #ffffee;text-align: left;'>
				<tr>
					<td align='left' style='text-align: left;font-family: Tahoma; font-size: 11px;color: #990000;'>
						<b>SQL Запрос:</b> <pre>{$q}</pre>
					</td>
				</tr>
			</table>
			</span></div>";
		}
		# stub
		else {
			return file_read(_SKIN."/db_error.tpl");
		}
	}


	/**
	* Query to DB
	*
	* @param string $q - query string
	*
	* @return resource - query result
	*/
	public function query(string $q) {

		global $debug;

		if($this->connecting()) {

			# start timer
			$start = microtime(true);

			# run query
			$query = $this->sql->query($q) or die ($this->error($q));

			# stop timer
			$finish = microtime(true);

			# query counter +1
			$this->cnt_querys++;

			# save info about query
			$this->querys[] = $q;

			# show info about all querys
			if($debug->show_debug) {

				# parse debug
				$q = strtr($q, array(
					'SELECT' => '<b>SELECT</b>',
					'INSERT' => '<b>INSERT</b>',
					'UPDATE' => '<b>UPDATE</b>',
					'DELETE' => '<b>DELETE</b>',
					'INTO'   => '<b>INTO</b>',
					'FROM'   => '<b>FROM</b>',
					'LEFT'   => '<b>LEFT</b>',
					'JOIN' 	 => '<b>JOIN</b>',
					'AS'     => '<b>AS</b>',
					'ON'     => '<b>ON</b>',
					'WHERE'  => '<b>WHERE</b>',
					'ORDER'  => '<b>ORDER</b>',
					'BY'     => '<b>BY</b>',
					'LIMIT'  => '<b>LIMIT</b>'
				));

				$timequery = $finish-$start;

				$debug->debug_info .= "<blockquote class='col-xs-12'>
							    <small>Запрос <b>#".$this->cnt_querys."</b></small>
							    <span class='text-danger'>{$q}</span>
							    <br /><small><span class='label label-info'>Timer: {$timequery}</span></small>
						       </blockquote>";
			}

			return $query;
		}
	}


	/**
	* The function inserts data from array into specified table.
	* ! It is not recommended to use this function in user part of CMS.
	*
	* @param array  $array - Data array, where key is name of field in table and value is data of this field.
	* @param string $table - Table name.
	*/
	public function insert_array(array $array, string $table) {

		$fields	= [];
		$values	= [];

		foreach($array AS $key=>$value) {

			$fields[] = $key;
			$values[] = "'".$value."'";
		}

		$q = "INSERT INTO {$table} (".implode(", ", $fields).") VALUES (".implode(", ", $values).")";

		$this->query($q);
	}


	/**
	 * The function updates data from array into specified table.
	 * ! It is not recommended to use this function in user part of CMS.
	 *
	 * @param array  $array   - Data array, where key is name of field in table and value is data of this field.
	 * @param string $table   - Table name.
	 * @param string $proviso - Condition (filter) for selecting target rows of table
	 */
	public function update_array(array $array, string $table, string $proviso) {

		$update = [];
		foreach($array AS $key=>$value) {
			$update[] = $key."='".$value."'";
		}

		$q = "UPDATE {$table} SET ".implode(", ", $update)." WHERE {$proviso}";

		$this->query($q);
	}


	/**
	 * Converts query results to simple array.
	 *
	 * @param resource $q - result resource
	 * @return array  - Returns data from database as numbered array.
	 */
	public function fetch_row($q) {
		if($this->connecting()) {
			return $q->fetch_row();
		}
	}


	/**
	 * Converts query results to associative array.
	 *
	 * @param resource $q - result resource
	 *
	 * @return array|null  - Returns data from database as associative array
	 */
	public function fetch_assoc($q) {
		if($this->connecting()) {
			return $q->fetch_assoc();
		}
	}


	/**
	 * Have a index query
	 *
	 * @return int return id
	 */
	public function insert_id() {
		if($this->connecting()) {
			return $this->sql->insert_id;
		}
	}


	/**
	 * The function checks if requested id is available.
	 *
	 * @param mixed       $id
	 * @param string      $table   - table name
	 * @param string      $field   - field name
	 * @param string|null $proviso - Additional condition (filter)
	 *
	 * @return int|boolean - Returns number of rows found that meet criteria or false in case of failure.
	 */
	public function check_id($id, string $table, string $field="id", string $proviso=NULL) {

		static $results = [];

		if($field == "id") {
			$id = round($id);
		}

		# more proviso
		if(trim($proviso) != "") {
			$proviso = " AND ".$proviso;
		}

		$res = $this->count($table, "{$field}='{$id}' {$proviso}");


		return $res > 0;
	}


	/**
	 * Function checks if there is list of requested id. And returns in form of array list of found or return false
	 *
	 * @param array       $ids     - array with ids
	 * @param string      $table   - table target
	 * @param string      $field   - name of table field containing identifier
	 * @param string|null $proviso - Additional condition (filter)
	 *
	 * @return array $result  - Returns array with data subarray. Name of  subarray is same as value being checked.
	 *                          Subarray contains keys: "check" boolean with result of checking value.
	 *                          Key "value" will contain name of value being checked
	 *                          Key "id_title" will contain name of field of main index of table with  data in which check was performed
	 *               	    Key "id_value" will contain value of main index from string which is indicated as found.
	 */
	public function check_array_ids(array $ids, string $table, string $field="id", string $proviso=NULL) {

		# write condition
		$primcond = "";
		foreach($ids AS $value) {
			$primcond = $this->qcond_or($primcond);
			$primcond .= " ".$field."='".$value."' ";
		}

		# more proviso
		if(trim($proviso) != "") {
			$proviso = " AND ".$proviso;
		}

		# Get primary key
		$pkey = $this->identy_primary_key($table);

		# query
		$data = [];
		$q = $this->query("SELECT ".$field.", ".$pkey." FROM ".$table." WHERE (".$primcond.") ".$proviso);
		while($row = $this->fetch_assoc($q)) {
			$data[$row[$pkey]] = $row[$field];
		}

		# work result
		$result = [];
		foreach($ids AS $k=>$value) {

			$result[$value]['value'] = $value;

			if(in_array($value, $data)) {
				$result[$value]['check'] = true;
				$result[$value]['id_title'] = $pkey;
				$result[$value]['id_value'] = array_search($value, $data);
			}
			else {
				$result[$value]['check'] = false;
			}
		}

		return $result;
	}


	/**
	 * Function handler Count()
	 *
	 * @param string $from    - table where counting
	 * @param string $proviso - counting condition
	 *
	 * @return int
	 */
	public function count($from, $proviso) {

		$results = [];

		# calc
		$query = "SELECT count(*) FROM ".$from." WHERE ".$proviso;
		$rkey = md5($query);

		# check result
		$c = [];
		if(!array_key_exists($rkey, $results)) {
			# check in DB
			$q = $this->query($query);

			$c = $this->fetch_row($q);
			$results[$rkey] = $c[0];
		}
		else {
			$c[0] = $results[$rkey];
		}

		return $c[0];
	}


	/**
	 * This function return sum rows in query
	 * Work only with Select and Show querys
	 *
	 * @param resource $q - query
	 *
	 * @return int
	 */
	public function num_rows($q) {
		return $q->num_rows;
	}


	/**
	 * Clear system symbols for query
	 *
	 * @param string $q - query
	 *
	 * @return string - returns query string in database cleared of extraneous characters
	 */
	public function escape_string(string $q) {

		$q = htmlspecialchars($q);
		$q = str_ireplace(
			array('{','}','$','&amp;gt;','\''),
			array('&#123;','&#125;','&#36;','&gt;','&#39;'),
			$q);

		if($this->db_connect) {
			return $this->sql->real_escape_string($q);
		}
		else {
			return $q;
		}
	}


	/**
	 * Function finds name of master key of table.
	 *
	 * @param string $table - table name
	 *
	 * @return mixed|null - name of column with master key of table.
	 */
	private function identy_primary_key(string $table) {

		$index = NULL;
		$q = $this->query("SHOW INDEX FROM ".$table);
		while($data = $this->fetch_assoc($q)) {
			if($data['Key_name'] == "PRIMARY") {
				$index = $data['Column_name'];
				break;
			}
		}

		return $index;
	}


	/**
	 * Disconnect DB
	 *
	 */
	public function close() {
		if(is_object($this->sql) && $this->sql->connect_errno == 0) {
			$this->sql->close();
		}
	}
}
