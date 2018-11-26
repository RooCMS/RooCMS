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


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class SitePageHTML
 */
class PageHTML {

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

		global $db, $structure, $parse, $files, $img, $tpl, $smarty;

		$q = $db->query("SELECT content FROM ".PAGES_HTML_TABLE." WHERE sid='".$structure->page_id."'");
		$data = $db->fetch_assoc($q);

		$data['content'] = $parse->text->html($data['content']);

		# load attached images
		$images = $img->load_images("pagesid=".$structure->page_id);
		$smarty->assign("images", $images);

		# load attached files
		$attachfile = $files->load_files("pagesid=".$structure->page_id);
		$smarty->assign("attachfile", $attachfile);

		$smarty->assign("content", $data['content']);

		$tpl->load_template("page_html");
	}
}