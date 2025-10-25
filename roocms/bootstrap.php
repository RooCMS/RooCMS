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
    'csp.cfg.php',      // content security policy
    'set.cfg.php',      // system settings
    'site.cfg.php',     // site settings
    'defpaths.php',     // constants for site paths
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