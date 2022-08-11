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
 * Class UI_Reg
 */
class UI_Reg {

	/**
	 * UI_Reg constructor.
	 */
	public function __construct() {

		global $roocms, $structure, $nav, $users, $post;

		# title
		$structure->page_title = "Регистрация";

		# breadcrumb
		$nav->breadcrumb[] = array('part'=>'reg', 'title'=>'Регистрация');

		# if users registred
		if($users->uid != 0) {
			go(SCRIPT_NAME."?part=ucp&act=ucp");
		}
		
		# action
		switch($roocms->act) {
			case 'join':
				if(isset($post->join)) {
					$this->join();
				}
				break;

			case 'expressreg':
				if(isset($post->expressreg)) {
					$this->expressreg();
				}
				break;

			case 'activation':
				$this->activation();
				break;

			case 'verification':
				$this->verification();
				break;
			
			default:
				$this->profile();
				break;
		}
	}


	/**
	 * load template "registration form"
	 */
	private function profile() {

		global $tpl;

		$tpl->load_template("reg_profile");
	}


	/**
	 * Функция проверки регистрационных данных (анкеты пользователя) и регистрации
	 */
	private function join() {

		global $db, $security, $post, $logger, $mailer, $users, $site, $smarty, $tpl;

		# nickname
		$users->check_create_nickname();

		# login
		$users->check_create_login();

		# email
		$users->valid_user_email($post->email);

		if(!isset($_SESSION['error']) && $post->valid_captcha()) {

			# password
			if(!isset($post->password)) {
				$post->password = $security->generate_password();
			}

			$salt = $security->generate_salt();
			$password = $security->hash_password($post->password, $salt);

			# check personal data
			$users->correct_personal_data();

			# activation code
			$activation = [];
			$activation['code'] = randcode(7);


			$db->query("INSERT INTO ".USERS_TABLE." (login, nickname, email, mailing, password, salt, date_create, date_update, last_visit, secret_key, activation_code,
								 user_name, user_surname, user_last_name, user_birthdate, user_sex)
							 VALUES ('".$post->login."', '".$post->nickname."', '".$post->email."', '".$post->mailing."', '".$password."', '".$salt."', '".time()."', '".time()."', '".time()."', '".randcode(16)."', '".$activation['code']."',
								 '".$post->user_name."', '".$post->user_surname."', '".$post->user_last_name."', '".$post->user_birthdate."', '".$post->user_sex."')");
			$uid = $db->insert_id();


			# avatar
			$av = $users->upload_avatar($uid);
			$db->query("UPDATE ".USERS_TABLE." SET avatar='".$av."' WHERE uid='".$uid."'");


			# activation link
			$activation['link'] = $site['domain'].SCRIPT_NAME."?part=reg&act=activation&email=".$post->email."&code=".$activation['code'];


			# notice user on email
			$smarty->assign("login", $post->login);
			$smarty->assign("nickname", $post->nickname);
			$smarty->assign("email", $post->email);
			$smarty->assign("password", $post->password);
			$smarty->assign("activation", $activation);
			$smarty->assign("site", $site);
			$message = $tpl->load_template("mail/new_registration", true);

			$mailer->send($post->email, "Вы зарегистрировались на сайте ".$site['title'], $message);

			# Mailing cookie
			if($post->mailing == 1) {
				$exp = time()+(60*60*24*7);
				setcookie("mailing", true, $exp);
			}

			# notice
			$logger->info("Поздравляем с Регистрацией. Вам осталось подтвердить адрес электронной почты для этого пройдите по ссылке отправленной Вам в письме.", false);

			# go
			go(SCRIPT_NAME."?part=reg&act=activation&email=".$post->email);
		}

		goback();
	}


	/**
	 * Express registration and subscribed
	 */
	private function expressreg() {

		global $post;

		$var = explode("@", $post->email);
		$post->nickname = $var[0];
		$post->mailing = 1;

		$this->join();
	}


	/**
	 * Функция активации аккаунта и проверки электронной почты
	 */
	private function activation() {

		global $get, $parse, $smarty, $tpl;

		$email = (isset($get->_email) && $parse->valid_email($get->_email)) ? $get->_email : "" ;
		$code  = (isset($get->_code)) ? $get->_code : "" ;

		# tpl
		$smarty->assign("email", $email);
		$smarty->assign("code",  $code);
		$tpl->load_template("reg_activation");
	}


	/**
	 * Функция проверки подтверждения регистрации пользователя.
	 */
	private function verification() {

		global $db, $parse, $logger, $post;
		
		if(isset($post->email, $post->code) && $parse->valid_email($post->email) && $db->check_id($post->email, USERS_TABLE, "email", "activation_code='".$post->code."'")) {
			$db->query("UPDATE ".USERS_TABLE." SET status='1', activation_code='', last_visit='".time()."' WHERE email='".$post->email."'");
			$logger->info("Спасибо. Ваша учетная запись активирована. Добро пожжаловать.", false);
			go("/");
		}

		# Если не сработало...
		$logger->error("Активация не удалась. Мы не нашли подходящих сведений в базе данных.", false);
		goback();

	}
}

/**
 * Init Class
 */
$uireg = new UI_Reg;
