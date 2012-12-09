<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Blocks settings
* @subpackage	PHP Blocks
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


class ACP_BLOCKS_PHP {

	//#####################################################
	//	Create
	function create() {

		global $db, $tpl, $smarty, $POST, $parse;

		if(@$_REQUEST['create_block']) {

			if(!isset($POST->title)) $parse->msg("Не указано название блока!", false);
			if(!isset($POST->alias) || $db->check_id($POST->alias, BLOCKS_TABLE, "alias"))	$parse->msg("Не указан алиас блока или он не уникален!", false);
			if(!isset($POST->content)) $parse->msg("Пустое тело блока!", false);

			if(!isset($_SESSION['error'])) {
				$db->query("INSERT INTO ".BLOCKS_TABLE."   (title, alias, content, date_create, date_modified, type)
				VALUES ('".$POST->title."', '".$POST->alias."', '".$POST->content."', '".time()."', '".time()."', 'php')");

				$parse->msg("Блок успешно добавлен!");

				go(CP."?act=blocks");
			}
			else go(CP."?act=blocks&part=create&type=php");
		}

		$content = $tpl->load_template("blocks_create_php", true);
		$smarty->assign("content", $content);
	}


	//#####################################################
	//	Edit
	function edit($id) {

		global $db, $tpl, $smarty;

		$q = $db->query("SELECT id, title, alias, content FROM ".BLOCKS_TABLE." WHERE id='".$id."'");
		$data = $db->fetch_assoc($q);

		$smarty->assign("data",$data);
		$content = $tpl->load_template("blocks_edit_php", true);
		$smarty->assign("content", $content);
	}


	//#####################################################
	//	Update
	function update($id) {

		global $db, $POST, $GET, $parse;

		if(@$_REQUEST['update_block']) {

			if(!isset($POST->title)) $parse->msg("Не указано название блока!", false);
			if(!isset($POST->alias) || $db->check_id($POST->alias, BLOCKS_TABLE, "alias", "alias!='".$POST->oldalias."'"))	$parse->msg("Не указан алиас блока или он не уникален!", false);
			if(!isset($POST->content)) $parse->msg("Пустое тело блока!", false);
			if(!isset($POST->id) || $POST->id != $GET->_block) $parse->msg("Системная ошибка...", false);

			if(!isset($_SESSION['error'])) {

				$db->query("UPDATE ".BLOCKS_TABLE."
								SET
									title='".$POST->title."',
									alias='".$POST->alias."',
									content='".$POST->content."',
									date_modified='".time()."'
								WHERE
									id='".$id."'");

				$parse->msg("Блок успешно обновлен!");
			}

			go(CP."?act=blocks");
		}
	}


	//#####################################################
	//	Delete
	function delete($id) {

		global $db, $parse;

		$db->query("DELETE FROM ".BLOCKS_TABLE." WHERE id='".$id."'");
		$parse->msg("Блок успешно удален!");
		go(CP."?act=blocks");
	}
}

?>