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
//	Protect initialization
//---------------------------------------------------------
const RooCMS = true;
//#########################################################

/**
 * define root roocms path
 */
defined('_SITEROOT') or define('_SITEROOT', dirname(__FILE__, 2));


/**
 * include roocms init file
 */
require_once _SITEROOT.'/roocms/init.php';

/**
 * Set cache headers
 */
nocache(); 


/**
 * Enable CORS for cross-domain requests
 */
set_header('Allow: GET, POST, PUT, PATCH, DELETE');
set_header('Access-Control-Allow-Origin: *');
set_header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
set_header('Access-Control-Allow-Headers: Content-Type, Authorization');

/**
 * Handle preflight OPTIONS request
 */
if (env('REQUEST_METHOD') === 'OPTIONS') {
    http_response_code(200);
    exit();
}

/**
 * get request URI and method with sanitization
 */
$uri = sanitize_log(env('REQUEST_URI') ?? '/');
$method = sanitize_string(env('REQUEST_METHOD') ?? 'GET');

/**
 * Extract path from URI (remove query string and API prefix)
 */
$path = sanitize_path($uri); 

/**
 * Invalid URL, stop working
 */
if ($path === false) {
    throw new InvalidArgumentException('Invalid URL provided');
}


/**
 * Remove /api prefix if present
 */
if (strpos($path, '/api') === 0) {
    $path = substr($path, 4);
}


/**
 * Ensure path starts with /
 */
if (substr($path, 0, 1) !== '/') {
    $path = '/' . $path;
}

/**
 * include api router file and routes configuration
 */
require_once _API.'/router.php';

/**
 * Dispatch request to appropriate handler
 */
try {
    $api->dispatch($method, $path);
} catch (Exception $e) {
    // Handle any uncaught exceptions
    http_response_code(500);
    
    $response = [
        'error' => true,
        'message' => 'Internal server error',
        'status_code' => 500,
        'timestamp' => format_timestamp(time())
    ];
    
    // Log the error if logging is available
    error_log('API Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

    
    // Don't expose exception details in production
    if (defined('ROOCMS_BUILD_VERSION') && ROOCMS_BUILD_VERSION === 'alpha') {
        $response['debug'] = [
            'exception' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
    }
    
    // output response
    output_json($response);
}
