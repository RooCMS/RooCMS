<?php
/**
* @package      RooCMS
* @subpackage	Frontend
* @subpackage	PHP Page
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
if(!defined('RooCMS')) die('Access Denied');
//#########################################################

$page_php = new PagePHP;

class PagePHP {

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

		$q = $db->query("SELECT content FROM ".PAGES_PHP_TABLE." WHERE sid='".$structure->page_id."'");
		$data = $db->fetch_assoc($q);

		ob_start();
			eval($parse->text->html($data['content']));
			$output = ob_get_contents();
		ob_end_clean();

		$smarty->assign("content", $output);

		$tpl->load_template("page_php");
	}
}

?>