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
if(!defined('RooCMS') || !defined('ACP')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class ACP_Pages_Story
 */
class ACP_Pages_Story {

	/**
	 * Edit content
	 *
	 * @param int $sid - Structure id
	 */
	public function edit(int $sid) {

		global $db, $files, $img, $parse, $tpl, $smarty;

		# download structure data
		$q = $db->query("SELECT id AS sid, title, alias, meta_description, meta_keywords FROM ".STRUCTURE_TABLE." WHERE id='".$sid."'");
		$data = $db->fetch_assoc($q);


		# download content
		$datacontent = [];
		$q = $db->query("SELECT id, sid, sort, content, date_modified FROM ".PAGES_STORY_TABLE." WHERE sid='".$sid."' ORDER BY sort, sid");
		while($row = $db->fetch_assoc($q)) {
			$data['lm'] = $parse->date->unix_to_rus($row['date_modified'], true, true, true);
			$datacontent[] = $row;


			# download attached images
			$attachimg = $img->load_images("page_story_id=".$row['id']);
			$smarty->assign("attachimg", $attachimg);

			# show attached images
			$attachedimages[$row['id']] = $tpl->load_template("attached_images", true);


			# download attached files
			$attachfile = $files->load_files("page_story_id=".$row['id']);
			$smarty->assign("attachfile", $attachfile);

			# show attached files
			$attachedfiles[$row['id']] = $tpl->load_template("attached_files", true);
		}

		$smarty->assign("data", $data);
		$smarty->assign("datacontent", $datacontent);

		# show attached images and files
		$smarty->assign("attachedimages", $attachedimages);
		$smarty->assign("attachedfiles", $attachedfiles);

		# show upload files & images form
		$tpl->load_image_upload_tpl("imagesupload");
		$tpl->load_files_upload_tpl("filesupload");

		$content = $tpl->load_template("pages_edit_story", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Update page content
	 *
	 * @param mixed $data - this object data params
	 */
	public function update($data) {


	}


	/**
	 * Remove page
	 *
	 * @param int $sid - Structure id
	 */
	public function delete(int $sid) {


	}
}
