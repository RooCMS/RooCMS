<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2023 alexandr Belov aka alex Roosso.
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
if(!defined('RooCMS') || !defined('ACP')) {
	die('Access Denied');
}
//#########################################################


# require trait
require_once "extends/trait_acp_useroperation.php";


/**
 * Class ACP_USERS
 */
class ACP_Users {

	use ACP_UserOperation;


	/**
	 * ACP_Users constructor.
	 */
	public function __construct() {

		global $roocms, $tpl;


		# Check user id
		$this->check_var_uid();


		# action
		switch($roocms->part) {

			case 'create_user':
				$this->create_new_user();
				break;

			case 'edit_user':
			case 'update_user':
			case 'delete_user':
				if($this->uid != 0) {
					switch($roocms->part) {
						case 'edit_user':
							$this->edit_user($this->uid);
							break;

						case 'update_user':
							$this->update_user($this->uid);
							break;

						case 'delete_user':
							$this->delete_user($this->uid);
							break;
					}
				}
				else {
					go(CP."?act=users");
				}
				break;

			default:
				$this->view_all_users();
				break;
		}

		# output
		$tpl->load_template("users");
	}


	/**
	 * Show users list
	 */
	private function view_all_users() {

		global $db, $smarty, $tpl, $parse;

		$data = [];
		$q = $db->query("SELECT u.uid, u.gid, u.status, u.login, u.nickname, u.avatar, u.email, u.mailing, u.title, u.user_sex, u.user_slogan,
					u.date_create, u.date_update, u.last_visit, u.activation_code, u.ban, u.ban_reason, u.ban_expiried,
					g.title AS gtitle
					FROM ".USERS_TABLE." AS u
					LEFT JOIN ".USERS_GROUP_TABLE." AS g ON (g.gid = u.gid)
					ORDER BY u.uid");
		while($row = $db->fetch_assoc($q)) {

			$row['date_create'] = $parse->date->unix_to_rus($row['date_create'], false, true, false);
			$row['date_update'] = $parse->date->unix_to_rus($row['date_update'], false, true, false);

			$row['last_visit'] = $parse->date->unix_to_rus($row['last_visit'], false, true, true);

			$data[] = $row;
		}

		# tpl
		$smarty->assign("data", $data);
		$content = $tpl->load_template("users_view_users", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Create new user
	 */
	private function create_new_user() {

		global $db, $security, $post, $logger, $users, $mailer, $site, $tpl, $smarty;

		if(isset($post->create_user)) {

			# nickname
			$users->check_create_nickname();

			# login
			$users->check_create_login();

			# email
			$users->valid_user_email($post->email);


			if(!isset($_SESSION['error'])) {

				#password
				if(!isset($post->password)) {
					$post->password = $security->generate_password();
				}
				$salt = $security->generate_salt();
				$password = $security->hash_password($post->password, $salt);

				# check_user data
				$this->check_users_data();

				$db->query("INSERT INTO ".USERS_TABLE." (login, nickname, email, mailing, title, password, salt, date_create, date_update, last_visit, status, gid, secret_key,
									 user_name, user_surname, user_last_name, user_birthdate, user_sex, user_slogan)
								 VALUES ('".$post->login."', '".$post->nickname."', '".$post->email."', '".$post->mailing."', '".$post->title."', '".$password."', '".$salt."', '".time()."', '".time()."', '".time()."', '1', '".$post->gid."', '".randcode(16)."',
								 	 '".$post->user_name."', '".$post->user_surname."', '".$post->user_last_name."', '".$post->user_birthdate."', '".$post->user_sex."', '".$post->user_slogan."')");
				$uid = $db->insert_id();

				# avatar
				$users->upload_avatar($uid);

				# recount users in group
				$this->count_users($post->gid);

				# notice user to email
				$smarty->assign("login", $post->login);
				$smarty->assign("nickname", $post->nickname);
				$smarty->assign("email", $post->email);
				$smarty->assign("password", $post->password);
				$smarty->assign("site", $site);
				$message = $tpl->load_template("mail/new_registration", true);

				$mailer->send($post->email, "Вас зарегистрировали на сайте ".$site['title'], $message);

				# notice
				$logger->info("Пользователь #".$uid." был успешно добавлен. Уведомление об учетной записи отправлено на его электронную почту.");

				# go
				if(isset($post->create_user['ae'])) {
					go(CP."?act=users");
				}

				go(CP."?act=users&part=edit_user&uid=".$uid);
			}

			goback();
		}

		# list groups
		$groups = $users->get_usergroups();

		# image types
		$imagetype = [];
		require _LIB."/mimetype.php";
		$smarty->assign("allow_images_type", $imagetype);

		# tpl
		$smarty->assign("groups", $groups);
		$content = $tpl->load_template("users_create_new_user", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Edit user data.
	 *
	 * @param int $uid - user identificator.
	 */
	private function edit_user(int $uid) {

		global $db, $users, $parse, $logger, $smarty, $tpl;

		# security superamin
		if($uid == 1 && $users->uid != 1) {
			$logger->error("Радктировать учетную запись суперадмина, может только суперадмин!");
			goback();
		}
		else {
			$q = $db->query("SELECT uid, gid, status, avatar, login, nickname, email, mailing, title, date_create, last_visit, user_name, user_surname, user_last_name, user_birthdate, user_sex, user_slogan, activation_code FROM ".USERS_TABLE." WHERE uid='".$uid."'");
			$user = $db->fetch_assoc($q);

			# user personal data birth date
			if($user['user_birthdate'] != 0) {
				$user['user_birthdate'] = $parse->date->jd_to_rusint($user['user_birthdate']);
			}

			# i am groot
			$i_am_groot = false;
			if($users->uid == $uid) {
				$i_am_groot = true;
			}

			# list groups
			$groups = $users->get_usergroups();

			# image types
			$imagetype = [];
			require _LIB."/mimetype.php";
			$smarty->assign("allow_images_type", $imagetype);

			# tpl
			$smarty->assign("i_am_groot", $i_am_groot);
			$smarty->assign("groups", $groups);
			$smarty->assign("user", $user);
			$content = $tpl->load_template("users_edit_user", true);
			$smarty->assign("content", $content);
		}
	}


	/**
	 * Update user data
	 *
	 * @param int $uid - user identificator
	 */
	private function update_user(int $uid) {

		global $db, $security, $parse, $post, $logger, $users, $mailer, $site, $smarty, $tpl;

		if(isset($post->update_user)) {

			$q = $db->query("SELECT login, nickname, email, avatar FROM ".USERS_TABLE." WHERE uid='".$uid."'");
			$udata = $db->fetch_assoc($q);

			$query = "";

			# login
			if(isset($post->login)) {
				$post->login = mb_strtolower($parse->text->transliterate($post->login));

				if(!$users->check_field("login", $post->login, $udata['login'])) {
					$logger->error("Логин пользователя #".$uid." не должен совпадать с логином другого пользователя!");
				}
				else {
					$query .= "login='".$post->login."', ";
				}
			}
			else {
				$logger->error("У пользователя #".$uid." должен быть логин.");
			}

			# nickname
			if(isset($post->nickname) && $users->check_field("nickname", $post->nickname, $udata['nickname'])) {
				$query .= "nickname='".$post->nickname."', ";
			}
			else {
				$logger->error("Не удалось обновить Никнейм пользователя #".$uid." (возможные он был некоректно указан, или такой никнейм уже есть в БД)", false);
			}

			# email
			if(isset($post->email) && $parse->valid_email($post->email) && $users->check_field("email", $post->email, $udata['email'])) {
				$query .= "email='".$post->email."', ";
			}
			else {
				$logger->error("Не удалось обновить Email пользователя #".$uid." (возможные он был некоректно указан, или такой email уже есть в БД)");
			}

			# upload / update avatar
			$av = $users->upload_avatar($uid, $udata['avatar']);
			$query .= "avatar='".$av."', ";

			# update
			if(!isset($_SESSION['error'])) {

				# check_user data
				$this->check_users_data($uid);

				# password
				if(isset($post->password)) {
					$salt = $security->generate_salt();
					$password = $security->hash_password($post->password, $salt);

					$query .= "password='".$password."', salt='".$salt."', ";
				}

				$db->query("UPDATE ".USERS_TABLE." SET 
									".$query."
									gid = '".$post->gid."',
									user_name = '".$post->user_name."',
									user_surname = '".$post->user_surname."',
									user_last_name = '".$post->user_last_name."',
									user_birthdate = '".$post->user_birthdate."',
									user_sex='".$post->user_sex."',
									user_slogan='".$post->user_slogan."',
									title='".$post->title."',
									status='".$post->status."',
									mailing='".$post->mailing."',
									date_update='".time()."' 
								WHERE uid='".$uid."'");

				# If changed user group
				if($post->gid != $post->now_gid) {
					# recount users
					$this->count_users($post->gid);
					$this->count_users($post->now_gid);
				}

				# notice
				$logger->info("Данные пользователя #".$uid." успешно обновлены.");

				# notice to email
				$smarty->assign("login", $post->login);
				$smarty->assign("nickname", $post->nickname);
				$smarty->assign("email", $post->email);
				$smarty->assign("password", $post->password);
				$smarty->assign("site", $site);
				$message = $tpl->load_template("mail/update_userdata", true);

				$mailer->send($post->email, "Ваши данные на \"".$site['title']."\" были обновлены администрацией", $message);

				# go
				if(isset($post->update_user['ae'])) {
					go(CP."?act=users");
				}

				go(CP."?act=users&part=edit_user&uid=".$uid);
			}
		}

		# goback
		goback();
	}


	/**
	 * remove user
	 *
	 * @param int $uid - user identificator
	 */
	private function delete_user(int $uid) {

		global $db, $img, $logger;

		# Oh, my god... damn...
		if($uid == 1) {
			$logger->error("Нельзя удалить учетную запись главного администратора!");
		}
		else {
			$q = $db->query("SELECT gid, avatar FROM ".USERS_TABLE." WHERE uid='".$uid."'");
			$data = $db->fetch_assoc($q);

			# remove avatat
			$img->erase_image(_UPLOADIMAGES."/".$data['avatar']);

			# remove user data
			$db->query("DELETE FROM ".USERS_TABLE." WHERE uid='".$uid."'");
			$logger->info("Пользователь #".$uid." был успешно удален из Базы Данных.");

			# recount user in group
			$this->count_users($data['gid']);

			# remove user pm
			$db->query("DELETE FROM ".USERS_PM_TABLE." WHERE to_uid='".$uid."'");

			# remove author_id in feed items
			$db->query("UPDATE ".PAGES_FEED_TABLE." SET author_id='0' WHERE author_id='".$uid."'");
		}

		# go
		go(CP."?act=users");
	}


	/**
	 * Check user data for insert/update
	 *
	 * @param int $uid - user id
	 */
	private function check_users_data(int $uid = 0) {

		global $db, $post, $users;

		# group
		$post->gid = ($db->check_id($post->gid, USERS_GROUP_TABLE, "gid")) ? $post->gid : 0 ;

		# correct for ID=1
		if($uid == 1) {
			$post->status = 1;
			$post->title = "a";
		}
		else {
			# status
			if(isset($post->status)) {
				$post->status = (int) filter_var($post->status, FILTER_VALIDATE_BOOLEAN);
			}

			# title
			$post->title = ($post->title == "a") ? "a" : "u" ;
		}

		# correct personal data
		$users->correct_personal_data();
	}
}

/**
 * Init class
 */
$acp_users = new ACP_Users;
