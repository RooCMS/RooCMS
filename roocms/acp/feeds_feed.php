<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Feeds
* @subpackage	Feed
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.1.2
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


class ACP_FEEDS_FEED {


	/* ####################################################
	 *		Edit Settings
	 */
	function settings($id) {

		global $db, $tpl, $smarty, $GET;

		if($db->check_id($GET->_page, STRUCTURE_TABLE, "id", "type='feed'")) {
			$q = $db->query("SELECT id, rss, items_per_page FROM ".STRUCTURE_TABLE." WHERE id='".$GET->_page."'");
			$feed = $db->fetch_assoc($q);

			$smarty->assign("feed",$feed);

			$content = $tpl->load_template("feeds_settings_feed", true);
			$smarty->assign("content", $content);
		}
		else goback();
	}


	/* ####################################################
	 *		Update Settings
	 */
	function update_settings($id) {

		global $db, $GET, $POST, $parse;

		if(@$_REQUEST['update_settings'] && $db->check_id($GET->_page, STRUCTURE_TABLE, "id", "type='feed'")) {
			$update = "";

			# RSS flag
			$update .= (isset($POST->rss) && $POST->rss == "1") ? " rss='1', " : " rss='0', " ;
			$update .= (isset($POST->items_per_page) && round($POST->items_per_page) > 0) ? " items_per_page='".round($POST->items_per_page)."', " : " items_per_page='10', " ;

			# up data to db
			$db->query("UPDATE ".STRUCTURE_TABLE." SET ".$update." date_modified='".time()."' WHERE id='".$GET->_page."'");

			$parse->msg("Настройки успешно обновлены");
		}

		# go
		goback();
	}


	/* ####################################################
	 *		Control
	 */
	function control($id) {

		global $db, $parse, $tpl, $smarty;

		$q = $db->query("SELECT id, title FROM ".STRUCTURE_TABLE." WHERE id='".$id."'");
		$feed = $db->fetch_assoc($q);

		$smarty->assign("feed", $feed);

		$feedlist = array();
		$q = $db->query("SELECT id, title, brief_item, date_publications, date_update FROM ".PAGES_FEED_TABLE." WHERE sid='".$id."' ORDER BY date_publications DESC, date_create DESC, date_update DESC");
		while($row = $db->fetch_assoc($q)) {
			$row['date_publications'] 	= $parse->date->unix_to_rus($row['date_publications']);
			$row['date_update'] 		= $parse->date->unix_to_rus($row['date_update'], false, true, true);
			$feedlist[] = $row;
		}

		$smarty->assign("feedlist",$feedlist);

		$content = $tpl->load_template("feeds_control_feed", true);
		$smarty->assign("content", $content);
	}


	/* ####################################################
	 *		Create New Item
	 */
	function create_item() {

		global $db, $parse, $gd, $debug, $POST, $GET, $tpl, $smarty;

		if(@$_REQUEST['create_item']) {
			if(!isset($POST->title)) 				$parse->msg("Не заполнен заголовок элемента",false);
			if(!isset($POST->brief_item)) 			$parse->msg("Не заполнен аннотация элемента",false);
			if(!isset($POST->full_item)) 			$parse->msg("Не заполнен подробный текст элемента",false);
			if(!isset($POST->date_publications)) 	$parse->msg("Не указана дата публикации",false);

			if(!isset($_SESSION['error'])) {

				$POST->date_publications = $parse->date->rusint_to_unix($POST->date_publications);

				$db->query("INSERT INTO ".PAGES_FEED_TABLE." (title, brief_item, full_item, date_create, date_update, date_publications, sid)
													  VALUES ('".$POST->title."', '".$POST->brief_item."', '".$POST->full_item."', '".time()."', '".time()."', '".$POST->date_publications."', '".$GET->_page."')");

				$parse->msg("Элемент ".$POST->title." успешно создан.");

				$fid = $db->insert_id();

				# attachment images
				$images = $gd->upload_image("images");
				if($images) {
					foreach($images AS $image) {
						$db->query("INSERT INTO ".IMAGES_TABLE." (attachedto, filename) VALUES ('feedid=".$fid."', '".$image."')");
						if($debug->debug) $parse->msg("Изображение ".$image." успешно загружено на сервер");
					}
				}

				# recount items
				$this->count_items($GET->_page);
			}

			# go
			go(CP."?act=feeds&part=control&page=".$GET->_page);
		}

		$imagesupload = $tpl->load_template("images_upload", true);
		$smarty->assign("imagesupload", $imagesupload);

		$content = $tpl->load_template("feeds_create_item_feed", true);
		$smarty->assign("content", $content);
	}


	/* ####################################################
	 *		Edit Item
	 */
	function edit_item($id) {

		global $db, $tpl, $smarty, $parse;


		$q = $db->query("SELECT id, sid, title, brief_item, full_item, date_publications FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");
		$item = $db->fetch_assoc($q);

		$item['date_publications'] = $parse->date->unix_to_rusint($item['date_publications']);

		$smarty->assign("item",$item);


		# download attached images
		$attachimg = array();
		$attachimg = Structure::load_images("feedid=".$id);
		$smarty->assign("attachimg", $attachimg);

		$attachedimages = $tpl->load_template("images_attach", true);
		$imagesupload = $tpl->load_template("images_upload", true);
		$smarty->assign("attachedimages", $attachedimages);
		$smarty->assign("imagesupload", $imagesupload);


		$content = $tpl->load_template("feeds_edit_item_feed", true);
		$smarty->assign("content", $content);
	}


	/* ####################################################
	 *		Update Item
	 */
	function update_item($id) {

		global $db, $parse, $gd, $debug, $POST, $GET;

		if(!isset($POST->title)) 		$parse->msg("Не заполнен заголовок элемента",false);
		if(!isset($POST->brief_item)) 	$parse->msg("Не заполнен аннотация элемента",false);
		if(!isset($POST->full_item)) 	$parse->msg("Не заполнен подробный текст элемента",false);
		if(!isset($POST->date_publications)) 	$parse->msg("Не указана дата публикации",false);

		if(!isset($_SESSION['error'])) {

			$POST->date_publications = $parse->date->rusint_to_unix($POST->date_publications);

			$db->query("UPDATE ".PAGES_FEED_TABLE." SET title='".$POST->title."', brief_item='".$POST->brief_item."', full_item='".$POST->full_item."', date_publications='".$POST->date_publications."', date_update='".time()."' WHERE id='".$id."'");

			$parse->msg("Элемент ".$POST->title." успешно отредактирован.");

			#sortable images
			if(isset($POST->sort)) {
				$sortimg = Structure::load_images("feedid=".$id);
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
					$db->query("INSERT INTO ".IMAGES_TABLE." (attachedto, filename) VALUES ('feedid=".$id."', '".$image."')");
					if($debug->debug) $parse->msg("Изображение ".$image." успешно загружено на сервер");
				}
			}

			#go
			go(CP."?act=feeds&part=control&page=".$GET->_page);
		}
		# back
		else goback();
	}


	/* #####################################################
	 *		Edit Item
	 */
	function delete_item($id) {

		global $db, $parse;

		$q = $db->query("SELECT sid FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");
		$row = $db->fetch_assoc($q);

		# del attached images
		$i = $db->query("SELECT filename FROM ".IMAGES_TABLE." WHERE attachedto='feedid=".$id."'");
		while($img = $db->fetch_assoc($i)) {
			unlink(_UPLOADIMAGES."/original/".$img['filename']);
			unlink(_UPLOADIMAGES."/resize/".$img['filename']);
			unlink(_UPLOADIMAGES."/thumb/".$img['filename']);
		}
		$db->query("DELETE FROM ".IMAGES_TABLE." WHERE attachedto='feedid=".$id."'");

		# delete item
		$db->query("DELETE FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");

		# recount items
		$this->count_items($row['sid']);

		# notice
		$parse->msg("Элемент id-".$id." успешно удален.");

		# go
		goback();
	}


	/* ####################################################
	 *	Delete
	 */
	function delete_feed($sid) {

		global $db;

		$where = "";
		$f = $db->query("SELECT id FROM ".PAGES_FEED_TABLE." WHERE sid='".$sid."'");
		while($fid = $db->fetch_assoc($f)) {
			$where .= (trim($where) != "") ? " attachedto='feedid=".$fid['id']."' " :  " OR attachedto='feedid=".$fid['id']."' " ;
		}

		# del attached images
		if(trim($where) != "") {
			$i = $db->query("SELECT filename FROM ".IMAGES_TABLE." WHERE ".$where);
			while($img = $db->fetch_assoc($i)) {
				unlink(_UPLOADIMAGES."/original/".$img['filename']);
				unlink(_UPLOADIMAGES."/resize/".$img['filename']);
				unlink(_UPLOADIMAGES."/thumb/".$img['filename']);
			}
			$db->query("DELETE FROM ".IMAGES_TABLE." WHERE ".$where);
		}

		$db->query("DELETE FROM ".PAGES_FEED_TABLE." WHERE sid='".$sid."'");
	}


	/* ####################################################
	 *	Count items
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