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


/**
 * Generator random code
 *
 * @param int   $ns      - Num of characters in code
 * @param mixed $symbols - Characters from which code will be generated
 *
 * @return string $Code
 */
function randcode(int $ns, $symbols="ABCEFHKLMNPRSTVXYZ123456789") {

	if(trim($symbols) == "") {
		$symbols = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	}

	settype($symbols, "string");

	$code = ""; $i = 0;

	while ($i < $ns) {
		$a = rand(0,1);
		mt_srand(); srand();
		$code .= ($a == 1)
			? $symbols[mt_rand(0, mb_strlen($symbols) - 1)]
			: $symbols[rand(0, mb_strlen($symbols) - 1)]	;
		$i++;
	}

	return $code;
}

/**
 * Mb transform first letter in string
 *
 * @param string $string
 *
 * @return string
 */
function mb_ucfirst(string $string) {
	return mb_strtoupper(mb_substr($string, 0, 1)).mb_strtolower(mb_substr($string, 1));
}

/**
 * Forwarding
 *
 * @param string $address - URL
 * @param int    $code    - Code forwading
 */
function go(string $address, int $code=301) {

	switch($code) {
		case 300:
			header($_SERVER['SERVER_PROTOCOL'].' 300 Multiple Choices');
			break;

		case 302:
			header($_SERVER['SERVER_PROTOCOL'].' 302 Found');
			break;

		case 303:
			header($_SERVER['SERVER_PROTOCOL'].' 303 See Other');
			break;

		case 304:
			header($_SERVER['SERVER_PROTOCOL'].' 304 Not Modified');
			break;

		case 307:
			header($_SERVER['SERVER_PROTOCOL'].' 307 Temporary Redirect');
			break;

		default:
			header($_SERVER['SERVER_PROTOCOL'].' 301 Moved Permanently');
			break;
	}

	header("Location: ".$address);
	exit;
}


/**
 * Move back
 */
function goback() {
	go(getenv("HTTP_REFERER"));
	exit;
}


/**
 * Cache headers
 */
function nocache() {

	$expires = time() + (60*60*24);

	header("Expires: ".gmdate("D, d M Y H:i:s", $expires)." GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
}

/**
 * Get response code from remote URL
 *
 * @param string $url -  remote url
 *
 * @return string - code response
 */
function get_http_response_code(string $url) {
	$headers = get_headers($url);
	return mb_substr($headers[0], 9, 3);
}

/**
 * Read data file
 *
 * @param string $file - full path to file
 *
 * @return string - data from file
 */
function file_read($file) {
	$buffer = "";

	if(is_file($file)) {
		$buffer .= file_get_contents($file);
	}

	return $buffer;
}
