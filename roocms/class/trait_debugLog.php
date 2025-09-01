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


trait DebugLog {

	/**
	 * Magic method for undefined property access
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get(string $name) : mixed {
		if(DEBUGMODE) {
			$this->log_undefined_property_access($name);
		}
		return null;
	}


	/**
	 * Magic method for undefined method calls
	 *
	 * @param string $name
	 * @param array $arguments
	 * @return void
	 */
	public function __call(string $name, array $arguments) : void {
		if(DEBUGMODE) {
			$this->log_undefined_method_call($name, $arguments);
		}
	}


	/**
	 * Log undefined property access to debug system
	 *
	 * @param string $property_name
	 * @return void
	 */
	private function log_undefined_property_access(string $property_name) : void {
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
		$caller = $trace[1] ?? $trace[0];
		
		$debug_info = [
			'type' => 'undefined_property',
			'property' => $property_name,
			'class' => get_class($this),
			'caller' => [
				'file' => basename($caller['file'] ?? 'unknown'),
				'line' => $caller['line'] ?? 0,
				'function' => $caller['function'] ?? 'global'
			],
			'available_properties' => $this->get_available_properties(),
			'suggestion' => $this->suggest_similar_property($property_name),
			'timestamp' => microtime(true)
		];

		$this->send_to_debugger($debug_info, 'Undefined Property Access');
	}


	/**
	 * Log undefined method call to debug system
	 *
	 * @param string $method_name
	 * @param array $arguments
	 * @return void
	 */
	private function log_undefined_method_call(string $method_name, array $arguments) : void {
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
		$caller = $trace[1] ?? $trace[0];
		
		$debug_info = [
			'type' => 'undefined_method',
			'method' => $method_name,
			'arguments' => $arguments,
			'argument_types' => array_map('get_debug_type', $arguments),
			'class' => get_class($this),
			'caller' => [
				'file' => basename($caller['file'] ?? 'unknown'),
				'line' => $caller['line'] ?? 0,
				'function' => $caller['function'] ?? 'global'
			],
			'available_methods' => $this->get_available_methods(),
			'suggestion' => $this->suggest_similar_method($method_name),
			'timestamp' => microtime(true)
		];

		$this->send_to_debugger($debug_info, 'Undefined Method Call');
	}


	/**
	 * Get available properties of the current object
	 *
	 * @return array
	 */
	private function get_available_properties() : array {
		$reflection = new ReflectionClass($this);
		$properties = [];
		
		foreach($reflection->getProperties() as $property) {
			if($property->isPublic()) {
				$properties[] = $property->getName();
			}
		}
		
		return $properties;
	}


	/**
	 * Get available methods of the current object
	 *
	 * @return array List of public method names
	 */
	private function get_available_methods() : array {
		return get_class_methods($this);
	}


	/**
	 * Suggest similar property name using Levenshtein distance
	 *
	 * @param string $property_name Property name to find suggestions for
	 * @return string|null Suggested property name or null
	 */
	private function suggest_similar_property(string $property_name) : ?string {
		$available = $this->get_available_properties();
		$closest = null;
		$shortest = -1;

		foreach($available as $property) {
			$distance = levenshtein($property_name, $property);
			if($distance <= 3 && ($distance < $shortest || $shortest < 0)) {
				$closest = $property;
				$shortest = $distance;
			}
		}

		return $closest;
	}


	/**
	 * Suggest similar method name using Levenshtein distance
	 *
	 * @param string $method_name Method name to find suggestions for
	 * @return string|null Suggested method name or null
	 */
	private function suggest_similar_method(string $method_name) : ?string {
		$available = $this->get_available_methods();
		$closest = null;
		$shortest = -1;

		foreach($available as $method) {
			$distance = levenshtein($method_name, $method);
			if($distance <= 3 && ($distance < $shortest || $shortest < 0)) {
				$closest = $method;
				$shortest = $distance;
			}
		}

		return $closest;
	}


	/**
	 * Send debug information to debugger if available
	 *
	 * @param array $debug_info Debug information array
	 * @param string $label Debug label
	 * @return void
	 */
	private function send_to_debugger(array $debug_info, string $label) : void {
		global $debug;
		
		if(isset($debug) && method_exists($debug, 'rundebug')) {
			$debug->rundebug($debug_info, $label, true);
		} else {
			// Fallback to error_log if debugger not available
			error_log(json_encode([
				'label' => $label,
				'data' => $debug_info
			], JSON_UNESCAPED_UNICODE));
		}
	}
}
