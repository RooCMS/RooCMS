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
 * @subpackage	User Control Panel
 * @author       alex Roosso
 * @copyright    2010-2018 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.1.1
 * @since        $date$
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('UI') || !defined('UCP')) {
	die('Access Denied');
}
//#########################################################


class UCP_LOGIN {

	/**
	 * Проверяем введенные данные
	 */
	public function __construct() {

		global $POST;


		/**
		 * Проверяем запрос
		 */
		if(isset($POST->userlogin)) {
			$this->entering();
		}
	}


	/**
	 * Функция авторизации на сайте в пользовательской части.
	 */
	private function entering() {

		global $db, $POST, $security;

		if(isset($POST->login, $POST->password) && $db->check_id($POST->login, USERS_TABLE, "login", "status='1'")) {

			$q = $db->query("SELECT uid, login, title, nickname, password, salt FROM ".USERS_TABLE." WHERE login='".$POST->login."' AND status='1'");
			$data = $db->fetch_assoc($q);

			# hash
			$dbpass = $security->hashing_password($POST->password, $data['salt']);

			if($dbpass == $data['password']) {

				# include session security_check hash
				$_SESSION['uid'] 	= $data['uid'];
				$_SESSION['login'] 	= $data['login'];
				$_SESSION['nickname'] 	= $data['nickname'];
				$_SESSION['title'] 	= $data['title'];
				$_SESSION['token'] 	= $security->hashing_token($data['login'], $dbpass, $data['salt']);
			}
			else {
				# неверный логин или пароль
				$this->incorrect_entering("Неверный логин или пароль.");
			}
		}
		else {
			# логин или пароль введены некоректно
			$this->incorrect_entering("Введены неверные данные.");
		}

		goback();
	}


	/**
	 * @param $msg - сообщение об ошибке передаваемое в шаблон
	 */
	private function incorrect_entering($msg) {

		global $logger;

		unset($_SESSION['uid']);
		unset($_SESSION['login']);
		unset($_SESSION['title']);
		unset($_SESSION['token']);

		sleep(3);

		# notice
		$logger->error($msg);
	}
}

/**
 * Init Class
 */
$ucplogin = new UCP_LOGIN;

?>