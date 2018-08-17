<?php
/**
 *   RooCMS - Open Source Free Content Managment System
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
* @copyright    2010-2019 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0.4
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
 * Class ACP_PAGES_PHP
 */
class ACP_Pages_PHP {

	/**
	* Редактирование PHP страницы
	*
	* @param boolean $sid - Структурный идентификатор
	*/
	public function edit($sid) {

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


	/**
	* Функция обновления PHP страницы
	*
	* @param boolean $sid - структурный идентификатор
	*/
	public function update($sid) {

		global $db, $logger, $post;

		$db->query("UPDATE ".PAGES_PHP_TABLE." SET content='".$post->content."', date_modified='".time()."' WHERE sid='".$sid."'");

		$logger->info("Страница #".$sid." успешно обновлена.");

		goback();
	}


	/**
	* Фнкция удаления PHP страницы
	*
	* @param boolean $sid - структурная еденица
	*/
	public function delete($sid) {

		global $db, $logger;

		# del pageunit
		$db->query("DELETE FROM ".PAGES_PHP_TABLE." WHERE sid='".$sid."'");

		# notice
		$logger->info("Страница #".$sid." успешно удалена");
	}
}