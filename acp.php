<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2023 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */


/**
* Init Administrator Control Paneel
*/
const ACP = true;
define('_SITEROOT', dirname(__FILE__));
require_once _SITEROOT."/roocms/init.php";
require_once INIT_ACP;

/**
* Output
*
* @return mixed frontend html
*/
$tpl->out();
