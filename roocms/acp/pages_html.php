<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.3.2
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
*   Copyright (C) 2010-2014 alex Roosso aka alexandr Belov info@roocms.com
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
*   along with this program.  If not, see <http://www.gnu.org/licenses/
*
*
*   RooCMS - Русская бесплатная система управления сайтом
*   Copyright (C) 2010-2014 alex Roosso (александр Белов) info@roocms.com
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

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('ACP')) die('Access Denied');
//#########################################################


/**
 * Class ACP_PAGES_HTML
 */
class ACP_PAGES_HTML {

	//#####################################################
	//	Edit
	function edit($sid) {

		global $db, $img, $tpl, $smarty, $parse;

		# download data
		$q = $db->query("SELECT h.id, h.sid, h.content, s.title, s.alias, s.meta_description, s.meta_keywords, h.date_modified
					FROM ".PAGES_HTML_TABLE." AS h
						LEFT JOIN ".STRUCTURE_TABLE." AS s ON (s.id = h.sid)
					WHERE h.sid='".$sid."'");
		$data = $db->fetch_assoc($q);
		$data['lm'] = $parse->date->unix_to_rus($data['date_modified'], true, true, true);

		$smarty->assign("data", $data);

		# download attached images
		$attachimg = array();
		$attachimg = $img->load_images("pagesid=".$sid);
		$smarty->assign("attachimg", $attachimg);

		# show attached images
		$attachedimages = $tpl->load_template("images_attach", true);
		$smarty->assign("attachedimages", $attachedimages);

		# show upload images form
		require_once _LIB."/mimetype.php";
		$smarty->assign("allow_images_type", $imagetype);
		$imagesupload = $tpl->load_template("images_upload", true);
		$smarty->assign("imagesupload", $imagesupload);

		$content = $tpl->load_template("pages_edit_html", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Обновляем контент страницы
	 *
	 * @param $sid - Structure element id
	 */
	function update($sid) {

		global $db, $parse, $img, $POST;

		#sortable images
		if(isset($POST->sort)) {
			$sortimg = $img->load_images("pagesid=".$sid);
			foreach($sortimg AS $k=>$v) {
				if(isset($POST->sort[$v['id']]) && $POST->sort[$v['id']] != $v['sort']) {
					$db->query("UPDATE ".IMAGES_TABLE." SET sort='".$POST->sort[$v['id']]."' WHERE id='".$v['id']."'");
					if(DEBUGMODE) $parse->msg("Изображению ".$v['id']." успешно присвоен порядок ".$POST->sort[$v['id']]);
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


		if(!isset($POST->content)) $POST->content = "";

		$db->query("UPDATE ".PAGES_HTML_TABLE." SET content='".$POST->content."', date_modified='".time()."' WHERE sid='".$sid."'");

		$parse->msg("Страница #".$sid." успешно обновлена.");

		goback();
	}


	//#####################################################
	//	Delete
	function delete($sid) {

		global $db, $img;

		# del attached images
		$img->delete_images("pagesid=".$sid);

		# del pageunit
		$db->query("DELETE FROM ".PAGES_HTML_TABLE." WHERE sid='".$sid."'");
	}
}
?>