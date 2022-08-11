<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2023 alexandr Belov aka alex Roosso. All rights reserved.
 * @author    alex Roosso <info@roocms.com>
 * @link      http://www.roocms.com
 * @license   http://www.gnu.org/licenses/gpl-3.0.html
 *
 * You should have received a copy of the GNU General Public License v3
 * along with this program.  If not, see http://www.gnu.org/licenses/
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
	 * UCP_Login constructor.
	 */
	public function __construct() {

		global $post;


		/**
		 * If auth query
		 */
		if(isset($post->userlogin)) {
			$this->entering();
		}

		goback();
	}


	/**
	 * Enter on site
	 */
	private function entering() {

		global $db, $post, $security, $logger;

		if(isset($post->login, $post->password) && $db->check_id($post->login, USERS_TABLE, "login", "status='1'")) {

			$q = $db->query("SELECT uid, login, title, nickname, password, salt FROM ".USERS_TABLE." WHERE login='".$post->login."' AND status='1'");
			$data = $db->fetch_assoc($q);

			# hash
			$phash = $security->hash_password($post->password, $data['salt']);

			if($phash == $data['password']) {

				# include session security_check hash
				$_SESSION['uid'] 	= $data['uid'];
				$_SESSION['login'] 	= $data['login'];
				$_SESSION['nickname'] 	= $data['nickname'];
				$_SESSION['title'] 	= $data['title'];
				$_SESSION['token'] 	= $security->get_token($data['login'], $phash, $data['salt']);

				# log
				$logger->log("Пользователь успешно авторизовался на сайте");
			}
			else {
				# wrong login or password
				$this->incorrect_entering("Авторизация не удалась: Неверный логин или пароль.");
			}
		}
		else {
			# incorrect login or password
			$this->incorrect_entering("Авторизация не удалась: Введены неверные данные.");
		}
	}


	/**
	 * @param string $msg - error subject for log
	 */
	private function incorrect_entering(string $msg) {

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
