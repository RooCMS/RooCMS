<?php
/**
* @package      RooCMS
* @subpackage	Frontend
* @subpackage	HTML Page
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.1.1
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
if(!defined('RooCMS')) die('Access Denied');
//#########################################################

$page_html = new PageHTML;

class PageHTML {

	/**
	* Initialisation
	*
	*/
	function __construct() {
		$this->load_content();
	}


	/**
	* Load Content
	*
	*/
	function load_content() {

		global $db, $structure, $parse, $tpl, $smarty;

		$q = $db->query("SELECT content FROM ".PAGES_HTML_TABLE." WHERE sid='".$structure->page_id."'");
		$data = $db->fetch_assoc($q);

		$data['content'] = $parse->text->html($data['content']);

		# load attached images
		$images = array();
		$images = $structure->load_images("pagesid=".$structure->page_id);
		$smarty->assign("images", $images);

		$smarty->assign("content", $data['content']);

		$tpl->load_template("page_html");
	}
}

?>