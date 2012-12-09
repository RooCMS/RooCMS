<?php
/*=========================================================
|	Title: RooCMS Feeds ACP
|	Author:	alex Roosso
|	Copyright: 2010-2014 (c) RooCMS.
|	Web: http://www.roocms.com
|	All rights reserved.
|----------------------------------------------------------
|	This program is free software; you can redistribute it and/or modify
|	it under the terms of the GNU General Public License as published by
|	the Free Software Foundation; either version 2 of the License, or
|	(at your option) any later version.
|
|	Данное программное обеспечение является свободным и распространяется
|	по лицензии Фонда Свободного ПО - GNU General Public License версия 2.
|	При любом использовании данного ПО вы должны соблюдать все условия
|	лицензии.
|----------------------------------------------------------
|	Build date: 		14:41 16.06.2012
|	Version file:		1.00 build 6
=========================================================*/

/**
* @package      RooCMS
* @subpackage	Admin Comtrol Panel
* @subpackage	Feeds
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.6
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


$acp_feeds = new ACP_FEEDS;

class ACP_FEEDS {

	# vars
	private $engine;		# ... object global structure operations
	private $unit;			# ... object for works content pages



	//#####################################################
	// Show must go on
	function __construct() {

		global $tpl;

		require_once _CLASS."/class_structure.php";
		$this->engine = new Structure();

		// initialise
		$this->init();

		# output
		$tpl->load_template("feeds");
	}


	//#####################################################
	// Initialisation
	private function init() {

		global $roocms, $GET, $db;

		# set object for works content
		if(isset($GET->_page)) {

			# init codeengine
			switch($this->engine->page_type) {
				case 'feed':
					require_once _ROOCMS."/acp/feeds_feed.php";
					$this->unit = new ACP_FEEDS_FEED;
					break;
			}

			# action
			switch($roocms->part) {
				# edit feed option
				case 'settings':
					$this->unit->settings($GET->_page);
					break;

				# update feed option
				case 'update_settings':
					$this->unit->update_settings($GET->_page);
					break;

				# cp feed items
				case 'control':
					$this->unit->control($GET->_page);
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
					if(@$_REQUEST['update_item'] && $db->check_id($GET->_item, PAGES_FEED_TABLE)) $this->unit->update_item($GET->_item);
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


	//#####################################################
	//	View list all pages
	private function view_all_feeds() {

		global $db, $tpl, $smarty;

		$data = array();
		$q = $db->query("SELECT id, alias, title, noindex, type, items FROM ".STRUCTURE_TABLE." WHERE type='feed' ORDER BY id ASC");
		while($row = $db->fetch_assoc($q)) {
			$row['ptype'] = $this->engine->page_types[$row['type']]['title'];
			$data[] = $row;
		}

		$smarty->assign("data", $data);
		$content = $tpl->load_template("feeds_view_list", true);
		$smarty->assign("content", $content);
	}
}
?>