<?php
declare(strict_types=1);
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
 * Generator random code
 *
 * @param int   $ns      - Num of characters in code
 * @param mixed $symbols - Characters from which code will be generated
 *
 * @return string $Code
 */
function randcode(int $ns = 6, string $symbols = "ABCEFHKLMNPRSTVXYZ123456789") : string {

    $symbols = trim($symbols) !== "" ? $symbols : "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

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
 * Get response code from remote URL
 *
 * @param string $url -  remote url
 *
 * @return int - code response
 */
function get_http_response_code(string $url) : int {
	// for debug mode, we need to disable SSL verification
    $ssl_verify = defined('DEBUGMODE') && DEBUGMODE ? false : true;
	
	// Use cURL for better SSL support and error handling
	$ch = curl_init();
	curl_setopt_array($ch, [
		CURLOPT_URL            => $url,
		CURLOPT_NOBODY         => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_MAXREDIRS      => 5,
		CURLOPT_CONNECTTIMEOUT => 5,
		CURLOPT_TIMEOUT        => 10,
		CURLOPT_USERAGENT      => 'RooCMS/1.0',
		CURLOPT_SSL_VERIFYPEER => $ssl_verify,
		CURLOPT_SSL_VERIFYHOST => $ssl_verify ? 2 : 0,
		CURLOPT_RETURNTRANSFER => true,
	]);
	
	curl_exec($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	
	return $http_code;
}


/**
 * Read data file
 *
 * @param string $file - full path to file
 *
 * @return string - data from file
 */
function file_read(string $file) : string {
	$data = "";

	if(is_file($file) && is_readable($file)) {
		$data .= file_get_contents($file);
	}

	return $data;
}


/**
 * Cache headers
 */
function nocache() : void {
    if (headers_sent()) {
		return;
	}

	header('Expires: Thu, 01 Jan 1970 00:00:01 GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
}
