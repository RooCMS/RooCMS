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
* @subpackage	Frontend
* @author       alex Roosso
* @copyright    2010-2018 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.4.2
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class PageFeed
 */
class PageFeed {

	# vars
	private $item_id	= 0;
	private $items_per_page	= 10;



	/**
	 * Lets begin...
	 * Why does the gull die?
	 */
	public function __construct() {

		global $GET, $db, $structure, $smarty;

		$feed['title'] 	= $structure->page_title;
		$feed['alias'] 	= $structure->page_alias;
		$feed['id'] 	= $structure->page_id;

		$smarty->assign("feed", $feed);

		if(isset($GET->_id) && $db->check_id(round($GET->_id), PAGES_FEED_TABLE, "id", "(date_end_publications = '0' || date_end_publications > '".time()."') AND status='1'")) {
			$this->item_id = round($GET->_id);
			$this->load_item($this->item_id);
		}
		elseif(isset($GET->_export) && $GET->_export == "RSS" && $structure->page_rss) {
			$this->load_feed_rss();
		}
		else {
			$this->load_feed();
		}
	}


	/**
	 * Загружаем фид
	 */
	private function load_feed() {

		global $db, $config, $structure, $tags,  $rss, $parse, $img, $tpl, $smarty;

		# set limit on per page
		$this->items_per_page = ($structure->page_items_per_page > 0) ? $structure->page_items_per_page : $config->feed_items_per_page ;
		$db->limit =& $this->items_per_page;

		# cond request
		$cond = $this->feed_condition();

		# order request
		$order = $this->feed_order();

		# calculate pages
		$db->pages_mysql(PAGES_FEED_TABLE, $cond);

		# get array pagination template array
		$pages = $this->construct_pagination();

		# RSS
		$rss->set_header_link();


		# Feed list
		$taglinks = array();
		$feeds    = array();
		$q = $db->query("SELECT id, title, brief_item, full_item, date_publications FROM ".PAGES_FEED_TABLE." WHERE ".$cond." ORDER BY ".$order." LIMIT ".$db->from.",".$db->limit);
		while($row = $db->fetch_assoc($q)) {

			if(trim($row['brief_item']) == "") {
				$row['brief_item'] = $row['full_item'];
			}

			$row['datepub']    = $parse->date->unix_to_rus($row['date_publications'],true);
			$row['date']       = $parse->date->unix_to_rus_array($row['date_publications']);
			$row['brief_item'] = $parse->text->html($row['brief_item']);

			$row['image']      = $img->load_images("feeditemid=".$row['id']."", 0, 1);

			$row['tags']       = array();


			$taglinks[$row['id']] = "feeditemid=".$row['id'];
			$feeds[$row['id']] = $row;
		}

		# tags collect
		if(!empty($taglinks)) {
			$alltags = $tags->read_tags($taglinks);
			foreach((array) $alltags AS $value) {
				$lid                      = explode("=", $value['linkedto']);
				$feeds[$lid[1]]['tags'][] = array("tag_id" => $value['tag_id'], "title" => $value['title']);
			}
		}

		# smarty
		$smarty->assign("feeds", $feeds);
		$smarty->assign("pages", $pages);
		$smarty->assign("rsslink", $rss->rss_link);

		$tpl->load_template("feed");
	}


	/**
	 * Load Feed Item
	 *
	 * @param int $id  - идентификатор новости
	 */
	private function load_item($id) {

		global $db, $parse, $tags, $files, $img, $tpl, $smarty, $site;

		# query data
		$q = $db->query("SELECT id, title, meta_description, meta_keywords, full_item, date_publications, sort FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");
		$item = $db->fetch_assoc($q);
		$item['datepub'] 	= $parse->date->unix_to_rus($item['date_publications'],true);
		$item['date']		= $parse->date->unix_to_rus_array($item['date_publications']);
		$item['full_item']	= $parse->text->html($item['full_item']);

		# tags
		$item['tags'] = $tags->read_tags("feeditemid=".$id);

		# add prev/next item
		$item = array_merge($item, $this->load_prevnext_item($item['id']));

		# load attached images
		$images = $img->load_images("feeditemid=".$id);
		$smarty->assign("images", $images);

		# load attached files
		$attachfile = $files->load_files("feeditemid=".$id);
		$smarty->assign("attachfile", $attachfile);

		# meta
		$site['title'] .= " - ".$item['title'];
		if(trim($item['meta_description']) != "") {
			$site['description']	= $item['meta_description'];
		}
		if(trim($item['meta_keywords']) != "") {
			$site['keywords']	= $item['meta_keywords'];
		}

		$smarty->assign("item", $item);
		$tpl->load_template("feed_item");
	}


	/**
	 * загружаем RSS фид
	 */
	private function load_feed_rss() {

		global $db, $rss, $structure;

		$q = $db->query("SELECT id, title, brief_item, date_publications FROM ".PAGES_FEED_TABLE." WHERE date_publications <= '".time()."' AND sid='".$structure->page_id."' AND status='1' ORDER BY date_publications DESC, date_create DESC, date_update DESC LIMIT ".$db->from.",".$db->limit);
		while($row = $db->fetch_assoc($q)) {
			# uri
			$newslink = SCRIPT_NAME."?page=".$structure->page_alias."&id=".$row['id'];

			# item
			$rss->create_item($newslink, $row['title'], $row['brief_item'], $newslink, $row['date_publications'], false, $structure->page_title);
			if($rss->lastbuilddate == 0) {
				$rss->set_lastbuilddate($row['date_publications']);
			}
		}
	}


	/**
	 * функция возвращает условие для запроса фида из БД
	 *
	 * @return string
	 */
	private function feed_condition() {

		global $structure;

		# query id's feeds begin
		$cond = " date_publications <= '".time()."' AND ( sid='".$structure->page_id."' ";

		$showchilds =& $structure->page_show_child_feeds;

		if($showchilds != "none") {
			$qfeeds = $this->construct_child_feeds($structure->page_id, $showchilds);
			foreach($qfeeds as $v) {
				# query id's feeds collect
				$cond .= " OR sid='".$v."' ";
			}
		}

		# query id's feeds final
		$cond .= " ) AND (date_end_publications = '0' || date_end_publications > '".time()."') AND status='1' ";


		# return
		return $cond;
	}


	/**
	 * Функция возвращает условие для порядка сортировки результата запроса из БД
	 *
	 * @return string
	 */
	private function feed_order() {

		global $structure;

		switch($structure->page_items_sorting) {
			case 'title_asc':
				$order = "title ASC, date_publications DESC";
				break;

			case 'title_desc':
				$order = "title DESC, date_publications DESC";
				break;

			case 'manual_sorting':
				$order = "sort ASC, date_publications DESC, date_create DESC";
				break;

			default: // case 'datepublication'
				$order = "date_publications DESC, date_create DESC, date_update DESC";
				break;
		}

		return $order;
	}


	/**
	 * Функция собирает данные по предыдущему и следующим элементом
	 *
	 * @param $id - Идентификатор текущего элемента
	 *
	 * @return array - массив данных с предыдущим и следующим элементами в ленте
	 */
	private function load_prevnext_item($id) {

		global $structure, $db, $img, $parse;

		# cond request
		$cond = $this->feed_condition();

		# order request
		$order = $this->feed_order();

		# query
		$i = 0; $previndex = -1; $nextindex = -1;
		$data = array(); $res = array();
		$q = $db->query("SELECT id, title, date_publications FROM ".PAGES_FEED_TABLE." WHERE ".$cond." ORDER BY ".$order."");
		while($row = $db->fetch_assoc($q)) {

			$row['datepub'] = $parse->date->unix_to_rus($row['date_publications']);
			$row['image']   = $img->load_images("feeditemid=".$row['id']."", 0, 1);

			$res[$i] = $row;

			if($row['id'] == $id) {
				$nextindex = $i - 1;
				if($nextindex >= 0) {
					$data['next'] = $res[$nextindex];
				}

				$previndex = $i + 1;
			}

			if($i == $previndex) {
				$data['prev'] = $res[$previndex];
				break;
			}

			$i++;
		}

		# return
		return $data;
	}


	/**
	 * Функция возвращает массив идентификаторов лент, для условий запроса к БД, в случаях когда лента публикует элементы из дочерних лент.
	 *
	 * @param        $sid - structure id
	 * @param string $type - rule
	 *
	 * @return array - id's
	 */
	private function construct_child_feeds($sid, $type="default") {

		global $structure;

		$feeds = array();

		$tfeeds = $structure->load_tree($sid, 0, false);
		foreach((array)$tfeeds AS $v) {
			if($v['page_type'] == "feed") {

				$feeds[$v['id']] = $v['id'];

				# default rule
				if($type == "default" && $v['show_child_feeds'] != "none") {
					$addfeeds = $this->construct_child_feeds($v['id'],$v['show_child_feeds']);
					$feeds = array_merge($feeds, $addfeeds);
				}

				# force rule
				if($type == "forced") {
					$addfeeds = $this->construct_child_feeds($v['id'],$type);
					$feeds = array_merge($feeds, $addfeeds);
				}
			}
		}


		return $feeds;
	}


	/**
	 * Функция формирует массив данных для постраничной навигации, который будет использован в шаблонах
	 *
	 * @return array
	 */
	private function construct_pagination() {

		global $db, $site;

		$pages = array();
		# prev
		if($db->prev_page != 0) {
			$pages[]['prev'] =& $db->prev_page;
		}
		# pages
		for($p=1;$p<=$db->pages;$p++) {
			$pages[]['n'] = $p;
		}
		# next
		if($db->next_page > 1) {
			$pages[]['next'] =& $db->next_page;
		}

		# Указываем в титуле страницу
		# Это можно было бы оставить на усмотрение верстальщиков. Но использование одинаковых титулов на целом ряде страниц неполезно для SEO
		# (Есть небольшая вероятность, что этот момент будет исправлен и перенесен на усмотрение верстальщиков в шаблоны)
		if($db->page > 1) {
			$site['title'] .= " (Страница: ".$db->page.")";
		}

		return $pages;
	}
}

?>