<?php
/**
 *   RooCMS - Open Source Free Content Managment System
 *   Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 *   Contacts: <info@roocms.com>
 *
 *   You should have received a copy of the GNU General Public License v3
 *   along with this program.  If not, see http://www.gnu.org/licenses/
 */

/**
 * @package     RooCMS
 * @subpackage  Module
 * @author      alex Roosso
 * @copyright   2010-2019 (c) RooCMS
 * @link        http://www.roocms.com
 * @version     1.1.1
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


/**
 * Class Module_Auth
 */
class Module_Tag_Cloud {

	# Название
	public $title = "Облако Тегов";

	# buffer out
	private $out = "";


	/**
	 * Start
	 */
	public function __construct() {

		global $tags, $tpl, $smarty;

		# get tag listen
		$taglist = $tags->list_tags();

		$ct = count($taglist);

		if($ct != 0) {
			# set min/max
			$min = $taglist[$ct-1]['amount'];
			$max = $taglist[0]['amount'];

			$minsize = 45;
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

			shuffle($taglist);

			$smarty->assign("tags", $taglist);
			$this->out .= $tpl->load_template("module_tagcloud", true);

			# finish
			echo $this->out;
		}
	}
}


/**
 * Init class
 */
$module_tagcloud = new Module_Tag_Cloud;