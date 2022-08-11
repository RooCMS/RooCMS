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


class UCP_CP {


	/**
	 * Init
	 */
	public function __construct() {

		global $structure, $nav, $roocms;

		# title
		$structure->page_title = "Личный кабинет";

		# move
		switch($roocms->move) {
			case 'edit_info':
				$this->edit_info();
				break;

			case 'update_info':
				$this->update_info();
				break;

			case 'mailing':
				$this->mailing();
				break;

			case 'unmailing':
				$this->unmailing();
				break;

			default:
				$this->cp();
				break;
		}

		# breadcrumb
		$nav->breadcrumb[] = array('part'=>'ucp', 'act'=>'ucp', 'title'=>'Личный кабинет');
	}


	/**
	 * Main UCP
	 */
	private function cp() {

		global $tpl;

		# tpl
		$tpl->load_template("ucp");
	}


	/**
	 * Функция редактирования личных данных
	 */
	private function edit_info() {

		global $structure, $nav, $users, $parse, $tpl, $smarty;

		# title
		$structure->page_title = "Изменяем личные данные";

		# breadcrumb
		$nav->breadcrumb[] = array('part'=>'ucp', 'act' => 'ucp', 'title'=>'Изменяем личные данные');

		# tpl
		$users->userdata['user_slogan_edit'] = $parse->text->clearhtml($users->userdata['user_slogan']);
		$smarty->assign("userdata", $users->userdata);
		$tpl->load_template("ucp_edit_info");
	}


	/**
	 * Функция обновляет личные данные пользователя
	 */
	private function update_info() {

		global $db, $post, $parse, $logger, $mailer, $users, $site, $tpl, $smarty;

		$query = "";

		# login
		if(isset($post->login)) {
			$post->login = mb_strtolower($parse->text->transliterate($post->login));

			if(!$users->check_field("login", $post->login, $users->userdata['login'])) {
				$logger->error("Ваш логин не был изменен. Возможно использование такого логина невозможно, попробуйте выбрать другой логин");
			}
		}
		else {
			$logger->error("Вы не указали логин.");
		}

		# nickname
		if(isset($post->nickname)) {
			if(!$users->check_field("nickname", $post->nickname, $users->userdata['nickname'])) {
				$logger->error("Такой псевдоним уже имеется у одного из пользователей. Пожалуйста, выберите другой псевдоним.");
			}
		}
		else {
			$logger->error("Вы не указали псевдоним.", false);
		}

		# email
		if(isset($post->email) && $parse->valid_email($post->email)) {
			if(!$users->check_field("email", $post->email, $users->userdata['email'])) {
				$logger->error("Указанный email уже существует в Базе Данных!");
			}
		}
		else {
			$logger->info("Вы не указали электронную почту (или указали в некорректном формате), поэтому мы сохранили ту, что была указана ранее.<br />На эту почту вам будут приходить уведомления с сайта. В случае если вы забудете свой пароль, восстановить его можно будет с помошью указанной почты.", false);
		}

		# personal data
		$users->correct_personal_data();

		# delete avatar
		if(isset($post->delete_avatar)) {
			$users->delete_avatar($users->uid);
			$query .= "avatar='', ";
		}
		else {
			# upload / update avatar
			$av = $users->upload_avatar($users->uid, $users->avatar);
			$query .= "avatar='".$av."', ";
		}

		# update
		if(!isset($_SESSION['error'])) {
			# password
			if(isset($post->password)) {
				$salt = $users->generate_salt();
				$password = $users->hash_password($post->password, $salt);

				$query .= "password='".$password."', salt='".$salt."', ";
			}

			$db->query("UPDATE ".USERS_TABLE." SET 
								login = '".$post->login."',
								nickname = '".$post->nickname."',
								email = '".$post->email."',
								mailing = '".$post->mailing."',
								".$query." 
								user_name = '".$post->user_name."',
								user_surname = '".$post->user_surname."',
								user_last_name = '".$post->user_last_name."',
								user_birthdate = '".$post->user_birthdate."',
								user_sex='".$post->user_sex."',
								user_slogan='".$post->user_slogan."',
								date_update='".time()."' 
							WHERE uid='".$users->userdata['uid']."'");

			# notice
			$logger->info("Ваши данные успешно обновлены.", false);

			# notice user on email
			$smarty->assign("login", $post->login);
			$smarty->assign("nickname", $post->nickname);
			$smarty->assign("email", $post->email);
			$smarty->assign("password", $post->password);
			$smarty->assign("site", $site);
			$message = $tpl->load_template("mail/update_userinfo", true);

			$mailer->send($post->email, "Ваши данные на \"".$site['title']."\" были обновлены", $message);

			# go out
			go(SCRIPT_NAME."?part=ucp&act=ucp");
		}

		# go
		goback();
	}


	/**
	 * Express subscribe on mailing
	 */
	private function mailing() {

		global $db, $users, $logger;

		# update
		$db->query("UPDATE ".USERS_TABLE." SET mailing='1', date_update='".time()."' WHERE uid='".$users->uid."'");

		# notice
		$logger->info("Спасибо, что подписались на рассылку.", false);

		# go
		goback();
	}


	/**
	 * Express unsubscribe on mailing
	 */
	private function unmailing() {

		global $db, $users, $logger;

		# update
		$db->query("UPDATE ".USERS_TABLE." SET mailing='0', date_update='".time()."' WHERE uid='".$users->uid."'");

		# notice
		$logger->info("Вы были успешно отписаны от получения рассылки", false);

		# go
		goback();
	}
}

/**
 * Init Class
 */
$ucpcp = new UCP_CP;
