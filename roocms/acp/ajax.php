<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @subpackage	Ajax Functions
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0
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


$acp_ajax = new ACP_AJAX;

class ACP_AJAX {


	/**
	* Start
	*
	*/
	function __construct() {

		global $roocms;

		// turn on ajax
		$roocms->ajax = true;

		switch($roocms->part) {
			case 'image_delete':
				$this->image_delete();
				break;
		}
	}


	/**
	* Удаление картинок посредством AJAX
	*
	*/
	private function image_delete() {

		global $db, $GET;

		if(isset($GET->_id) && $db->check_id($GET->_id, IMAGES_TABLE)) {

			$q = $db->query("SELECT filename FROM ".IMAGES_TABLE." WHERE id='".$GET->_id."'");
			$row = $db->fetch_assoc($q);

			$q = $db->query("SELECT count(*) FROM ".IMAGES_TABLE." WHERE filename='".$row['filename']."'");
			$c = $db->fetch_row($q);

			if($c[0] == 1) {
				unlink(_UPLOADIMAGES."/original/".$row['filename']);
				unlink(_UPLOADIMAGES."/resize/".$row['filename']);
				unlink(_UPLOADIMAGES."/thumb/".$row['filename']);
			}

			$db->query("DELETE FROM ".IMAGES_TABLE." WHERE id='".$GET->_id."'");

			echo "<font class=\"red\">Изображение ".$GET->_id." удалено!</font>";
		}
	}
}

?>