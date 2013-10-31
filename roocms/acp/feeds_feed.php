<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Feeds
* @subpackage	Feed
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.5.4
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


class ACP_FEEDS_FEED {

        /**
        * Действия для редактирования настроек ленты
        *
        * @param int $id - Идентификатор ленты
        */
	function settings($id) {

		global $db, $config, $tpl, $smarty, $GET;

		if($db->check_id($GET->_page, STRUCTURE_TABLE, "id", "page_type='feed'")) {
			$q = $db->query("SELECT id, rss, items_per_page, thumb_img_width, thumb_img_height FROM ".STRUCTURE_TABLE." WHERE id='".$GET->_page."'");
			$feed = $db->fetch_assoc($q);

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
	 * @param $id - идентификатор ленты
	 */
	function update_settings($id) {

		global $db, $img, $GET, $POST, $parse;

		if(@$_REQUEST['update_settings'] && $db->check_id($GET->_page, STRUCTURE_TABLE, "id", "page_type='feed'")) {
			# update buffer
			$update = "";

			# RSS flag
			$update .= (isset($POST->rss) && $POST->rss == "1") ? " rss='1', " : " rss='0', " ;
			$update .= (isset($POST->items_per_page) && round($POST->items_per_page) >= 0) ? " items_per_page='".round($POST->items_per_page)."', " : "" ;

			# thumbnail check
			$img->check_post_thumb_parametrs();

			# up data to db
			$db->query("UPDATE ".STRUCTURE_TABLE." SET ".$update." thumb_img_width='".$POST->thumb_img_width."', thumb_img_height='".$POST->thumb_img_height."', date_modified='".time()."' WHERE id='".$GET->_page."'");

			$parse->msg("Настройки успешно обновлены");
		}

		# переход
		goback();
	}


	/**
	 * Функция вызова настроек ленты для редактирования.
	 * @param int $id - идентификатор ленты
	 */
	function control($id) {

		global $db, $parse, $tpl, $smarty;

		$q = $db->query("SELECT id, title, alias FROM ".STRUCTURE_TABLE." WHERE id='".$id."'");
		$feed = $db->fetch_assoc($q);

		$smarty->assign("feed", $feed);

		$feedlist = array();
		$q = $db->query("SELECT id, status, title, brief_item, date_publications, date_end_publications, date_update FROM ".PAGES_FEED_TABLE." WHERE sid='".$id."' ORDER BY date_publications DESC, date_create DESC, date_update DESC");
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
	function create_item() {

		global $db, $parse, $img, $POST, $GET, $tpl, $smarty;

		if(@$_REQUEST['create_item']) {
			if(!isset($POST->title)) 	$parse->msg("Не заполнен заголовок элемента",false);
			if(!isset($POST->brief_item)) 	$parse->msg("Не заполнен аннотация элемента",false);
			if(!isset($POST->full_item)) 	$parse->msg("Не заполнен подробный текст элемента",false);

			# дата публикации и продолжительности
			if(!isset($POST->date_publications)) 		$POST->date_publications	= date("d.m.Y",time());
			if(!isset($POST->date_end_publications))	$POST->date_end_publications	= 0;

			#meta
			if(!isset($POST->meta_description))	$POST->meta_description	= "";
			if(!isset($POST->meta_keywords))	$POST->meta_keywords	= "";


			if(!isset($_SESSION['error'])) {

				$POST->date_publications = $parse->date->rusint_to_unix($POST->date_publications);
				if($POST->date_end_publications != 0) $POST->date_end_publications = $parse->date->rusint_to_unix($POST->date_end_publications);

				if($POST->date_end_publications != 0 && $POST->date_end_publications <= $POST->date_publications) $POST->date_end_publications = 0;


				# insert
				$db->query("INSERT INTO ".PAGES_FEED_TABLE." (title, meta_description, meta_keywords,
									      brief_item, full_item,
									      date_create, date_update, date_publications, date_end_publications, sid)
								      VALUES ('".$POST->title."', '".$POST->meta_description."', '".$POST->meta_keywords."',
									      '".$POST->brief_item."', '".$POST->full_item."', '".time()."', '".time()."',
									      '".$POST->date_publications."', '".$POST->date_end_publications."', '".$GET->_page."')");

				#notice
				$parse->msg("Элемент ".$POST->title." успешно создан.");

				# get feed id
				$fid = $db->insert_id();

				# read thumbnail parametrs
				$q = $db->query("SELECT thumb_img_width, thumb_img_height FROM ".STRUCTURE_TABLE." WHERE id='".$GET->_page."'");
				$thumbsize = $db->fetch_assoc($q);

				# attachment images
				$images = $img->upload_image("images", "", array($thumbsize['thumb_img_width'], $thumbsize['thumb_img_height']));
				if($images) {
					foreach($images AS $image) {
						$img->insert_images($image, "feedid=".$fid);
					}
				}

				# recount items
				$this->count_items($GET->_page);
			}

			# переход
			go(CP."?act=feeds&part=control&page=".$GET->_page);
		}

		# show upload images form
		require_once _LIB."/mimetype.php";
		$smarty->assign("allow_images_type", $imagetype);
		$imagesupload = $tpl->load_template("images_upload", true);
		$smarty->assign("imagesupload", $imagesupload);

		$content = $tpl->load_template("feeds_create_item_feed", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Функция вызова параметров элемента ленты для их редактирвоания
	 * @param $id - идентификатор элемента ленты
	 */
	function edit_item($id) {

		global $db, $img, $tpl, $smarty, $parse;


		$q = $db->query("SELECT id, sid, status, title, meta_description, meta_keywords, brief_item, full_item, date_publications, date_end_publications FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");
		$item = $db->fetch_assoc($q);

		$item['date_publications'] = $parse->date->unix_to_rusint($item['date_publications']);
       		if($item['date_end_publications'] != 0)
		$item['date_end_publications'] = $parse->date->unix_to_rusint($item['date_end_publications']);

		$smarty->assign("item",$item);


		# download attached images
		$attachimg = array();
		$attachimg = $img->load_images("feedid=".$id);
		$smarty->assign("attachimg", $attachimg);


		# show attached images
		$attachedimages = $tpl->load_template("images_attach", true);
		$smarty->assign("attachedimages", $attachedimages);

		# show upload images form
		require_once _LIB."/mimetype.php";
		$smarty->assign("allow_images_type", $imagetype);
		$imagesupload = $tpl->load_template("images_upload", true);
		$smarty->assign("imagesupload", $imagesupload);


		$content = $tpl->load_template("feeds_edit_item_feed", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Обновляем данные элемента ленты
	 *
	 * @param int $id - item id
	 */
	function update_item($id) {

		global $db, $parse, $img, $POST, $GET;

		if(!isset($POST->title)) 	$parse->msg("Не заполнен заголовок элемента",false);
		if(!isset($POST->brief_item)) 	$parse->msg("Не заполнен аннотация элемента",false);
		if(!isset($POST->full_item)) 	$parse->msg("Не заполнен подробный текст элемента",false);

		# status
		if(!isset($POST->status) || $POST->status >= 2) $POST->status = 1;

		# дата публикации и продолжительности
		if(!isset($POST->date_publications)) 		$POST->date_publications	= date("d.m.Y",time());
		if(!isset($POST->date_end_publications))	$POST->date_end_publications	= 0;

		# meta
		if(!isset($POST->meta_description))	$POST->meta_description	= "";
		if(!isset($POST->meta_keywords))	$POST->meta_keywords	= "";

		if(!isset($_SESSION['error'])) {

                        $POST->date_publications = $parse->date->rusint_to_unix($POST->date_publications);
                        if($POST->date_end_publications != 0) $POST->date_end_publications = $parse->date->rusint_to_unix($POST->date_end_publications);

                        if($POST->date_end_publications != 0 && $POST->date_end_publications <= $POST->date_publications) $POST->date_end_publications = 0;

		        $db->query("UPDATE ".PAGES_FEED_TABLE." SET status='".$POST->status."',
		        					    title='".$POST->title."',
							            meta_description='".$POST->meta_description."',
							            meta_keywords='".$POST->meta_keywords."',
							            brief_item='".$POST->brief_item."',
							            full_item='".$POST->full_item."',
							            date_publications='".$POST->date_publications."',
							            date_end_publications='".$POST->date_end_publications."',
							            date_update='".time()."'
						              WHERE id='".$id."'");

			$parse->msg("Элемент ".$POST->title." успешно отредактирован.");

			#sortable images
			if(isset($POST->sort)) {
				$sortimg = $img->load_images("feedid=".$id);
				foreach($sortimg AS $k=>$v) {
					if(isset($POST->sort[$v['id']]) && $POST->sort[$v['id']] != $v['sort']) {
						$db->query("UPDATE ".IMAGES_TABLE." SET sort='".$POST->sort[$v['id']]."' WHERE id='".$v['id']."'");
						if(DEBUGMODE) $parse->msg("Изображению ".$v['id']." успешно присвоен порядок ".$POST->sort[$v['id']]);
					}
				}
			}


			# read thumbnail parametrs
			$q = $db->query("SELECT thumb_img_width, thumb_img_height FROM ".STRUCTURE_TABLE." WHERE id='".$GET->_page."'");
			$thumbsize = $db->fetch_assoc($q);

			# attachment images
			$images = $img->upload_image("images", "", array($thumbsize['thumb_img_width'], $thumbsize['thumb_img_height']));
			if($images) {
				foreach($images AS $image) {
					$img->insert_images($image, "feedid=".$id);
				}
			}

			#go
			go(CP."?act=feeds&part=control&page=".$GET->_page);
		}
		# back
		else goback();
	}


	/**
	 * Функция удаления отдельног элемента ищ ленты
	 * @param $id - идентификатор ленты
	 */
	function delete_item($id) {

		global $db, $parse, $img;

		$q = $db->query("SELECT sid FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");
		$row = $db->fetch_assoc($q);

		# del attached images
		$img->delete_images("feedid=".$id);

		# delete item
		$db->query("DELETE FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");

		# recount items
		$this->count_items($row['sid']);

		# уведомление
		$parse->msg("Элемент id-".$id." успешно удален.");

		# переход
		goback();
	}


	/**
	 * Функция удаления ленты
	 *
	 * @param $sid - structure element id
	 */
	function delete_feed($sid) {

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
	function count_items($sid) {

		global $db;

		# count
		$q = $db->query("SELECT count(*) as items FROM ".PAGES_FEED_TABLE." WHERE sid='".$sid."'");
		$row = $db->fetch_assoc($q);

		# save
		$db->query("UPDATE ".STRUCTURE_TABLE." SET items='".$row['items']."' WHERE id='".$sid."'");
	}
}
?>