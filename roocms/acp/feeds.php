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


class ACP_Feeds {

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
	 * init action
	 */
	private function init() {

		global $roocms, $get, $post, $db;

		# set object for works content
		if(isset($get->_page) && array_key_exists($this->engine->page_type, $this->engine->content_types)) {

			# init codeengine
			switch($this->engine->page_type) {
				case 'feed':
					require_once _ROOCMS."/acp/feeds_feed.php";
					$this->unit = new ACP_Feeds_Feed($this->get_settings());
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


				# modify
				case 'edit_item':
				case 'update_item':
				case 'migrate_item':
				case 'status_on_item':
				case 'status_off_item':
				case 'delete_item':
					if($db->check_id($get->_item, PAGES_FEED_TABLE)) {
						switch($roocms->part) {
							# edit item in feed
							case 'edit_item':
								$this->unit->edit_item($get->_item);
								break;

							# update item in feed
							case 'update_item':
								if(isset($post->update_item)) {
									$this->unit->update_item($get->_item);
								}
								goback();
								break;

							# migrate item in feeds
							case 'migrate_item':
								$this->unit->migrate_item($get->_item);
								break;

							# update status item in feed to on
							case 'status_on_item':
								$this->unit->change_item_status($get->_item, 1);
								break;

							# update status item in feed to off
							case 'status_off_item':
								$this->unit->change_item_status($get->_item, 0);
								break;

							# delete item from feed
							case 'delete_item':
								$this->unit->delete_item($get->_item);
								break;
						}

					}
					else {
						goback();
					}
					break;

				default:
					go(CP."?act=structure");
					break;
			}
		}
		else {
			# go
			go(CP."?act=structure");
		}
	}


	/**
	 * get array with feed settings
	 *
	 * @return array<integer|string|boolean|array>
	 */
	private function get_settings(): array {

		return array(
			'id'                 => $this->engine->page_sid,
			'alias'              => $this->engine->page_alias,
			'title'              => $this->engine->page_title,
			'rss'                => $this->engine->page_rss,
			'show_child_feeds'   => $this->engine->page_show_child_feeds,
			'items_per_page'     => $this->engine->page_items_per_page,
			'items_sorting'      => $this->engine->page_items_sorting,
			'thumb_img_width'    => $this->engine->page_thumb_img_width,
			'thumb_img_height'   => $this->engine->page_thumb_img_height,
			'append_info_before' => $this->engine->page_append_info_before,
			'append_info_after'  => $this->engine->page_append_info_after,
			'subfeeds'           => $this->engine->load_tree($this->engine->page_sid)
		);
	}
}

/**
 * Init Class
 */
$acp_feeds = new ACP_Feeds;
