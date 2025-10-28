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

/**
 * include bootstrap
 */
require_once 'bootstrap.php';

//#########################################################
//	Protect
//---------------------------------------------------------
if(!defined('RooCMS')) {roocms_protect();}
//#########################################################



/**
 * list of configs
 */
$dbconfigs = [
    'db.cfg.php',       // database settings
    'deftables.php',    // constants for site tables
];

/**
 * Include configs
 */
foreach($dbconfigs as $config) {
    if(file_exists(_SITEROOT."/roocms/config/".$config)) {
        require_once _SITEROOT."/roocms/config/".$config;
    }
}


/** 
 * Register database connection first
 */
$container->register(DbConnect::class, fn() => new DbConnect(), true);

/** 
 * Register database models
 */
$container->register(Db::class, function(DependencyContainer $c) {
    return new Db($c->get(DbConnect::class));
}, true);


/**
 * Initialize db for backward compatibility
 */
try {
    $db = $container->get(Db::class);
} catch (Throwable $e) {
    // Log and provide clear message in debug mode
    error_log('Database initialization failed: ' . $e->getMessage());
    if(defined('DEBUGMODE') && DEBUGMODE) {
        throw $e;
    }
    // graceful fallback: stop initialization
    getout(500, 'Database initialization error.');
}

/** 
 * Register debugger if available
 */
if($debug instanceof Debugger) {
    $container->register(Debugger::class, fn() => $debug, true);
}

/**
 * Register request
 */
$container->register(Request::class, Request::class, true);

/**
 * Register site settings
 */
$container->register(SiteSettings::class, fn() => new SiteSettings($db), true);
$container->register(SiteSettingsService::class, SiteSettingsService::class, true);
$container->register(SiteSettingsManageService::class, SiteSettingsManageService::class, true);

/** 
 * Register dependencies
 */
$container->register(Auth::class, Auth::class, true);
$container->register(User::class, User::class, true);
$container->register(Role::class, Role::class, true);
$container->register(ModerateService::class, ModerateService::class, true);
$container->register(UserService::class, UserService::class, true);
$container->register(UserManageService::class, UserManageService::class, true);
$container->register(Structure::class, Structure::class, true);
$container->register(Mailer::class, Mailer::class, true);
$container->register(GD::class, GD::class, true);
$container->register(Files::class, Files::class, true);
$container->register(FilesService::class, FilesService::class, true);
$container->register(DbBackuper::class, DbBackuper::class, true);
$container->register(BackupService::class, BackupService::class, true);
$container->register(RegistrationService::class, RegistrationService::class, true);
$container->register(UserRecoveryService::class, UserRecoveryService::class, true);
$container->register(UserValidationService::class, UserValidationService::class, true);
$container->register(UserListService::class, UserListService::class, true);
$container->register(EmailService::class, EmailService::class, true);
$container->register(AuthenticationService::class, AuthenticationService::class, true);
$container->register(StructureService::class, StructureService::class, true);
$container->register(StructureManageService::class, StructureManageService::class, true);
$container->register(DebugService::class, DebugService::class, true);


/** 
 * Health check for database connection
 */
if(DEBUGMODE) {
    $health = $db->get_health_status();
    if($health['status'] === 'unhealthy') {
        error_log('Database health check failed: ' . json_encode($health, JSON_UNESCAPED_UNICODE));
    }
}