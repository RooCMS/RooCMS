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
 * Class Module_Auth
 */
class Module_Tag_Cloud extends Modules {

	# Title
	public $title = "Облако Тегов";

	# buffer out
	protected $out = "";


	/**
	 * Start
	 */
	protected function begin() {

		global $tags, $tpl, $smarty;

		# get tag listen
		$taglist = $tags->list_tags();

		$ct = count($taglist);

		if($ct != 0) {
			# set min/max
			$min = $taglist[$ct-1]['amount'];
			$max = $taglist[0]['amount'];

			$minsize = 50;
			$maxsize = 100;

			foreach ($taglist AS $key=>$value) {
				if($min == $max) {
					$fontsize = round(($maxsize - $minsize)/2+$minsize);
				}
				else {
					$fontsize = round(((($maxsize-$minsize)/$max)*$value['amount'])+$minsize);
				}

				$ukey = urlencode($value['title']);

				$taglist[$key] = array('title'=>$value['title'], 'amount'=>$value['amount'], 'fontsize'=>$fontsize, 'ukey'=>$ukey);
			}

			# template
			$smarty->assign("tags", $taglist);
			$this->out = $tpl->load_template("module/tag_cloud", true);
		}
	}
}
