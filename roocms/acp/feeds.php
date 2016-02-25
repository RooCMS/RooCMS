<?php
/**
* @package      RooCMS
* @subpackage	Admin Comtrol Panel
* @subpackage	Feeds
* @author       alex Roosso
* @copyright    2010-2016 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.2.3
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*	RooCMS - Russian free content managment system
*   Copyright (C) 2010-2016 alex Roosso aka alexandr Belov info@roocms.com
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
*   RooCMS - Русская бесплатная система управления сайтом
*   Copyright (C) 2010-2016 alex Roosso (александр Белов) info@roocms.com
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


class ACP_FEEDS {

	# objects
	private $engine;	# ... object global structure operations
	private $unit;		# ... object for works content pages



	/**
	 * Show must go on ...
	 */
	public function __construct() {

		global $tpl;

		require_once _CLASS."/class_structure.php";
		$this->engine = new Structure();

		# initialise
		$this->init();

		# output
		$tpl->load_template("feeds");
	}


	/**
	 * Инициализируем действие
	 */
	private function init() {

		global $roocms, $GET, $POST, $db;

		# set object for works content
		if(isset($GET->_page) && array_key_exists($this->engine->page_type, $this->engine->page_types) && $this->engine->page_types[$this->engine->page_type]['enable'] == 1) {

			$feeds_data = array(
				'id'			=> $this->engine->page_id,
				'alias'			=> $this->engine->page_alias,
				'title'			=> $this->engine->page_title,
				'rss'			=> $this->engine->page_rss,
				'show_child_feeds'	=> $this->engine->page_show_child_feeds,
				'items_per_page'	=> $this->engine->page_items_per_page,
				'items_sorting'		=> $this->engine->page_items_sorting,
				'thumb_img_width'	=> $this->engine->page_thumb_img_width,
				'thumb_img_height'	=> $this->engine->page_thumb_img_height
			);

			# init codeengine
			switch($this->engine->page_type) {
				case 'feed':
					require_once _ROOCMS."/acp/feeds_feed.php";
					$this->unit = new ACP_FEEDS_FEED($feeds_data);
					break;
			}

			# action
			switch($roocms->part) {
				# edit feed option
				case 'settings':
					$this->unit->settings();
					break;

				# update feed option
				case 'update_settings':
					$this->unit->update_settings();
					break;

				# cp feed items
				case 'control':
					$this->unit->control();
					break;

				# create new item in feed
				case 'create_item':
					$this->unit->create_item();
					break;

				# edit item in feed
				case 'edit_item':
					if($db->check_id($GET->_item, PAGES_FEED_TABLE)) $this->unit->edit_item($GET->_item);
					else goback();
					break;

				# update item in feed
				case 'update_item':
					if(isset($POST->update_item) && $db->check_id($GET->_item, PAGES_FEED_TABLE)) $this->unit->update_item($GET->_item);
					else goback();
					break;

				# update item in feed
				case 'migrate_item':
					if($db->check_id($GET->_item, PAGES_FEED_TABLE)) $this->unit->migrate_item($GET->_item);
					else goback();
					break;

				# update status item in feed to on
				case 'status_on_item':
					if($db->check_id($GET->_item, PAGES_FEED_TABLE)) $this->unit->change_item_status($GET->_item, 1);
					else goback();
					break;

				# update status item in feed to off
				case 'status_off_item':
					if($db->check_id($GET->_item, PAGES_FEED_TABLE)) $this->unit->change_item_status($GET->_item, 0);
					else goback();
					break;

				# delete item from feed
				case 'delete_item':
					if($db->check_id($GET->_item, PAGES_FEED_TABLE)) $this->unit->delete_item($GET->_item);
					else goback();
					break;

				default:
					$this->view_all_feeds();
					break;
			}
		}
		else $this->view_all_feeds();
	}


	/**
	* Функция просмотра списка лент
	*/
	private function view_all_feeds() {

		global $db, $tpl, $smarty;

		// FIXME: replace this query
		$data = array();
		$q = $db->query("SELECT id, alias, title, noindex, page_type, items FROM ".STRUCTURE_TABLE." WHERE page_type='feed' ORDER BY id ASC");
		while($row = $db->fetch_assoc($q)) {
			$row['ptype'] = $this->engine->page_types[$row['page_type']]['title'];
			$data[] = $row;
		}

		$smarty->assign("data", $data);
		$content = $tpl->load_template("feeds_view_list", true);
		$smarty->assign("content", $content);
	}
}

/**
 * Init Class
 */
$acp_feeds = new ACP_FEEDS;
?>