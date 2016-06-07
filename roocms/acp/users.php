<?php
/**
 * @package      RooCMS
 * @subpackage   Admin Control Panel
 * @subpackage   Users settings
 * @author       alex Roosso
 * @copyright    2010-2016 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.3.4
 * @since        $date$
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 *   RooCMS - Russian free content managment system
 *   Copyright (C) 2010-2016 alex Roosso aka alexandr Belov info@roocms.com
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see http://www.gnu.org/licenses/
 *
 *
 *   RooCMS - Русская бесплатная система управления сайтом
 *   Copyright (C) 2010-2016 alex Roosso (александр Белов) info@roocms.com
 *
 *   Это программа является свободным программным обеспечением. Вы можете
 *   распространять и/или модифицировать её согласно условиям Стандартной
 *   Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
 *   Обеспечения, версии 3 или, по Вашему желанию, любой более поздней версии.
 *
 *   Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
 *   ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
 *   ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
 *   Общественную Лицензию GNU для получения дополнительной информации.
 *
 *   Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
 *   с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
 */

//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS') || !defined('ACP')) die('Access Denied');
//#########################################################


/**
 * Class ACP_USERS
 */
class ACP_USERS {

	# vars
	private $uid = 0;
	private $gid = 0;



	/**
	 * Вперед и только веперед
	 */
	public function __construct() {

		global $db, $roocms, $tpl, $GET;


		# Проверяем идентификатор юзера
		if(isset($GET->_uid) && $db->check_id($GET->_uid, USERS_TABLE, "uid")) $this->uid = $GET->_uid;
		# Проверка идентификтора группы
		if(isset($GET->_gid) && $db->check_id($GET->_gid, USERS_GROUP_TABLE, "gid")) $this->gid = $GET->_gid;


		# action
		switch($roocms->part) {

			case 'create_user':
				$this->create_new_user();
				break;

			case 'edit_user':
				if($this->uid != 0) $this->edit_user($this->uid);
				else go(CP."?act=users");
				break;

			case 'update_user':
				if($this->uid != 0) $this->update_user($this->uid);
				else go(CP."?act=users");
				break;

			case 'delete_user':
				if($this->uid != 0) $this->delete_user($this->uid);
				else go(CP."?act=users");
				break;

			case 'create_group':
				$this->create_new_group();
				break;

			case 'edit_group':
				if($this->gid != 0) $this->edit_group($this->gid);
				else go(CP."?act=users&part=group_list");
				break;

			case 'update_group':
				if($this->gid != 0) $this->update_group($this->gid);
				else go(CP."?act=users&part=group_list");
				break;

			case 'delete_group':
				if($this->gid != 0) $this->delete_group($this->gid);
				else go(CP."?act=users&part=group_list");
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

		$data = array();
		$q = $db->query("SELECT u.uid, u.gid, u.status, u.login, u.nickname, u.avatar, u.email, u.title, u.date_create, u.date_update, u.last_visit, u.user_sex, g.title AS gtitle
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

		$data = array();
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

		global $db, $config, $img, $smarty, $users, $tpl, $POST, $parse, $security, $site;

		if(isset($POST->create_user) || isset($POST->create_user_ae)) {

			# nickname
			if(!isset($POST->nickname) || trim($POST->nickname) == "") $POST->nickname = mb_ucfirst($POST->login);
			$POST->nickname = $users->check_new_nickname($POST->nickname);

			# login
			if(!isset($POST->login) || trim($POST->login) == "") {
				if(isset($POST->nickname) && trim($POST->nickname) != "") $POST->login = mb_strtolower($parse->text->transliterate($POST->nickname));
				else $parse->msg("У пользователя должен быть логин!", false);
			}
			else $POST->login = $parse->text->transliterate($POST->login);
			if(isset($POST->login) && trim($POST->login) != "" && $db->check_id($POST->login, USERS_TABLE, "login")) $parse->msg("Пользователь с таким логином уже существует", false);

			# email
			if(!isset($POST->email) || trim($POST->email) == "") $parse->msg("Обязательно указывать электронную почту для каждого пользователя", false);
			if(isset($POST->email) && trim($POST->email) != "" && !$parse->valid_email($POST->email)) $parse->msg("Некоректный адрес электронной почты", false);
			if(isset($POST->email) && trim($POST->email) != "" && $db->check_id($POST->email, USERS_TABLE, "email")) $parse->msg("Пользователь с таким адресом почты уже существует", false);

			# title
			$POST->title = (isset($POST->title) && $POST->title == "a") ? "a" : "u" ;

			# group
			$POST->gid = (isset($POST->gid) && $db->check_id($POST->gid, USERS_GROUP_TABLE, "gid")) ? $POST->gid : 0 ;

			if(!isset($_SESSION['error'])) {

				#password
				if(!isset($POST->password) || trim($POST->password) == "") $POST->password = $security->create_new_password();
				$salt = $security->create_new_salt();
				$password = $security->hashing_password($POST->password, $salt);

				# personal data
				if(!isset($POST->user_name)) 					$POST->user_name = "";
				if(!isset($POST->user_surname)) 				$POST->user_surname = "";
				if(!isset($POST->user_last_name)) 				$POST->user_last_name = "";
				if(isset($POST->user_birthdate) && $POST->user_birthdate != "") $POST->user_birthdate = $parse->date->rusint_to_unix($POST->user_birthdate);
				else 								$POST->user_birthdate = 0;
				if(!isset($POST->user_sex))					$POST->user_sex = "n";
				elseif($POST->user_sex == "m")					$POST->user_sex = "m";
				elseif($POST->user_sex == "f")					$POST->user_sex = "f";
				else								$POST->user_sex = "n";



				$db->query("INSERT INTO ".USERS_TABLE." (login, nickname, email, title, password, salt, date_create, date_update, last_visit, status, gid,
									 user_name, user_surname, user_last_name, user_birthdate, user_sex)
								 VALUES ('".$POST->login."', '".$POST->nickname."', '".$POST->email."', '".$POST->title."', '".$password."', '".$salt."', '".time()."', '".time()."', '".time()."', '1', '".$POST->gid."',
								 	 '".$POST->user_name."', '".$POST->user_surname."', '".$POST->user_last_name."', '".$POST->user_birthdate."', '".$POST->user_sex."')");
				$uid = $db->insert_id();


				# avatar
				$av = $img->upload_image("avatar", "", array($config->users_avatar_width, $config->users_avatar_height), array("filename"=>"av_".$uid, "watermark"=>false, "modify"=>false));
				if(isset($av[0])) $db->query("UPDATE ".USERS_TABLE." SET avatar='".$av[0]."' WHERE uid='".$uid."'");

				# Если мы переназначаем группу пользователя
				if(isset($POST->gid) && $POST->gid != 0) {
					# пересчитываем пользователей
					$this->count_users($POST->gid);
				}

				# Уведомление пользователю на электропочту
				$smarty->assign("login", $POST->login);
				$smarty->assign("nickname", $POST->nickname);
				$smarty->assign("email", $POST->email);
				$smarty->assign("password", $POST->password);
				$smarty->assign("site", $site);
				$message = $tpl->load_template("email_new_registration", true);

				sendmail($POST->email, "Вас зарегистрировали на сайте ".$site['title'], $message);


				# уведомление
				$parse->msg("Пользователь был успешно добавлен. Уведомление об учетной записи отправлено на его электронную почту.");

				# переход
				if(isset($POST->create_user_ae)) go(CP."?act=users");
				else go(CP."?act=users&part=edit_user&uid=".$uid);
			}
			else goback();
		}

		# groups
		$groups = array();
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

		global $db, $smarty, $tpl, $POST, $parse;

		if(isset($POST->create_group) || isset($POST->create_group_ae)) {

			# title
			if(!isset($POST->title) || trim($POST->title) == "") $parse->msg("У группы должно быть название!", false);
			if(isset($POST->title) && trim($POST->title) != "" && $db->check_id($POST->title, USERS_GROUP_TABLE, "title")) $parse->msg("Группа с таким название уже существует", false);

			if(!isset($_SESSION['error'])) {

				$db->query("INSERT INTO ".USERS_GROUP_TABLE." (title, date_create, date_update)
								       VALUES ('".$POST->title."', '".time()."', '".time()."')");
				$gid = $db->insert_id();

				# уведомление
				$parse->msg("Группа была успешно создана.");

				# переход
				if(isset($POST->create_group_ae)) go(CP."?act=users&part=group_list");
				else go(CP."?act=users&part=edit_group&gid=".$gid);
			}
			else goback();
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

		global $db, $users, $parse, $smarty, $tpl;

		# security superamin
		if($uid == 1 && $users->uid != 1) {
			$parse->msg("Радктировать учетную запись суперадмина, может только суперадмин!", false);
			goback();
		}
		else {
			$q = $db->query("SELECT uid, gid, status, avatar, login, nickname, email, title, date_create, last_visit, user_name, user_surname, user_last_name, user_birthdate, user_sex FROM ".USERS_TABLE." WHERE uid='".$uid."'");
			$user = $db->fetch_assoc($q);

			# user personal data birth date
			if($user['user_birthdate'] != 0) $user['user_birthdate'] = date("d.m.Y", $user['user_birthdate']);
			else $user['user_birthdate'] = "";

			# i am groot
			$i_am_groot = false;
			if($users->uid == $uid) $i_am_groot = true;

			# groups
			$groups = array();
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

		# отрисовываем шаблон
		$smarty->assign("group", $group);
		$content = $tpl->load_template("users_edit_group", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Функция обновляет данные пользователя в БД
	 *
	 * @param int $uid - уникальный идентификатор пользователя
	 */
	private function update_user($uid) {

		global $db, $POST, $config, $site, $users, $img, $security, $parse, $smarty, $tpl;

		if(isset($POST->update_user) || isset($POST->update_user_ae)) {

			$q = $db->query("SELECT login, nickname, email, avatar FROM ".USERS_TABLE." WHERE uid='".$uid."'");
			$udata = $db->fetch_assoc($q);

			$query = "";

			# login
			if(isset($POST->login) && trim($POST->login) != "")
				if(!$users->check_field("login", $POST->login, $udata['login']))
					$parse->msg("Логин не должен совпадать с логином другого пользователя!", false);
				else
					$query .= "login='".$POST->login."', ";

			else
				$parse->msg("У пользователя должен быть логин.", false);

			# nickname
			if(isset($POST->nickname) && trim($POST->nickname) != "")
				if(!$users->check_field("nickname", $POST->nickname, $udata['nickname']))
					$parse->msg("Никнейм не должен совпадать с никнеймом другого пользователя!", false);
				else
					$query .= "nickname='".$POST->nickname."', ";

			else
				$parse->msg("У пользователя должен быть Никнейм.", false);


			# email
			if(isset($POST->email) && trim($POST->email) != "")
				if(!$users->check_field("email", $POST->email, $udata['email']))
					$parse->msg("Указанный email уже существует в Базе Данных!", false);
				else
					$query .= "email='".$POST->email."', ";

			else
				$parse->msg("E-mail должен быть указан обязательно для каждого пользователя.", false);

			# status
			$query .= ((isset($POST->status) && $POST->status == 1) || $uid == 1) ? "status='1', " : "status='0', " ;

			# title
			$query .= ((isset($POST->title) && $POST->title == "a") || $uid == 1) ? "title='a', " : "title='u', " ;

			# group
			$query .= (isset($POST->gid) && $db->check_id($POST->gid, USERS_GROUP_TABLE, "gid")) ? "gid='".$POST->gid."', " : "gid='0', " ;

			# personal data
			if(isset($POST->user_name)) 						$query .= " user_name='".$POST->user_name."',";
			else									$query .= " user_name='',";
			if(isset($POST->user_surname)) 						$query .= " user_surname='".$POST->user_surname."',";
			else									$query .= " user_surname='',";
			if(isset($POST->user_last_name)) 					$query .= " user_last_name='".$POST->user_last_name."',";
			else									$query .= " user_last_name='',";
			if(isset($POST->user_birthdate) && $POST->user_birthdate != "") 	$query .= " user_birthdate='".$parse->date->rusint_to_unix($POST->user_birthdate)."',";
			else 									$query .= " user_birthdate='0',";
			if(!isset($POST->user_sex))						$query .= " user_sex='n',";
			elseif($POST->user_sex == "m")						$query .= " user_sex='m',";
			elseif($POST->user_sex == "f")						$query .= " user_sex='f',";
			else									$query .= " user_sex='n',";

			# avatar
			$av = $img->upload_image("avatar", "", array($config->users_avatar_width, $config->users_avatar_height), array("filename"=>"av_".$uid, "watermark"=>false, "modify"=>false));
			if(isset($av[0])) {
				if($udata['avatar'] != "" && $udata['avatar'] != $av[0]) unlink(_UPLOADIMAGES."/".$udata['avatar']);
				$query .= "avatar='".$av[0]."', ";
			}


			# update
			if(!isset($_SESSION['error'])) {

				# password
				if(isset($POST->password) && trim($POST->password) != "") {
					$salt = $security->create_new_salt();
					$password = $security->hashing_password($POST->password, $salt);

					$query .= "password='".$password."', salt='".$salt."', ";
				}

				$db->query("UPDATE ".USERS_TABLE." SET ".$query." date_update='".time()."' WHERE uid='".$uid."'");

				# Если мы переназначаем группу пользователя
				if(isset($POST->gid) && $POST->gid != $POST->now_gid) {
					# пересчитываем пользователей
					if($POST->gid != 0)	$this->count_users($POST->gid);
					if($POST->now_gid != 0)	$this->count_users($POST->now_gid);
				}


				# notice
				$parse->msg("Данные пользователя #{$uid} успешно обновлены.");

				# Уведомление пользователю на электропочту
				$smarty->assign("login", $POST->login);
				$smarty->assign("nickname", $POST->nickname);
				$smarty->assign("email", $POST->email);
				$smarty->assign("password", $POST->password);
				$smarty->assign("site", $site);
				$message = $tpl->load_template("email_update_userdata", true);

				sendmail($POST->email, "Ваши данные на \"".$site['title']."\" были обновлены администрацией", $message);


				# переход
				if(isset($POST->update_user_ae)) go(CP."?act=users");
				else go(CP."?act=users&part=edit_user&uid=".$uid);
			}
			else goback();
		}
		else goback();
	}


	/**
	 * Функция обновляет данные группы пользователей БД
	 *
	 * @param int $gid - уникальный идентификатор группы
	 */
	private function update_group($gid) {

		global $db, $POST, $users, $parse;

		if(isset($POST->update_group) || isset($POST->update_group_ae)) {

			$q = $db->query("SELECT title FROM ".USERS_GROUP_TABLE." WHERE gid='".$gid."'");
			$gdata = $db->fetch_assoc($q);

			$query = "";

			# login
			if(isset($POST->title) && trim($POST->title) != "")
				if(!$users->check_field("title", $POST->title, $gdata['title'], USERS_GROUP_TABLE))
					$parse->msg("Название группы не может совпадать с названием другой группы!", false);
				else
					$query .= "title='".$POST->title."', ";

			else
				$parse->msg("У группы должно быть название.", false);

			# update
			if(!isset($_SESSION['error'])) {

				# update
				$db->query("UPDATE ".USERS_GROUP_TABLE." SET ".$query." date_update='".time()."' WHERE gid='".$gid."'");
				$this->count_users($gid);

				# notice
				$parse->msg("Данные группы #{$gid} успешно обновлены.");

				# переход
				if(isset($POST->update_group_ae)) go(CP."?act=users&part=group_list");
				else go(CP."?act=users&part=edit_group&gid=".$gid);
			}
			else goback();
		}
		else goback();
	}


	/**
	 * Функция удаляет выбранного пользователя из БД
	 *
	 * @param int $uid - уникальный идентификатор пользователя
	 */
	private function delete_user($uid) {

		global $db, $parse;

		# О Боже, только не это...
		if($uid == 1) {
			$parse->msg("Нельзя удалить учетную запись главного администратора!", false);
		}
		else {
			$q = $db->query("SELECT gid, avatar FROM ".USERS_TABLE." WHERE uid='".$uid."'");
			$data = $db->fetch_assoc($q);

			# удаляем аватарку.
			if($data['avatar'] != "" && file_exists(_UPLOADIMAGES."/".$data['avatar'])) unlink(_UPLOADIMAGES."/".$data['avatar']);

			$this->count_users($data['gid']);

			# удаляем юзера
			$db->query("DELETE FROM ".USERS_TABLE." WHERE uid='".$uid."'");
			$parse->msg("Пользователь #{$uid} был успешно удален из Базы Данных.");

			# удаляем его почту
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

		global $db, $parse;

		$db->query("UPDATE ".USERS_TABLE." SET gid='0' WHERE gid='".$gid."'");

		$db->query("DELETE FROM ".USERS_GROUP_TABLE." WHERE gid='".$gid."'");
		$parse->msg("Группа #{$gid} был успешна удалена из Базы Данных.");

		# go
		goback();
	}


	/**
	 * Функция проверяет кол-во пользователей состоящих в группе.
	 *
	 * @param int $gid - уникальный идентификатор группы
	 */
	private function count_users($gid) {

		global $db, $parse;

		# count
		$q = $db->query("SELECT count(*) FROM ".USERS_TABLE." WHERE gid='".$gid."'");
		$c = $db->fetch_row($q);

		# update
		$db->query("UPDATE ".USERS_GROUP_TABLE." SET users='".$c[0]."' WHERE gid='".$gid."'");

		# уведомление
		if(DEBUGMODE) $parse->msg("Информация о кол-ве пользователей для группы {$gid} обновлена.");
	}
}


/**
 * Init class
 */
$acp_users = new ACP_USERS;
?>