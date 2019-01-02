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
 * Class Module_Auth
 */
class Module_Last_feed extends Modules {

	# Title
	public $title = "Последние публикации";

	# buffer out
	protected $out = "";


	/**
	 * Start
	 */
	protected function begin() {

		global $db, $img, $parse, $tpl, $smarty;

		$feeds = [];
		$q = $db->query("SELECT f.id, s.alias, f.title, f.date_publications FROM ".PAGES_FEED_TABLE." AS f
					LEFT JOIN ".STRUCTURE_TABLE." AS s ON (s.id = f.sid)
					WHERE f.date_publications <= '".time()."' AND (f.date_end_publications = '0' || f.date_end_publications > '".time()."') AND f.status='1' 
					ORDER BY f.date_publications DESC LIMIT 0,4");
		while($row = $db->fetch_assoc($q)) {
			$row['datepub']    = $parse->date->unix_to_rus($row['date_publications'],true);
			$row['date']       = $parse->date->unix_to_rus_array($row['date_publications']);

			$row['image']      = $img->load_images("feeditemid=".$row['id']."", 0, 1);

			$feeds[$row['id']] = $row;
		}

		# template
		$smarty->assign("feeds", $feeds);
		$this->out .= $tpl->load_template("module/last_feed", true);
	}
}
