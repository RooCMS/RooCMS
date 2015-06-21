<?php
/**
 * @package      RooCMS
 * @subpackage   Admin Control Panel
 * @subpackage   Users settings
 * @author       alex Roosso
 * @copyright    2010-2015 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.2
 * @since        $date$
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 *   RooCMS - Russian free content managment system
 *   Copyright (C) 2010-2014 alex Roosso aka alexandr Belov info@roocms.com
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
 *   Copyright (C) 2010-2014 alex Roosso (александр Белов) info@roocms.com
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
	function __construct() {

		global $db, $roocms, $security, $tpl, $GET;


		# Проверяем идентификатор юзера
		if(isset($GET->_uid) && $db->check_id($GET->_uid, USERS_TABLE, "uid")) $this->uid = $GET->_uid;
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

		$q = $db->query("SELECT uid, status, login, nickname, email, title, date_create, date_update, last_visit FROM ".USERS_TABLE." ORDER BY uid ASC");
		while($row = $db->fetch_assoc($q)) {

			$row['date_create'] = $parse->date->unix_to_rus($row['date_create'], false, true, false);
			$row['date_update'] = $parse->date->unix_to_rus($row['date_update'], false, true, false);

			$row['last_visit'] = $parse->date->unix_to_rus($row['last_visit'], false, true, true);

			$data[] = $row;
		}

		$smarty->assign("data", $data);
		$content = $tpl->load_template("users_view_list", true);
		$smarty->assign("content", $content);
	}


	/**
	 * Функция для создания нового пользователя
	 */
	private function create_new_user() {

		global $db, $smarty, $tpl, $POST, $parse, $security, $site;

		if(isset($POST->create_user) || isset($POST->create_user_ae)) {

			# nickname
			if(!isset($POST->nickname) || trim($POST->nickname) == "") $POST->nickname = mb_ucfirst($POST->login);
			$POST->nickname = $this->check_new_nickname($POST->nickname);

			# login
			if(!isset($POST->login) || trim($POST->login) == "") $parse->msg("У пользователя должен быть логин!", false);
			else $POST->login = $parse->text->transliterate($POST->login);
			if(isset($POST->login) && trim($POST->login) != "" && $db->check_id($POST->login, USERS_TABLE, "login")) $parse->msg("Пользователь с таким логином уже существует", false);

			# email
			if(!isset($POST->email) || trim($POST->email) == "") $parse->msg("Обязательно указывать электронную почту для каждого пользователя", false);
			if(isset($POST->email) && trim($POST->email) != "" && !$parse->valid_email($POST->email)) $parse->msg("Некоректный адрес электронной почты", false);
			if(isset($POST->email) && trim($POST->email) != "" && $db->check_id($POST->email, USERS_TABLE, "email")) $parse->msg("Пользователь с таким адресом почты уже существует", false);

			# title
			$POST->title = (isset($POST->title) && $POST->title == "a") ? "a" : "u" ;

			if(!isset($_SESSION['error'])) {

				#password
				if(!isset($POST->password) || trim($POST->password) == "") $POST->password = $security->create_new_password();
				$salt = $security->create_new_salt();
				$password = $security->hashing_password($POST->password, $salt);

				$db->query("INSERT INTO ".USERS_TABLE." (login, nickname, email, title, password, salt, date_create, date_update, last_visit, status)
								 VALUES ('".$POST->login."', '".$POST->nickname."', '".$POST->email."', '".$POST->title."', '".$password."', '".$salt."', '".time()."', '".time()."', '".time()."', '1')");
				$uid = $db->insert_id();

				# Уведомление пользователю на электропочту
				$smarty->assign("login", $POST->login);
				$smarty->assign("nickname", $POST->nickname);
				$smarty->assign("email", $POST->email);
				$smarty->assign("password", $POST->password);
				$smarty->assign("site", $site);
				$message = $tpl->load_template("email_new_registration", true);

				sendmail($POST->email, "Вас зарегестрировали на сайте ".$site['title'], $message);


				# уведомление
				$parse->msg("Пользователь был успешно добавлен. Уведомление об учетной записи отправлено на его электронную почту.");

				# переход
				if(isset($POST->create_user_ae)) go(CP."?act=users");
				else go(CP."?act=users&part=edit_user&uid=".$uid);
			}
			else goback();
		}


		# отрисовываем шаблон
		$content = $tpl->load_template("users_create_new_user", true);
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
			$q = $db->query("SELECT uid, status, login, nickname, email, title, date_create, last_visit FROM ".USERS_TABLE." WHERE uid='".$uid."'");
			$user = $db->fetch_assoc($q);


			$i_am_groot = false;
			if($users->uid == $uid) $i_am_groot = true;

			# отрисовываем шаблон
			$smarty->assign("i_am_groot", $i_am_groot);
			$smarty->assign("user", $user);
			$content = $tpl->load_template("users_edit_user", true);
			$smarty->assign("content", $content);
		}
	}


	/**
	 * Функция обновляет данные пользователя в БД
	 *
	 * @param int $uid - уникальный идентификатор пользователя
	 */
	private function update_user($uid) {

		global $db, $POST, $parse, $security, $smarty, $tpl, $site;

		if(isset($POST->update_user) || isset($POST->update_user_ae)) {

			$q = $db->query("SELECT login, nickname, email FROM ".USERS_TABLE." WHERE uid='".$uid."'");
			$udata = $db->fetch_assoc($q);

			$query = "";

			# login
			if(isset($POST->login) && trim($POST->login) != "")
				if(!$this->check_field("login", $POST->login, $udata['login']))
					$parse->msg("Логин не должен совпадать с логином другого пользователя!", false);
				else
					$query .= "login='".$POST->login."', ";

			else
				$parse->msg("У пользователя должен быть логин.", false);

			# nickname
			if(isset($POST->nickname) && trim($POST->nickname) != "")
				if(!$this->check_field("nickname", $POST->nickname, $udata['nickname']))
					$parse->msg("Никнейм не должен совпадать с никнеймом другого пользователя!", false);
				else
					$query .= "nickname='".$POST->nickname."', ";

			else
				$parse->msg("У пользователя должен быть Никнейм.", false);

			# email
			if(isset($POST->email) && trim($POST->email) != "")
				if(!$this->check_field("email", $POST->email, $udata['email']))
					$parse->msg("Указанный email уже существует в Базе Данных!", false);
				else
					$query .= "email='".$POST->email."', ";

			else
				$parse->msg("E-mail должен быть указан обязательно для каждого пользователя.", false);

			# status
			$query .= ((isset($POST->status) && $POST->status == 1) || $uid == 1) ? "status='1', " : "status='0', " ;

			# title
			$query .= (isset($POST->title) && $POST->title == "a") ? "title='a', " : "title='u', " ;


			# update
			if(!isset($_SESSION['error'])) {

				# password
				if(isset($POST->password) && trim($POST->password) != "") {
					$salt = $security->create_new_salt();
					$password = $security->hashing_password($POST->password, $salt);

					$query .= "password='".$password."', salt='".$salt."', ";
				}

				$db->query("UPDATE ".USERS_TABLE." SET ".$query." date_update='".time()."' WHERE uid='".$uid."'");

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
			$db->query("DELETE FROM ".USERS_TABLE." WHERE uid='".$uid."'");
			$parse->msg("Пользователь #{$uid} был успешно удален из Базы Данных.");
		}

		# go
		goback();
	}


	/**
	 * Проверяем поля на уникальность
	 *
	 * ВНИМАНИЕ! Не расчитывайте на эту функцию, она временная.
	 *
	 * @param string $field   - поле
	 * @param string $name    - значение поля
	 * @param string $without - Выражение исключения для mysql запроса
	 *
	 * @return bool $res - true - если значение не уникально, false - если значение уникально
	 */
	private function check_field($field, $name, $without="") {

		global $db;

		$res = false;

		if(trim($without) != trim($name)) {

			$w = (trim($without) != "") ? $field."!='".$without."'" : "" ;

			if(!$db->check_id($name, USERS_TABLE, $field, $w))
				$res = true;
		}
		else $res = true;

		return $res;
	}


	/**
	 * Функция проверяет Никнейм на уникальность.
	 * В случае повторения добавляет к никнейму несколько цифр.
	 *
	 * ВНИМАНИЕ! Не расчитывайте на эту функцию. Она временная.
	 *
	 * @param string $nickname - Никнейм
	 *
	 * @return string
	 */
	private function check_new_nickname($nickname) {

		global $db;

		if($db->check_id($nickname, USERS_TABLE, "nickname")) {
			$nickname = $this->check_new_nickname($nickname.randcode(2,"0123456789"));
		}

		return $nickname;
	}
}


/**
 * Init class
 */
$acp_pages = new ACP_USERS;
?>