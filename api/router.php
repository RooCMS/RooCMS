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


/**
 * api v1 controller loader
 */
spl_autoload_register(function(string $controller_name) {
    
    // allowed controllers
    $controllers = [
        'BaseController'    => _API . '/v1/controller_base.php',
        'HealthController'  => _API . '/v1/controller_health.php',
        'CspController'     => _API . '/v1/controller_csp.php',
        'AuthController'    => _API . '/v1/controller_auth.php',
        'AuthMiddleware'    => _API . '/v1/middleware_auth.php',
        'RoleMiddleware'    => _API . '/v1/middleware_role.php'
    ];
    
    // try to load the controller
    if(isset($controllers[$controller_name])) {
        if(file_exists($controllers[$controller_name])) {
            require_once $controllers[$controller_name];            
            return true;
        }
    }
    
    return false;
});


/**
 * Create controller and middleware factories and router instance
 */
$controllerFactory = new DefaultControllerFactory($db);
$middlewareFactory = new DefaultMiddlewareFactory($db, new Role(), new Auth($db));
$api = new ApiHandler($controllerFactory, $middlewareFactory);

/**
 * API v1 Routes
 */

// Health check endpoints
$api->get('/v1/health', 'HealthController@index');
$api->get('/v1/health/details', 'HealthController@details');

// CSP report endpoint
$api->post('/v1/csp-report', 'CspController@report');

// Authentication endpoints (public)
$api->post('/v1/auth/login', 'AuthController@login');
$api->post('/v1/auth/register', 'AuthController@register');
$api->post('/v1/auth/refresh', 'AuthController@refresh');

// Authentication endpoints (protected)
$api->post('/v1/auth/logout', 'AuthController@logout', ['AuthMiddleware']);

// Password management endpoints (public)
$api->post('/v1/auth/password/recovery', 'AuthController@recovery_password');
$api->post('/v1/auth/password/reset', 'AuthController@reset_password');

// Password management endpoints (protected)
$api->put('/v1/auth/password', 'AuthController@update_password', ['AuthMiddleware']);

// Future routes will be added here
// Example:
// $api->get('/v1/users', 'UsersController@index');
// $api->get('/v1/users/{id}', 'UsersController@show');
// $api->post('/v1/users', 'UsersController@store');
// $api->put('/v1/users/{id}', 'UsersController@update');
// $api->delete('/v1/users/{id}', 'UsersController@destroy');

// Admin endpoints (require authentication + admin role)
// Attention: RoleMiddleware requires AuthMiddleware, which checks the token and sets $GLOBALS['authenticated_user']
// Example admin routes with role-based access control:
// For moderator access or higher (moderator, admin, superuser):
// $api->get('/v1/admin/dashboard', 'AdminController@dashboard', ['AuthMiddleware', 'RoleMiddleware@require_moderator_access']);
// For admin access only (admin, superuser):
// $api->get('/v1/admin/users', 'AdminController@users', ['AuthMiddleware', 'RoleMiddleware@require_admin_access']);
// For superuser access only:
// $api->post('/v1/admin/system/config', 'AdminController@updateSystemConfig', ['AuthMiddleware', 'RoleMiddleware@require_superuser_access']);

// Default route for API root
$api->get('/', function() {
    $response = [
        'success' => true,
        'message' => 'RooCMS API',
        'timestamp' => date('Y-m-d H:i:s'),
        'version' => defined('ROOCMS_FULL_VERSION') ? ROOCMS_FULL_VERSION : '...',
        'endpoints' => [
            'health' => 'GET /api/v1/health',
            'health_details' => 'GET /api/v1/health/details',
            'csp_report' => 'POST /api/v1/csp-report',
            'auth_login' => 'POST /api/v1/auth/login',
            'auth_register' => 'POST /api/v1/auth/register',
            'auth_logout' => 'POST /api/v1/auth/logout',
            'auth_refresh' => 'POST /api/v1/auth/refresh',
            'password_update' => 'PUT /api/v1/auth/password',
            'password_recovery' => 'POST /api/v1/auth/password/recovery',
            'password_reset' => 'POST /api/v1/auth/password/reset'
        ]
    ];
    
    // output response
    output_json($response);
});
