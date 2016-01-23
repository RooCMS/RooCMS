<?php
/**
* @package      RooCMS
* @subpackage	Admin Control Panel
* @author       alex Roosso
* @copyright    2010-2016 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.1
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
*   RooCMS - Russian free content managment system
*   Copyright (C) 2010-2016 alex Roosso aka alexandr Belov info@roocms.com
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
*   Copyright (C) 2010-2016 alex Roosso (александр Белов) info@roocms.com
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
if(!defined('RooCMS') || !defined('ACP')) die('Access Denied');
//#########################################################


/**
 * Class ACP_PAGES
 */
class ACP_PAGES {

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
					$this->unit = new ACP_PAGES_HTML;
					break;

				case 'php':
					require_once _ROOCMS."/acp/pages_php.php";
					$this->unit = new ACP_PAGES_PHP;
					break;
			}
		}

		# action
		switch($roocms->part) {

			case 'edit':
				$this->unit->edit($this->engine->page_id);
				break;

			case 'update':
				if(isset($POST->update_page)) $this->unit->update($this->engine->page_id);
				else goback();
				break;

			default:
				$this->view_all_pages();
				break;
		}


		# output
		$tpl->load_template("pages");
	}


	/**
	 * Функция просмотра списка страниц
	 *
	 */
	private function view_all_pages() {

		global $db, $tpl, $smarty, $parse;

		$q = $db->query("SELECT h.id, h.sid, h.date_modified, p.title, p.alias, p.noindex, p.page_type
					FROM ".PAGES_HTML_TABLE." AS h
					LEFT JOIN ".STRUCTURE_TABLE." AS p ON (p.id = h.sid)
					ORDER BY p.id ASC");
		while($row = $db->fetch_assoc($q)) {
			$row['lm'] = $parse->date->unix_to_rus($row['date_modified'], false, true, true);
			$row['ptype'] = $this->engine->page_types[$row['page_type']]['title'];
			$data[] = $row;
		}

		$q = $db->query("SELECT h.id, h.sid, h.date_modified, p.title, p.alias, p.noindex, p.page_type
					FROM ".PAGES_PHP_TABLE." AS h
					LEFT JOIN ".STRUCTURE_TABLE." AS p ON (p.id = h.sid)
					ORDER BY p.id ASC");
		while($row = $db->fetch_assoc($q)) {
			$row['lm'] = $parse->date->unix_to_rus($row['date_modified'], false, true, true);
			$row['ptype'] = $this->engine->page_types[$row['page_type']]['title'];
			$data[] = $row;
		}

		uasort($data, array('ACP_PAGES', 'sort_data'));

		$smarty->assign("data", $data);
		$content = $tpl->load_template("pages_view_list", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Callback func для сортировки $data по sid
	 *
	 * @param array $a
	 * @param array $b
	 *
	 * @return int
	 */
	private function sort_data($a, $b) {
		return strcmp($a["sid"], $b["sid"]);
	}
}

/**
 * Init class
 */
$acp_pages = new ACP_PAGES;
?>