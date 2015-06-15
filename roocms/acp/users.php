<?php
/**
 * @package      RooCMS
 * @subpackage   Admin Control Panel
 * @subpackage   Users settings
 * @author       alex Roosso
 * @copyright    2010-2015 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.0
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
	private $id = 0;



	/**
	 * Вперед и только веперед
	 */
	function __construct() {

		global $roocms, $security, $tpl, $GET;


		# Проверяем идентификатор юзера
		if(isset($GET->_id) && $db->check_id($GET->_id, USERS_TABLE)) $this->id = $GET->_id;


		# action
		switch($roocms->part) {

			case 'create':
				$this->create_new_user();
				break;

			case 'edit':

				break;

			case 'update':

				break;

			case 'delete':

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

		$q = $db->query("SELECT id, login, nickname, email, date_create, date_update, last_visit FROM ".USERS_TABLE." ORDER BY id ASC");
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
	function create_new_user() {

		global $db, $smarty, $tpl, $POST, $parse, $security, $site;

		if(isset($POST->create_user) || isset($POST->create_user_ae)) {

			# login
			if($db->check_id($POST->login, USERS_TABLE, "login")) $parse->msg("Пользователь с таким логином уже существует", false);

			# nickname
			if(!isset($POST->nickname) || trim($POST->nickname) == "") $POST->nickname = mb_ucfirst($POST->login);
			$POST->nickname = $this->check_new_nickname($POST->nickname);

			# email
			if(!$parse->valid_email($POST->email)) $parse->msg("Некоректный адрес электронной почты", false);
			if($db->check_id($POST->email, USERS_TABLE, "email")) $parse->msg("Пользователь с таким адресом почты уже существует", false);


			if(!isset($_SESSION['error'])) {

				#password
				if(!isset($POST->password) || trim($POST->password) == "") $POST->password = $security->create_new_password();
				$salt = $security->create_new_salt();
				$password = $security->hashing_password($POST->password, $salt);

				$db->query("INSERT INTO ".USERS_TABLE." (login, nickname, email, password, salt, date_create, date_update, last_visit)
								 VALUES ('".$POST->login."', '".$POST->nickname."', '".$POST->email."', '".$password."', '".$salt."', '".time()."', '".time()."', '".time()."')");
				$id = $db->insert_id();

				# Уведомление пользователю на электропочту
				$smarty->assign("login", $POST->login);
				$smarty->assign("nickname", $POST->nickname);
				$smarty->assign("password", $POST->password);
				$smarty->assign("site", $site);
				$message = $tpl->load_template("email_new_registration", true);

				sendmail($POST->email, "Вас зарегестрировали на сайте ".$site['title'], $message);


				# уведомление
				$parse->msg("Пользователь был успешно добавлен. Уведомление об учетной записи отправлено на его электронную почту.");

				# переход
				if(isset($POST->create_user_ae)) go(CP."?act=users");
				else go(CP."?act=users&part=edit&user=".$sid);
			}
			else goback();
		}


		# отрисовываем шаблон
		$content = $tpl->load_template("users_create_new_user", true);
		$smarty->assign("content", $content);
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