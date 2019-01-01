<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2019 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */


/**
 * Init RooCMS
 */
define('_SITEROOT', dirname(__FILE__));
require_once _SITEROOT."/roocms/init.php";

require_once(_LIB."/phpqrcode.php");


if(isset($get->_url)) {
	$get->_url = str_ireplace('%and%', '&', $get->_url);
	$qrcontent = _DOMAIN.$get->_url;
	QRcode::png($qrcontent);
}

if(isset($get->_tel)) {
	$get->_url = str_ireplace(' ', '', $get->_tel);
	$qrcontent = "tel:".$get->_tel;
	QRcode::png($qrcontent,false, QR_ECLEVEL_L, 4, 0);
}