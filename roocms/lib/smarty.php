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


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


/**
* Set const smarty folder
*/
const SMARTY_DIR = _SMARTY.'/';

/**
* Require Smarty
*/
require_once _SMARTY."/Smarty.class.php";

/**
* Init Smarty
*
* @var Smarty
*/
$smarty = new Smarty();

/**
 * Add folder users plugins for smarty
 */
$smarty->addPluginsDir(_LIB.'/smarty_plugins');
