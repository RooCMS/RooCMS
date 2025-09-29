<?php declare(strict_types=1);
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



class Shteirlitz {

	/**
	 * Encode string using XOR encryption
	 *
	 * @param string $str String to encode
	 * @param string $pass Password for encryption
	 * @param string $salt Salt for encryption
	 * @return string Base64 encoded encrypted string
	 */
	public function encode(string $str, string $pass="", string $salt=""): string {
		return base64_encode($this->code($str, $pass, $salt));
	}


	/**
	 * Decode string using XOR decryption
	 *
	 * @param string $str Base64 encoded string to decode
	 * @param string $pass Password for decryption
	 * @param string $salt Salt for decryption
	 * @return string Decrypted string
	 * @throws \InvalidArgumentException If input is not valid base64
	 */
	public function decode(string $str, string $pass="", string $salt=""): string {
		// Validate base64 input
		if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $str)) {
			throw new \InvalidArgumentException('Invalid base64 string provided');
		}

		$decoded = base64_decode($str, true);
		if ($decoded === false) {
			throw new \InvalidArgumentException('Failed to decode base64 string');
		}

		return $this->code($decoded, $pass, $salt);
	}


	/**
	 * Core XOR encryption/decryption method
	 *
	 * @param string $str String to process
	 * @param string $pass Password
	 * @param string $salt Salt
	 * @return string Processed string
	 */
	private function code(string $str, string $pass="", string $salt=""): string {

		$len = strlen($str);

		// Prevent infinite loop for empty strings
		if ($len === 0) {
			return '';
		}

		$n = $len > 100 ? 8 : 2;

		$gamma = '';
		while(strlen($gamma) < $len ) {
			// Use hex output for text-safe XOR operations
			$hash = pack('H*', hash('sha3-256', $pass . $gamma . $salt));
			$gamma .= substr($hash, 0, $n);
		}

		// Ensure gamma is exactly the same length as input
		$gamma = substr($gamma, 0, $len);

		return $str ^ $gamma;
	}
}

// This class was brought here from version 1.4 and will be here for some time simply to be.
// The idea for which it was created will be implemented in the future.
