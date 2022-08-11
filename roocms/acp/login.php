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
if(!defined('RooCMS') || (!defined('ACP') && !defined('INSTALL'))) {
	die('Access Denied');
}
//#########################################################


class ACP_Login {

	/**
	 * Chec input data
	 */
	public function __construct() {

		global $db, $post, $security, $smarty, $tpl, $site, $logger;


		$smarty->assign("site", $site);


		# check
		if(isset($post->login, $post->password)) {

			if($db->check_id($post->login, USERS_TABLE, "login", "status='1' AND title='a'")) {

				$q = $db->query("SELECT uid, login, nickname, title, password, salt FROM ".USERS_TABLE." WHERE login='".$post->login."' AND status='1' AND title='a'");
				$data = $db->fetch_assoc($q);

				# hash
				$phash = $security->hash_password($post->password, $data['salt']);

				if($phash == $data['password']) {

					$_SESSION['uid'] 	= $data['uid'];
					$_SESSION['login'] 	= $data['login'];
					$_SESSION['title'] 	= $data['title'];
					$_SESSION['nickname'] 	= $data['nickname'];
					$_SESSION['token'] 	= $security->get_token($data['login'], $phash, $data['salt']);

					# log
					$logger->log("Пользователь успешно авторизовался в Панели управления");

					# go
					goback();
				}
			}

			# wrong login or password
			$this->incorrect_entering($post->login, mb_strlen($post->password));
		}


		# load template
		$tpl->load_template("login");
	}


	/**
	 * Show error message
	 *
	 * @param string $login    - введенный логин
	 * @param string $password - введенный пароль
	 */
	private function incorrect_entering(string $login, string $password) {

		global $smarty, $logger;

		# log
		$logger->error("Неудачная попытка авторизации - логин: ".$login." пароль: *".$password." символов*");

		session_destroy();

		sleep(3);
		$smarty->assign("error_login", "Неверный логин или пароль.");
	}
}

/**
 * Init Class
 */
$acplogin = new ACP_Login;
