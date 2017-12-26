<?php
/**
 *   RooCMS - Russian Open Source Free Content Managment System
 *   Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
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
 *   RooCMS - Бесплатная система управления контентом с открытым исходным кодом
 *   Copyright © 2010-2018 александр Белов (alex Roosso). Все права защищены.
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
 * @subpackage   Admin Control Panel
 * @subpackage   Feeds
 * @subpackage   Feed
 * @author       alex Roosso
 * @copyright    2010-2018 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.14.1
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


class ACP_Feeds_Feed {

	# vars
	private $feed     = array();	# structure parametrs
	private $userlist = array();



	/**
	 * "Ключ на старт"
	 *
	 * @param $structure_data
	 */
	public function __construct($structure_data) {
		$this->feed =& $structure_data;
	}


	/**
	 * Функция вызова ленты для редактирования.
	 */
	public function control() {

		global $db, $parse, $tags, $tpl, $smarty;

		switch($this->feed['items_sorting']) {
			case 'title_asc':
				$order = "title ASC, date_publications DESC";
				break;

			case 'title_desc':
				$order = "title DESC, date_publications DESC";
				break;

			case 'manual_sorting':
				$order = "sort ASC, date_publications DESC, date_create DESC";
				break;

			default:  // case 'datepublication'
				$order = "date_publications DESC, date_create DESC, date_update DESC";
				break;
		}

		$smarty->assign("feed", $this->feed);


		# feed items
		$taglinks = array();
		$feedlist = array();
		$q = $db->query("SELECT id, status, title, brief_item, date_publications, date_end_publications, date_update, views FROM ".PAGES_FEED_TABLE." WHERE sid='".$this->feed['id']."' ORDER BY ".$order);
		while($row = $db->fetch_assoc($q)) {

			$row['publication_status'] = ($row['date_end_publications'] < time() && $row['date_end_publications'] != 0) ? "hide" : "show" ;

			$row['date_publications'] = $parse->date->unix_to_rus($row['date_publications']);

			if($row['date_end_publications'] != 0) {
				$row['date_end_publications'] = $parse->date->unix_to_rus($row['date_end_publications']);
			}

			$row['date_update'] = $parse->date->unix_to_rus($row['date_update'], false, true, true);

			$taglinks[$row['id']] = "feeditemid=".$row['id'];
			$feedlist[$row['id']] = $row;
		}


		# tags collect
		$alltags = $tags->read_tags($taglinks);
		foreach((array)$alltags AS $value) {
			$lid = explode("=",$value['linkedto']);
			$feedlist[$lid[1]]['tags'][] = array("tag_id"=>$value['tag_id'], "title"=>$value['title']);
		}


		$smarty->assign("feedlist",$feedlist);

		$content = $tpl->load_template("feeds_control_feed", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Функция создает новые эелемент ленты, занося его параметры в БД
	 */
	public function create_item() {

		global $db, $users, $logger, $tags, $files, $img, $POST, $tpl, $smarty;

		# insert db
		if(isset($POST->create_item)) {

			# Проверяем вводимые поля на ошибки
			$this->check_post_data_fields();
			$this->control_post_data_date();
			$this->control_post_data_meta();

			if(!isset($_SESSION['error'])) {

				# Проверяем "неважные" поля
				$this->correct_post_fields();

				# insert
				$db->query("INSERT INTO ".PAGES_FEED_TABLE." (title, meta_description, meta_keywords,
									      brief_item, full_item, author_id,
									      date_create, date_update, date_publications, date_end_publications,
									      sort, sid)
								      VALUES ('".$POST->title."', '".$POST->meta_description."', '".$POST->meta_keywords."',
									      '".$POST->brief_item."', '".$POST->full_item."', '".$POST->author_id."',
									      '".time()."', '".time()."', '".$POST->date_publications."', '".$POST->date_end_publications."',
									      '".$POST->itemsort."', '".$this->feed['id']."')");

				# get feed item id
				$fiid = $db->insert_id();

				# save tags
				$tags->save_tags($POST->tags, "feeditemid=".$fiid);


				# attachment images
				$images = $img->upload_image("images", "", array($this->feed['thumb_img_width'], $this->feed['thumb_img_height']));
				if($images) {
					foreach($images AS $image) {
						$img->insert_images($image, "feeditemid=".$fiid);
					}
				}

				# attachment files
				$attachs = $files->upload("files");
				if($attachs) {
					foreach($attachs AS $attach) {
						$files->insert_file($attach, "feeditemid=".$fiid);
					}
				}


				# recount items
				$this->count_items($this->feed['id']);


				# notice
				$logger->info("Элемент #".$fiid." <".$POST->title."> успешно создан.");

				// TODO: Переделать!
				# mailling
				$this->mailing($fiid, $POST->title,$POST->brief_item, $POST->force);
			}

			# переход
			go(CP."?act=feeds&part=control&page=".$this->feed['id']);
		}

		# userlist
		$this->userlist = $users->get_userlist();

		# popular tags
		$poptags = $tags->list_tags(true, 15);

		# show upload files & images form
		$tpl->load_image_upload_tpl("imagesupload");
		$tpl->load_files_upload_tpl("filesupload");

		# feed data
		$smarty->assign("feed", $this->feed);

		# tags
		$smarty->assign("poptags", $poptags);

		# users
		$smarty->assign("userdata", $users->userdata);
		$smarty->assign("userlist", $this->userlist);

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

		global $db, $users, $tags, $files, $img, $tpl, $smarty, $parse;

		# userlist
		$this->userlist = $users->get_userlist();

		# get data
		$q = $db->query("SELECT id, sid, status, sort, title, meta_description, meta_keywords, brief_item, full_item, author_id, date_publications, date_end_publications FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");
		$item = $db->fetch_assoc($q);


		$item['date_publications'] = $parse->date->unix_to_rusint($item['date_publications']);

		if($item['date_end_publications'] != 0) {
			$item['date_end_publications'] = $parse->date->unix_to_rusint($item['date_end_publications']);
		}

		# item tags
		$item['tags'] = implode(", ", array_map(array("Tags", "get_tag_title"), $tags->read_tags("feeditemid=".$id)));


		$smarty->assign("item",$item);

		# popular tags
		$poptags = $tags->list_tags(true, 15);


		# download attached images
		$attachimg = $img->load_images("feeditemid=".$id);
		$smarty->assign("attachimg", $attachimg);

		# show attached images
		$attachedimages = $tpl->load_template("images_attach", true);
		$smarty->assign("attachedimages", $attachedimages);


		# download attached files
		$attachfile = $files->load_files("feeditemid=".$id);
		$smarty->assign("attachfile", $attachfile);

		# show attached files
		$attachedfiles = $tpl->load_template("files_attach", true);
		$smarty->assign("attachedfiles", $attachedfiles);


		# show upload files & images form
		$tpl->load_image_upload_tpl("imagesupload");
		$tpl->load_files_upload_tpl("filesupload");


		# feed data
		$smarty->assign("feed", $this->feed);

		# tags
		$smarty->assign("poptags", $poptags);

		# users
		$smarty->assign("userdata", $users->userdata);
		$smarty->assign("userlist", $this->userlist);

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

		global $db, $logger, $tags, $files, $img, $POST, $get;

		# Проверяем вводимые поля на ошибки
		$this->check_post_data_fields();
		$this->control_post_data_date();
		$this->control_post_data_meta();

		# update
		if(!isset($_SESSION['error'])) {

                        # Проверяем "неважные" поля
			$this->correct_post_fields();

			# update
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
						date_update = '".time()."',
						author_id = '".$POST->author_id."'
					WHERE
						id = '".$id."'");

			# save tags
			$tags->save_tags($POST->tags, "feeditemid=".$id);

			# notice
			$logger->info("Элемент ".$POST->title." (#".$id.") успешно отредактирован.");

			# sortable images
			if(isset($POST->sort)) {
				$sortimg = $img->load_images("feeditemid=".$id);
				foreach($sortimg AS $v) {
					if(isset($POST->sort[$v['id']]) && $POST->sort[$v['id']] != $v['sort']) {
						$db->query("UPDATE ".IMAGES_TABLE." SET sort='".$POST->sort[$v['id']]."' WHERE id='".$v['id']."'");
					}
				}
			}


			# attachment images
			$images = $img->upload_image("images", "", array($this->feed['thumb_img_width'], $this->feed['thumb_img_height']));
			if($images) {
				foreach($images AS $image) {
					$img->insert_images($image, "feeditemid=".$id);
				}
			}


			# attachment files
			$attachs = $files->upload("files");
			if($attachs) {
				foreach($attachs AS $attach) {
					$files->insert_file($attach, "feeditemid=".$id);
				}
			}


			# go
			go(CP."?act=feeds&part=control&page=".$get->_page);
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
			$logger->info("Элемент #".$id." успешно перемещен.");

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
		if($status >= 2 || $status < 0) {
			$status = 1;
		}

		# обновляем инфу в бд
		$db->query("UPDATE ".PAGES_FEED_TABLE." SET status='".$status."' WHERE id='".$id."'");

		# уведомление
		$mstatus = ($status == 1) ? "Видимый" : "Скрытый" ;
		$logger->info("Элемент #".$id." успешно изменил свой статус на <".$mstatus.">.");

		# переход
		goback();
	}


	/**
	 * Функция удаления отдельного элемента из ленты
	 *
	 * @param $id - идентификатор элемента ленты
	 */
	public function delete_item($id) {

		global $db, $logger, $img;

		# получаем информацию
		$q = $db->query("SELECT sid FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");
		$row = $db->fetch_assoc($q);

		# del attached images
		$img->delete_images("feeditemid=".$id);

		# delete item
		$db->query("DELETE FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");

		# recount items
		$this->count_items($row['sid']);

		# уведомление
		$logger->info("Элемент #".$id." успешно удален.");

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

		$cond = "";
		$f = $db->query("SELECT id FROM ".PAGES_FEED_TABLE." WHERE sid='".$sid."'");
		while($fid = $db->fetch_assoc($f)) {
			$cond .= (trim($cond) != "") ? " OR attachedto='feeditemid=".$fid['id']."' " :  " attachedto='feeditemid=".$fid['id']."' " ;
		}

		# del attached images
		if(trim($cond) != "") {
                	$img->delete_images($cond, true);
		}

		$db->query("DELETE FROM ".PAGES_FEED_TABLE." WHERE sid='".$sid."'");
	}


	/**
	 * Действия для редактирования настроек ленты
	 */
	public function settings() {

		global $db, $config, $tpl, $smarty, $get;

		if($db->check_id($get->_page, STRUCTURE_TABLE, "id", "page_type='feed'")) {

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
		else {
			goback();
		}
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

			$logger->info("Настройки ленты #".$this->feed['id']." успешно обновлены.");
		}

		# переход
		goback();
	}


	/**
	 * Функция пересчета элементов в фиде
	 *
	 * @param int $sid - структурный идентификатор ленты
	 */
	public function count_items($sid) {

		global $db;

		# count
		$items = $db->count(PAGES_FEED_TABLE, "sid='".$sid."'");

		# save
		$db->query("UPDATE ".STRUCTURE_TABLE." SET items='".$items."' WHERE id='".$sid."'");
	}


	/**
	 * Функция првоеряет вводимые поля элеметна ленты на ошибки.
	 */
	private function check_post_data_fields() {

		global $POST, $logger;

		# title
		if(!isset($POST->title)) {
			$logger->error("Не заполнен заголовок элемента", false);
		}

		# brief item
		if(!isset($POST->brief_item)) {
			$POST->brief_item = "";
		}

		# full desc item
		if(!isset($POST->full_item)) {
			$logger->error("Не заполнен подробный текст элемента", false);
		}

		# status
		if(!isset($POST->status) || $POST->status >= 2) {
			$POST->status = 1;
		}
	}


	/**
	 *  Функция првоеряет вводимые даты элеметна ленты на ошибки.
	 */
	private function control_post_data_date() {

		global $POST, $parse;

		# дата публикации
		if(!isset($POST->date_publications)) {
			$POST->date_publications = date("d.m.Y",time());
		}

		# дата завершения публикации
		if(!isset($POST->date_end_publications)) {
			$POST->date_end_publications = 0;
		}

		# date publications
		$POST->date_publications = $parse->date->rusint_to_unix($POST->date_publications);

		# date end publications
		if($POST->date_end_publications != 0) {
			$POST->date_end_publications = $parse->date->rusint_to_unix($POST->date_end_publications);
		}

		if($POST->date_end_publications <= $POST->date_publications) {
			$POST->date_end_publications = 0;
		}
	}


	/**
	 *  Функция првоеряет вводимые мета данные элеметна ленты на ошибки.
	 */
	private function control_post_data_meta() {

		global $POST;

		# meta description
		if(!isset($POST->meta_description)){
			$POST->meta_description	= "";
		}

		# meta keywords
		if(!isset($POST->meta_keywords)) {
			$POST->meta_keywords = "";
		}
	}


	/**
	 * Функция предназначена для кореекции необязательных полей,
	 * что бы не вызывать ошибок про обращении в БД
	 */
	private function correct_post_fields() {

		global $users, $POST;

		# tags
		if(!isset($POST->tags)) {
			$POST->tags = NULL;
		}

		# sort
		if(!isset($POST->itemsort) || round($POST->itemsort) < 0) {
			$POST->itemsort = 0;
		}
		else {
			$POST->itemsort = round($POST->itemsort);
		}

		# userlist
		$this->userlist = $users->get_userlist();

		# author
		if(!isset($POST->author_id) || !array_key_exists($POST->author_id, $this->userlist)) {
			$POST->author_id = 0;
		}
	}


	// TODO: Переделать!
	/**
	 * Это временная функция
	 *
	 * @param     $id
	 * @param     $title
	 * @param     $subject
	 * @param int $force
	 */
	private function mailing($id, $title, $subject, $force=-1) {

		global $users, $logger, $parse, $site;

		if($force != -1) {

			if($force == 1) {
				# all
				$userlist = $users->get_userlist(1,0,-1, NULL, true);
			}
			else {
				# только подписчики
				$userlist = $users->get_userlist(1,0,1, NULL, true);
			}

			# html
			$subject = $parse->text->html($subject);
			$subject = "<h1>".$title."</h1>
					".$subject."
					<br /><br /><a href='".$site['domain']."/index.php?page=".$this->feed['alias']."&id=".$id."'>Читать полностью</a>";

			$log = "";
			foreach($userlist AS $val) {

				# send
				sendmail($val['email'], $title, $subject);

				# log
				$log .= " ".$val['email'];
			}

			$logger->info("Новость отправлена по адресам: ".$log);
		}
	}
}