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
 * Output JSON
 * @param mixed $data - data to output
 * @return void
 */
function output_json(mixed $data) : void {
	// Clear all output buffers to prevent conflicts with gzip buffering
	while (ob_get_level()) {
		ob_end_clean();
	}
	
	if (!headers_sent()) {
		header('Content-Type: application/json; charset=utf-8');
	}
	
	echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	exit();
}


/**
 * Output HTML
 * @param string $data - data to output
 * @param bool $hsc - use htmlspacialchars
 * @param bool $exit - exit after output
 * @return void
 */
function output_html(string $data, ?bool $exit = true, ?bool $hsc = null) : void {
	if (!headers_sent()) {
		header('Content-Type: text/html; charset=utf-8');
	}

	echo ($hsc !== null) ? htmlspecialchars($data, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : $data;

	if ($exit) exit();
}


/**
 * Output HTML
 * @param string $data - data to output
 * @param bool $hsc - use htmlspacialchars
 * @return void
 */
function render_html(string $data, ?bool $hsc = null) : void {
	// Render HTML without exiting
	output_html($data, false, $hsc);
}