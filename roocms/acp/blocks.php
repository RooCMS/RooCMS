<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Blocks settings
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.1
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


$acp_blocks = new ACP_BLOCKS;

class ACP_BLOCKS {

	private $unit;			# ... object for works content blocks

	private $block = 0;		# ID block
	private $types = array(	"html"	=>	true,
							"php"	=>	true);


	//#####################################################
	//	Lets begin
	function __construct() {

		global $tpl;

		$this->init();
		$this->action();

		# output
		$tpl->load_template("blocks");
	}


	//#####################################################
	//	Initilisation
	private function init() {

		global $db, $GET;


		if(isset($GET->_block) && $db->check_id($GET->_block, BLOCKS_TABLE)) {
			$this->block = $GET->_block;
			$q = $db->query("SELECT type FROM ".BLOCKS_TABLE." WHERE id='".$this->block."'");
			$t = $db->fetch_assoc($q);
			$GET->_type = $t['type'];
		}


		if(isset($GET->_type) && array_key_exists($GET->_type, $this->types) && $this->types[$GET->_type]) {
			switch($GET->_type) {
				case 'html':
					require_once _ROOCMS."/acp/blocks_html.php";
					$this->unit = new ACP_BLOCKS_HTML;
					break;

				case 'php':
					require_once _ROOCMS."/acp/blocks_php.php";
					$this->unit = new ACP_BLOCKS_PHP;
					break;
			}
		}
	}


	//#####################################################
	// Let's act
	private function action() {

		global $roocms;

		switch($roocms->part) {
			case 'create':
			$this->unit->create();
			break;

			case 'edit':
			$this->unit->edit($this->block);
			break;

			case 'update':
			$this->unit->update($this->block);
			break;

			case 'delete':
			$this->unit->delete($this->block);
			break;

			default:
			$this->view_all_blocks();
			break;
		}
	}


	//#####################################################
	//	View all blocks
	private function view_all_blocks() {

		global $db, $tpl, $smarty;

		$data = array();
		$q = $db->query("SELECT id, alias, type, title FROM ".BLOCKS_TABLE." ORDER BY id ASC");
		while($row = $db->fetch_assoc($q)) {
			$data[] = $row;
		}

		$smarty->assign("data", $data);
		$content = $tpl->load_template("blocks_view_list", true);
		$smarty->assign("content", $content);
	}
}
?>