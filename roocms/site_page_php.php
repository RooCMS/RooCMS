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
 * @package      RooCMS
 * @subpackage   Frontend
 * @author       alex Roosso
 * @copyright    2010-2019 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.0.2
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
 * Class SitePagePHP
 */
class SitePagePHP {

	/**
	* Initialisation
	*
	*/
	public function __construct() {
		$this->load_content();
	}


	/**
	* Load Content
	*
	*/
	public function load_content() {

		global $db, $structure, $parse, $tpl, $smarty;

		$q = $db->query("SELECT content FROM ".PAGES_PHP_TABLE." WHERE sid='".$structure->page_id."'");
		$data = $db->fetch_assoc($q);

		ob_start();
			eval($parse->text->html($data['content']));
			$output = ob_get_contents();
		ob_end_clean();

		$smarty->assign("content", $output);

		$tpl->load_template("page_php");
	}
}