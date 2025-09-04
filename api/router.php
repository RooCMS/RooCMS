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
//	Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('403:Access denied');
}
//#########################################################

/**
 * API Routes Configuration
 * Define all API routes here
 */

// Include required classes
require_once _API . '/v1/controller_base.php';
require_once _API . '/v1/controller_health.php';
require_once _API . '/v1/controller_csp.php';

/**
 * Create controller factory and router instance
 */
$controllerFactory = new DefaultControllerFactory($db);
$api = new ApiHandler($controllerFactory);

/**
 * API v1 Routes
 */

// Health check endpoints
$api->get('/v1/health', 'HealthController@index');
$api->get('/v1/health/details', 'HealthController@details');
// CSP report endpoint
$api->post('/v1/csp-report', 'CspController@report');

// Future routes will be added here
// Example:
// $api->get('/v1/users', 'UsersController@index');
// $api->get('/v1/users/{id}', 'UsersController@show');
// $api->post('/v1/users', 'UsersController@store');
// $api->put('/v1/users/{id}', 'UsersController@update');
// $api->delete('/v1/users/{id}', 'UsersController@destroy');

// Default route for API root
$api->get('/', function() {
    $response = [
        'success' => true,
        'message' => 'RooCMS API v1',
        'timestamp' => date('Y-m-d H:i:s'),
        'version' => defined('ROOCMS_FULL_VERSION') ? ROOCMS_FULL_VERSION : '2.0.0 alpha',
        'endpoints' => [
            'health' => '/api/v1/health',
            'health_details' => '/api/v1/health/details'
        ]
    ];
    
    // output response
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
});

// Legacy test route (backwards compatibility)
$api->get('/test', function() {
    $response = [
        'success' => true,
        'message' => 'API test endpoint (deprecated)',
        'timestamp' => date('Y-m-d H:i:s'),
        'redirect' => '/api/v1/health'
    ];
    
    // output response
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
});

