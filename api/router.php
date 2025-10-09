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
//	Protect
//---------------------------------------------------------
if(!defined('RooCMS')) {roocms_protect();}
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
        'MediaController'           => _API . '/v1/controller_media.php',
        'StructureController'       => _API . '/v1/controller_structure.php',
        'AdminStructureController'  => _API . '/v1/controller_adminStructure.php',
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
$container->register(CspController::class, CspController::class);
$container->register(HealthController::class, HealthController::class);
$container->register(UsersController::class, UsersController::class);
$container->register(AuthController::class, AuthController::class);
$container->register(AdminSettingsController::class, AdminSettingsController::class);
$container->register(BackupController::class, BackupController::class);
$container->register(DebugController::class, DebugController::class);
$container->register(MediaController::class, MediaController::class);
$container->register(StructureController::class, StructureController::class);
$container->register(AdminStructureController::class, AdminStructureController::class);

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

// Media endpoints
$api->get('/v1/media', 'MediaController@index'); // List media files (public with filters)
$api->get('/v1/media/{id}', 'MediaController@show'); // Get media info (public)
$api->get('/v1/media/{id}/file', 'MediaController@download'); // Download file (public)
$api->post('/v1/media/upload', 'MediaController@upload', ['AuthMiddleware']); // Upload file (authenticated)
$api->put('/v1/media/{id}', 'MediaController@update', ['AuthMiddleware']); // Update metadata (authenticated)
$api->delete('/v1/media/{id}', 'MediaController@delete', ['AuthMiddleware']); // Delete file (authenticated)

// Structure endpoints (public)
$api->get('/v1/structure/tree', 'StructureController@tree'); // Get site structure tree
$api->get('/v1/structure/page/{id}', 'StructureController@show_page'); // Get page by ID
$api->get('/v1/structure/page/slug/{slug}', 'StructureController@show_page_by_slug'); // Get page by slug
$api->get('/v1/structure/navigation', 'StructureController@navigation'); // Get navigation menu
$api->get('/v1/structure/breadcrumbs/{id}', 'StructureController@breadcrumbs'); // Get breadcrumbs by ID
$api->get('/v1/structure/breadcrumbs/slug/{slug}', 'StructureController@breadcrumbs_by_slug'); // Get breadcrumbs by slug
$api->get('/v1/structure/seo/{id}', 'StructureController@seo'); // Get SEO metadata by ID
$api->get('/v1/structure/seo/slug/{slug}', 'StructureController@seo_by_slug'); // Get SEO metadata by slug
$api->get('/v1/structure/current', 'StructureController@current'); // Get current page info
$api->get('/v1/structure/search', 'StructureController@search'); // Search pages
$api->get('/v1/structure/status/{status}', 'StructureController@pages_by_status'); // Get pages by status

// Admin Structure endpoints (require authentication + admin role)
$api->get('/v1/admin/structure', 'AdminStructureController@index', ['AuthMiddleware', 'RoleMiddleware@admin_access']); // Get all pages
$api->get('/v1/admin/structure/{id}', 'AdminStructureController@show', ['AuthMiddleware', 'RoleMiddleware@admin_access']); // Get page by ID
$api->post('/v1/admin/structure', 'AdminStructureController@create', ['AuthMiddleware', 'RoleMiddleware@admin_access']); // Create new page
$api->put('/v1/admin/structure/{id}', 'AdminStructureController@update', ['AuthMiddleware', 'RoleMiddleware@admin_access']); // Update page
$api->delete('/v1/admin/structure/{id}', 'AdminStructureController@delete', ['AuthMiddleware', 'RoleMiddleware@admin_access']); // Delete page
$api->patch('/v1/admin/structure/{id}/status', 'AdminStructureController@change_status', ['AuthMiddleware', 'RoleMiddleware@admin_access']); // Change status
$api->put('/v1/admin/structure/reorder', 'AdminStructureController@reorder', ['AuthMiddleware', 'RoleMiddleware@admin_access']); // Reorder pages

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
            'media_index' => 'GET /api/v1/media',	
            'media_show' => 'GET /api/v1/media/{id}',
            'media_download' => 'GET /api/v1/media/{id}/file',
            'media_upload' => 'POST /api/v1/media/upload',
            'media_update' => 'PUT /api/v1/media/{id}',
            'media_delete' => 'DELETE /api/v1/media/{id}',
            'structure_tree' => 'GET /api/v1/structure/tree',
            'structure_page' => 'GET /api/v1/structure/page/{id}',
            'structure_page_by_slug' => 'GET /api/v1/structure/page/slug/{slug}',
            'structure_navigation' => 'GET /api/v1/structure/navigation',
            'structure_breadcrumbs' => 'GET /api/v1/structure/breadcrumbs/{id}',
            'structure_breadcrumbs_by_slug' => 'GET /api/v1/structure/breadcrumbs/slug/{slug}',
            'structure_seo' => 'GET /api/v1/structure/seo/{id}',
            'structure_seo_by_slug' => 'GET /api/v1/structure/seo/slug/{slug}',
            'structure_current' => 'GET /api/v1/structure/current',
            'structure_search' => 'GET /api/v1/structure/search',
            'structure_pages_by_status' => 'GET /api/v1/structure/status/{status}',
            'admin_structure_index' => 'GET /api/v1/admin/structure',
            'admin_structure_show' => 'GET /api/v1/admin/structure/{id}',
            'admin_structure_create' => 'POST /api/v1/admin/structure',
            'admin_structure_update' => 'PUT /api/v1/admin/structure/{id}',
            'admin_structure_delete' => 'DELETE /api/v1/admin/structure/{id}',
            'admin_structure_change_status' => 'PATCH /api/v1/admin/structure/{id}/status',
            'admin_structure_reorder' => 'PUT /api/v1/admin/structure/reorder',
            'admin_backup_create' => 'POST /api/v1/backup/create',
            'admin_backup_restore' => 'POST /api/v1/backup/restore',
            'admin_backup_list' => 'GET /api/v1/backup/list',
            'admin_backup_delete' => 'DELETE /api/v1/backup/delete/{filename}',
            'admin_backup_download' => 'GET /api/v1/backup/download/{filename}',
            'admin_backup_logs' => 'GET /api/v1/backup/logs',
            'admin_backup_status' => 'GET /api/v1/backup/status',
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
