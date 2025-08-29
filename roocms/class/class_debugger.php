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
		
		switch($errno) {
			case E_ERROR:			# critical
				$erlevel = 0; $ertitle = "Critical error";
				break;

			case E_USER_ERROR:		# critical
				$erlevel = 0; $ertitle = "Critical user error";
				break;

			case E_RECOVERABLE_ERROR :	# warning(?)critical
				$erlevel = 1; $ertitle = "Critical error in software";
				break;

			case E_WARNING:			# warning
				$erlevel = 1; $ertitle = "Critical error";
				break;

			case E_USER_WARNING:	# warning
				$erlevel = 1; $ertitle = "Non-critical user error";
				break;

			case E_CORE_WARNING:	# warning
				$erlevel = 1; $ertitle = "Non-critical kernel error";
				break;

			case E_COMPILE_WARNING:	# warning
				$erlevel = 1; $ertitle = "Non-critical Zend error";
				break;

			case E_NOTICE:			# notice
				$erlevel = 2; $ertitle = "Error";
				break;

			case E_USER_NOTICE:		# notice
				$erlevel = 2; $ertitle = "User error";
				break;

			default:				# unknown
				$erlevel = 3; $ertitle = "Unknown error";
				break;
		}

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
	 * Debug function
	 *
	 * @param mixed $var - variable/data/object for debugging
	 *
	 * @return void     - show variable dump for $var
	 */
	public function rundebug(mixed $var) : void {
		static $use = 1;

		# shutdown register
		if($use == 1) {
			register_shutdown_function([$this,'shutdown']);
		}

		# print var
		ob_start();

			if(is_object($var) || is_array($var)) {
				$var = (array) $var;
			}

			print_r(json_encode($var, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

			$output = ob_get_contents();

		ob_end_clean();

		$this->debug_dump[] = $output;

		# step
		$use++;
	}


	/**
	 * Shutdown (show debug information)
	 * TODO: exchange this to debug log
	 */
	public static function shutdown() : void {

		global $debug, $db;

		foreach($debug->debug_dump AS $k=>$v) {
			echo '<pre style="overflow: auto;max-height: 300px;">'.htmlspecialchars($v).'</pre>';
		}


		if($debug->show_debug) {
			echo '<pre>'.json_encode($debug->debug_info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).'</pre>';
		}

		# functions

		$func_array = get_defined_functions();
		foreach($func_array['user'] AS $v) {
			echo $v.'();<br>';
		}

		$cl_array = get_declared_classes();
		foreach($cl_array AS $v) {
			echo $v.'<br>';
		}

		$const_array = get_defined_constants(true);
		foreach($const_array['user'] AS $k=>$v) {
			echo $k.' - '.htmlspecialchars($v).'<br>';
		}

		echo 'DB queries: <b>'.$db->cnt_querys.'</b><br>
		Memory usage: <span style="cursor: help;" title="'.round($debug->memory_peak_usage/1024/1024, 2).' bytes max"><b>'.round($debug->productivity_memory/1024/1024, 2).' MB</b></span><br>
		Script execution time: <b>'.$debug->productivity_time.' ms</b>';

		exit;
	}
}
