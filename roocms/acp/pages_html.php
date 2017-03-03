<?php
/**
 *   RooCMS - Russian free content managment system
 *   Copyright © 2010-2017 alexandr Belov aka alex Roosso. All rights reserved.
 *   Contacts: <info@roocms.com>
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see http://www.gnu.org/licenses/
 *
 *
 *   RooCMS - Русская бесплатная система управления контентом
 *   Copyright © 2010-2017 александр Белов (alex Roosso). Все права защищены.
 *   Для связи: <info@roocms.com>
 *
 *   Это программа является свободным программным обеспечением. Вы можете
 *   распространять и/или модифицировать её согласно условиям Стандартной
 *   Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
 *   Обеспечения, версии 3 или, по Вашему желанию, любой более поздней версии.
 *
 *   Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
 *   ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
 *   ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
 *   Общественную Лицензию GNU для получения дополнительной информации.
 *
 *   Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
 *   с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
 */

/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @author       alex Roosso
* @copyright    2010-2017 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.3.5
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
class ACP_PAGES_HTML {

	//#####################################################
	//	Edit
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
	 * @param $sid - Structure element id
	 */
	public function update($sid) {

		global $db, $logger, $files, $img, $POST;

		#sortable images
		if(isset($POST->sort)) {
			$sortimg = $img->load_images("pagesid=".$sid);
			foreach($sortimg AS $k=>$v) {
				if(isset($POST->sort[$v['id']]) && $POST->sort[$v['id']] != $v['sort']) {
					$db->query("UPDATE ".IMAGES_TABLE." SET sort='".$POST->sort[$v['id']]."' WHERE id='".$v['id']."'");
					$logger->info("Изображению ".$v['id']." успешно присвоен порядок ".$POST->sort[$v['id']]);
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
		$attachs = $files->upload("files");
		if($attachs) {
			foreach($attachs AS $attach) {
				$files->insert_file($attach, "pagesid=".$sid);
			}
		}


		if(!isset($POST->content)) {
			$POST->content = "";
		}

		$db->query("UPDATE ".PAGES_HTML_TABLE." SET content='".$POST->content."', date_modified='".time()."' WHERE sid='".$sid."'");

		$logger->info("Страница #".$sid." успешно обновлена.");

		goback();
	}


	//#####################################################
	//	Delete
	public function delete($sid) {

		global $db, $img, $logger;

		# del attached images
		$img->delete_images("pagesid=".$sid);

		# del pageunit
		$db->query("DELETE FROM ".PAGES_HTML_TABLE." WHERE sid='".$sid."'");

		# notice
		$logger->info("Страница #".$sid." успешно удалена");
	}
}
?>