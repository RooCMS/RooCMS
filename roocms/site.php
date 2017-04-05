<?php
/**
 *   RooCMS - Russian Open Source Free Content Managment System
 *   Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 *   Contacts: <info@roocms.com>
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
 *   RooCMS - Бесплатная система управления контентом с открытым исходным кодом
 *   Copyright © 2010-2018 александр Белов (alex Roosso). Все права защищены.
 *   Для связи: <info@roocms.com>
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

/**
* @package      RooCMS
* @subpackage   Frontend
* @author       alex Roosso
* @copyright    2010-2018 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.1.3
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


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
if($users->title == "a" || array_key_exists(0, $structure->page_group_access) || array_key_exists($users->gid, $structure->page_group_access)) {
	$structure->access = true;
}
else {
	$structure->access = false;
}


/**
 * Init Blocks & Modules
 */
if(!class_exists("Blocks"))  {
	require_once "site_blocks.php";
}
if(!class_exists("Modules")) {
	require_once "site_module.php";
}


if($structure->access) {
	if(trim($roocms->part) == "") {
		/**
		* Load structure unit
		*/
		switch($structure->page_type) {
			case 'html':
				require_once "site_page_html.php";
				$page_html = new PageHTML;
				break;

			case 'php':
				require_once "site_page_php.php";
				$page_php = new PagePHP;
				break;

			case 'feed':
				require_once "site_page_feed.php";
				$page_feed = new PageFeed;
				break;
		}
	}
}
else {
	$tpl->load_template("access_denied");
}

?>