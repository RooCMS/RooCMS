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
 * Class ACP_PAGES_HTML
 */
class ACP_Pages_HTML {

	/**
	 * Edit content
	 *
	 * @param int $sid - Structure id
	 */
	public function edit(int $sid) {

		global $db, $files, $img, $parse, $tpl, $smarty;

		# download data
		$q = $db->query("SELECT h.id, h.sid, h.content, s.title, s.alias, s.meta_description, s.meta_keywords, h.date_modified
					FROM ".PAGES_HTML_TABLE." AS h
						LEFT JOIN ".STRUCTURE_TABLE." AS s ON (s.id = h.sid)
					WHERE h.sid='".$sid."'");
		$data = $db->fetch_assoc($q);
		$data['lm'] = $parse->date->unix_to_rus($data['date_modified'], true, true, true);

		$smarty->assign("data", $data);


		# download attached images
		$attachimg = $img->load_images("pagesid=".$sid);
		$smarty->assign("attachimg", $attachimg);

		# show attached images
		$attachedimages = $tpl->load_template("attached_images", true);
		$smarty->assign("attachedimages", $attachedimages);


		# download attached files
		$attachfile = $files->load_files("pagesid=".$sid);
		$smarty->assign("attachfile", $attachfile);

		# show attached files
		$attachedfiles = $tpl->load_template("attached_files", true);
		$smarty->assign("attachedfiles", $attachedfiles);


		# show upload files & images form
		$tpl->load_image_upload_tpl("imagesupload");
		$tpl->load_files_upload_tpl("filesupload");

		$content = $tpl->load_template("pages_edit_html", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Update page content
	 *
	 * @param mixed $data - this object data params
	 */
	public function update($data) {

		global $db, $logger, $files, $img, $post;

		#sortable images
		$img->update_images_info("pagesid", $data->page_sid);


		# attachment images
		$images = $img->upload_image("images", "", array($data->page_thumb_img_width, $data->page_thumb_img_height));
		if($images) {
			foreach($images AS $image) {
				$img->insert_images($image, "pagesid=".$data->page_sid);
			}
		}

		# attachment files
		$files->upload("files", "pagesid=".$data->page_sid);

		# db
		$db->query("UPDATE ".PAGES_HTML_TABLE." SET content='".$post->content."', date_modified='".time()."' WHERE sid='".$data->page_sid."'");

		# notice
		$logger->info("Страница #".$data->page_sid." успешно обновлена.");

		# go
		goback();
	}


	/**
	 * Remove page
	 *
	 * @param int $sid - Structure id
	 */
	public function delete(int $sid) {

		global $db, $img;

		# del attached images
		$img->remove_images("pagesid=".$sid);

		# del pageunit
		$db->query("DELETE FROM ".PAGES_HTML_TABLE." WHERE sid='".$sid."'");
	}
}
