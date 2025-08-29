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
 * Class Debugger
 */
class Debugger {

	use DebugLog;

	# hand flag show full debug text
	public  $show_debug          	= false;

	# debug info
	public  $debug_info				= []; # buffer for debug info text
	private $debug_dump				= []; # data dump for developers

	# Timer
	private $starttime				= 0;
	public  $productivity_time		= 0.0;

	# Memory
	private $memory_usage			= 0;
	public  $productivity_memory	= 0;
	public  $memory_peak_usage		= 0;

	# error log file
	public  $exist_errors			= false;

	# requirement php extension
	private $reqphpext				= ["Core", "standard", "mysqli", "session", "mbstring", "calendar", "date", "pcre", "xml", "SimpleXML", "gd", "curl"];

	public  $phpextensions			= []; # list installed php extends
	public  $nophpextensions		= []; # list non installed php extends required for RooCMS


	/**
	 * Construct
	 */
	public function __construct() {

		# set error handler
		set_error_handler([$this,'debug_critical_error']);

		# default : error hide
		$this->error_report(false);

        	# for admins all time measure productivity
		if(DEBUGMODE) {
			# start Debug timer
			$this->start_productivity();

			# check required php extends
			$this->check_phpextensions();

			# try show error
			$this->error_report(true);

			# check error log
			$this->check_errorlog();
		}

		# show debug info
		if($this->show_debug) {
			register_shutdown_function([$this,'shutdown'], false);
		}
	}


	/**
	 * Start productivity timer measure script working
	 */
	private function start_productivity() : void {

		# timer
		$this->starttime = $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true);

		# memory
		$this->memory_usage = MEMORYUSAGE;
	}


	/**
	 * Stop productivity timer measure script working
	 */
	public function end_productivity() : void {

		# timer
		$endtime = microtime(true);
		$totaltime = round(($endtime - $this->starttime), 4);

		$this->productivity_time = $totaltime;

		# memory
		$this->productivity_memory 	= memory_get_usage() - $this->memory_usage;
		$this->memory_peak_usage 	= memory_get_peak_usage();
	}


	/**
	 * Check required php extensions
	 */
	private function check_phpextensions() : void {

		$this->phpextensions = get_loaded_extensions();

		foreach($this->reqphpext AS $v) {
			if(!in_array($v, $this->phpextensions)) {
				$this->nophpextensions[] = $v;
			}
		}
	}


	/**
	 * Check log file for errors
	 * and set flag 'true' if file not empty
	 */
	private function check_errorlog() : void {
		if(is_file(ERRORSLOG) && filesize(ERRORSLOG) != 0) {
			$this->exist_errors = true;
		}

		if(is_file(SYSERRLOG) && filesize(SYSERRLOG) != 0) {
			$this->exist_errors = true;
		}
	}


	/**
	 * System Error Interceptor
	 *
	 * @param mixed $errno - error number
	 * @param mixed $msg   - message od error
	 * @param mixed $file  - filename with error
	 * @param mixed $line  - string number with error
	 *
	 * @return null|boolean
	 */
	public static function debug_critical_error(int $errno, string $msg, string $file, int $line) : bool {

        # read error in file
		$subj = file_read(ERRORSLOG);
		
		[$erlevel, $ertitle] = match($errno) {
			E_ERROR, E_USER_ERROR => [0, match($errno) {
				E_ERROR => "Critical error",
				E_USER_ERROR => "Critical user error"
			}],
			E_RECOVERABLE_ERROR, E_WARNING, E_USER_WARNING, E_CORE_WARNING, E_COMPILE_WARNING => [1, match($errno) {
				E_RECOVERABLE_ERROR => "Critical error in software",
				E_WARNING => "Critical error", 
				E_USER_WARNING => "Non-critical user error",
				E_CORE_WARNING => "Non-critical kernel error",
				E_COMPILE_WARNING => "Non-critical Zend error"
			}],
			E_NOTICE, E_USER_NOTICE => [2, match($errno) {
				E_NOTICE => "Error",
				E_USER_NOTICE => "User error"
			}],
			default => [3, "Unknown error"]
		};

		if($erlevel == 0) {
			register_shutdown_function([$this, 'shutdown']);
		}

		$time = date('d.m.Y H:i:s');

		$error = json_encode(['time' => $time, 'uri' => $_SERVER['REQUEST_URI'], 'title' => $ertitle, 'errno' => $errno, 'msg' => $msg, 'line' => $line, 'file' => $file], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		$subj .= $error.",\r\n";

		$f = fopen(ERRORSLOG, 'w+');
		if(is_writable(ERRORSLOG)) {
			fwrite($f, $subj);
		}
		fclose($f);

		# hide error if not use debugmode
		if(error_reporting() == 0 && $erlevel == 0) {
			$msg = 'Sorry, something went wrong. We are already working on fixing the cause.<br>'.$time.'<br><a href="javascript:history.back(1)">< Back</a>';
			$messager = file_read(CACHE_DIR.'tpl/critical.html');
			$messager = str_replace('{MESSAGE_CRITICAL_ERROR}', $msg, $messager);
			exit($messager);
		}

        if(DEBUGMODE) { // TODO: exchange this to debug log
			echo '<pre>'.$error.'</pre>';
		}

		# We kill the standard handler, so that he would not give out anything to spy (:
		return true;
	}


	/**
	 * on/off error log
	 *
	 * @param boolean $show
	 */
	private static function error_report(bool $show = false) : void {

		/**
		 * Set up error log
		 */
		ini_set('error_log', SYSERRLOG);
		error_reporting(0);
		ini_set('display_errors',			0);


		if($show) {
			error_reporting(E_ALL);			#8191
			ini_set('display_startup_errors',	1);
			ini_set('display_errors',			1);
			ini_set('html_errors',				1);
			ini_set('report_memleaks',			1);
			ini_set('log_errors_max_len',		4096);
			ini_set('ignore_repeated_errors',	1);
			ini_set('ignore_repeated_source',	1);
		}
	}


	/**
	 * Debug function with enhanced variable analysis
	 *
	 * @param mixed $var - variable/data/object for debugging
	 * @param string|null $label - optional label for the debug entry
	 * @param bool $detailed - include detailed type information
	 *
	 * @return void
	 */
	public function rundebug(mixed $var, ?string $label = null, bool $detailed = true) : void {
		static $use = 1;

		# shutdown register
		if($use == 1) {
			register_shutdown_function([$this,'shutdown']);
		}

		# get caller info
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
		$caller = $trace[1] ?? $trace[0];
		$file = basename($caller['file'] ?? 'unknown');
		$line = $caller['line'] ?? 0;
		$function = $caller['function'] ?? 'global';

		# analyze variable
		$debug_entry = [
			'id' => $use,
			'label' => $label ?? "Debug #{$use}",
			'caller' => [
				'file' => $file,
				'line' => $line,
				'function' => $function
			],
			'type' => get_debug_type($var),
			'timestamp' => microtime(true)
		];

		# detailed analysis
		if($detailed) {
			$debug_entry['analysis'] = match(true) {
				is_null($var) => ['value' => null, 'info' => 'NULL value'],
				is_bool($var) => ['value' => $var, 'info' => $var ? 'TRUE' : 'FALSE'],
				is_int($var) => ['value' => $var, 'info' => "Integer: {$var}"],
				is_float($var) => ['value' => $var, 'info' => "Float: {$var}"],
				is_string($var) => [
					'value' => $var,
					'info' => 'String length: ' . mb_strlen($var, 'UTF-8'),
					'encoding' => mb_detect_encoding($var, 'UTF-8, ASCII, ISO-8859-1', true)
				],
				is_array($var) => [
					'value' => $var,
					'info' => 'Array with ' . count($var) . ' elements',
					'keys' => array_keys($var),
					'is_assoc' => array_keys($var) !== range(0, count($var) - 1)
				],
				is_object($var) => [
					'class' => get_class($var),
					'info' => 'Object of class: ' . get_class($var),
					'methods' => get_class_methods($var),
					'properties' => get_object_vars($var),
					'parent' => get_parent_class($var) ?: null,
					'interfaces' => class_implements($var),
					'traits' => class_uses($var)
				],
				is_resource($var) => [
					'type' => get_resource_type($var),
					'info' => 'Resource of type: ' . get_resource_type($var)
				],
				default => ['value' => $var, 'info' => 'Unknown type']
			};
		} else {
			$debug_entry['value'] = $var;
		}

		# format output
		if($detailed) {
			// Store structured data directly for REST API output
			$this->debug_dump[] = $debug_entry;
		} else {
			// For simple dumps, store raw data for REST API
			$simple_entry = [
				'id' => $use,
				'label' => $label ?? "Dump #{$use}",
				'caller' => [
					'file' => $file,
					'line' => $line,
					'function' => $function
				],
				'type' => get_debug_type($var),
				'timestamp' => microtime(true),
				'value' => $var,
				'simple_dump' => true
			];
			$this->debug_dump[] = $simple_entry;
		}

		# step
		$use++;
	}


	/**
	 * Format SQL query for better readability
	 *
	 * @param string $query
	 * @return string
	 */
	private function format_sql_query(string $query): string {
		$keywords = ['SELECT', 'FROM', 'WHERE', 'JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'INNER JOIN', 
					'ORDER BY', 'GROUP BY', 'HAVING', 'LIMIT', 'INSERT', 'UPDATE', 'DELETE', 'SET'];
		
		$formatted = $query;
		foreach($keywords as $keyword) {
			$formatted = preg_replace('/\b' . $keyword . '\b/i', "\n" . $keyword, $formatted);
		}
		
		return trim($formatted);
	}


	/**
	 * Shutdown (return debug information in REST format)
	 */
	public static function shutdown() : void {

		global $debug, $db;

		// Prepare debug dumps
		$debug_dumps = [];
		foreach($debug->debug_dump AS $k => $v) {
			if(is_array($v) && isset($v['simple_dump']) && $v['simple_dump'] === true) {
				// Simple dump format - use structured data
				unset($v['simple_dump']); // Remove the flag
				$debug_dumps[] = $v;
			} elseif(is_array($v)) {
				// Structured debug data - use as is
				$debug_dumps[] = $v;
			} else {
				// Legacy string format
				$debug_dumps[] = [
					'id' => $k,
					'type' => 'legacy',
					'content' => $v
				];
			}
		}

		// Get defined functions, classes, constants
		$func_array = get_defined_functions();
		$user_functions = $func_array['user'] ?? [];

		$cl_array = get_declared_classes();
		$user_classes = array_filter($cl_array, function($class) {
			$reflection = new ReflectionClass($class);
			return $reflection->isUserDefined();
		});

		$const_array = get_defined_constants(true);
		$user_constants = $const_array['user'] ?? [];

		// End productivity measurement
		$debug->end_productivity();

		// Prepare REST response
		$response = [
			'debug' => [
				'info' => [
					'performance' => [
						'execution_time' => $debug->productivity_time,
						'memory_usage' => round($debug->productivity_memory / 1024 / 1024, 2),
						'memory_peak' => round($debug->memory_peak_usage / 1024 / 1024, 2),
						'db_queries' => $db->cnt_querys ?? 0
					],
					'dumps' => $debug_dumps,
					'database' => $debug->show_debug ? $debug->debug_info : null,
					'environment' => [
						'php_version' => PHP_VERSION,
						'extensions' => [
							'loaded' => $debug->phpextensions,
							'missing' => $debug->nophpextensions
						],
						'user_functions' => $user_functions,
						'user_classes' => $user_classes,
						'user_constants' => $user_constants
					],
					'errors' => [
						'exist' => $debug->exist_errors,
						'log_files' => [
							'errors' => ERRORSLOG,
							'system' => SYSERRLOG
						]
					]
				]
			],
			'status' => 'debug',
			'timestamp' => date('c')
		];

		// Set JSON headers
		// header('Content-Type: application/json; charset=utf-8');
		
		// Output JSON response
		echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		exit;
	}
}
