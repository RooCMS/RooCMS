<?php
/**
 * RooCMS - Open Source Free Content Managment System
 * @copyright © 2010-2018 alexandr Belov aka alex Roosso. All rights reserved.
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


/**
 * Class ACP_USERS
 */
class ACP_Users {

	# vars
	private $uid = 0;
	private $gid = 0;



	/**
	 * Вперед и только веперед
	 */
	public function __construct() {

		global $roocms, $tpl;


		# Проверяем идентификатор юзера
		$this->check_var_uid();
		# Проверка идентификтора группы
		$this->check_var_gid();


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

			case 'create_group':
				$this->create_new_group();
				break;

			case 'edit_group':
			case 'update_group':
			case 'delete_group':
				if($this->gid != 0) {
					switch($roocms->part) {
						case 'edit_group':
							$this->edit_group($this->gid);
							break;

						case 'update_group':
							$this->update_group($this->gid);
							break;

						case 'delete_group':
							$this->delete_group($this->gid);
							break;
					}
				}
				else {
					go(CP."?act=users&part=group_list");
				}
				break;

			case 'exclude_user_group':
				if($this->uid != 0 && $this->gid != 0) {
					$this->exclude_user_group($this->uid, $this->gid);
				}
				else {
					goback();
				}
				break;

			case 'group_list':
				$this->view_all_groups();
				break;

			default:
				$this->view_all_users();
				break;
		}

		# output
		$tpl->load_template("users");
	}


	/**
	 * Выводим список пользователей.
	 */
	private function view_all_users() {

		global $db, $smarty, $tpl, $parse;

		$data = [];
		$q = $db->query("SELECT u.uid, u.gid, u.status, u.login, u.nickname, u.avatar, u.email, u.mailing, u.title, u.user_sex, u.user_slogan,
					u.date_create, u.date_update, u.last_visit, u.activation_code, u.ban, u.ban_reason, u.ban_expiried,
					g.title AS gtitle
					FROM ".USERS_TABLE." AS u
					LEFT JOIN ".USERS_GROUP_TABLE." AS g ON (g.gid = u.gid)
					ORDER BY u.uid ASC");
		while($row = $db->fetch_assoc($q)) {

			$row['date_create'] = $parse->date->unix_to_rus($row['date_create'], false, true, false);
			$row['date_update'] = $parse->date->unix_to_rus($row['date_update'], false, true, false);

			$row['last_visit'] = $parse->date->unix_to_rus($row['last_visit'], false, true, true);

			$data[] = $row;
		}

		$smarty->assign("data", $data);
		$content = $tpl->load_template("users_view_users", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Выводим список групп.
	 */
	private function view_all_groups() {

		global $db, $smarty, $tpl, $parse;

		$data = [];
		$q = $db->query("SELECT gid, title, users, date_create, date_update FROM ".USERS_GROUP_TABLE." ORDER BY gid ASC");
		while($row = $db->fetch_assoc($q)) {

			$row['date_create'] = $parse->date->unix_to_rus($row['date_create'], false, true, false);
			$row['date_update'] = $parse->date->unix_to_rus($row['date_update'], false, true, false);

			$data[] = $row;
		}

		$smarty->assign("data", $data);
		$content = $tpl->load_template("users_view_groups", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Функция для создания нового пользователя
	 */
	private function create_new_user() {

		global $db, $smarty, $users, $tpl, $post, $logger, $security, $site;

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
					$post->password = $security->create_new_password();
				}
				$salt = $security->create_new_salt();
				$password = $security->hashing_password($post->password, $salt);

				# check_user data
				$this->check_users_data();

				$db->query("INSERT INTO ".USERS_TABLE." (login, nickname, email, mailing, title, password, salt, date_create, date_update, last_visit, status, gid,
									 user_name, user_surname, user_last_name, user_birthdate, user_sex, user_slogan)
								 VALUES ('".$post->login."', '".$post->nickname."', '".$post->email."', '".$post->mailing."', '".$post->title."', '".$password."', '".$salt."', '".time()."', '".time()."', '".time()."', '1', '".$post->gid."',
								 	 '".$post->user_name."', '".$post->user_surname."', '".$post->user_last_name."', '".$post->user_birthdate."', '".$post->user_sex."', '".$post->user_slogan."')");
				$uid = $db->insert_id();


				# avatar
				$users->upload_avatar($uid);

				# Если мы переназначаем группу пользователя
				if(isset($post->gid)) {
					# пересчитываем пользователей
					$this->count_users($post->gid);
				}

				# Уведомление пользователю на электропочту
				$smarty->assign("login", $post->login);
				$smarty->assign("nickname", $post->nickname);
				$smarty->assign("email", $post->email);
				$smarty->assign("password", $post->password);
				$smarty->assign("site", $site);
				$message = $tpl->load_template("email_new_registration", true);

				sendmail($post->email, "Вас зарегистрировали на сайте ".$site['title'], $message);


				# уведомление
				$logger->info("Пользователь #".$uid." был успешно добавлен. Уведомление об учетной записи отправлено на его электронную почту.");

				# переход
				if(isset($post->create_user_ae['ae'])) {
					go(CP."?act=users");
				}

				go(CP."?act=users&part=edit_user&uid=".$uid);
			}

			goback();
		}

		# groups
		$groups = [];
		$q = $db->query("SELECT gid, title, users FROM ".USERS_GROUP_TABLE." ORDER BY gid ASC");
		while($row = $db->fetch_assoc($q)) {
			$groups[] = $row;
		}

		# отрисовываем шаблон
		$smarty->assign("groups", $groups);
		$content = $tpl->load_template("users_create_new_user", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Функция для создания новой группы
	 */
	private function create_new_group() {

		global $db, $smarty, $tpl, $post, $logger;

		if(isset($post->create_group)) {

			# title
			if(!isset($post->title)) {
				$logger->error("У группы должно быть название!");
			}
			if(isset($post->title) && $db->check_id($post->title, USERS_GROUP_TABLE, "title")) {
				$logger->error("Группа с таким название уже существует");
			}

			if(!isset($_SESSION['error'])) {

				$db->query("INSERT INTO ".USERS_GROUP_TABLE." (title, date_create, date_update)
								       VALUES ('".$post->title."', '".time()."', '".time()."')");
				$gid = $db->insert_id();

				# уведомление
				$logger->info("Группа #".$gid." была успешно создана.");

				# переход
				if(isset($post->create_group['ae'])) {
					go(CP."?act=users&part=group_list");
				}

				go(CP."?act=users&part=edit_group&gid=".$gid);
			}

			goback();
		}


		# отрисовываем шаблон
		$content = $tpl->load_template("users_create_new_group", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Функция редактирования пользователя.
	 *
	 * @param int $uid - уникальный ид пользователя.
	 */
	private function edit_user($uid) {

		global $db, $users, $logger, $smarty, $tpl;

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
				$user['user_birthdate'] = date("d.m.Y", $user['user_birthdate']);
			}
			else {
				$user['user_birthdate'] = "";
			}

			# i am groot
			$i_am_groot = false;
			if($users->uid == $uid) {
				$i_am_groot = true;
			}

			# groups
			$groups = [];
			$q = $db->query("SELECT gid, title, users FROM ".USERS_GROUP_TABLE." ORDER BY gid ASC");
			while($row = $db->fetch_assoc($q)) {
				$groups[] = $row;
			}

			# отрисовываем шаблон
			$smarty->assign("i_am_groot", $i_am_groot);
			$smarty->assign("groups", $groups);
			$smarty->assign("user", $user);
			$content = $tpl->load_template("users_edit_user", true);
			$smarty->assign("content", $content);
		}
	}


	/**
	 * Функция редактирования группы.
	 *
	 * @param int $gid - уникальный ид группы.
	 */
	private function edit_group($gid) {

		global $db, $smarty, $tpl;

		$q = $db->query("SELECT gid, title FROM ".USERS_GROUP_TABLE." WHERE gid='".$gid."'");
		$group = $db->fetch_assoc($q);

		$guser = [];
		$u = $db->query("SELECT uid, nickname, login, avatar, status, ban FROM ".USERS_TABLE." WHERE gid='".$gid."' ORDER BY uid ASC");
		while($row = $db->fetch_assoc($u)) {
			$guser[$row['uid']] = $row;
		}

		# отрисовываем шаблон
		$smarty->assign("group", $group);
		$smarty->assign("users", $guser);
		$content = $tpl->load_template("users_edit_group", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Функция обновляет данные пользователя в БД
	 *
	 * @param int $uid - уникальный идентификатор пользователя
	 */
	private function update_user($uid) {

		global $db, $post, $config, $site, $users, $img, $security, $logger, $parse, $smarty, $tpl;

		if(isset($post->update_user)) {

			$q = $db->query("SELECT login, nickname, email, avatar FROM ".USERS_TABLE." WHERE uid='".$uid."'");
			$udata = $db->fetch_assoc($q);

			$query = "";

			# login
			if(isset($post->login)) {
				$post->login = mb_strtolower($parse->text->transliterate($post->login));

				if(!$users->check_field("login", $post->login, $udata['login'])) {
					$logger->error("Логин не должен совпадать с логином другого пользователя!");
				}
				else {
					$query .= "login='".$post->login."', ";
				}
			}
			else {
				$logger->error("У пользователя должен быть логин.");
			}

			# nickname
			if(isset($post->nickname) && $users->check_field("nickname", $post->nickname, $udata['nickname'])) {
				$query .= "nickname='".$post->nickname."', ";
			}
			else {
				$logger->error("Не удалось обновить Никнейм пользователя (возможные он был некоректно указан, или такой никнейм уже есть в БД)", false);
			}


			# email
			if(isset($post->email) && $parse->valid_email($post->email) && $users->check_field("email", $post->email, $udata['email'])) {
				$query .= "email='".$post->email."', ";
			}
			else {
				$logger->error("Не удалось обновить Email (возможные он был некоректно указан, или такой email уже есть в БД)");
			}

			# avatar
			$av = $users->upload_avatar($uid);
			if(isset($av[0])) {
				if($udata['avatar'] != "" && $udata['avatar'] != $av[0]) {
					$img->erase_image(_UPLOADIMAGES."/".$udata['avatar']);
				}
				$query .= "avatar='".$av[0]."', ";
			}


			# update
			if(!isset($_SESSION['error'])) {

				# check_user data
				$this->check_users_data($uid);

				# password
				if(isset($post->password)) {
					$salt = $security->create_new_salt();
					$password = $security->hashing_password($post->password, $salt);

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

				# Если мы переназначаем группу пользователя
				if(isset($post->gid, $post->now_gid) && $post->gid != $post->now_gid) {
					# пересчитываем пользователей
					$this->count_users($post->gid);
					$this->count_users($post->now_gid);
				}


				# notice
				$logger->info("Данные пользователя #".$uid." успешно обновлены.");

				# Уведомление пользователю на электропочту
				$smarty->assign("login", $post->login);
				$smarty->assign("nickname", $post->nickname);
				$smarty->assign("email", $post->email);
				$smarty->assign("password", $post->password);
				$smarty->assign("site", $site);
				$message = $tpl->load_template("email_update_userdata", true);

				sendmail($post->email, "Ваши данные на \"".$site['title']."\" были обновлены администрацией", $message);

				# переход
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
	 * Функция обновляет данные группы пользователей БД
	 *
	 * @param int $gid - уникальный идентификатор группы
	 */
	private function update_group($gid) {

		global $db, $post, $users, $logger;

		if(isset($post->update_group)) {

			$q = $db->query("SELECT title FROM ".USERS_GROUP_TABLE." WHERE gid='".$gid."'");
			$gdata = $db->fetch_assoc($q);

			$query = "";

			# login
			if(isset($post->title)) {
				if(!$users->check_field("title", $post->title, $gdata['title'], USERS_GROUP_TABLE)) {
					$logger->error("Название группы не может совпадать с названием другой группы!");
				}
				else {
					$query .= "title='".$post->title."', ";
				}
			}
			else {
				$logger->error("У группы должно быть название.");
			}

			# update
			if(!isset($_SESSION['error'])) {

				# update
				$db->query("UPDATE ".USERS_GROUP_TABLE." SET ".$query." date_update='".time()."' WHERE gid='".$gid."'");
				$this->count_users($gid);

				# notice
				$logger->info("Данные группы #".$gid." успешно обновлены.");

				# переход
				if(isset($post->update_group['ae'])) {
					go(CP."?act=users&part=group_list");
				}

				go(CP."?act=users&part=edit_group&gid=".$gid);
			}

			# goback
			goback();
		}

		# goback
		goback();

	}


	/**
	 * Функция удаляет выбранного пользователя из БД
	 *
	 * @param int $uid - уникальный идентификатор пользователя
	 */
	private function delete_user($uid) {

		global $db, $img, $logger;

		# О Боже, только не это...
		if($uid == 1) {
			$logger->msg("Нельзя удалить учетную запись главного администратора!");
		}
		else {
			$q = $db->query("SELECT gid, avatar FROM ".USERS_TABLE." WHERE uid='".$uid."'");
			$data = $db->fetch_assoc($q);

			# удаляем аватарку.
			$img->erase_image(_UPLOADIMAGES."/".$data['avatar']);

			# удаляем юзера
			$db->query("DELETE FROM ".USERS_TABLE." WHERE uid='".$uid."'");
			$logger->info("Пользователь #".$uid." был успешно удален из Базы Данных.");

			# пересчитываем пользователей в группе.
			$this->count_users($data['gid']);

			# удаляем его переписку
			$db->query("DELETE FROM ".USERS_PM_TABLE." WHERE to_uid='".$uid."'");
		}

		# go
		goback();
	}


	/**
	 * Функция удаляет выбранную группу из БД
	 *
	 * @param int $gid - уникальный идентификатор группы
	 */
	private function delete_group($gid) {

		global $db, $logger;

		$db->query("UPDATE ".USERS_TABLE." SET gid='0' WHERE gid='".$gid."'");

		$db->query("DELETE FROM ".USERS_GROUP_TABLE." WHERE gid='".$gid."'");
		$logger->info("Группа #".$gid." был успешна удалена из Базы Данных.");

		# go
		goback();
	}


	/**
	 * Исключаем пользователя из группы
	 *
	 * @param int $uid - уникальный идентификатор пользователя.
	 * @param int $gid - Уникальный идентификатор группы
	 */
	private function exclude_user_group($uid, $gid) {

		global $db, $logger;

		$q = $db->query("SELECT gid FROM ".USERS_TABLE." WHERE uid='".$uid."'");
		$data = $db->fetch_assoc($q);

		if($data['gid'] == $gid) {
			$db->query("UPDATE ".USERS_TABLE." SET gid='0' WHERE uid='".$uid."'");

			# уведомление
			$logger->info("Пользователь #".$uid." был успешно исключен из группы #".$gid.".");

			# пересчитываем пользователей в группе.
			$this->count_users($gid);
		}

		# go
		goback();
	}

	/**
	 * Функция проверяет кол-во пользователей состоящих в группе.
	 *
	 * @param int $gid - уникальный идентификатор группы
	 */
	private function count_users($gid) {

		global $db, $logger;

		if($gid != 0 && $db->check_id($gid, USERS_GROUP_TABLE, "gid")) {
			# count
			$c = $db->count(USERS_TABLE, "gid='".$gid."'");

			# update
			$db->query("UPDATE ".USERS_GROUP_TABLE." SET users='".$c."' WHERE gid='".$gid."'");

			# уведомление
			$logger->info("Информация о кол-ве пользователей для группы #".$gid." обновлена.");
		}
	}


	/**
	 * Check user data for insert/update
	 *
	 * @param int $uid - user id
	 */
	private function check_users_data($uid = 0) {

		global $db, $post, $users;

		# group
		$post->gid = (isset($post->gid) && $db->check_id($post->gid, USERS_GROUP_TABLE, "gid")) ? $post->gid : 0 ;

		# status
		$post->status = ((isset($post->status) && $post->status == 1)) ? 1 : 0 ;

		# title
		$post->title = ((isset($post->title) && $post->title == "a")) ? "a" : "u" ;

		# correct for ID=1
		if($uid == 1) {
			$post->status = 1;
			$post->title = "a";
		}

		# correct personal data
		$users->correct_personal_data();
	}


	/**
	 * Check & init $this->uid
	 */
	private function check_var_uid() {

		global $db, $get;

		if(isset($get->_uid) && $db->check_id($get->_uid, USERS_TABLE, "uid")) {
			$this->uid = $get->_uid;
		}
	}


	/**
	 * Check & init $this->gid
	 */
	private function check_var_gid() {

		global $db, $get;

		if(isset($get->_gid) && $db->check_id($get->_gid, USERS_GROUP_TABLE, "gid")) {
			$this->gid = $get->_gid;
		}
	}
}


/**
 * Init class
 */
$acp_users = new ACP_Users;