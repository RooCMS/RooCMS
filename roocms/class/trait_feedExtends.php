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


trait FeedExtends {

	/**
	 * Function returns condition for sorting result query from database.
	 *
	 * @param string $rule - rule for sorting
	 *
	 * @return string
	 */
	protected function feed_order(string $rule) {

		switch($rule) {
			case 'title_asc':
				$order = "title ASC, date_publications DESC, id DESC";
				break;

			case 'title_desc':
				$order = "title DESC, date_publications DESC, id DESC";
				break;

			case 'manual_sorting':
				$order = "sort ASC, date_publications DESC, date_create DESC, id DESC";
				break;

			default: // case 'datepublication'
				$order = "date_publications DESC, date_create DESC, date_update DESC, id DESC";
				break;
		}

		return $order;
	}
}
