<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * Copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
 * Contacts: <info@roocms.com>
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
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
	 * @param string $password	- нехешированный пароль пользователя
	 * @param string $salt		- сальт паользователя
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
	 * @param string $login		- логин пользователя
	 * @param string $password	- хеш пароля пользователя
	 * @param string $salt		- сальт пользователя
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