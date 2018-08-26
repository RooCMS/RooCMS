<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */

/**
 * @package     RooCMS
 * @subpackage	Library
 * @author      alex Roosso
 * @copyright   2010-2019 (c) RooCMS
 * @link        http://www.roocms.com
 * @version     1.0.1
 * @since       $date$
 * @license     http://www.gnu.org/licenses/gpl-3.0.html
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################

require_once _LIB."/phpqrcode/qrlib.php";

/*
$QRcode		= new QRcode();
$QRtools	= new QRtools;

$smarty->assign("QRcode",	$QRcode);
$smarty->assign("QRtools",	$QRtools);
*/