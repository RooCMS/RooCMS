<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Pages settings
* @subpackage	PHP Page
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.3
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


class ACP_PAGES_PHP {


	//#####################################################
	//	Edit
	function edit($sid) {

		global $db, $tpl, $smarty, $parse;

		$q = $db->query("SELECT h.id, h.sid, h.content, p.title, p.alias, p.meta_description, p.meta_keywords, h.date_modified
							FROM ".PAGES_PHP_TABLE." AS h
							LEFT JOIN ".STRUCTURE_TABLE." AS p ON (p.id = h.sid)
							WHERE h.sid='".$sid."'");
		$data = $db->fetch_assoc($q);
		$data['lm'] = $parse->date->unix_to_rus($data['date_modified'], true, true, true);

		$smarty->assign("data", $data);

		$content = $tpl->load_template("pages_edit_php", true);

		$smarty->assign("content", $content);
	}


	//#####################################################
	//	Update
	function update($sid) {

		global $db, $parse, $POST;

		$db->query("UPDATE ".PAGES_PHP_TABLE." SET content='".$POST->content."', date_modified='".time()."' WHERE sid='".$sid."'");

		$parse->msg("Страница #".$sid." успешно обновлена.");

		goback();
	}


	//#####################################################
	//	Delete
	function delete($sid) {

		global $db;

		# del pageunit
		$db->query("DELETE FROM ".PAGES_PHP_TABLE." WHERE sid='".$sid."'");
	}
}
?>