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
    define('_SITEROOT', substr(__DIR__, 0, -7));
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
 * Include helpers
 */
require_once _ROOCMS."/helpers/functions.php";
require_once _ROOCMS."/helpers/sanitize.php";


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
        'ControllerFactory'         => _CLASS . '/interface_controllerFactory.php',
        'DefaultControllerFactory'  => _CLASS . '/class_defaultControllerFactory.php',
        'MiddlewareFactory'         => _CLASS . '/interface_middlewareFactory.php',
        'DefaultMiddlewareFactory'  => _CLASS . '/class_defaultMiddlewareFactory.php',
        'ApiHandler'                => _CLASS . '/class_apiHandler.php',
        'Auth'                      => _CLASS . '/class_auth.php',
        'Role'                      => _CLASS . '/class_role.php',
        'Shteirlitz'                => _CLASS . '/class_shteirlitz.php'
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
 * Initialize db
 */
$db = new Db();



// Health check for database connection
if(DEBUGMODE) {
    $health = $db->get_health_status();
    if($health['status'] === 'unhealthy') {
        error_log('Database health check failed: ' . json_encode($health, JSON_UNESCAPED_UNICODE));
    }
}