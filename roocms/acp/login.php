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
* @subpackage	Admin Control Panel
* @author       alex Roosso
* @copyright    2010-2019 (c) RooCMS
* @link         http://www.roocms.com
* @version      2.0.8
* @since        $date$
* @license      http://www.gnu.org/licenses/gpl-3.0.html
*/


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || (!defined('ACP') && !defined('INSTALL'))) {
	die('Access Denied');
}
//#########################################################


class ACP_Login {

	/**
	 * Проверяем введенные данные
	 */
	public function __construct() {

		global $db, $post, $security, $smarty, $tpl, $site, $logger;


		$smarty->assign("site", $site);


		# check
		if(isset($post->login, $post->password)) {

			if($db->check_id($post->login, USERS_TABLE, "login", "status='1' AND title='a'")) {

				$q = $db->query("SELECT uid, login, nickname, title, password, salt FROM ".USERS_TABLE." WHERE login='".$post->login."' AND status='1' AND title='a'");
				$data = $db->fetch_assoc($q);

				$dbpass = $security->hashing_password($post->password, $data['salt']);

				if($dbpass == $data['password']) {

					$_SESSION['uid'] 	= $data['uid'];
					$_SESSION['login'] 	= $data['login'];
					$_SESSION['title'] 	= $data['title'];
					$_SESSION['nickname'] 	= $data['nickname'];
					$_SESSION['token'] 	= $security->hashing_token($data['login'], $dbpass, $data['salt']);

					# log
					$logger->log("Успешная авторизация под логином: ".$post->login);

					# go
					goback();
				}
			}

			# неверный логин или пароль
			$this->incorrect_entering($post->login, mb_strlen($post->password));
		}


		# load template
		$tpl->load_template("login");
	}


	/**
	 * Функция вывода сообщения о некоректной попытки входа
	 *
	 * @param string $login    - введенный логин
	 * @param string $password - введенный пароль
	 */
	private function incorrect_entering($login, $password) {

		global $smarty, $logger;

		# log
		$logger->log("Попытка авторизации - логин: ".$login." пароль: *".$password." символов*");

		session_destroy();

		sleep(3);
		$smarty->assign("error_login", "Неверный логин или пароль.");
	}
}

/**
 * Init Class
 */
$acplogin = new ACP_Login;