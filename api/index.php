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
//	Anti Hack initialization
//---------------------------------------------------------
const RooCMS = true;
//#########################################################

/**
 * define root roocms path
 */
defined('_SITEROOT') or define('_SITEROOT', str_ireplace(DIRECTORY_SEPARATOR."api", "", dirname(__FILE__)));

/**
 * include roocms init file
 */
require_once _SITEROOT.'/roocms/init.php';



/**
 * get uri
 */
if(isset($_SERVER['REQUEST_URI'])) {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = explode('/', trim($uri, '/'));
} else {
    $uri = [];
}

/**
 * get version, resource and id from uri
 */
$version =  $uri[1] ?? '';
$resource = $uri[2] ?? '';
$id =       $uri[3] ?? null;

/**
 * get method from request
 */
$method = $_SERVER['REQUEST_METHOD'];


// Response for test
$response = [
    'api' => 'RooCMS API Test',
    'version' => $version,
    'resource' => $resource,
    'id' => $id,
    'method' => $method,
    'uri' => $_SERVER['REQUEST_URI'],
    'parsed_uri' => $uri,
    'status' => 'working'
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
