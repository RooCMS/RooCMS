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
 * define root roocms path
 */
if(!defined('_SITEROOT')) {
    define('_SITEROOT', dirname(__DIR__));
}

/**
 * list of configs
 */
$configs = [
    'csp.cfg.php',  // content security policy
    'set.cfg.php',  // system settings
    'config.php',   // site settings
    'defines.php',  // site constants
];


/**
 * Include configs
 */
foreach($configs as $config) {
    if(file_exists(_SITEROOT."/roocms/config/".$config)) {
        require_once _SITEROOT."/roocms/config/".$config;
    }
}


/**
 * list of helpers
 */
$helpers = [
    'functions.php',  // functions
    'sanitize.php',   // sanitize helpers
    'output.php',     // output helpers
];


/**
 * Include helpers
 */
foreach($helpers as $helper) {
    if(file_exists(_ROOCMS."/helpers/".$helper)) {
        require_once _ROOCMS."/helpers/".$helper;
    }
}


/**
 * RooCMS class loader
 */
spl_autoload_register(function(string $class_name) {
    
    // allowed classes
    $allowed_classes = [
        'Debugger'                  => _CLASS . '/class_debugger.php',
        'DebugLog'                  => _CLASS . '/trait_debugLog.php',
        'Db'                        => _CLASS . '/class_db.php',
        'DbConnect'                 => _CLASS . '/class_dbConnect.php',
        'DbQueryBuilder'            => _CLASS . '/class_dbQueryBuilder.php',
        'DbExtends'                 => _CLASS . '/trait_dbExtends.php',
        'DbMigrator'                => _CLASS . '/class_dbMigrator.php',
        'DbBackuper'                => _CLASS . '/class_dbBackuper.php',
        'DbLogger'                  => _CLASS . '/trait_dbLogger.php',
        'SiteSettings'              => _CLASS . '/class_siteSettings.php',
        'Themes'                    => _CLASS . '/class_themes.php',
        'ThemeConfig'               => _CLASS . '/class_themeConfig.php',
        'TemplateRenderer'          => _CLASS . '/interface_templateRenderer.php',
        'ThemeConfigInterface'      => _CLASS . '/interface_themeConfig.php',
        'TemplateRendererPhp'       => _CLASS . '/class_templateRendererPhp.php',
        'TemplateRendererHtml'      => _CLASS . '/class_templateRendererHtml.php',
        'Mailer'                    => _CLASS . '/class_mailer.php',
        'Auth'                      => _CLASS . '/class_auth.php',
        'Role'                      => _CLASS . '/class_role.php',
        'User'                      => _CLASS . '/class_user.php',
        'Shteirlitz'                => _CLASS . '/class_shteirlitz.php',
        'ApiHandler'                => _CLASS . '/class_apiHandler.php',
        'DependencyContainer'       => _CLASS . '/class_dependencyContainer.php',
        'ControllerFactory'         => _CLASS . '/interface_controllerFactory.php',
        'DefaultControllerFactory'  => _CLASS . '/class_defaultControllerFactory.php',
        'MiddlewareFactory'         => _CLASS . '/interface_middlewareFactory.php',
        'DefaultMiddlewareFactory'  => _CLASS . '/class_defaultMiddlewareFactory.php',
        'SiteSettingsService'       => _SERVICES . '/siteSettings.php',
        'UserService'               => _SERVICES . '/user.php',
        'AuthenticationService'     => _SERVICES . '/authentication.php',
        'RegistrationService'       => _SERVICES . '/registration.php',
        'UserValidationService'     => _SERVICES . '/userValidation.php',
        'EmailService'              => _SERVICES . '/email.php',
        'UserRecoveryService'       => _SERVICES . '/userRecovery.php',
        'BackupService'             => _SERVICES . '/backup.php'
    ];
   
    // try to load the class
    if(isset($allowed_classes[$class_name])) {
        if(file_exists($allowed_classes[$class_name])) {
            require_once $allowed_classes[$class_name];         
            return true;
        }
    }
    
    return false;
});

/**
 * include debug and run debugging project
 */
require_once _ROOCMS."/helpers/debug.php";

/**
 * Initialize Dependency Container
 */
$container = new DependencyContainer();

// Register database connection first
$container->register(DbConnect::class, function() {
    return new DbConnect();
}, true); // Singleton

// Register database service with proper DI
$container->register(Db::class, function(DependencyContainer $c) {
    return new Db($c->get(DbConnect::class));
}, true); // Singleton


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
    exit('Database initialization error.');
}

// register debugger if available
if($debug instanceof Debugger) {
    $container->register(Debugger::class, fn() => $debug, true);
}

// register services
$container->register(Auth::class, Auth::class, true); // Singleton
$container->register(User::class, User::class, true); // Singleton
$container->register(Role::class, Role::class, true); // Singleton
$container->register(UserService::class, UserService::class, true); // Singleton
$container->register(SiteSettings::class, SiteSettings::class, true); // Singleton
$container->register(SiteSettingsService::class, SiteSettingsService::class, true); // Singleton
$container->register(Mailer::class, Mailer::class, true); // Singleton
$container->register(DbBackuper::class, DbBackuper::class, true); // Singleton
$container->register(BackupService::class, BackupService::class, true); // Singleton
$container->register(AuthenticationService::class, AuthenticationService::class, true); // Singleton
$container->register(RegistrationService::class, RegistrationService::class, true); // Singleton
$container->register(EmailService::class, EmailService::class, true); // Singleton
$container->register(UserRecoveryService::class, UserRecoveryService::class, true); // Singleton
$container->register(UserValidationService::class, UserValidationService::class, true); // Singleton


// Template renderers and themes
$container->register(TemplateRendererPhp::class, TemplateRendererPhp::class, true);
$container->register(TemplateRendererHtml::class, TemplateRendererHtml::class, true);
$container->register(Themes::class, function(DependencyContainer $c) {
	// Inject renderers via DI, themes dir defaults to 'themes'
	return new Themes(
        $c->get(SiteSettings::class),
		$c->get(TemplateRendererPhp::class),
		$c->get(TemplateRendererHtml::class),
		'themes'
	);
}, true);


// Health check for database connection
if(DEBUGMODE) {
    $health = $db->get_health_status();
    if($health['status'] === 'unhealthy') {
        error_log('Database health check failed: ' . json_encode($health, JSON_UNESCAPED_UNICODE));
    }
}