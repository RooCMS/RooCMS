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


//#########################################################
//	Tests are for CLI only
//---------------------------------------------------------
if(PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('403:Access denied');
}
//#########################################################


/**
 * Minimal bootstrap for unit tests
 * Does NOT load site configs or connect to the database: only pure classes and helpers
 */
const RooCMS = true;

define('_SITEROOT', dirname(__DIR__));
const _ROOCMS   = _SITEROOT.'/roocms';
const _MODULES  = _ROOCMS.'/modules';
const _SERVICES = _ROOCMS.'/services';
const _HELPERS  = _ROOCMS.'/helpers';


/**
 * Same stub as in roocms/bootstrap.php (protect checks in the included files call it)
 */
function roocms_protect(): never {
    http_response_code(403);
    exit('403:Access denied');
}


/**
 * Helpers and pure classes under test
 */
require_once _HELPERS.'/sanitize.php';
require_once _MODULES.'/class_role.php';
require_once _MODULES.'/class_apiHandler.php';
require_once _MODULES.'/di/interface_controllerFactory.php';
require_once _MODULES.'/di/interface_middlewareFactory.php';
require_once _MODULES.'/di/class_dependencyContainer.php';
require_once _SERVICES.'/userValidation.php';
