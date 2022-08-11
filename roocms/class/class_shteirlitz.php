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

class Shteirlitz {

	/**
	 * Encode
	 *
	 * @param        $str
	 * @param string $salt - salt
	 * @param string $pass - pass
	 *
	 * @return string
	 */
	public function encode($str, string $pass="", string $salt="") {
		return base64_encode($this->code($str, $pass, $salt));
	}


	/**
	 * Decode
	 *
	 * @param        $str
	 * @param string $salt - salt
	 * @param string $pass - pass
	 *
	 * @return mixed
	 */
	public function decode($str, string $pass="", string $salt="") {
		return $this->code(base64_decode($str), $pass, $salt);
	}


	/**
	 * Encode XOP
	 *
	 * @param        $str
	 * @param string $salt - salt
	 * @param string $pass - password
	 *
	 * @return mixed
	 */
	private function code($str, string $pass="", string $salt="") {

		$len = strlen($str);
		$n = $len > 100 ? 8 : 2;

		$gamma = '';
		while(strlen($gamma) < $len ) {
			$gamma .= substr(pack('H*', sha1($pass.$gamma.$salt)), 0, $n);
		}

		return $str^$gamma;
	}
}
