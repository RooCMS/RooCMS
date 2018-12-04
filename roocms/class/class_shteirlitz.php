<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2019 alexandr Belov aka alex Roosso. All rights reserved.
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
	 * Шифруем
	 *
	 * @param        $str
	 * @param string $salt  - соль
	 * @param string $passw - пароль
	 *
	 * @return string
	 */
	public function encode($str, $passw="", $salt="") {
		return base64_encode($this->code($str, $passw, $salt));
	}


	/**
	 * Расшифровываем
	 *
	 * @param        $str
	 * @param string $salt  - соль
	 * @param string $passw - пароль
	 *
	 * @return mixed
	 */
	public function decode($str, $passw="", $salt="") {
		return $this->code(base64_decode($str), $passw, $salt);
	}


	/**
	 * Кодируем XOP
	 *
	 * @param        $str
	 * @param string $salt		- соль
	 * @param string $passw		- пароль
	 *
	 * @return mixed
	 */
	private function code($str, $passw="", $salt="") {

		$len = strlen($str);
		$n = $len > 100 ? 8 : 2;

		$gamma = '';
		while(strlen($gamma) < $len ) {
			$gamma .= substr(pack('H*', sha1($passw.$gamma.$salt)), 0, $n);
		}

		return $str^$gamma;
	}
}
