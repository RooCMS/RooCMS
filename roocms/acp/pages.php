<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Pages settings
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.8
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


$acp_pages = new ACP_PAGES;

class ACP_PAGES {

	# vars
	private $engine;		# [object] global structure operations
	private $unit;			# [object] for works content pages



    /**
    * Show must go on
    *
    */
	function __construct() {

		global $tpl;

		require_once _CLASS."/class_structure.php";
		$this->engine = new Structure();

		// initialise
		$this->init();

		# output
		$tpl->load_template("pages");
	}


    /**
    * Initialisation
    *
    */
	private function init() {

		global $roocms, $GET;

		# set object for works content
		if(isset($GET->_page)) {
			switch($this->engine->page_type) {
				case 'html':
					require_once _ROOCMS."/acp/pages_html.php";
					$this->unit = new ACP_PAGES_HTML;
					break;

				case 'php':
					require_once _ROOCMS."/acp/pages_php.php";
					$this->unit = new ACP_PAGES_PHP;
					break;
			}
		}

		# action
		switch($roocms->part) {

			case 'edit':
				$this->unit->edit($this->engine->page_id);
				break;

			case 'update':
				if(@$_REQUEST['update_page']) $this->unit->update($this->engine->page_id);
				else goback();
				break;

			default:
				$this->view_all_pages();
				break;
		}
	}


    /**
    * View list all pages
    *
    */
	private function view_all_pages() {

		global $db, $tpl, $smarty, $parse;

		$q = $db->query("SELECT h.id, h.sid, h.date_modified, p.title, p.alias, p.noindex, p.type
							FROM ".PAGES_HTML_TABLE." AS h
							LEFT JOIN ".STRUCTURE_TABLE." AS p ON (p.id = h.sid)
							ORDER BY p.id ASC");
		while($row = $db->fetch_assoc($q)) {
			$row['lm'] = $parse->date->unix_to_rus($row['date_modified'], false, true, true);
			$row['ptype'] = $this->engine->page_types[$row['type']]['title'];
			$data[] = $row;
		}

		$q = $db->query("SELECT h.id, h.sid, h.date_modified, p.title, p.alias, p.noindex, p.type
							FROM ".PAGES_PHP_TABLE." AS h
							LEFT JOIN ".STRUCTURE_TABLE." AS p ON (p.id = h.sid)
							ORDER BY p.id ASC");
		while($row = $db->fetch_assoc($q)) {
			$row['lm'] = $parse->date->unix_to_rus($row['date_modified'], false, true, true);
			$row['ptype'] = $this->engine->page_types[$row['type']]['title'];
			$data[] = $row;
		}

		uasort($data, array('ACP_PAGES', 'sort_data'));

		$smarty->assign("data", $data);
		$content = $tpl->load_template("pages_view_list", true);
		$smarty->assign("content", $content);
	}


    /**
    * Callback func для сортировки $data по sid
    *
    * @param array $a
    * @param array $b
    */
	private function sort_data($a, $b) {
		return strcmp($a["sid"], $b["sid"]);
	}
}
?>