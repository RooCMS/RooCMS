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

/**
 * define root roocms path
 */
if(!defined('_SITEROOT')) {
    define('_SITEROOT', dirname(__FILE__, 2));
}

/**
 * list of configs
 */
$configs = [
    'site.cfg.php',     // site settings
    'defpaths.php',     // constants for site paths
    'set.cfg.php',      // system settings
    'csp.cfg.php',      // content security policy
    'defroocms.php',    // constants for roocms versions
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
        'DbHelpers'                 => _MODULES . '/db/trait_dbHelpers.php',
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
        'Structure'                 => _MODULES . '/class_structure.php',
        'Themes'                    => _MODULES . '/ui/class_themes.php',
        'ThemeConfig'               => _MODULES . '/ui/class_themeConfig.php',
        'TemplateRenderer'          => _MODULES . '/ui/interface_templateRenderer.php',
        'ThemeConfigInterface'      => _MODULES . '/ui/interface_themeConfig.php',
        'TemplateRendererPhp'       => _MODULES . '/ui/class_templateRendererPhp.php',
        'TemplateRendererHtml'      => _MODULES . '/ui/class_templateRendererHtml.php',
        'TemplatePathResolver'      => _MODULES . '/ui/trait_templatePathResolver.php',
        'Mailer'                    => _MODULES . '/class_mailer.php',
        'Auth'                      => _MODULES . '/class_auth.php',
        'Role'                      => _MODULES . '/class_role.php',
        'User'                      => _MODULES . '/class_user.php',
        'GD'                        => _MODULES . '/class_gd.php',
        'GDExtends'                 => _MODULES . '/trait_gdExtends.php',
        'Files'                     => _MODULES . '/class_files.php',
        'FilesExtends'              => _MODULES . '/trait_filesExtends.php',
        'FileManagerImage'          => _MODULES . '/trait_fileManagerImage.php',
        'FileManagerDoc'            => _MODULES . '/trait_fileManagerDoc.php',
        'FileManagerVideo'          => _MODULES . '/trait_fileManagerVideo.php',
        'FileManagerAudio'          => _MODULES . '/trait_fileManagerAudio.php',
        'FileManagerArch'           => _MODULES . '/trait_fileManagerArch.php',
        'Shteirlitz'                => _MODULES . '/class_shteirlitz.php',
        'ApiHandler'                => _MODULES . '/class_apiHandler.php',
        'DependencyContainer'       => _MODULES . '/di/class_dependencyContainer.php',
        'ControllerFactory'         => _MODULES . '/di/interface_controllerFactory.php',
        'DefaultControllerFactory'  => _MODULES . '/di/class_defaultControllerFactory.php',
        'MiddlewareFactory'         => _MODULES . '/di/interface_middlewareFactory.php',
        'DefaultMiddlewareFactory'  => _MODULES . '/di/class_defaultMiddlewareFactory.php',
        'SiteSettingsService'       => _SERVICES . '/siteSettings.php',
        'SiteSettingsManageService' => _SERVICES . '/siteSettingsManage.php',
        'UserService'               => _SERVICES . '/user.php',
        'UserManageService'         => _SERVICES . '/userManage.php',
        'AuthenticationService'     => _SERVICES . '/authentication.php',
        'RegistrationService'       => _SERVICES . '/registration.php',
        'UserValidationService'     => _SERVICES . '/userValidation.php',
        'UserRecoveryService'       => _SERVICES . '/userRecovery.php',
        'UserListService'           => _SERVICES . '/userList.php',
        'EmailService'              => _SERVICES . '/email.php',
        'FilesService'              => _SERVICES . '/files.php',
        'FilesCommonService'        => _SERVICES . '/filesCommon.php',
        'StructureService'          => _SERVICES . '/structure.php',
        'StructureManageService'    => _SERVICES . '/structureManage.php',
        'StructureCommonService'    => _SERVICES . '/structureCommon.php',
        'ModerateService'           => _SERVICES . '/moderate.php',
        'DebugService'              => _SERVICES . '/debug.php',
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