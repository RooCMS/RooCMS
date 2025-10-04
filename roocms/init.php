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

/**
 * Security check function - works without classes for early file protection
 * This function can be called before any autoloading or class initialization
 *
 * @return never
 */
function roocms_protect(): never {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('403:Access denied');
}

//#########################################################
//	Protect
//---------------------------------------------------------
if(!defined('RooCMS')) {roocms_protect();}
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
    if(file_exists(_HELPERS."/".$helper)) {
        require_once _HELPERS."/".$helper;
    }
}


/**
 * RooCMS class loader
 */
spl_autoload_register(function(string $class_name) {
    
    // allowed classes
    $allowed_classes = [
        'Debugger'                  => _MODULES . '/class_debugger.php',
        'DebugLog'                  => _MODULES . '/trait_debugLog.php',
        'Db'                        => _MODULES . '/db/class_db.php',
        'DbConnect'                 => _MODULES . '/db/class_dbConnect.php',
        'DbQueryBuilder'            => _MODULES . '/db/class_dbQueryBuilder.php',
        'DbExtends'                 => _MODULES . '/db/trait_dbExtends.php',
        'DbLogger'                  => _MODULES . '/db/trait_dbLogger.php',
        'DbMigrator'                => _MODULES . '/db/class_dbMigrator.php',
        'DbBackuper'                => _MODULES . '/db/class_dbBackuper.php',
        'DbBackuperExtends'         => _MODULES . '/db/trait_dbBackuperExtends.php',
        'DbBackuperMSQL'            => _MODULES . '/db/trait_dbBackuperMSQL.php',
        'DbBackuperPSQL'            => _MODULES . '/db/trait_dbBackuperPSQL.php',
        'DbBackuperFB'              => _MODULES . '/db/trait_dbBackuperFB.php',
        'Request'                   => _MODULES . '/class_request.php',
        'SiteSettings'              => _MODULES . '/class_siteSettings.php',
        'Themes'                    => _MODULES . '/ui/class_themes.php',
        'ThemeConfig'               => _MODULES . '/ui/class_themeConfig.php',
        'TemplateRenderer'          => _MODULES . '/ui/interface_templateRenderer.php',
        'ThemeConfigInterface'      => _MODULES . '/ui/interface_themeConfig.php',
        'TemplateRendererPhp'       => _MODULES . '/ui/class_templateRendererPhp.php',
        'TemplateRendererHtml'      => _MODULES . '/ui/class_templateRendererHtml.php',
        'Mailer'                    => _MODULES . '/class_mailer.php',
        'Auth'                      => _MODULES . '/class_auth.php',
        'Role'                      => _MODULES . '/class_role.php',
        'User'                      => _MODULES . '/class_user.php',
        'GD'                        => _MODULES . '/class_gd.php',
        'GDExtends'                 => _MODULES . '/trait_gdExtends.php',
        'Files'                     => _MODULES . '/class_files.php',
        'MediaImage'                => _MODULES . '/trait_mediaImage.php',
        'MediaDoc'                  => _MODULES . '/trait_mediaDoc.php',
        'MediaVideo'                => _MODULES . '/trait_mediaVideo.php',
        'MediaAudio'                => _MODULES . '/trait_mediaAudio.php',
        'MediaArch'                 => _MODULES . '/trait_mediaArch.php',
        'Shteirlitz'                => _MODULES . '/class_shteirlitz.php',
        'ApiHandler'                => _MODULES . '/class_apiHandler.php',
        'DependencyContainer'       => _MODULES . '/di/class_dependencyContainer.php',
        'ControllerFactory'         => _MODULES . '/di/interface_controllerFactory.php',
        'DefaultControllerFactory'  => _MODULES . '/di/class_defaultControllerFactory.php',
        'MiddlewareFactory'         => _MODULES . '/di/interface_middlewareFactory.php',
        'DefaultMiddlewareFactory'  => _MODULES . '/di/class_defaultMiddlewareFactory.php',
        'SiteSettingsService'       => _SERVICES . '/siteSettings.php',
        'UserService'               => _SERVICES . '/user.php',
        'AuthenticationService'     => _SERVICES . '/authentication.php',
        'RegistrationService'       => _SERVICES . '/registration.php',
        'UserValidationService'     => _SERVICES . '/userValidation.php',
        'EmailService'              => _SERVICES . '/email.php',
        'UserRecoveryService'       => _SERVICES . '/userRecovery.php',
        'FilesService'              => _SERVICES . '/files.php',
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
require_once _HELPERS."/debug.php";

/**
 * Initialize Dependency Container
 */
$container = new DependencyContainer();

/** 
 * Register database connection first
 */
$container->register(DbConnect::class, function() {
    return new DbConnect();
}, true);

/** 
 * Register database service with proper DI
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
    exit('Database initialization error.');
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

/** 
 * Register dependencies
 */
$container->register(Auth::class, Auth::class, true);
$container->register(User::class, User::class, true);
$container->register(Role::class, Role::class, true);
$container->register(UserService::class, UserService::class, true);
$container->register(Mailer::class, Mailer::class, true);
$container->register(GD::class, GD::class, true);
$container->register(Files::class, Files::class, true);
$container->register(FilesService::class, FilesService::class, true);
$container->register(DbBackuper::class, DbBackuper::class, true);
$container->register(BackupService::class, BackupService::class, true);
$container->register(AuthenticationService::class, AuthenticationService::class, true);
$container->register(RegistrationService::class, RegistrationService::class, true);
$container->register(EmailService::class, EmailService::class, true);
$container->register(UserRecoveryService::class, UserRecoveryService::class, true);
$container->register(UserValidationService::class, UserValidationService::class, true);

/**
 * Register template renderers and themes
 */
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


/** 
 * Health check for database connection
 */
if(DEBUGMODE) {
    $health = $db->get_health_status();
    if($health['status'] === 'unhealthy') {
        error_log('Database health check failed: ' . json_encode($health, JSON_UNESCAPED_UNICODE));
    }
}