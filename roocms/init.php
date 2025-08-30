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
    define('_SITEROOT', str_ireplace(DIRECTORY_SEPARATOR."roocms", "", dirname(__FILE__)));
}


/**
 * list of configs
 */
$configs = [
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
 * Include functions
 */
require_once _ROOCMS."/helpers/functions.php";


/**
 * RooCMS class loader
 */
spl_autoload_register(function(string $class_name) {
    
    // allowed classes
    $allowed_classes = [
        'Debugger'     => _CLASS . '/class_debugger.php',
        'DebugLog'     => _CLASS . '/trait_debugLog.php'
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
 * include debug helper functions
 */
require_once _ROOCMS."/helpers/debug.php";

