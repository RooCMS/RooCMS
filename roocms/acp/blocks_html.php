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
* @subpackage	Blocks settings
* @subpackage	HTML Blocks
* @author       alex Roosso
* @copyright    2010-2017 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.4.1
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


class ACP_BLOCKS_HTML {

	/**
	 * Создаем HTML блок
	 */
	public function create() {

		global $config, $db, $files, $img, $tpl, $smarty, $logger, $POST;


		# default thumb size
		$default_thumb_size = array('width'	=> $config->gd_thumb_image_width,
					    'height'	=> $config->gd_thumb_image_height);
		$smarty->assign("default_thumb_size", $default_thumb_size);


		if(isset($POST->create_block)) {

			# check parametrs
			$this->check_block_parametrs();

			if(!isset($_SESSION['error'])) {
				$db->query("INSERT INTO ".BLOCKS_TABLE."   (title, alias, content, thumb_img_width, thumb_img_height, date_create, date_modified, block_type)
								    VALUES ('".$POST->title."', '".$POST->alias."', '".$POST->content."', '".$POST->thumb_img_width."', '".$POST->thumb_img_height."', '".time()."', '".time()."', 'html')");
				$id = $db->insert_id();


				$thumbsize['thumb_img_width'] = ($POST->thumb_img_width != 0) ? $POST->thumb_img_width : $config->gd_thumb_image_width ;
				$thumbsize['thumb_img_height'] = ($POST->thumb_img_height != 0) ? $POST->thumb_img_height : $config->gd_thumb_image_height ;


				# attachment images
				$images = $img->upload_image("images", "", array($thumbsize['thumb_img_width'], $thumbsize['thumb_img_height']));
				if($images) {
					foreach($images AS $image) {
                                                $img->insert_images($image, "blockid=".$id);
					}
				}

				# attachment files
				$attachs = $files->upload("files");
				if($attachs) {
					foreach($attachs AS $attach) {
						$files->insert_file($attach, "blockid=".$id);
					}
				}


				$logger->info("Блок #".$id." успешно добавлен!");

				go(CP."?act=blocks");
			}
			else go(CP."?act=blocks&part=create&type=html");
		}

		# show upload files & images form
		$tpl->load_image_upload_tpl("imagesupload");
		$tpl->load_files_upload_tpl("filesupload");

		$content = $tpl->load_template("blocks_create_html", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Редактируем HTML блок
	 *
	 * @param $id - идентификатор блока
	 */
	public function edit($id) {

		global $config, $db, $files, $img, $tpl, $smarty;

		$q = $db->query("SELECT id, title, alias, content, thumb_img_width, thumb_img_height FROM ".BLOCKS_TABLE." WHERE id='".$id."'");
		$data = $db->fetch_assoc($q);

		# default thumb size
		$default_thumb_size = array('width'	=> $config->gd_thumb_image_width,
					    'height'	=> $config->gd_thumb_image_height);
		$smarty->assign("default_thumb_size", $default_thumb_size);

		# download attached images
		$attachimg = $img->load_images("blockid=".$id);
		$smarty->assign("attachimg", $attachimg);

		# show attached images
		$attachedimages = $tpl->load_template("images_attach", true);
		$smarty->assign("attachedimages", $attachedimages);


		# download attached files
		$attachfile = $files->load_files("blockid=".$id);
		$smarty->assign("attachfile", $attachfile);

		# show attached files
		$attachedfiles = $tpl->load_template("files_attach", true);
		$smarty->assign("attachedfiles", $attachedfiles);


		# show upload files & images form
		$tpl->load_image_upload_tpl("imagesupload");
		$tpl->load_files_upload_tpl("filesupload");


		$smarty->assign("data",$data);
		$content = $tpl->load_template("blocks_edit_html", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Обновляем HTML блок
	 *
	 * @param $id - идентификатор
	 */
	public function update($id) {

		global $config, $db, $files, $img, $POST, $GET, $logger;

		if(isset($POST->update_block)) {

			# check parametrs
			$this->check_block_parametrs();

			if(!isset($POST->id) || $POST->id != $GET->_block) {
				$logger->error("Системная ошибка...");
			}

			if(!isset($_SESSION['error'])) {

				$db->query("UPDATE ".BLOCKS_TABLE."
					        SET
						    title='".$POST->title."',
						    alias='".$POST->alias."',
						    content='".$POST->content."',
						    thumb_img_width='".$POST->thumb_img_width."',
						    thumb_img_height='".$POST->thumb_img_height."',
						    date_modified='".time()."'
					        WHERE
						    id='".$id."'");

				#sortable images
				if(isset($POST->sort)) {
					$sortimg = $img->load_images("blockid=".$id);
					foreach($sortimg AS $v) {
						if(isset($POST->sort[$v['id']]) && $POST->sort[$v['id']] != $v['sort']) {
							$db->query("UPDATE ".IMAGES_TABLE." SET sort='".$POST->sort[$v['id']]."' WHERE id='".$v['id']."'");
							$logger->info("Изображению ".$v['id']." успешно присвоен порядок ".$POST->sort[$v['id']]);
						}
					}
				}


				$thumbsize['thumb_img_width'] = ($POST->thumb_img_width != 0) ? $POST->thumb_img_width : $config->gd_thumb_image_width ;
				$thumbsize['thumb_img_height'] = ($POST->thumb_img_height != 0) ? $POST->thumb_img_height : $config->gd_thumb_image_height ;

				# attachment images
				$images = $img->upload_image("images", "", array($thumbsize['thumb_img_width'], $thumbsize['thumb_img_height']));
				if($images) {
					foreach($images AS $image) {
						$img->insert_images($image, "blockid=".$id);
					}
				}


				# attachment files
				$attachs = $files->upload("files");
				if($attachs) {
					foreach($attachs AS $attach) {
						$files->insert_file($attach, "blockid=".$id);
					}
				}


				$logger->info("Блок успешно обновлен!");
			}

			go(CP."?act=blocks");
		}
		else goback();
	}


	/**
	 * Удаление блока
	 *
	 * @param $id - идентификатор блока
	 */
	public function delete($id) {

		global $db, $img, $logger;

                $img->delete_images("blockid=".$id);

		$db->query("DELETE FROM ".BLOCKS_TABLE." WHERE id='".$id."'");

		$logger->info("Блок #".$id." успешно удален!");
		go(CP."?act=blocks");
	}


	/**
	 * Check Block Parametrs
	 */
	private function check_block_parametrs() {

		global $db, $parse, $POST, $logger;


		if(!isset($POST->title)) {
			$logger->error("Не указано название блока!");
		}

		$check_alias = (isset($POST->oldalias)) ? "alias!='".$POST->oldalias."'" : "" ;

		if(!isset($POST->alias) || $db->check_id($parse->text->transliterate($POST->alias), BLOCKS_TABLE, "alias", $check_alias)) {
			$logger->error("Не указан алиас блока или он не уникален!");
		}
		else {
			$POST->alias = $parse->text->transliterate($POST->alias);
			$POST->alias = preg_replace(array('(\s\s+)','(\-\-+)','(__+)','([^a-zA-Z0-9\-_])'), array('','','',''), $POST->alias);
			if(is_numeric($POST->alias)) {
				$POST->alias .= randcode(3, "abcdefghijklmnopqrstuvwxyz");
			}
		}

		// Упраздняем временно...
		// if(!isset($POST->content)) $parse->msg("Пустое тело блока!", false);
		if(!isset($POST->content)) {
			$POST->content = "";
		}

		# check thumb size
		if(!isset($POST->thumb_img_width)) {
			$POST->thumb_img_width = 0;
		}
		if(!isset($POST->thumb_img_height)) {
			$POST->thumb_img_height = 0;
		}
	}
}

?>