<?php
/**
 * @package      RooCMS
 * @subpackage	 Admin Control Panel
 * @author       alex Roosso
 * @copyright    2010-2015 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      2.0.1
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
if(!defined('RooCMS') || (!defined('ACP') && !defined('INSTALL') && !defined('MULTIUPLOAD'))) die('Access Denied');
//#########################################################


class ACP_SECURITY {

	/**
	 * @var bool
	 */
	var $access = false;


	/**
	 * Функция проверки текущего доступа пользователя.
	 * В случае успешной проверки функция изменяет флаг $access на true
	 */
	function ACP_SECURITY() {

		global $db, $roocms, $security;

		if(isset($roocms->sess['login']) && trim($roocms->sess['login']) != "" && $db->check_id($roocms->sess['login'], USERS_TABLE, "login", "status='1'")
		&& isset($roocms->sess['token']) && strlen($roocms->sess['token']) == 32) {
			$q = $db->query("SELECT login, password, salt FROM ".USERS_TABLE." WHERE login='".$roocms->sess['login']."' AND status='1'");
			$data = $db->fetch_assoc($q);

			$token = $security->hashing_token($roocms->sess['login'], $data['password'], $data['salt']);

			# check access
			if($token == $roocms->sess['token']) {

				# update time last visited
				$db->query("UPDATE ".USERS_TABLE." SET last_visit='".time()."' WHERE login='".$roocms->sess['login']."' AND status='1'");

				# access granted
				$this->access = true;
			}
			else {
				# access denied
				$this->access = false;
			}
		}
		else $this->access = false;
	}
}

/**
 * Init Class
 */
$acpsecurity = new ACP_SECURITY;

?>