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
 * Class Debugger
 */
class Debugger {

	use DebugLog;

	private $debug_dump				= []; // data dump for developers

	private $starttime				= 0;
	public  $productivity_time		= 0.0;
	public  $productivity_memory	= 0;
	public  $memory_peak_usage		= 0;

	public  $exist_errors			= false;

	private $required_extensions	= ['Core', 'pdo', 'standard', 'mbstring', 'calendar', 'date', 'pcre', 'gd', 'curl', 'openssl', 'json', 'fileinfo', 'zip', 'exif'];
	
	private bool $shutdown_registered = false;
	private int $debug_counter = 0;



	/**
	 * Construct
	 */
	public function __construct() {

		// set error and exception handlers
		set_error_handler([$this,'debug_critical_error']);
		set_exception_handler([$this,'debug_exception_handler']);

		// default : error hide
		$this->error_report(false);

        // for admins all time measure productivity
		if(DEBUGMODE) {
			// start productivity timer
			$this->starttime = env('REQUEST_TIME_FLOAT') ?? microtime(true);

			// try show error
			$this->error_report(true);

			// check error log
			$this->check_errorlog();

			// shutdown register
			if(!$this->shutdown_registered) {
				register_shutdown_function([$this,'shutdown']);
				$this->shutdown_registered = true;
			}
		}
	}


	/**
	 * Stop productivity timer measure script working
	 */
	public function end_productivity() : void {
		// timer
		$endtime = microtime(true);
		$totaltime = round(($endtime - $this->starttime), 4);

		$this->productivity_time = $totaltime;

		// memory
		$this->productivity_memory 	= memory_get_usage() - MEMORYUSAGE;
		$this->memory_peak_usage 	= memory_get_peak_usage();
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
	 * @return null|boolean
	 */
	public static function debug_critical_error(int $errno, string $msg, string $file, int $line) : bool {

        // read error in file
		$subj = read_file(ERRORSLOG);
		
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

		if($erlevel == 0 && !$this->shutdown_registered) {
			register_shutdown_function([$this, 'shutdown']);
			$this->shutdown_registered = true;
		}

		$time = date('d.m.Y H:i:s');

		$error = json_encode([
			'time' => $time,
			'uri' => sanitize_log(env('REQUEST_URI') ?? ''),
			'title' => $ertitle,
			'errno' => $errno,
			'msg' => $msg,
			'line' => $line,
			'file' => $file
		], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		$subj .= $error.",\r\n";

		$f = fopen(ERRORSLOG, 'w+');
		if(is_writable(ERRORSLOG)) {
			fwrite($f, $subj);
		}
		fclose($f);

		// hide error if not use debugmode
		if(error_reporting() == 0 && $erlevel == 0) {
			$msg = 'Sorry, something went wrong. We are already working on fixing the cause.<br>' . $time . '<br><a href="javascript:history.back(1)">< Back</a>';
			$messager = read_file(_ASSETS.'/critical.html');
			$messager = str_replace('{MESSAGE_CRITICAL_ERROR}', $msg, $messager);
			exit($messager);
			// This return will never execute, but helps static analysis
			return false; // TODO: Maybe it will break the analyzer?
		}

		// We kill the standard handler, so that he would not give out anything to spy (:
		return true;
	}


	/**
	 * Exception Handler for uncaught exceptions
	 *
	 * @param Throwable $exception
	 * @return void
	 */
	public static function debug_exception_handler(Throwable $exception): void {
		
		$time = date('d.m.Y H:i:s');
		$class = get_class($exception);
		$message = $exception->getMessage();
		$file = $exception->getFile();
		$line = $exception->getLine();
		$trace = $exception->getTraceAsString();
		
		// Determine exception severity
		$severity = match(true) {
			$exception instanceof Error => 'Fatal Error',
			$exception instanceof TypeError => 'Type Error', 
			$exception instanceof ParseError => 'Parse Error',
			$exception instanceof ArgumentCountError => 'Argument Error',
			$exception instanceof ArithmeticError => 'Arithmetic Error',
			$exception instanceof AssertionError => 'Assertion Error',
			default => 'Uncaught Exception'
		};

		// Read existing log
		$subj = read_file(ERRORSLOG);
		
		// Create exception log entry
		$error_data = [
			'time' => $time,
			'uri' => sanitize_log(env('REQUEST_URI') ?? 'CLI'),
			'type' => 'exception',
			'severity' => $severity,
			'class' => $class,
			'message' => $message,
			'file' => $file,
			'line' => $line,
			'trace' => explode("\n", $trace)
		];
		
		$error_json = json_encode($error_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		$subj .= $error_json . ",\r\n";

		// Write to log file
		$f = fopen(ERRORSLOG, 'w+');
		if(is_writable(ERRORSLOG)) {
			fwrite($f, $subj);
		}
		fclose($f);

		// Display error if in debug mode
		if(DEBUGMODE) {
			$safe_message = htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
			$safe_file = htmlspecialchars($file, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
			
			echo json_encode([
				'error' => 'exception',
				'severity' => $severity,
				'class' => $class,
				'message' => $safe_message,
				'file' => basename($safe_file),
				'line' => $line,
				'time' => $time
			], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		} else {
			// User-friendly error message
			echo json_encode([
				'error' => 'internal_error',
				'message' => 'Something went wrong. We are working on fixing the issue.',
				'time' => $time,
				'reference' => substr(md5($time . $message), 0, 8)
			], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
		}

		exit(1);
	}


	/**
	 * on/off error log
	 *
	 * @param boolean $show
	 */
	private function error_report(bool $show = false) : void {

		// Set up error log
		ini_set('error_log', SYSERRLOG);
		error_reporting(0);
		ini_set('display_errors', 'off');


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
	 * @return void
	 */
	public function rundebug(mixed $var, ?string $label = null, bool $detailed = true) : void {

		// shutdown register
		if(!$this->shutdown_registered) {
			register_shutdown_function([$this,'shutdown']);
			$this->shutdown_registered = true;
		}

		// get caller info
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
		$caller = $trace[1] ?? $trace[0];
		$file = basename($caller['file'] ?? 'unknown');
		$line = $caller['line'] ?? 0;
		$function = $caller['function'] ?? 'global';

		// analyze variable
		$debug_entry = [
			'label' => $label ?? 'Debug #'.$this->debug_counter,
			'caller' => [
				'file' => $file,
				'line' => $line,
				'function' => $function
			],
			'type' => get_debug_type($var),
			'timestamp' => microtime(true)
		];

		// detailed analysis
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

		// format output
		if($detailed) {
			// Store structured data directly for REST API output
			$this->debug_dump[] = $debug_entry;
		} else {
			// For simple dumps, store raw data for REST API
			$simple_entry = [
				'label' => $label ?? 'Dump #'.$this->debug_counter,
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

		$this->debug_counter++;
	}


	/**
	 * Format SQL query for better readability
	 *
	 * @param string $query
	 * @return string
	 */
	public function format_sql_query(string $query): string {
		$keywords = ['SELECT', 'FROM', 'WHERE', 'JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'INNER JOIN', 
					'ORDER BY', 'GROUP BY', 'HAVING', 'LIMIT', 'INSERT', 'UPDATE', 'DELETE', 'SET'];
		
		foreach($keywords as $keyword) {
			$query = preg_replace('/\b' . $keyword . '\b/i', $keyword, $query);
		}
		
		return trim($query);
	}


    /**
     * Check filesystem health
	 * @return array
     */
    private function check_filesystem_health(): array {
        $checks = [];
        $overall_status = 'ok';
        
        // TODO: add check for all directories (or remove this check)
		// Check important directories
        $directories = [
            'uploads' => _UPLOAD ?? _SITEROOT . '/upload',
			'uploads_writable' => is_writable(_UPLOAD ?? _SITEROOT . '/upload'),
            'storage' => _STORAGE ?? _SITEROOT . '/storage',
            'logs' => _LOGS ?? _SITEROOT . '/storage/logs',
			'logs_writable' => is_writable(_LOGS ?? _SITEROOT . '/storage/logs')
        ];
        
        foreach ($directories as $name => $path) {
            if (defined('_' . strtoupper($name))) {
                if (is_dir($path) && is_writable($path)) {
                    $checks[$name] = [
                        'status' => 'ok',
                        'message' => 'Directory exists and writable',
                        'path' => $path
                    ];
                } else {
                    $checks[$name] = [
                        'status' => 'error',
                        'message' => 'Directory not writable or missing',
                        'path' => $path
                    ];
                    $overall_status = 'error';
                }
            }
        }
        
        // Check disk space
        $free_space = disk_free_space(_SITEROOT);
        $total_space = disk_total_space(_SITEROOT);
        
        if ($free_space !== false && $total_space !== false) {
            $free_percent = ($free_space / $total_space) * 100;
            
            $checks['disk_space'] = [
                'status' => $free_percent > 10 ? 'ok' : 'warning',
                'message' => sprintf('%.2f%% free space available', $free_percent),
                'free_bytes' => $free_space,
                'total_bytes' => $total_space
            ];
            
            if ($free_percent < 5) {
                $checks['disk_space']['status'] = 'error';
                $overall_status = 'error';
            }
        }
        
        return [
            'status' => $overall_status,
            'checks' => $checks
        ];
    }


    /**
     * Check PHP health and configuration
	 * @return array
     */
    private function check_php(): array {
        $checks = [];
        $overall_status = 'ok';
        
        // PHP version check
        $php_version = phpversion();
        $checks['version'] = [
            'status' => version_compare($php_version, '8.1.0', '>=') ? 'ok' : 'error',
            'message' => 'PHP version: ' . $php_version,
            'value' => $php_version
        ];
        
        if ($checks['version']['status'] === 'error') {
            $overall_status = 'error';
        }
        
        // missing extensions
        $missing_extensions = [];
        
        foreach ($this->required_extensions as $extension) {
            if (!extension_loaded($extension)) {
                $missing_extensions[] = $extension;
            }
        }
        
        $checks['extensions'] = [
            'status' => empty($missing_extensions) ? 'ok' : 'error',
            'message' => empty($missing_extensions) ? 'All required extensions loaded' : 'Missing extensions: ' . implode(', ', $missing_extensions),
            'missing' => $missing_extensions
        ];
        
        if (!empty($missing_extensions)) {
            $overall_status = 'error';
        }
        
        // Memory limit
        $memory_limit = ini_get('memory_limit');
        $checks['memory_limit'] = [
            'status' => 'ok',
            'message' => 'Memory limit: ' . $memory_limit,
            'value' => $memory_limit
        ];
        
        // Max execution time
        $max_execution_time = ini_get('max_execution_time');
        $checks['max_execution_time'] = [
            'status' => 'ok',
            'message' => 'Max execution time: ' . $max_execution_time . ' seconds',
            'value' => $max_execution_time
        ];
        
        return [
            'status' => $overall_status,
            'checks' => $checks
        ];
    }


	/**
     * Get server load (Linux/Unix only)
	 * @return array|null
     */
    private function get_server_load(): ?array {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return [
                '1min' => $load[0],
                '5min' => $load[1],
                '15min' => $load[2]
            ];
        }
        
        return null;
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
				'initiator' => [
					'protocol' => env('HTTPS'),
					'host' => env('SERVER_NAME'),
					'port' => env('SERVER_PORT'),
					'ip' => env('SERVER_ADDR'),
					'user_agent' => env('HTTP_USER_AGENT'),
					'referer' => env('HTTP_REFERER'),
					'request_uri' => env('REQUEST_URI'),
					'request_method' => env('REQUEST_METHOD'),
					'request_time' => env('REQUEST_TIME'),
					'request_time_float' => env('REQUEST_TIME_FLOAT')
				],
				'info' => [
					'performance' => [
						'execution_time' => $debug->productivity_time,
						'memory_usage' => round($debug->productivity_memory / 1024 / 1024, 2),
						'memory_peak' => round($debug->memory_peak_usage / 1024 / 1024, 2),
						'db_queries' => $db->get_query_count() ?? 0,
						'query_stats' => $db->get_query_stats()
					],
					'dumps' => $debug_dumps,
					'environment' => [
						'php' => $debug->check_php(),
						'user_environment' => [
							'user_functions' => $user_functions,
							'user_classes' => $user_classes,
							'user_constants' => $user_constants
						]
					],
					'filesystem' => $debug->check_filesystem_health()['checks'] ?? [],
					'errors' => [
						'exist' => $debug->exist_errors,
						'log_files' => [
							'errors' => ERRORSLOG,
							'system' => SYSERRLOG
						]
					],
					'server_load' => $debug->get_server_load(),
					'process_id' => getmypid()
				]
			],
			'status' => 'debug',
			'timestamp' => date('c')
		];

		// Output JSON response
		// write debug info to DEBUGSLOG instead of output
		$debug_entry = json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).",\r\n";
		
		// Use append mode to avoid truncating existing logs
		$df = fopen(DEBUGSLOG, 'a');
		if($df !== false) {
			if(is_writable(DEBUGSLOG)) {
				fwrite($df, $debug_entry);
			}
			fclose($df);
		}

		exit;
	}
}
