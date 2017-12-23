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
* @subpackage	Blocks settings
* @author       alex Roosso
* @copyright    2010-2018 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.2
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


class ACP_Blocks {

	private $unit;			# ... object for works content blocks

	private $block = 0;		# ID block
	private $types = array(	"html"	=> true,
				"php"	=> true);


	/**
	* Поехали
	*     (с) Гагарин
	*/
	public function __construct() {

		global $tpl;

		$this->init();
		$this->action();

		# выводим
		$tpl->load_template("blocks");
	}


	/**
	* Инициализация установки
	*/
	private function init() {

		global $db, $GET;

		if(isset($GET->_block) && $db->check_id($GET->_block, BLOCKS_TABLE)) {
			$this->block = $GET->_block;
			$q = $db->query("SELECT block_type FROM ".BLOCKS_TABLE." WHERE id='".$this->block."'");
			$t = $db->fetch_assoc($q);
			$GET->_type = $t['block_type'];
		}

		if(isset($GET->_type) && array_key_exists($GET->_type, $this->types) && $this->types[$GET->_type]) {
			switch($GET->_type) {
				case 'html':
					require_once _ROOCMS."/acp/blocks_html.php";
					$this->unit = new ACP_Blocks_HTML;
					break;

				case 'php':
					require_once _ROOCMS."/acp/blocks_php.php";
					$this->unit = new ACP_Blocks_PHP;
					break;
			}
		}
	}


	/**
	* Определяем задачи для каждой цели
	*/
	private function action() {

		global $roocms;

		switch($roocms->part) {
			case 'create':
				$this->unit->create();
				break;

			case 'edit':
				$this->unit->edit($this->block);
				break;

			case 'update':
				$this->unit->update($this->block);
				break;

			case 'delete':
				$this->unit->delete($this->block);
				break;

			default:
				$this->view_all_blocks();
				break;
		}
	}


	/**
	* Видим все блоки
	*/
	private function view_all_blocks() {

		global $db, $tpl, $smarty;

		$data = array();
		$q = $db->query("SELECT id, alias, block_type, title FROM ".BLOCKS_TABLE." ORDER BY id ASC");
		while($row = $db->fetch_assoc($q)) {
			$data[] = $row;
		}

		$smarty->assign("data", $data);
		$content = $tpl->load_template("blocks_view_list", true);
		$smarty->assign("content", $content);
	}
}

/**
 * Init Class
 */
$acp_blocks = new ACP_Blocks;