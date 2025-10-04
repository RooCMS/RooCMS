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
 * Database Connection Class for RooCMS
 *
 * Provides a connection to the database using PDO with support for
 * MySQL/MariaDB, PostgreSQL, and Firebird databases. Features include
 * connection pooling, health monitoring, and error handling.
 */
class DbConnect {

    private PDO $pdo;
    private string $driver 		= '';
	private array $config 		= [];
	private bool $is_connected 	= false;



	/**
	 * Constructor with auto-connection
	 *
	 * @param string|null $driver
	 * @param array|null $config
	 */
	public function __construct(?string $driver = null, ?array $config = null) {
		global $db_info;

		$this->config = $config ?? $db_info;
		$this->driver = strtolower($driver ?? $this->config['type'] ?? 'mysql');

		if(empty($this->config['host']) || empty($this->config['base'])) {
			throw new InvalidArgumentException('Database host and database name are required');
		}

		$this->connect();
	}


	/**
	 * Connection to the database through PDO
	 *
	 * @return void
	 * @throws PDOException
	 */
	private function connect(): void {
		try {
			$dsn = $this->build_dsn();
			$options = $this->get_pdo_options();

			$this->pdo = new PDO(
				$dsn,
				$this->config['user'] ?? '',
				$this->config['pass'] ?? '',
				$options
			);

			$this->is_connected = true;

			// Additional configuration for specific databases
			$this->configure_database();

		} catch(PDOException $e) {
			$this->handle_error("Error connecting to the database: " . $e->getMessage());
		}
	}


	/**
	 * Building DSN string for different types of databases
	 *
	 * @return string DSN connection string
	 * @throws InvalidArgumentException
	 */
	private function build_dsn(): string {
		$host = $this->config['host'] ?? 'localhost';
		$port = $this->config['port'] ?? null;
		$database = $this->config['base'] ?? '';

		return match($this->driver) {
			'mysql', 'mysqli', 'mariadb' => sprintf(
				'mysql:host=%s;dbname=%s;charset=utf8mb4%s',
				$host,
				$database,
				$port ? ";port=$port" : ';port=3306'
			),

			'pgsql', 'postgres', 'postgresql' => sprintf(
				'pgsql:host=%s;dbname=%s%s',
				$host,
				$database,
				$port ? ";port=$port" : ';port=5432'
			),

			'firebird' => sprintf(
				'firebird:dbname=%s:%s',
				$host,
				$database
			),

			default => throw new InvalidArgumentException('Unsupported database driver: ' . $this->driver . '. Supported only server databases: mysql, mariadb, postgresql, firebird')
		};
	}


	/**
	 * PDO options for security and performance
	 *
	 * @return array PDO connection options
	 */
	private function get_pdo_options(): array {
		return [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
			PDO::ATTR_STRINGIFY_FETCHES => false,
			PDO::ATTR_CASE => PDO::CASE_NATURAL,
			PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
			PDO::ATTR_PERSISTENT => $this->config['persistent'] ?? false,
			PDO::ATTR_TIMEOUT => $this->config['timeout'] ?? 10,
		];
	}


	/**
	 * Additional configuration for specific databases
	 *
	 * @return void
	 */
	private function configure_database(): void {
		switch($this->driver) {
			case 'mysql':
			case 'mysqli':
			case 'mariadb':
				$this->configure_mysql();
				break;

			case 'pgsql':
			case 'postgres':
			case 'postgresql':
				$this->configure_postgres();
				break;

			case 'firebird':
				$this->configure_firebird();
				break;

			default:
				break;
		}
	}


	/**
	 * Configuration MySQL/MariaDB
	 *
	 * @return void
	 */
	private function configure_mysql(): void {
		$this->pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
		$this->pdo->exec("SET sql_mode = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");
		$this->pdo->exec("SET time_zone = '+00:00'");
	}


	/**
	 * Configuration PostgreSQL
	 *
	 * @return void
	 */
	private function configure_postgres(): void {
		$this->pdo->exec("SET NAMES 'UTF8'");
		$this->pdo->exec("SET timezone = 'UTC'");
	}


	/**
	 * Configuration Firebird
	 *
	 * @return void
	 */
	private function configure_firebird(): void {
		// Firebird basic settings for working with UTF-8
		$this->pdo->exec("SET NAMES UTF8");

		// Setting the date format
		$this->pdo->exec("SET SQL DIALECT 3");
	}


	/**
	 * Getting PDO object
	 *
	 * @return PDO
	 */
	public function get_pdo(): PDO {
		return $this->pdo;
	}


	/**
	 * Getting driver type
	 *
	 * @return string
	 */
	public function get_driver(): string {
		return $this->driver;
	}


	/**
	 * Checking connection status
	 *
	 * @return bool
	 */
	public function is_connected(): bool {
		return $this->is_connected;
	}


    /**
	 * Getting information about the DB
	 * 
	 * @return array
	 */
	public function get_database_info(): array {
		return [
			'driver' => $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME),
			'version' => $this->pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
			'client_version' => $this->pdo->getAttribute(PDO::ATTR_CLIENT_VERSION),
			'connection_status' => $this->pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS),
		];
	}


	/**
	 * Error handling
	 *
	 * @param string $message Error message
	 * @return void
	 * @throws Exception
	 */
	private function handle_error(string $message): void {
		throw new Exception($message);
	}
}