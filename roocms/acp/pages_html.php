<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Pages settings
* @subpackage	HTML Page
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.6
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
*   Данное программное обеспечение является свободным и распространяется
*   по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
*   При любом использовании данного ПО вы должны соблюдать все условия
*   лицензии.
*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('ACP')) die('Access Denied');
//#########################################################


class ACP_PAGES_HTML {


	//#####################################################
	//	Edit
	function edit($sid) {

		global $db, $tpl, $smarty, $parse;

		# download data
		$q = $db->query("SELECT h.id, h.sid, h.content, p.title, p.alias, p.meta_description, p.meta_keywords, h.date_modified
							FROM ".PAGES_HTML_TABLE." AS h
							LEFT JOIN ".STRUCTURE_TABLE." AS p ON (p.id = h.sid)
							WHERE h.sid='".$sid."'");
		$data = $db->fetch_assoc($q);
		$data['lm'] = $parse->date->unix_to_rus($data['date_modified'], true, true, true);

		$smarty->assign("data", $data);

		# download attached images
		$attachimg = array();
		$attachimg = Structure::load_images("pagesid=".$sid);
		$smarty->assign("attachimg", $attachimg);

		$attachedimages = $tpl->load_template("images_attach", true);
		$imagesupload = $tpl->load_template("images_upload", true);
		$smarty->assign("attachedimages", $attachedimages);
		$smarty->assign("imagesupload", $imagesupload);

		$content = $tpl->load_template("pages_edit_html", true);
		$smarty->assign("content", $content);
	}


	//#####################################################
	//	Update
	function update($sid) {

		global $db, $debug, $parse, $POST, $files, $gd;

		#sortable images
		if(isset($POST->sort)) {
			$sortimg = Structure::load_images("pagesid=".$sid);
			foreach($sortimg AS $k=>$v) {
				if(isset($POST->sort[$v['id']]) && $POST->sort[$v['id']] != $v['sort']) {
					$db->query("UPDATE ".IMAGES_TABLE." SET sort='".$POST->sort[$v['id']]."' WHERE id='".$v['id']."'");
					if($debug->debug) $parse->msg("Изображению ".$v['id']." успешно присвоен порядок ".$POST->sort[$v['id']]);
				}
			}
		}

		# attachment images
		$images = $gd->upload_image("images");
		if($images) {
			foreach($images AS $image) {
				$db->query("INSERT INTO ".IMAGES_TABLE." (attachedto, filename) VALUES ('pagesid=".$sid."', '".$image."')");
				if($debug->debug) $parse->msg("Изображение ".$image." успешно загружено на сервер");
			}
		}


		$db->query("UPDATE ".PAGES_HTML_TABLE." SET content='".$POST->content."', date_modified='".time()."' WHERE sid='".$sid."'");

		$parse->msg("Страница #".$sid." успешно обновлена.");

		goback();
	}


	//#####################################################
	//	Delete
	function delete($sid) {

		global $db;

		# del attached images
		$i = $db->query("SELECT filename FROM ".IMAGES_TABLE." WHERE attachedto='pagesid=".$sid."'");
		while($img = $db->fetch_assoc($i)) {
			unlink(_UPLOADIMAGES."/original/".$img['filename']);
			unlink(_UPLOADIMAGES."/resize/".$img['filename']);
			unlink(_UPLOADIMAGES."/thumb/".$img['filename']);
		}
		$db->query("DELETE FROM ".IMAGES_TABLE." WHERE attachedto='pagesid=".$sid."'");

		# del pageunit
		$db->query("DELETE FROM ".PAGES_HTML_TABLE." WHERE sid='".$sid."'");
	}
}
?>