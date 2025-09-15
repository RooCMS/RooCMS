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
//	Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('403:Access denied');
}
//#########################################################



/**
 * Getting env-variable with type conversion
 * 
 * @param string $key - env-variable name
 * @param mixed $default - default value
 * 
 * @return mixed - env-variable value
 */
function env(string $key, mixed $default = null): mixed {
    // Get value from sources with priority: $_ENV -> getenv() -> $_SERVER
    $value = $_ENV[$key] ?? (($env = getenv($key)) !== false ? $env : null) ?? $_SERVER[$key] ?? null;
    
    // Return default if not found
    if ($value === null) {
        return $default;
    }
    
    // Non-string values (like floats from $_SERVER) return as-is
    if (!is_string($value)) {
        return $value;
    }
    
    // Type casting for string values using match expression
    return match (strtolower($value)) {
        'true', '(true)' => true,
        'false', '(false)' => false,
        'null', '(null)' => null,
        'empty', '(empty)' => '',
        default => is_numeric($value) 
            ? (str_contains($value, '.') ? (float)$value : (int)$value)
            : $value
    };
}


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
 * Calculate percentage
 *
 * @param int $n - number
 * @param int $from - from number
 *
 * @return int - percentage
 */
function percent(int $n, int $from) : int {

	return round(($n / $from) * 100);
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
 * Extract Bearer token from HTTP headers
 *
 * @return string|null
 */
function get_bearer_token() : ?string {
	// Try common server variables
	$candidates = [
		env('HTTP_AUTHORIZATION'),
		env('Authorization'),
		env('HTTP_Authorization'),
		env('REDIRECT_HTTP_AUTHORIZATION')
	];

	foreach($candidates as $header) {
		if(is_string($header) && $header !== '') {
			if(preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
				return $matches[1];
			}
		}
	}

	// Fallback to getallheaders()/apache_request_headers()
	$all = function_exists('getallheaders') ? getallheaders() : (function_exists('apache_request_headers') ? apache_request_headers() : []);
	if(is_array($all)) {
		foreach($all as $key => $value) {
			if(stripos((string)$key, 'Authorization') === 0 && is_string($value)) {
				if(preg_match('/Bearer\s+(.*)$/i', $value, $m)) {
					return $m[1];
				}
			}
		}
	}

	return null;
}


/**
 * Read data file
 *
 * @param string $file - full path to file
 *
 * @return string|false - data from file or false on failure
 */
function file_read(string $file) : string|false {
	if(is_file($file) && is_readable($file)) {
		return file_get_contents($file);
	}

	return false;
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


/**
 * Convert hex color to array
 *
 * @param string $hexcolor - hex color
 *
 * @return array - array of color
 */
function cvrt_color_h2d(string $hexcolor) : array {
	if(mb_strlen($hexcolor) != 7 || mb_strpos($hexcolor, "#") === false) {
		return [];
	}

	return [
		"r" => hexdec(mb_substr($hexcolor, 1, 2)),
		"g" => hexdec(mb_substr($hexcolor, 3, 2)),
		"b" => hexdec(mb_substr($hexcolor, 5, 2))
	];
}


/**
 * Format timestamp
 *
 * @param mixed $timestamp - timestamp
 *
 * @return string - formatted timestamp
 */
function format_timestamp(mixed $timestamp) : string {
	return date('Y-m-d H:i:s', is_string($timestamp) ? strtotime($timestamp) : $timestamp);
}


/**
 * Output JSON
 *
 * @param mixed $data - data to output
 *
 * @return void
 */
function output_json(mixed $data) : void {
	if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
    }
	echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	exit();
}