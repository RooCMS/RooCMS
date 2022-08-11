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
if(!defined('RooCMS') || !defined('UI')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class UI_RePass
 */
class UI_RePass {



	public function __construct() {

		global $structure, $nav, $roocms, $users, $post;

		# title
		$structure->page_title = "Восстановление пароля";

		# breadcrumb
		$nav->breadcrumb[] = array('part'=>'repass', 'title'=>'Восстановление пароля');

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
	 * load repass form
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
	 * Reminder password
	 */
	private function reminder() {

		global $db, $roocms, $site, $post, $logger, $parse, $mailer, $smarty, $tpl;

		# log
		$logger->log("Запрос на восстановление пароля для почтового ящика: ".$post->email." с IP:".$roocms->userip);

		# check
		if(isset($post->email) && $parse->valid_email($post->email) && $post->valid_captcha() && $db->check_id($post->email, USERS_TABLE, "email")) {

			# userdata
			$q = $db->query("SELECT nickname, secret_key FROM ".USERS_TABLE." WHERE email='".$post->email."'");
			$userdata = $db->fetch_assoc($q);

			# confirm link
			$confirmlink = $site['domain'].SCRIPT_NAME."?part=repass&act=confirm&email=".$post->email."&code=".$userdata['secret_key'];


			# user notice on email
			$smarty->assign("userdata", $userdata);
			$smarty->assign("confirmlink", $confirmlink);
			$smarty->assign("site", $site);
			$message = $tpl->load_template("mail/confirm_repass", true);

			$mailer->send($post->email, "Запрос на восстановление пароля для сайта: ".$site['title'], $message);


			# notice
			$logger->info("Инструкции для восстановления пароля, отправлены Вам на электронную почту", false);

			# go
			go(SCRIPT_NAME."?part=repass&act=confirm&email=".$post->email);
		}
		else {
			# bad result
			$logger->error("Не удалось выполнить запрос на восстановление пароля.", false);
			goback();
		}
	}



	/**
	 * Функция подтверждения запроса на смену пароля.
	 */
	private function verification() {

		global $db, $security, $parse, $logger, $post, $mailer, $site, $smarty, $tpl;

		if(isset($post->email, $post->code) && $parse->valid_email($post->email) && $post->valid_captcha() && $db->check_id($post->email, USERS_TABLE, "email", "secret_key='".$post->code."'")) {

			# new password
			$salt = $security->generate_salt();
			$pass = randcode(10);
			$password = $security->hash_password($pass, $salt);

			# userdata
			$q = $db->query("SELECT login, nickname FROM ".USERS_TABLE." WHERE email='".$post->email."'");
			$userdata = $db->fetch_assoc($q);

			# update
			$db->query("UPDATE ".USERS_TABLE." SET salt='".$salt."', password='".$password."', secret_key='".randcode(16)."', last_visit='".time()."' WHERE email='".$post->email."'");


			# user notice on email
			$smarty->assign("udata", $userdata);
			$smarty->assign("pass", $pass);
			$smarty->assign("site", $site);
			$message = $tpl->load_template("mail/send_repass", true);

			$mailer->send($post->email, "Ваш новый пароль для сайта: ".$site['title'], $message);


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
