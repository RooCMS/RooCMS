<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2019 alexandr Belov aka alex Roosso. All rights reserved.
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
if(!defined('RooCMS') || !defined('UI')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class UI_RePass
 */
class UI_RePass {



	public function __construct() {

		global $structure, $roocms, $users, $post;

		# title
		$structure->page_title = "Восстановление пароля";

		# breadcumb
		$structure->breadcumb[] = array('part'=>'repass', 'title'=>'Восстановление пароля');

		# if users registred
		if($users->uid != 0) {
			go(SCRIPT_NAME."?part=ucp&act=ucp");
		}

		# action
		switch($roocms->act) {
			case 'reminder':
				if(isset($post->reminder)) {
					$this->reminder();
				}
				break;

			case 'confirm':
				$this->confirm();
				break;

			case 'verification':
				$this->verification();
				break;

			default:
				$this->form();
				break;
		}
	}


	/**
	 * Функция с формой восстановления пароля
	 */
	private function form() {

		global $tpl;


		$tpl->load_template("repass_form");
	}


	/**
	 * Функция подтверждения запроса на смену пароля
	 */
	private function confirm() {

		global $get, $parse, $smarty, $tpl;

		$email = (isset($get->_email) && $parse->valid_email($get->_email)) ? $get->_email : "" ;
		$code  = (isset($get->_code)) ? $get->_code : "" ;

		# tpl
		$smarty->assign("email", $email);
		$smarty->assign("code",  $code);
		$tpl->load_template("repass_confirm");
	}


	/**
	 * Функция восстановления пароля
	 */
	private function reminder() {

		global $db, $roocms, $site, $post, $users, $parse, $logger, $smarty, $tpl;

		# log
		$logger->log("Запрос на восстановление пароля для почтового ящика: ".$post->email." с IP:".$roocms->userip);

		# check
		if(isset($post->email) && $parse->valid_email($post->email) && $db->check_id($post->email, USERS_TABLE, "email")) {

			$confirm = [];
			$confirm['code'] = randcode(10);

			# set secret key
			$db->query("UPDATE ".USERS_TABLE." SET secret_key='".$confirm['code']."' WHERE email='".$post->email."'");

			# userdata
			$q = $db->query("SELECT nickname FROM ".USERS_TABLE." WHERE email='".$post->email."'");
			$userdata = $db->fetch_assoc($q);

			# confirm link
			$confirm['link'] = $site['domain'].SCRIPT_NAME."?part=repass&act=confirm&email=".$post->email."&code=".$confirm['code'];


			# Уведомление пользователю на электропочту
			$smarty->assign("nickname", $userdata['nickname']);
			$smarty->assign("confirm", $confirm);
			$smarty->assign("site", $site);
			$message = $tpl->load_template("email_confirm_repass", true);

			sendmail($post->email, "Запрос на восстановление пароля для сайта: ".$site['title'], $message);


			# уведомление
			$logger->info("Инструкции для восстановления пароля, отправлены Вам на электронную почту", false);

			# переход
			go(SCRIPT_NAME."?part=repass&act=confirm&email=".$post->email);
		}
		else {
			# bad result
			$logger->error("Невозможно выполнить запрос на восстановление пароля. Мы не нашли данных о Вашей учетной записи.", false);
			goback();
		}
	}



	/**
	 * Функция подтверждения запроса на смену пароля.
	 */
	private function verification() {

		global $db, $parse, $logger, $post, $site, $security, $smarty, $tpl;

		if(isset($post->email, $post->code) && $parse->valid_email($post->email) && $db->check_id($post->email, USERS_TABLE, "email", "secret_key='".$post->code."'")) {

			# new password
			$salt = $security->create_new_salt();
			$pass = randcode(10);
			$password = $security->hashing_password($pass, $salt);

			# userdata
			$q = $db->query("SELECT login, nickname FROM ".USERS_TABLE." WHERE email='".$post->email."'");
			$userdata = $db->fetch_assoc($q);

			# update
			$db->query("UPDATE ".USERS_TABLE." SET salt='".$salt."', password='".$password."', secret_key='', last_visit='".time()."' WHERE email='".$post->email."'");


			# Уведомление пользователю на электропочту
			$smarty->assign("userdata", $userdata);
			$smarty->assign("pass", $pass);
			$smarty->assign("site", $site);
			$message = $tpl->load_template("email_send_repass", true);

			sendmail($post->email, "Ваш новый пароль для сайта: ".$site['title'], $message);


			# log
			$logger->info("Новый пароль создан. Проверьте Ваш почтовый ящик.");
			go("/");
		}
		else {
			# bad result
			$logger->error("Не удалось сгенерировать новый пароль. Предоставленные данные неверны.");
			goback();
		}

	}
}

/**
 * Init Class
 */
$uirepass = new UI_RePass;