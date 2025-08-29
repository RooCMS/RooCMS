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
	 * Wow! This is magic...
	 *
	 * @param $name
	 *
	 * @return null
	 */
	public function __get(string $name) : mixed {
		# debug log
		if(DEBUGMODE) {
			$trace = debug_backtrace();
			$pi = pathinfo($trace[0]['file']);

			# call log 
			echo 'Attempt to get undefined property: '.$name.' ; Source: '.$pi['filename'].' line '.$trace[0]['line']."\n";
		}

		return null;
	}


	public function __call(string $name, array $arguments) : void {
		# debug log
		if(DEBUGMODE) {
			echo 'Method call '.$name.' '.implode(', ', $arguments)."\n";
		}
	}
}
