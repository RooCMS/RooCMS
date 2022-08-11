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


trait ParserValidData {

	/**
	 * Validate email
	 *
	 * @param string $email - email
	 *
	 * @return bool
	 */
	public function valid_email(string $email) {

		$pattern = '/^[\.\-_A-Za-z0-9]+?@[\.\-A-Za-z0-9]+?\.[A-Za-z0-9]{2,6}$/';

		return (bool) preg_match($pattern, trim($email));

	}


	/**
	 * Validate phone
	 *
	 * will be confirmed:
	 *      Code country with plus and none
	 *      Without code country
	 *      City code from 3 to 5 num with hooks and without hook
	 *      Phone number from 5 to 7 number
	 *      special symbols (hyphen, spaces) counted but may be skipped
	 * Example:
	 *      +1 (5555) 555-55-55
	 *      +1 5555 5555555
	 *      55555555555
	 *
	 * @param mixed $phone - phone number
	 *
	 * @return bool
	 */
	public function valid_phone($phone){

		$pattern = "/^[\+]?[0-9]?(\s)?(\-)?(\s)?(\()?[0-9]{3,5}(\))?(\s)?(\-)?(\s)?[0-9]{1,3}(\s)?(\-)?(\s)?[0-9]{2}(\s)?(\-)?(\s)?[0-9]{2}\Z/";

		return (bool) preg_match($pattern, trim($phone));

	}
}
