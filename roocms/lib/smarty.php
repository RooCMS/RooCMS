<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 * Contacts: <info@roocms.com>
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */

/**
* @package      RooCMS
* @subpackage	Library
* @author       alex Roosso
* @copyright    2010-2019 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.1
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
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
define('SMARTY_DIR', _SMARTY.'/');

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