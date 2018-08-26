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
* @package      RooCMS
* @subpackage	Admin Control Panel
* @author       alex Roosso
* @copyright    2010-2019 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.3.6
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
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
	 * Функция собирает данные страницы для редактирования
	 *
	 * @param int $sid - Структурный идентификатор
	 */
	public function edit($sid) {

		global $db, $files, $img, $tpl, $smarty, $parse;

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
		$attachedimages = $tpl->load_template("images_attach", true);
		$smarty->assign("attachedimages", $attachedimages);


		# download attached files
		$attachfile = $files->load_files("pagesid=".$sid);
		$smarty->assign("attachfile", $attachfile);

		# show attached files
		$attachedfiles = $tpl->load_template("files_attach", true);
		$smarty->assign("attachedfiles", $attachedfiles);


		# show upload files & images form
		$tpl->load_image_upload_tpl("imagesupload");
		$tpl->load_files_upload_tpl("filesupload");

		$content = $tpl->load_template("pages_edit_html", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Обновляем контент страницы
	 *
	 * @param int $sid - Structure element id
	 */
	public function update($sid) {

		global $db, $logger, $files, $img, $post;

		#sortable images
		if(isset($post->sort)) {
			$sortimg = $img->load_images("pagesid=".$sid);
			foreach($sortimg AS $v) {
				if(isset($post->sort[$v['id']]) && $post->sort[$v['id']] != $v['sort']) {
					$db->query("UPDATE ".IMAGES_TABLE." SET sort='".$post->sort[$v['id']]."' WHERE id='".$v['id']."'");
				}
			}
		}

		# read thumbnail parametrs
		$q = $db->query("SELECT thumb_img_width, thumb_img_height FROM ".STRUCTURE_TABLE." WHERE id='".$sid."'");
		$thumbsize = $db->fetch_assoc($q);

		# attachment images
		$images = $img->upload_image("images", "", array($thumbsize['thumb_img_width'], $thumbsize['thumb_img_height']));
		if($images) {
			foreach($images AS $image) {
				$img->insert_images($image, "pagesid=".$sid);
			}
		}


		# attachment files
		$files->upload("files", "pagesid=".$sid);


		if(!isset($post->content)) {
			$post->content = "";
		}

		$db->query("UPDATE ".PAGES_HTML_TABLE." SET content='".$post->content."', date_modified='".time()."' WHERE sid='".$sid."'");

		$logger->info("Страница #".$sid." успешно обновлена.");

		goback();
	}


	/**
	 * Удаляем страницу из таблицы
	 *
	 * @param int $sid - Структурный ID
	 */
	public function delete($sid) {

		global $db, $img, $logger;

		# del attached images
		$img->delete_images("pagesid=".$sid);

		# del pageunit
		$db->query("DELETE FROM ".PAGES_HTML_TABLE." WHERE sid='".$sid."'");
	}
}