<?php
/**
* @package      RooCMS
* @subpackage	Frontend
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.14
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
if(!defined('RooCMS')) die('Access Denied');
//#########################################################


/**
 * Class PageFeed
 */
class PageFeed {

	var $item_id 		= 0;
	var $items_per_page	= 10;


	/**
	 * Lets begin...
	 * Why does the gull die?
	 */
	function __construct() {

		global $GET, $db, $structure, $smarty;

		$feed['title'] 	= $structure->page_title;
		$feed['alias'] 	= $structure->page_alias;
		$feed['id'] 	= $structure->page_id;

		$smarty->assign("feed", $feed);

		if(isset($GET->_id) && $db->check_id($GET->_id, PAGES_FEED_TABLE, "id", "(date_end_publications = '0' || date_end_publications > '".time()."') AND status='1'")) {
			$this->item_id = $GET->_id;
			$this->load_item($this->item_id);
		}
		elseif(isset($GET->_export) && $GET->_export == "RSS" && $structure->page_rss == 1) $this->load_feed_rss();
		else $this->load_feed();
	}


        /**
        * Load Feed Item
        *
        * @param int $id  - идентификатор новости
        */
	private function load_item($id) {

		global $db, $structure, $parse, $img, $tpl, $smarty, $site;

		# query data
		$q = $db->query("SELECT id, title, meta_description, meta_keywords, full_item, date_publications FROM ".PAGES_FEED_TABLE." WHERE id='".$id."'");
		$item = $db->fetch_assoc($q);
		$item['datepub'] 	= $parse->date->unix_to_rus($item['date_publications'],true);
		$item['date']		= $parse->date->unix_to_rus_array($item['date_publications']);
		$item['full_item']	= $parse->text->html($item['full_item']);

		# load attached images
		$images = array();
                $images = $img->load_images("feedid=".$id);
		$smarty->assign("images", $images);

		$smarty->assign("item", $item);

		# meta
		$site['title'] .= " - ".$item['title'];
		if(trim($item['meta_description']) != "")	$site['description']	= $item['meta_description'];
		if(trim($item['meta_keywords']) != "")		$site['keywords']	= $item['meta_keywords'];

		$tpl->load_template("feed_item");
	}


	/**
	 * Загружаем фид
	 */
	private function load_feed() {

		global $db, $config, $structure, $rss, $parse, $img, $tpl, $smarty;

		# set limit on per page
		if($structure->page_items_per_page > 0) $this->items_per_page =& $structure->page_items_per_page;
		else $this->items_per_page =& $config->feed_items_per_page;
		$db->limit =& $this->items_per_page;

		# calculate pages
		$db->pages_mysql(PAGES_FEED_TABLE, "date_publications <= '".time()."' AND sid='".$structure->page_id."' AND (date_end_publications = '0' || date_end_publications > '".time()."') AND status='1'");

		$pages = array();
		# prev
		if($db->prev_page != 0) $pages[]['prev'] =& $db->prev_page;
		# pages
		for($p=1;$p<=$db->pages;$p++) {
			$pages[]['n'] = $p;
		}
		# next
		if($db->next_page != 0) $pages[]['next'] =& $db->next_page;

		$smarty->assign("pages", $pages);

		# RSS
		if($structure->page_rss == 1) $rss->set_header_link();

		$smarty->assign("rsslink", $rss->rss_link);


		# Feed list
		$feeds = array();
		$q = $db->query("SELECT id, title, brief_item, date_publications FROM ".PAGES_FEED_TABLE." WHERE date_publications <= '".time()."' AND sid='".$structure->page_id."' AND (date_end_publications = '0' || date_end_publications > '".time()."') AND status='1' ORDER BY date_publications DESC, date_create DESC, date_update DESC LIMIT ".$db->from.",".$db->limit);
		while($row = $db->fetch_assoc($q)) {
			$row['datepub']		= $parse->date->unix_to_rus($row['date_publications'],true);
			$row['date'] 		= $parse->date->unix_to_rus_array($row['date_publications']);
			$row['brief_item']	= $parse->text->html($row['brief_item']);

			$row['image'] 		= $img->load_images("feedid=".$row['id']."", 0, 1);

			$feeds[] = $row;
		}

		$smarty->assign("feeds", $feeds);

		$tpl->load_template("feed");
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
			if($rss->lastbuilddate == 0) $rss->set_lastbuilddate($row['date_publications']);
		}
	}
}

/**
 * init Class
 */
$page_feed = new PageFeed;

?>