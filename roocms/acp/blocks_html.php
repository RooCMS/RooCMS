<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Blocks settings
* @subpackage	HTML Blocks
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.2.1
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*	RooCMS - Russian free content managment system
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


class ACP_BLOCKS_HTML {

    # vars
    private $structure;



    /**
    * Инициализируем класс
    *
    */
    function __construct() {
		require_once _CLASS."/class_structure.php";
		$this->structure = new Structure(false, false);
    }


	//#####################################################
	//	Create
	function create() {

		global $db, $tpl, $smarty, $parse, $POST, $gd;

		if(@$_REQUEST['create_block']) {

			if(!isset($POST->title)) $parse->msg("Не указано название блока!", false);
			if(!isset($POST->alias) || $db->check_id($POST->alias, BLOCKS_TABLE, "alias"))	$parse->msg("Не указан алиас блока или он не уникален!", false);
			# if(!isset($POST->content)) $parse->msg("Пустое тело блока!", false); //Упраздняем временно

			if(!isset($_SESSION['error'])) {
				$db->query("INSERT INTO ".BLOCKS_TABLE."   (title, alias, content, date_create, date_modified, type)
													VALUES ('".$POST->title."', '".$POST->alias."', '".$POST->content."', '".time()."', '".time()."', 'html')");

				$id = $db->insert_id();

				# attachment images
				$images = $gd->upload_image("images");
				if($images) {
					foreach($images AS $image) {
						$db->query("INSERT INTO ".IMAGES_TABLE." (attachedto, filename) VALUES ('blockid=".$id."', '".$image."')");
						if(DEBUGMODE) $parse->msg("Изображение ".$image." успешно загружено на сервер");
					}
				}

				$parse->msg("Блок успешно добавлен!");

				go(CP."?act=blocks");
			}
			else go(CP."?act=blocks&part=create&type=html");
		}

		# show upload images form
		require_once _LIB."/mimetype.php";
		$smarty->assign("allow_images_type", $imagetype);
		$imagesupload = $tpl->load_template("images_upload", true);
		$smarty->assign("imagesupload", $imagesupload);

		$content = $tpl->load_template("blocks_create_html", true);
		$smarty->assign("content", $content);
	}


	//#####################################################
	//	Edit
	function edit($id) {

		global $db, $tpl, $smarty;

		$q = $db->query("SELECT id, title, alias, content FROM ".BLOCKS_TABLE." WHERE id='".$id."'");
		$data = $db->fetch_assoc($q);


		# download attached images
		$attachimg = array();
		$attachimg = $this->structure->load_images("blockid=".$id);
		$smarty->assign("attachimg", $attachimg);

		# show attached images
		$attachedimages = $tpl->load_template("images_attach", true);
		$smarty->assign("attachedimages", $attachedimages);

		# show upload images form
		require_once _LIB."/mimetype.php";
		$smarty->assign("allow_images_type", $imagetype);
		$imagesupload = $tpl->load_template("images_upload", true);
		$smarty->assign("imagesupload", $imagesupload);


		$smarty->assign("data",$data);
		$content = $tpl->load_template("blocks_edit_html", true);
		$smarty->assign("content", $content);
	}


	//#####################################################
	//	Update
	function update($id) {

		global $db, $POST, $GET, $parse, $gd;

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

					$sortimg = $this->structure->load_images("blockid=".$id);
					foreach($sortimg AS $k=>$v) {
						if(isset($POST->sort[$v['id']]) && $POST->sort[$v['id']] != $v['sort']) {
							$db->query("UPDATE ".IMAGES_TABLE." SET sort='".$POST->sort[$v['id']]."' WHERE id='".$v['id']."'");
							if(DEBUGMODE) $parse->msg("Изображению ".$v['id']." успешно присвоен порядок ".$POST->sort[$v['id']]);
						}
					}
				}

				# attachment images
				$images = $gd->upload_image("images");
				if($images) {
					foreach($images AS $image) {
						$db->query("INSERT INTO ".IMAGES_TABLE." (attachedto, filename) VALUES ('blockid=".$id."', '".$image."')");
						if(DEBUGMODE) $parse->msg("Изображение ".$image." успешно загружено на сервер");
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