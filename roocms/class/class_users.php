<?php
/**
 *   RooCMS - Russian free content managment system
 *   Copyright © 2010-2017 alexandr Belov aka alex Roosso. All rights reserved.
 *   Contacts: <info@roocms.com>
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
 *   RooCMS - Русская бесплатная система управления контентом
 *   Copyright © 2010-2017 александр Белов (alex Roosso). Все права защищены.
 *   Для связи: <info@roocms.com>
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

/**
 * @package      RooCMS
 * @subpackage   Engine RooCMS classes
 * @author       alex Roosso
 * @copyright    2010-2017 (c) RooCMS
 * @link         http://www.roocms.com
 * @version      1.4
 * @since        $date$
 * @license      http://www.gnu.org/licenses/gpl-3.0.html
 */


//#########################################################
// Anti Hack
//---------------------------------------------------------
if(!defined('RooCMS')) {
	die('Access Denied');
}
//#########################################################


/**
 * Class Users
 */
class Users extends Security {

	# user uniq data
	public	$uid		= 0;		# [int]		user id
	public	$login		= "";		# [string]	user login
	public	$nickname	= "";		# [string]	user nickname
	public	$avatar		= "";		# [string]	user avatar
	public	$email		= "";		# [string]	user nickname
	public	$title		= "u";		# [enum]	user title
	public	$gid		= 0;		# [int]		user group id
	public	$gtitle		= "";		# [string]	user group title
	public	$token		= "";		# [string]	user security token

	# user ban status
	public  $ban		= 0;		# [enum]	ban status
	public  $ban_reason	= "";		# [string]	ban reason
	public  $ban_expiried	= 0;		# [int]		ban date expiried (unixtimestamp)


	public	$userdata	= array('uid'=>0);

	# user global data
	private	$usersession	= "";		# [string]	user session
	private $userip		= "";		# [string]	user ip address
	private	$useragent	= "";		# [string]	user agent string
	private $referer	= "";		# [string]	user referer



	/**
	 * Work your magic
	 */
	public function __construct() {

		global $roocms;


		# get user data
		$this->usersession	&= $roocms->usersession;
		$this->userip		&= $roocms->userip;
		$this->useragent 	&= $roocms->useragent;
		$this->referer		&= $roocms->referer;


		# init user
		$this->init_user();


		if($this->uid != 0) {
			# control user data for security
			$this->control_userdata();

			# update users info
			$this->update_user_time_last_visit($this->uid);
		}
	}


	/**
	 * Получаем персональные данные пользователя
	 */
	private function init_user() {

		global $db, $roocms, $parse;

		if(isset($roocms->sess['login']) && trim($roocms->sess['login']) != "" && $db->check_id($roocms->sess['login'], USERS_TABLE, "login", "status='1'") && isset($roocms->sess['token']) && strlen($roocms->sess['token']) == 32) {

			# get data
			$q    = $db->query("SELECT u.uid, u.gid, u.login, u.nickname, u.avatar, u.email,
 							u.user_name, u.user_surname, u.user_last_name, u.user_birthdate, u.user_sex,
							u.title, u.password, u.salt, u.ban, u.ban_reason, u.ban_expiried,
							g.title as gtitle
						FROM ".USERS_TABLE." AS u
						LEFT JOIN ".USERS_GROUP_TABLE." AS g ON (g.gid = u.gid)
						WHERE u.login='".$roocms->sess['login']."' AND u.status='1'");
			$data = $db->fetch_assoc($q);

			# uid
			$this->uid	= $data['uid'];
			# gid
			$this->gid	= $data['gid'];
			# gtitle
			$this->gtitle	= $data['gtitle'];
			# login
			$this->login	= $data['login'];
			# title
			$this->title	= $data['title'];
			# nickname
			$this->nickname	= $data['nickname'];
			# avatar
			$this->avatar	= $data['avatar'];
			# email
			$this->email	= $data['email'];
			# ban
			$this->ban		= $data['ban'];
			$this->ban_reason	= $data['ban_reason'];
			$this->ban_expiried	= $data['ban_expiried'];

			# array userdata
			$this->userdata = array(
				'uid'			=> $data['uid'],
				'gid'			=> $data['gid'],
				'gtitle'		=> $data['gtitle'],
				'login'			=> $data['login'],
				'nickname'		=> $data['nickname'],
				'avatar'		=> $data['avatar'],
				'email'			=> $data['email'],
				'title'			=> $data['title'],
				'user_name'		=> $data['user_name'],
				'user_surname'		=> $data['user_surname'],
				'user_last_name'	=> $data['user_last_name'],
				'user_birthdate'	=> $parse->date->unix_to_rus($data['user_birthdate']),
				'user_birthdaten'	=> date("d.m.Y", $data['user_birthdate']),
				'user_sex'		=> $data['user_sex'],
				'ban'			=> $data['ban'],
				'ban_reason'		=> $data['ban_reason'],
				'ban_expiried'		=> $parse->date->unix_to_rus($data['ban_expiried'])
			);


			# security token
			$this->token	= $this->hashing_token($roocms->sess['login'], $data['password'], $data['salt']);
		}
	}


	/**
	 * Обновляем простую информацию пользователя, вроде времени последнего визита на сайт.
	 *
	 * @param int $uid - уникальные идентификатор пользователя
	 */
	private function update_user_time_last_visit($uid) {

		global $db, $roocms;

		# update time last visited
		$db->query("UPDATE ".USERS_TABLE." SET last_visit='".time()."', user_ip='".$roocms->userip."' WHERE uid='".$uid."' AND status='1'");
	}


	/**
	 * Функция получения данных о пользователе.
	 *
	 * @param int $uid идентификатор пользователя
	 */
	public function get_user_data($uid) {

		global $db;
	}


	/**
	 * Проверяем поля на уникальность
	 *
	 * ВНИМАНИЕ! Не расчитывайте на эту функцию, она временная.
	 *
	 * @param string $field   - поле
	 * @param string $name    - значение поля
	 * @param string $without - Выражение исключения для mysql запроса
	 * @param string $table	  - Таблица для проверки
	 *
	 * @return bool $res - true - если значение не уникально, false - если значение уникально
	 */
	public function check_field($field, $name, $without="", $table=USERS_TABLE) {

		global $db;

		$res = false;

		if(trim($without) != trim($name)) {

			$w = (trim($without) != "") ? $field."!='".$without."'" : "" ;

			if(!$db->check_id($name, $table, $field, $w)) {
				$res = true;
			}
		}
		else {
			$res = true;
		}

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
	public function uniq_nickname($nickname) {

		global $db;

		if($db->check_id($nickname, USERS_TABLE, "nickname")) {
			$nickname = $this->uniq_nickname($nickname.randcode(2,"0123456789"));
		}

		return $nickname;
	}


	/**
	 * Функция удаляет пользовательский аватар.
	 *
	 * @param $uid - Уникальный идентификатор пользователя
	 */
	public function delete_avatar($uid) {

		global $db;

		if($db->check_id($uid, USERS_TABLE, "uid", "avatar!=''") && ($this->uid == $uid || $this->title == "a")) {

			$q = $db->query("SELECT avatar FROM ".USERS_TABLE." WHERE uid='".$uid."'");
			$data = $db->fetch_assoc($q);

			if(file_exists(_UPLOADIMAGES."/".$data['avatar'])) {
				unlink(_UPLOADIMAGES."/".$data['avatar']);
				$db->query("UPDATE ".USERS_TABLE." SET avatar='' WHERE uid='".$uid."'");
			}
		}
	}


	/**
	 * Функция првоерки почты пользователя.
	 * Проверяем на дубли и корректность.
	 *
	 * @param $email - адрес электронной почты пользователя.
	 */
	public function valid_user_email($email) {

		global $db, $logger, $parse;

		if(!isset($email) || trim($email) == "") {
			$logger->error("Электронная почта обязательная для каждого пользователя");
		}
		if(isset($email) && trim($email) != "" && !$parse->valid_email($email)) {
			$logger->error("Некоректный адрес электронной почты");
		}
		if(isset($email) && trim($email) != "" && $db->check_id($email, USERS_TABLE, "email")) {
			$logger->error("Пользователь с таким адресом почты уже существует");
		}
	}


	/**
	 * Проверяем персональные данные пользователя при попытки их создания и обновления
	 */
	public function correct_personal_data() {

		global $POST, $parse;

		# user name/surname/last_name
		if(!isset($POST->user_name)) {
			$POST->user_name = "";
		}
		if(!isset($POST->user_surname)) {
			$POST->user_surname = "";
		}
		if(!isset($POST->user_last_name)) {
			$POST->user_last_name = "";
		}

		# user birthdate
		if(isset($POST->user_birthdate) && $POST->user_birthdate != "") {
			$POST->user_birthdate = $parse->date->rusint_to_unix($POST->user_birthdate);
		}
		else {
			$POST->user_birthdate = 0;
		}

		#check user sex
		if(isset($POST->user_sex) && $POST->user_sex == "m") {
			$POST->user_sex = "m";
		}
		elseif(isset($POST->user_sex) && $POST->user_sex == "f") {
			$POST->user_sex = "f";
		}
		else {
			$POST->user_sex = "n";
		}
	}


	/**
	 * Функция проверяет никнейм пользователя указанный во время создания или обновления учетной записи
	 */
	public function check_post_new_nickname() {

		global $POST;

		# Если никнейм не ввведен, делаем никнем из логина
		if(!isset($POST->nickname) && isset($POST->login)) {
			$POST->nickname = mb_ucfirst($POST->nickname);
		}

		# теперь проверяем на никальность
		if(isset($POST->nickname)) {
			$POST->nickname = $this->uniq_nickname($POST->nickname);
		}
	}
}
?>