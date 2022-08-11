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


class ACP_Blocks_HTML {

	/**
	 * Create block HTML
	 */
	public function create() {

		global $config, $db, $files, $img, $tpl, $smarty, $logger, $post;


		# default thumb size
		$default_thumb_size = array('width'  => $config->gd_thumb_image_width,
					    'height' => $config->gd_thumb_image_height);
		$smarty->assign("default_thumb_size", $default_thumb_size);


		if(isset($post->create_block)) {

			# check parametrs
			$this->check_block_parametrs();

			if(!isset($_SESSION['error'])) {
				$db->query("INSERT INTO ".BLOCKS_TABLE."   (title, alias, content, thumb_img_width, thumb_img_height, date_create, date_modified, block_type)
								    VALUES ('".$post->title."', '".$post->alias."', '".$post->content."', '".$post->thumb_img_width."', '".$post->thumb_img_height."', '".time()."', '".time()."', 'html')");
				$id = $db->insert_id();

				$thumbsize = [];
				$thumbsize['thumb_img_width'] = ($post->thumb_img_width != 0) ? $post->thumb_img_width : $config->gd_thumb_image_width;
				$thumbsize['thumb_img_height'] = ($post->thumb_img_height != 0) ? $post->thumb_img_height : $config->gd_thumb_image_height;


				# attachment images
				$images = $img->upload_image("images", "", array($thumbsize['thumb_img_width'], $thumbsize['thumb_img_height']));
				if($images) {
					foreach($images AS $image) {
                                                $img->insert_images($image, "blockid=".$id);
					}
				}

				# attachment files
				$files->upload("files", "blockid=".$id);

				# log
				$logger->info("Блок #".$id." успешно добавлен!");

				# go
				go(CP."?act=blocks");
			}

			# go
			go(CP."?act=blocks&part=create&type=html");
		}

		# show upload files & images form
		$tpl->load_image_upload_tpl("imagesupload");
		$tpl->load_files_upload_tpl("filesupload");

		$content = $tpl->load_template("blocks_create_html", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Edit block HTML
	 *
	 * @param int $id
	 */
	public function edit(int $id) {

		global $config, $db, $files, $img, $tpl, $smarty;

		$q = $db->query("SELECT id, title, alias, content, thumb_img_width, thumb_img_height FROM ".BLOCKS_TABLE." WHERE id='".$id."'");
		$data = $db->fetch_assoc($q);

		# default thumb size
		$default_thumb_size = array('width'  => $config->gd_thumb_image_width,
					    'height' => $config->gd_thumb_image_height);
		$smarty->assign("default_thumb_size", $default_thumb_size);

		# download attached images
		$attachimg = $img->load_images("blockid=".$id);
		$smarty->assign("attachimg", $attachimg);

		# show attached images
		$attachedimages = $tpl->load_template("attached_images", true);
		$smarty->assign("attachedimages", $attachedimages);


		# download attached files
		$attachfile = $files->load_files("blockid=".$id);
		$smarty->assign("attachfile", $attachfile);

		# show attached files
		$attachedfiles = $tpl->load_template("attached_files", true);
		$smarty->assign("attachedfiles", $attachedfiles);


		# show upload files & images form
		$tpl->load_image_upload_tpl("imagesupload");
		$tpl->load_files_upload_tpl("filesupload");


		$smarty->assign("data",$data);
		$content = $tpl->load_template("blocks_edit_html", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Update block HTML
	 *
	 * @param int $id
	 */
	public function update(int $id) {

		global $config, $db, $files, $img, $post, $get, $logger;

		if(isset($post->update_block)) {

			# check parametrs
			$this->check_block_parametrs();

			if($post->id != $get->_block) {
				$logger->error("Системная ошибка...");
			}

			if(!isset($_SESSION['error'])) {

				$db->query("UPDATE ".BLOCKS_TABLE."
					        SET
						    title='".$post->title."',
						    alias='".$post->alias."',
						    content='".$post->content."',
						    thumb_img_width='".$post->thumb_img_width."',
						    thumb_img_height='".$post->thumb_img_height."',
						    date_modified='".time()."'
					        WHERE
						    id='".$id."'");

				#sortable images
				$img->update_images_info("blockid", $id);

				$thumbsize = [];
				$thumbsize['thumb_img_width'] = ($post->thumb_img_width != 0) ? $post->thumb_img_width : $config->gd_thumb_image_width ;
				$thumbsize['thumb_img_height'] = ($post->thumb_img_height != 0) ? $post->thumb_img_height : $config->gd_thumb_image_height ;

				# attachment images
				$images = $img->upload_image("images", "", array($thumbsize['thumb_img_width'], $thumbsize['thumb_img_height']));
				if($images) {
					foreach($images AS $image) {
						$img->insert_images($image, "blockid=".$id);
					}
				}

				# attachment files
				$files->upload("files", "blockid=".$id);

				# log
				$logger->info("Блок #".$id." успешно обновлен!");
			}

			# go
			go(CP."?act=blocks");
		}

		# go
		goback();
	}


	/**
	 * Remove block
	 *
	 * @param int $id
	 */
	public function delete(int $id) {

		global $db, $img, $logger;

                $img->remove_images("blockid=".$id);

                # query
		$db->query("DELETE FROM ".BLOCKS_TABLE." WHERE id='".$id."'");

		# log
		$logger->info("Блок #".$id." успешно удален!");

		# go
		go(CP."?act=blocks");
	}


	/**
	 * Check Block Parametrs
	 */
	private function check_block_parametrs() {

		global $db, $parse, $post, $logger;


		if(!isset($post->title)) {
			$logger->error("Не указано название блока!", false);
		}

		if(!isset($post->alias)) {
			$logger->error("Не указан алиас блока!", false);
		}
		else {
			$post->alias = $parse->text->correct_aliases($post->alias);

			$check_alias = (isset($post->oldalias)) ? "alias!='".$post->oldalias."'" : "" ;

			if($db->check_id($post->alias, BLOCKS_TABLE, "alias", $check_alias)) {
				$logger->error("Алиас блока не уникален!", false);
			}
		}

		// Упраздняем временно...
		// if(!isset($post->content)) $parse->msg("Пустое тело блока!", false);

		# check thumb size
		if(!isset($post->thumb_img_width)) {
			$post->thumb_img_width = 0;
		}
		if(!isset($post->thumb_img_height)) {
			$post->thumb_img_height = 0;
		}
	}
}
