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
 * @subpackage	User Control Panel
 * @author       alex Roosso
 * @copyright    2010-2019 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.1.1
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('UI') || !defined('UCP')) {
	die('Access Denied');
}
//#########################################################


class UCP_Login {

	/**
	 * Проверяем введенные данные
	 */
	public function __construct() {

		global $post;


		/**
		 * Проверяем запрос
		 */
		if(isset($post->userlogin)) {
			$this->entering();
		}
	}


	/**
	 * Функция авторизации на сайте в пользовательской части.
	 */
	private function entering() {

		global $db, $post, $security;

		if(isset($post->login, $post->password) && $db->check_id($post->login, USERS_TABLE, "login", "status='1'")) {

			$q = $db->query("SELECT uid, login, title, nickname, password, salt FROM ".USERS_TABLE." WHERE login='".$post->login."' AND status='1'");
			$data = $db->fetch_assoc($q);

			# hash
			$dbpass = $security->hashing_password($post->password, $data['salt']);

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
	 * @param string $msg - сообщение об ошибке передаваемое в шаблон
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
$ucplogin = new UCP_Login;