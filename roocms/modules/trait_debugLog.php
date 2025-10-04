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
		$message = sprintf(
			'Undefined property access: %s::%s',
			get_class($this),
			$property_name
		);

		error_log($message);
	}


	/**
	 * Log undefined method call to debug system
	 *
	 * @param string $method_name
	 * @param array $arguments
	 * @return void
	 */
	private function log_undefined_method_call(string $method_name, array $arguments) : void {
		$message = sprintf(
			'Undefined method call: %s::%s()',
			get_class($this),
			$method_name
		);

		error_log($message);
	}
}
