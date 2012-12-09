<?php
/**
* @package      RooCMS
* @subpackage	Structure unit initialisation
* @author       alex Roosso
* @copyright    2010-2014 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.4
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


nocache();


/**
* Meta SEO
*
*/
$site['title']			= $structure->page_title;
$site['description']	= $structure->page_meta_desc;
$site['keywords']		= $structure->page_meta_keys;


/**
* Load Blocks
*/
require_once "functions_blocks.php";


/**
* Load structure unit
*/
switch($structure->page_type) {
	case 'html':
		require_once "functions_page_html.php";
		break;

	case 'php':
		require_once "functions_page_php.php";
		break;

	case 'feed':
		require_once "functions_page_feed.php";
		break;
}

?>