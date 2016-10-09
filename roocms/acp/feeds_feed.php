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
* @subpackage	Feeds
* @subpackage	Feed
* @author       alex Roosso
* @copyright    2010-2017 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.10.1
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('ACP')) die('Access Denied');
//#########################################################


class ACP_FEEDS_FEED {

	# vars
	private $feed = array();	# structure parametrs



	/**
	 * "Ключ на старт"
	 *
	 * @param $structure_data
	 */
	public function __construct($structure_data) {
		$this->feed =& $structure_data;
	}


        /**
        * Действия для редактирования настроек ленты
        */
	public function settings() {

		global $db, $config, $tpl, $smarty, $GET;

		if($db->check_id($GET->_page, STRUCTURE_TABLE, "id", "page_type='feed'")) {

			$feed =& $this->feed;

			# Уведомление о глобальном отключении RSS лент
			$feed['rss_warn'] = (!$config->rss_power) ? true : false ;

			# глобальное значение количества элементов на страницу
			$feed['global_items_per_page'] =& $config->feed_items_per_page;

			$smarty->assign("feed",$feed);


			# default thumb size
			$default_thumb_size = array('width'	=> $config->gd_thumb_image_width,
						    'height'	=> $config->gd_thumb_image_height);
			$smarty->assign("default_thumb_size", $default_thumb_size);


			$content = $tpl->load_template("feeds_settings_feed", true);
			$smarty->assign("content", $content);
		}
		else goback();
	}


	/**
	 * Функция обновления настроек ленты
	 */
	public function update_settings() {

		global $db, $img, $POST, $logger;

		if(isset($POST->update_settings)) {
			# update buffer
			$update = "";

			# RSS flag
			$update .= (isset($POST->rss) && $POST->rss == "1") ? " rss='1', " : " rss='0', " ;
			$update .= (isset($POST->items_per_page) && round($POST->items_per_page) >= 0) ? " items_per_page='".round($POST->items_per_page)."', " : "" ;

			# thumbnail check
			$img->check_post_thumb_parametrs();

			$update .= (isset($POST->items_sorting) && ($POST->items_sorting == "title_asc" || $POST->items_sorting == "title_desc" || $POST->items_sorting == "manual_sorting"))
				? " items_sorting = '".$POST->items_sorting."', " : " items_sorting = 'datepublication', " ;

			# show_child_feeds
			$show_child_feeds = "none";
			if(isset($POST->show_child_feeds)) {
				switch($POST->show_child_feeds) {
					case 'default':
						$show_child_feeds = "default";
						break;

					case 'forced':
						$show_child_feeds = "forced";
						break;

					default:
						$show_child_feeds = "none";
						break;
				}
			}


			# up data to db
			$db->query("UPDATE ".STRUCTURE_TABLE."
					SET
						".$update."
						show_child_feeds='".$show_child_feeds."',
						thumb_img_width='".$POST->thumb_img_width."',
						thumb_img_height='".$POST->thumb_img_height."',
						date_modified='".time()."'
					WHERE
						id='".$this->feed['id']."'");

			$logger->info("Настройки успешно обновлены.");
		}

		# переход
		goback();
	}


	/**
	 * Функция вызова ленты для редактирования.
	 */
	public function control() {

		global $db, $parse, $tpl, $smarty;

		switch($this->feed['items_sorting']) {
			case 'datepublication':
				$order = "date_publications DESC, date_create DESC, date_update DESC";
				break;

			case 'title_asc':
				$order = "title ASC, date_publications DESC";
				break;

			case 'title_desc':
				$order = "title DESC, date_publications DESC";
				break;

			case 'manual_sorting':
				$order = "sort ASC, date_publications DESC, date_create DESC";
				break;

			default:
				$order = "date_publications DESC, date_create DESC, date_update DESC";
				break;
		}

		$smarty->assign("feed", $this->feed);

		$feedlist = array();
		$q = $db->query("SELECT id, status, title, brief_item, date_publications, date_end_publications, date_update FROM ".PAGES_FEED_TABLE." WHERE sid='".$this->feed['id']."' ORDER BY ".$order);
		while($row = $db->fetch_assoc($q)) {
        		$row['publication_status'] = ($row['date_end_publications'] < time() && $row['date_end_publications'] != 0) ? "hide" : "show" ;

			$row['date_publications'] 	= $parse->date->unix_to_rus($row['date_publications']);
			if($row['date_end_publications'] != 0)
			$row['date_end_publications'] 	= $parse->date->unix_to_rus($row['date_end_publications']);
			$row['date_update'] 		= $parse->date->unix_to_rus($row['date_update'], false, true, true);

			$feedlist[] = $row;
		}

		$smarty->assign("feedlist",$feedlist);

		$content = $tpl->load_template("feeds_control_feed", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Функция создает новые эелемент ленты, занося его параметры в БД
	 */
	public function create_item() {

		global $db, $parse, $logger, $files, $img, $POST, $tpl, $smarty;

		if(isset($POST->create_item)) {

			# Проверяем вводимые поля на ошибки
			$this->check_item_fields();

			if(!isset($_SESSION['error'])) {

				$POST->date_publications = $parse->date->rusint_to_unix($POST->date_publications);
				if($POST->date_end_publications != 0) $POST->date_end_publications = $parse->date->rusint_to_unix($POST->date_end_publications);

				if($POST->date_end_publications != 0 && $POST->date_end_publications <= $POST->date_publications) $POST->date_end_publications = 0;

				# sort
				if(!isset($POST->itemsort) || $POST->itemsort < 0) $POST->itemsort = 0;
				else $POST->itemsort = round($POST->itemsort);

				# insert
				$db->query("INSERT INTO ".PAGES_FEED_TABLE." (title, meta_description, meta_keywords,
									      brief_item, full_item,
									      date_create, date_update, date_publications, date_end_publications,
									      sort, sid)
								      VALUES ('".$POST->title."', '".$POST->meta_description."', '".$POST->meta_keywords."',
									      '".$POST->brief_item."', '".$POST->full_item."', '".time()."', '".time()."',
									      '".$POST->date_publications."', '".$POST->date_end_publications."',
									      '".$POST->itemsort."', '".$this->feed['id']."')");

				# notice
				$logger->info("Элемент ".$POST->title." успешно создан.");

				# get feed id
				$fid = $db->insert_id();


				# attachment images
				$images = $img->upload_image("images", "", array($this->feed['thumb_img_width'], $this->feed['thumb_img_height']));
				if($images) {
					foreach($images AS $image) {
						$img->insert_images($image, "feedid=".$fid);
					}
				}


				# attachment files
				$attachs = $files->upload("files");
				if($attachs) {
					foreach($attachs AS $attach) {
						$files->insert_file($attach, "feedid=".$fid);
					}
				}


				# recount items
				$this->count_items($this->feed['id']);
			}

			# переход
			go(CP."?act=feeds&part=control&page=".$this->feed['id']);
		}


		# show upload files & images form
		$tpl->load_image_upload_tpl("imagesupload");
		$tpl->load_files_upload_tpl("filesupload");

		# feed data
		$smarty->assign("feed", $this->feed);


		# tpl
		$content = $tpl->load_template("feeds_create_item_feed", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Функция вызова параметров элемента ленты для их редактирвоания
	 *
	 * @param $id - идентификатор элемента ленты
	 */
	public function edit_item($id) {

		global $db, $files, $img, $tpl, $smarty, $parse;


		$q = $db->query("SELECT id, sid, status, sort, title, meta_description, meta_keywords, brief_item, full_item, date_publications, date_end_publications FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");
		$item = $db->fetch_assoc($q);


		$item['date_publications'] = $parse->date->unix_to_rusint($item['date_publications']);

		if($item['date_end_publications'] != 0)
			$item['date_end_publications'] = $parse->date->unix_to_rusint($item['date_end_publications']);


		$smarty->assign("item",$item);


		# download attached images
		$attachimg = $img->load_images("feedid=".$id);
		$smarty->assign("attachimg", $attachimg);

		# show attached images
		$attachedimages = $tpl->load_template("images_attach", true);
		$smarty->assign("attachedimages", $attachedimages);


		# download attached files
		$attachfile = $files->load_files("feedid=".$id);
		$smarty->assign("attachfile", $attachfile);

		# show attached files
		$attachedfiles = $tpl->load_template("files_attach", true);
		$smarty->assign("attachedfiles", $attachedfiles);


		# show upload files & images form
		$tpl->load_image_upload_tpl("imagesupload");
		$tpl->load_files_upload_tpl("filesupload");


		# feed data
		$smarty->assign("feed", $this->feed);


		# tpl
		$content = $tpl->load_template("feeds_edit_item_feed", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Обновляем данные элемента ленты
	 *
	 * @param int $id - item id
	 */
	public function update_item($id) {

		global $db, $parse, $logger, $files, $img, $POST, $GET;

		# Проверяем вводимые поля на ошибки
		$this->check_item_fields();

		# update
		if(!isset($_SESSION['error'])) {

                        $POST->date_publications = $parse->date->rusint_to_unix($POST->date_publications);
                        if($POST->date_end_publications != 0) $POST->date_end_publications = $parse->date->rusint_to_unix($POST->date_end_publications);

                        if($POST->date_end_publications != 0 && $POST->date_end_publications <= $POST->date_publications) $POST->date_end_publications = 0;

			# sort
			if(!isset($POST->itemsort) || $POST->itemsort < 0) $POST->itemsort = 0;
			else $POST->itemsort = round($POST->itemsort);

		        $db->query("UPDATE ".PAGES_FEED_TABLE."
		        		SET
		        			status = '".$POST->status."',
		        			sort = '".$POST->itemsort."',
						title = '".$POST->title."',
						meta_description = '".$POST->meta_description."',
						meta_keywords = '".$POST->meta_keywords."',
						brief_item = '".$POST->brief_item."',
						full_item = '".$POST->full_item."',
						date_publications = '".$POST->date_publications."',
						date_end_publications = '".$POST->date_end_publications."',
						date_update = '".time()."'
					WHERE
						id = '".$id."'");

			$logger->info("Элемент ".$POST->title." успешно отредактирован.");

			# sortable images
			if(isset($POST->sort)) {
				$sortimg = $img->load_images("feedid=".$id);
				foreach($sortimg AS $k=>$v) {
					if(isset($POST->sort[$v['id']]) && $POST->sort[$v['id']] != $v['sort']) {
						$db->query("UPDATE ".IMAGES_TABLE." SET sort='".$POST->sort[$v['id']]."' WHERE id='".$v['id']."'");
						$logger->info("Изображению ".$v['id']." успешно присвоен порядок ".$POST->sort[$v['id']]);
					}
				}
			}


			# attachment images
			$images = $img->upload_image("images", "", array($this->feed['thumb_img_width'], $this->feed['thumb_img_height']));
			if($images) {
				foreach($images AS $image) {
					$img->insert_images($image, "feedid=".$id);
				}
			}


			# attachment files
			$attachs = $files->upload("files");
			if($attachs) {
				foreach($attachs AS $attach) {
					$files->insert_file($attach, "feedid=".$id);
				}
			}


			# go
			go(CP."?act=feeds&part=control&page=".$GET->_page);
		}
		# back
		else goback();
	}


	/**
	 * Функция переноса элемента из одной ленты в другую
	 *
	 * @param $id - идентификатор элемента ленты
	 */
	public function migrate_item($id) {

		global $db, $logger, $tpl, $smarty, $POST;


		# Migrate
		if(isset($POST->migrate_item) && isset($POST->from) && isset($POST->to) && $db->check_id($POST->from, STRUCTURE_TABLE, "id", "page_type='feed'") && $db->check_id($POST->to, STRUCTURE_TABLE, "id", "page_type='feed'")) {

			$db->query("UPDATE ".PAGES_FEED_TABLE."
		        		SET
		        			sid = '".$POST->to."',
						date_update = '".time()."'
					WHERE
						id = '".$id."'");

			# recount items
			$this->count_items($POST->from);
			$this->count_items($POST->to);


			# notice
			$logger->info("Элемент id : ".$id." успешно перемещен.");

			#go
			go(CP."?act=feeds&part=control&page=".$POST->to);
		}


		# get data item from db
		$q = $db->query("SELECT id, sid, title FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");
		$data = $db->fetch_assoc($q);

		# smarty vars
		$smarty->assign("item", $data);


		# get data feeds from db
		$feeds = array();
		$q = $db->query("SELECT id, title, alias FROM ".STRUCTURE_TABLE." WHERE page_type='feed' ORDER BY id ASC");
		while($row = $db->fetch_assoc($q)) {
			$feeds[$row['id']] = $row;
		}

		# smarty vars
		$smarty->assign("feeds", $feeds);


		# tpl
		$content = $tpl->load_template("feeds_migrate_item_feed", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Функция изменяет статус элемента ленты
	 *
	 * @param     $id - идентификатор элемента ленты
	 * @param int $status - 1= Видимый , 2=Скрытый
	 */
	public function change_item_status($id, $status = 1) {
		global $db, $logger;

		$status = round($status);
		if($status >= 2 || $status < 0) $status = 1;

		#db
		$db->query("UPDATE ".PAGES_FEED_TABLE." SET status='".$status."' WHERE id='".$id."'");

		# notice
		$mstatus = ($status == 1) ? "Видимый" : "Скрытый" ;
		$logger->info("Элемент #".$id." успешно изменил свой статус на <".$mstatus.">.");

		goback();
	}


	/**
	 * Функция удаления отдельного элемента из ленты
	 *
	 * @param $id - идентификатор элемента ленты
	 */
	public function delete_item($id) {

		global $db, $logger, $img;

		$q = $db->query("SELECT sid FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");
		$row = $db->fetch_assoc($q);

		# del attached images
		$img->delete_images("feedid=".$id);

		# delete item
		$db->query("DELETE FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");

		# recount items
		$this->count_items($row['sid']);

		# уведомление
		$logger->info("Элемент id-".$id." успешно удален.");

		# переход
		goback();
	}


	/**
	 * Функция удаления ленты
	 *
	 * @param $sid - structure element id
	 */
	public function delete_feed($sid) {

		global $db, $img;

		$where = "";
		$f = $db->query("SELECT id FROM ".PAGES_FEED_TABLE." WHERE sid='".$sid."'");
		while($fid = $db->fetch_assoc($f)) {
			$where .= (trim($where) != "") ? " OR attachedto='feedid=".$fid['id']."' " :  " attachedto='feedid=".$fid['id']."' " ;
		}

		# del attached images
		if(trim($where) != "") {
                	$img->delete_images($where, true);
		}

		$db->query("DELETE FROM ".PAGES_FEED_TABLE." WHERE sid='".$sid."'");
	}


	/**
	 * Функция пересчета элементов в фиде
	 *
	 * @param int $sid - структурный идентификатор ленты
	 */
	public function count_items($sid) {

		global $db;

		# count
		$q = $db->query("SELECT count(*) as items FROM ".PAGES_FEED_TABLE." WHERE sid='".$sid."'");
		$row = $db->fetch_assoc($q);

		# save
		$db->query("UPDATE ".STRUCTURE_TABLE." SET items='".$row['items']."' WHERE id='".$sid."'");
	}


	private function check_item_fields() {

		global $POST, $logger;

		if(!isset($POST->title)) 	$logger->error("Не заполнен заголовок элемента");
		if(!isset($POST->full_item)) 	$logger->error("Не заполнен подробный текст элемента");

		# status
		if(!isset($POST->status) || $POST->status >= 2) $POST->status = 1;

		# дата публикации и продолжительности
		if(!isset($POST->date_publications)) 		$POST->date_publications	= date("d.m.Y",time());
		if(!isset($POST->date_end_publications))	$POST->date_end_publications	= 0;

		# meta
		if(!isset($POST->meta_description))	$POST->meta_description	= "";
		if(!isset($POST->meta_keywords))	$POST->meta_keywords	= "";
		if(!isset($POST->brief_item)) 		$POST->brief_item = "";
	}
}
?>