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
 * Class Blocks
 */
class Blocks {

	/**
	 * Load block
	 *
	 * @param int|string $buid - alias or id
	 *
	 * @return false|mixed|string $output
	 */
	public function load($buid) {

		global $db, $parse, $files, $img, $smarty, $tpl;
                static $use_blocks = [];

                $output = "";

		$buid = str_ireplace("'", "", $buid);
                if(!array_key_exists($buid, $use_blocks)) {

			$buid = str_ireplace(array('\\','"','&quot;'), '', $buid);

			$q = $db->query("SELECT id, alias, content, block_type FROM ".BLOCKS_TABLE." WHERE id='".$buid."' OR alias='".$buid."'");
			$data = $db->fetch_assoc($q);

			if(!empty($data)) {
				if($data['block_type'] == "php") {
					ob_start();
						eval($parse->text->html($data['content']));

						$output = ob_get_contents();
					ob_end_clean();
				}
				else {
					$output = $parse->text->html($data['content']);

					# load attached images
					$images = $img->load_images("blockid=".$data['id']);

					# load attached files
					$attachfile = $files->load_files("blockid=".$data['id']);


					$smarty->assign("attachfile", $attachfile);
					$smarty->assign("images", $images);
					$smarty->assign("block_id", $data['id']);
					$smarty->assign("block_alias", $data['alias']);

					$imgs = $tpl->load_template("block_attached", true);

					$output .= $imgs;
				}

				$use_blocks[$buid] = $output;
			}
			else {
				if(DEBUGMODE) {
					$output = "Блок с ID или ALIAS - \"".$buid."\" не найден";
				}
			}

                }
                else {
                	$output = $use_blocks[$buid];
		}

		return $output;
	}
}


/**
 * Init Class
 */
$blocks = new Blocks;

/**
 * assign in templates
 */
$smarty->assign("blocks", $blocks);
