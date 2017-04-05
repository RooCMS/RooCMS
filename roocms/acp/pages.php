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
* @subpackage	Admin Control Panel
* @author       alex Roosso
* @copyright    2010-2018 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.2
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('ACP')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class ACP_PAGES
 */
class ACP_Pages {

	# vars
	private $engine;	# [object] global structure operations
	private $unit;		# [object] for works content pages



	/**
	* Show must go on
	*
	*/
	public function __construct() {

		global $roocms, $GET, $POST, $tpl;

		require_once _CLASS."/class_structure.php";
		$this->engine = new Structure();


		# set object for works content
		if(isset($GET->_page)) {
			switch($this->engine->page_type) {
				case 'html':
					require_once _ROOCMS."/acp/pages_html.php";
					$this->unit = new ACP_Pages_HTML;
					break;

				case 'php':
					require_once _ROOCMS."/acp/pages_php.php";
					$this->unit = new ACP_Pages_PHP;
					break;
			}
		}

		# action
		switch($roocms->part) {

			case 'edit':
				$this->unit->edit($this->engine->page_id);
				break;

			case 'update':
				if(isset($POST->update_page)) {
					$this->unit->update($this->engine->page_id);
				}
				else {
					goback();
				}
				break;

			default:
				go(CP."?act=structure");
				break;
		}


		# output
		$tpl->load_template("pages");
	}
}

/**
 * Init class
 */
$acp_pages = new ACP_Pages;
?>