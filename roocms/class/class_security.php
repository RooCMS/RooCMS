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
* @subpackage	Engine RooCMS classes
* @author       alex Roosso
* @copyright    2010-2019 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.2.2
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
 * Class Security
 */
class Security extends Shteirlitz {

	private $pass_leight = 7;
	private $salt_leight = 5;



	/**
	 * Функция хешерования пароля пользователя
	 *
	 * @param $password	- нехешированный пароль пользователя
	 * @param $salt		- сальт паользователя
	 *
	 * @return string	- хешированный пароль пользователя
	 */
	public function hashing_password($password, $salt) {
		$hash = md5(md5($password).md5($salt));
		return $hash;
	}


	/**
	 * Функция генерирует хешобразный ключ для проверки текущего доступа
	 * Временный ключ генерируется на основе текущей сессии пользователя.
	 *
	 * @param $login	- логин пользователя
	 * @param $password	- хеш пароля пользователя
	 * @param $salt		- сальт пользователя
	 *
	 * @return string - токен
	 */
	public function hashing_token($login, $password, $salt) {

		global $roocms;

		$token = md5(md5($roocms->usersession).md5($login).md5($password).md5($salt));
		return $token;
	}


	/**
	 * Функция генерирует новый пароль
	 *
	 * @return string - new password
	 */
	public function create_new_password() {
		$password = randcode($this->pass_leight, "ABCDEFGHJKLMNPQRSTUVWXYZabcdefhjkmnprstvwxyz123456789");
		return $password;
	}


	/**
	 * Функция генерирует новый сальт
	 *
	 * @return string
	 */
	public function create_new_salt() {
		$salt = randcode($this->salt_leight, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*(-)+{=}:?>~<,./[|]");
		return $salt;
	}


	/**
	 * Паранои много не бывает.
	 * Проверяем данные авторизации, не было ли попыток совершения подмены данных
	 */
	protected function control_userdata() {

		global $roocms, $logger;

		$destroy = false;

		# check uid
		if($roocms->sess['uid'] != $this->uid) {
			$destroy = true;
		}

		# check login
		if($roocms->sess['login'] != $this->login) {
			$destroy = true;
		}

		# check title
		if($roocms->sess['title'] != $this->title) {
			$destroy = true;
		}

		# check nickname
		if($roocms->sess['nickname'] != $this->nickname) {
			$destroy = true;
		}

		# check token
		if($roocms->sess['token'] != $this->token) {
			$destroy = true;
		}

		if($destroy) {
			# destroy data
			$roocms->sess = [];
			session_destroy();

			# notice and stop
			$logger->error("Ваши данные изменились! Требуется пройти тоавризацию.");
			go("/");
		}
	}
}