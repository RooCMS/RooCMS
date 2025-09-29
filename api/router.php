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
        'BaseController'            => _API . '/v1/controller_base.php',
        'HealthController'          => _API . '/v1/controller_health.php',
        'CspController'             => _API . '/v1/controller_csp.php',
        'AuthController'            => _API . '/v1/controller_auth.php',
        'UsersController'           => _API . '/v1/controller_users.php',
        'BackupController'          => _API . '/v1/controller_backup.php',
        'AdminSettingsController'   => _API . '/v1/controller_adminSettings.php',
        'DebugController'           => _API . '/v1/controller_debug.php',
        'AuthMiddleware'            => _API . '/v1/middleware_auth.php',
        'RoleMiddleware'            => _API . '/v1/middleware_role.php'
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
 * Register controllers
 */
$container->register(\CspController::class, \CspController::class);
$container->register(\HealthController::class, \HealthController::class);
$container->register(\UsersController::class, \UsersController::class);
$container->register(\AuthController::class, \AuthController::class);
$container->register(\AdminSettingsController::class, \AdminSettingsController::class);
$container->register(\BackupController::class, \BackupController::class);
$container->register(\DebugController::class, \DebugController::class);

/**
 * Create controller and middleware factories and router instance
 */
$controllerFactory = new DefaultControllerFactory($container);
$middlewareFactory = new DefaultMiddlewareFactory(
    $container->get(AuthenticationService::class),
    $container->get(UserValidationService::class), 
    $container->get(Role::class)
);
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
$api->post('/v1/auth/logout/all', 'AuthController@logout_all', ['AuthMiddleware']);
$api->post('/v1/auth/refresh/revoke', 'AuthController@revoke_refresh', ['AuthMiddleware']);

// Password management endpoints (public)
$api->post('/v1/auth/password/recovery', 'AuthController@recovery_password');
$api->post('/v1/auth/password/reset', 'AuthController@reset_password');

// Password management endpoints (protected)
$api->put('/v1/auth/password', 'AuthController@update_password', ['AuthMiddleware']);

// Users endpoints
$api->get('/v1/users', 'UsersController@index');
$api->get('/v1/users/me', 'UsersController@me', ['AuthMiddleware']);
$api->get('/v1/users/{user_id}', 'UsersController@show');
$api->post('/v1/users/me/verify-email', 'UsersController@request_verify_email', ['AuthMiddleware']);
$api->get('/v1/users/verify-email/{verification_code}', 'UsersController@verify_email');
$api->patch('/v1/users/me', 'UsersController@update_me', ['AuthMiddleware']);
$api->delete('/v1/users/me', 'UsersController@delete_me', ['AuthMiddleware']);
$api->put('/v1/users/{user_id}', 'UsersController@update_user', ['AuthMiddleware', 'RoleMiddleware@admin_access']);
$api->delete('/v1/users/{user_id}', 'UsersController@delete_user', ['AuthMiddleware', 'RoleMiddleware@admin_access']);

// Settings routes (admin only)
$api->get('/v1/admin/settings', 'AdminSettingsController@index', ['AuthMiddleware', 'RoleMiddleware@admin_access']);
$api->get('/v1/admin/settings/group-{group}', 'AdminSettingsController@get_group', ['AuthMiddleware', 'RoleMiddleware@admin_access']);
$api->get('/v1/admin/settings/key-{key}', 'AdminSettingsController@get_setting', ['AuthMiddleware', 'RoleMiddleware@admin_access']);
$api->put('/v1/admin/settings/key-{key}', 'AdminSettingsController@update_setting', ['AuthMiddleware', 'RoleMiddleware@admin_access']);
$api->patch('/v1/admin/settings', 'AdminSettingsController@update_settings', ['AuthMiddleware', 'RoleMiddleware@admin_access']);
$api->get('/v1/admin/settings/reset/all', 'AdminSettingsController@reset_all', ['AuthMiddleware', 'RoleMiddleware@admin_access']);
$api->get('/v1/admin/settings/reset/group-{group}', 'AdminSettingsController@reset_group', ['AuthMiddleware', 'RoleMiddleware@admin_access']);
$api->get('/v1/admin/settings/reset/key-{key}', 'AdminSettingsController@reset_setting', ['AuthMiddleware', 'RoleMiddleware@admin_access']);

// Backup endpoints (admin only)
$api->post('/v1/backup/create', 'BackupController@create', ['AuthMiddleware', 'RoleMiddleware@admin_access']);
$api->post('/v1/backup/restore', 'BackupController@restore', ['AuthMiddleware', 'RoleMiddleware@admin_access']);
$api->get('/v1/backup/list', 'BackupController@list', ['AuthMiddleware', 'RoleMiddleware@admin_access']);
$api->delete('/v1/backup/delete/{filename}', 'BackupController@delete', ['AuthMiddleware', 'RoleMiddleware@admin_access']);
$api->get('/v1/backup/download/{filename}', 'BackupController@download', ['AuthMiddleware', 'RoleMiddleware@admin_access']);
$api->get('/v1/backup/logs', 'BackupController@logs', ['AuthMiddleware', 'RoleMiddleware@admin_access']);
$api->get('/v1/backup/status', 'BackupController@status', ['AuthMiddleware', 'RoleMiddleware@admin_access']);

// Debug endpoints (admin only)
$api->post('/v1/admin/debug/clear', 'DebugController@clear', ['AuthMiddleware', 'RoleMiddleware@admin_access']);

// Future routes will be added here
// Example:
// Admin endpoints (require authentication + admin role)
// Attention: RoleMiddleware requires AuthMiddleware, which checks the token and sets $GLOBALS['authenticated_user']
// Example admin routes with role-based access control:
// For moderator access or higher (moderator, admin, superuser):
// $api->get('/v1/admin/dashboard', 'AdminController@dashboard', ['AuthMiddleware', 'RoleMiddleware@moderator_access']);
// For admin access only (admin, superuser):
// $api->get('/v1/admin/users', 'AdminController@users', ['AuthMiddleware', 'RoleMiddleware@admin_access']);
// For superuser access only:
// $api->post('/v1/admin/system/config', 'AdminController@updateSystemConfig', ['AuthMiddleware', 'RoleMiddleware@superuser_access']);

/**
 * Default route for API root
 */
$api->get('/', function() {
    $response = [
        'success' => true,
        'message' => 'RooCMS API',
        'timestamp' => format_timestamp(time()),
        'version' => defined('ROOCMS_FULL_VERSION') ? ROOCMS_FULL_VERSION : '...',
        'endpoints' => [
            'health' => 'GET /api/v1/health',
            'health_details' => 'GET /api/v1/health/details',
            'csp_report' => 'POST /api/v1/csp-report',
            'auth_login' => 'POST /api/v1/auth/login',
            'auth_register' => 'POST /api/v1/auth/register',
            'auth_logout' => 'POST /api/v1/auth/logout',
            'auth_logout_all' => 'POST /api/v1/auth/logout/all',
            'auth_refresh' => 'POST /api/v1/auth/refresh',
            'auth_refresh_revoke' => 'POST /api/v1/auth/refresh/revoke',	
            'password_update' => 'PUT /api/v1/auth/password',
            'password_recovery' => 'POST /api/v1/auth/password/recovery',
            'password_reset' => 'POST /api/v1/auth/password/reset',
            'users_me' => 'GET /api/v1/users/me',
            'users_index' => 'GET /api/v1/users',
            'users_show' => 'GET /api/v1/users/{user_id}',
            'users_request_verify_email' => 'POST /api/v1/users/me/verify-email',
            'users_verify_email' => 'GET /api/v1/users/verify-email/{verification_code}',
            'users_update_me' => 'PATCH /api/v1/users/me',
            'users_delete_me' => 'DELETE /api/v1/users/me',
            'users_update_user' => 'PUT /api/v1/users/{user_id}',
            'users_delete_user' => 'DELETE /api/v1/users/{user_id}',
            'backup_create' => 'POST /api/v1/backup/create',
            'backup_restore' => 'POST /api/v1/backup/restore',
            'backup_list' => 'GET /api/v1/backup/list',
            'backup_delete' => 'DELETE /api/v1/backup/delete/{filename}',
            'backup_download' => 'GET /api/v1/backup/download/{filename}',
            'backup_logs' => 'GET /api/v1/backup/logs',
            'backup_status' => 'GET /api/v1/backup/status',
            'admin_settings_index' => 'GET /api/v1/admin/settings',
            'admin_settings_get_group' => 'GET /api/v1/admin/settings/group-{group}',
            'admin_settings_get_setting' => 'GET /api/v1/admin/settings/key-{key}',
            'admin_settings_update_setting' => 'PUT /api/v1/admin/settings/key-{key}',
            'admin_settings_update_settings' => 'PATCH /api/v1/admin/settings',
            'admin_settings_reset_all' => 'GET /api/v1/admin/settings/reset/all',
            'admin_settings_reset_group' => 'GET /api/v1/admin/settings/reset/group-{group}',
            'admin_settings_reset_setting' => 'GET /api/v1/admin/settings/reset/key-{key}',
            'admin_debug_clear' => 'POST /api/v1/admin/debug/clear'
        ]
    ];

    // output response
    output_json($response);
});
