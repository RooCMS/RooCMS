<?php
/**
* @package      RooCMS
* @subpackage	Frontend
* @author       alex Roosso
* @copyright    2010-2015 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.1
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
*   Copyright (C) 2010-2014 alex Roosso aka alexandr Belov info@roocms.com
*
*   This program is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   This program is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with this program.  If not, see http://www.gnu.org/licenses/
*
*
*   RooCMS - Русская бесплатная система управления сайтом
*   Copyright (C) 2010-2014 alex Roosso (александр Белов) info@roocms.com
*
*   Это программа является свободным программным обеспечением. Вы можете
*   распространять и/или модифицировать её согласно условиям Стандартной
*   Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
*   Обеспечения, версии 3 или, по Вашему желанию, любой более поздней версии.
*
*   Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
*   ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
*   ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
*   Общественную Лицензию GNU для получения дополнительной информации.
*
*   Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
*   с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
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
$site['title']		= $structure->page_title;
$site['description']	= $structure->page_meta_desc;
if(!empty($site)) {
	$site['keywords']	= $structure->page_meta_keys;
}


/**
 * Проверяем имеется ли у пользователя доступ к странице.
 */
if($users->title = "a"|| array_key_exists(0, $structure->page_group_access) || array_key_exists($users->gid, $structure->page_group_access))
	$structure->access = true;
else
	$structure->access = false;


/**
 * Load Blocks
 */
require_once "functions_blocks.php";


if($structure->access) {

	/**
	* Load structure unit
	*/
	switch($structure->page_type) {
		case 'html':
			require_once "functions_page_html.php";
			/**
			 * init Class
			 */
			$page_html = new PageHTML;
			break;

		case 'php':
			require_once "functions_page_php.php";
			/**
			 * Init Class
			 */
			$page_php = new PagePHP;
			break;

		case 'feed':
			require_once "functions_page_feed.php";
			/**
			 * init Class
			 */
			$page_feed = new PageFeed;
			break;
	}
}
else {
	$tpl->load_template("access_denied");
}

?>