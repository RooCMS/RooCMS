<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2025 alexandr Belov aka alex Roosso. All rights reserved.
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
			: $symbols[rand(0, mb_strlen($symbols) - 1)];
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
	// for debug mode, we need to disable SSL verification
	if(DEBUGMODE) {
		$ssl_verify = false;
	} else {
		$ssl_verify = true;
	}
	
	// Use cURL for better SSL support and error handling
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_USERAGENT, 'RooCMS/1.0');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl_verify);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $ssl_verify);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	
	return (string) $http_code;
}


/**
 * Get client IP address
 *
 * @return string - client IP address
 */
function get_client_ip() {
    $ip = 'unknown';
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    
    // Защита от множественных IP (последний IP - реальный)
    if (strpos($ip, ',') !== false) {
        $ips = explode(',', $ip);
        $ip = trim(end($ips));
    }
    
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : 'unknown';
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
