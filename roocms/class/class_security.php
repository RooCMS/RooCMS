<?php
/**
* @package      RooCMS
* @subpackage	Engine RooCMS classes
* @author       alex Roosso
* @copyright    2010-2015 (c) RooCMS
* @link         http://www.roocms.com
* @version      1.0
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


/**
 * Class Security
 */
class Security {

	var $token = "";



	function Security() {


	}


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
		$password = randcode(7, "ABCDEFGHJKLMNPQRSTUVWXYZabcdefhjkmnprstvwxyz0123456789");
		return $password;
	}


	/**
	 * Функция генерирует новый сальт
	 *
	 * @return string
	 */
	public function create_new_salt() {
		$salt = randcode(4, "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890!@#$%^&*(){}:?><,./[]");
		return $salt;
	}
}



?>