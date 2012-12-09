<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Blocks settings
* @subpackage	HTML Blocks
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.2
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


class ACP_BLOCKS_HTML {

	//#####################################################
	//	Create
	function create() {

		global $db, $tpl, $smarty, $parse, $POST, $gd, $debug;

		if(@$_REQUEST['create_block']) {

			if(!isset($POST->title)) $parse->msg("Не указано название блока!", false);
			if(!isset($POST->alias) || $db->check_id($POST->alias, BLOCKS_TABLE, "alias"))	$parse->msg("Не указан алиас блока или он не уникален!", false);
			if(!isset($POST->content)) $parse->msg("Пустое тело блока!", false);

			if(!isset($_SESSION['error'])) {
				$db->query("INSERT INTO ".BLOCKS_TABLE."   (title, alias, content, date_create, date_modified, type)
													VALUES ('".$POST->title."', '".$POST->alias."', '".$POST->content."', '".time()."', '".time()."', 'html')");

				$id = $db->insert_id();

				# attachment images
				$images = $gd->upload_image("images");
				if($images) {
					foreach($images AS $image) {
						$db->query("INSERT INTO ".IMAGES_TABLE." (attachedto, filename) VALUES ('blockid=".$id."', '".$image."')");
						if($debug->debug) $parse->msg("Изображение ".$image." успешно загружено на сервер");
					}
				}

				$parse->msg("Блок успешно добавлен!");

				go(CP."?act=blocks");
			}
			else go(CP."?act=blocks&part=create&type=html");
		}

		$imagesupload = $tpl->load_template("images_upload", true);
		$smarty->assign("imagesupload", $imagesupload);

		$content = $tpl->load_template("blocks_create_html", true);
		$smarty->assign("content", $content);
	}


	//#####################################################
	//	Edit
	function edit($id) {

		global $db, $tpl, $smarty;

		require_once _CLASS."/class_structure.php";

		$q = $db->query("SELECT id, title, alias, content FROM ".BLOCKS_TABLE." WHERE id='".$id."'");
		$data = $db->fetch_assoc($q);


		# download attached images
		$attachimg = array();
		$attachimg = Structure::load_images("blockid=".$id);
		$smarty->assign("attachimg", $attachimg);

		$attachedimages = $tpl->load_template("images_attach", true);
		$imagesupload = $tpl->load_template("images_upload", true);
		$smarty->assign("attachedimages", $attachedimages);
		$smarty->assign("imagesupload", $imagesupload);


		$smarty->assign("data",$data);
		$content = $tpl->load_template("blocks_edit_html", true);
		$smarty->assign("content", $content);
	}


	//#####################################################
	//	Update
	function update($id) {

		global $db, $POST, $GET, $parse, $gd, $debug;

		if(@$_REQUEST['update_block']) {

			if(!isset($POST->title)) $parse->msg("Не указано название блока!", false);
			if(!isset($POST->alias) || $db->check_id($POST->alias, BLOCKS_TABLE, "alias", "alias!='".$POST->oldalias."'"))	$parse->msg("Не указан алиас блока или он не уникален!", false);
			if(!isset($POST->content)) $parse->msg("Пустое тело блока!", false);
			if(!isset($POST->id) || $POST->id != $GET->_block) $parse->msg("Системная ошибка...", false);

			if(!isset($_SESSION['error'])) {

				$db->query("UPDATE ".BLOCKS_TABLE."
								SET
									title='".$POST->title."',
									alias='".$POST->alias."',
									content='".$POST->content."',
									date_modified='".time()."'
								WHERE
									id='".$id."'");

				#sortable images
				if(isset($POST->sort)) {

                    require_once _CLASS."/class_structure.php";

					$sortimg = Structure::load_images("blockid=".$id);
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
						$db->query("INSERT INTO ".IMAGES_TABLE." (attachedto, filename) VALUES ('blockid=".$id."', '".$image."')");
						if($debug->debug) $parse->msg("Изображение ".$image." успешно загружено на сервер");
					}
				}

				$parse->msg("Блок успешно обновлен!");
			}

			go(CP."?act=blocks");
		}
	}


	//#####################################################
	//	Delete
	function delete($id) {

		global $db, $parse;

		$q = $db->query("SELECT filename FROM ".IMAGES_TABLE." WHERE attachedto='blockid=".$id."'");
		while($img = $db->fetch_assoc($q)) {
			unlink(_UPLOADIMAGES."/original/".$img['filename']);
			unlink(_UPLOADIMAGES."/resize/".$img['filename']);
			unlink(_UPLOADIMAGES."/thumb/".$img['filename']);
		}
		$db->query("DELETE FROM ".IMAGES_TABLE." WHERE attachedto='blockid=".$id."'");

		$db->query("DELETE FROM ".BLOCKS_TABLE." WHERE id='".$id."'");
		$parse->msg("Блок успешно удален!");
		go(CP."?act=blocks");
	}
}

?>